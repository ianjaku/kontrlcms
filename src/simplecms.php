<?php

namespace invacto\SimpleCMS;


use Exception;
use Psr\Http\Message\UploadedFileInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class SimpleCMS {

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var App
     */
    private $app;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var string
     */
    private $imageDirectory;

    public function __construct(string $viewDirectory, string $imageDirectory)
    {
        $this->imageDirectory = $imageDirectory;
        $fileSystemLoader = new FilesystemLoader($viewDirectory);
        $this->twig = new Environment($fileSystemLoader, []);

        $textFunction = new TwigFunction('text', function($context, $name, $defaultText = "") {
            $text = $this->findSnippet($context, $name, $defaultText);
            return '<span class="simplecms__editable" data-name="'.$name.'"  spellcheck="false">' . $text . '</span>';
        }, ["is_safe" => ["html"], "needs_context" => true]);
        $this->twig->addFunction($textFunction);

        $this->twig->addFunction(new TwigFunction('img', function($context, $name, $defaultValue = "", $alt = "", $other = "") {
            $src = $this->findSnippet($context, $name, null);
            if ($src == null) {
                $src = $defaultValue;
            } else {
                $src = '/storage/' . $src;
            }
            return '<img src="'.$src.'" alt="'.$alt.'" data-simplecms-img="'.$name.'" '.$other.'>';
        }, ["is_safe" => ["html"], "needs_context" => true]));

        $this->twig->addFunction(new TwigFunction('bgImg', function($context, $name, $defaultValue = "") {
            $src = $this->findSnippet($context, $name, null);
            if ($src == null) {
                $src = $defaultValue;
            } else {
                $src = '/storage/' . $src;
            }
            return 'style="background-image: url(\''.$src.'\')" data-simplecms-bg-image="'.$name.'"';
        }, ["is_safe" => ["html"], "needs_context" => true]));

        $this->twig->addFunction(new TwigFunction('wysiwyg', function ($context, $name, $defaultValue = "<h1>Your text</h1>") {
            $text = $this->findSnippet($context, $name, $defaultValue);
//            return $text;
            return '<div class="simplecms__editor" data-simplecms-name="'.$name.'">'.$text.'</div>';
        }, ["is_safe" => ["html"], "needs_context" => true]));

        $this->twig->addFunction(new TwigFunction('head', function($context) {
            $pageFile = $context['__pageFile'];
            return "
            <script lang='js'>
                const PAGE_NAME = '$pageFile';
            </script>
            <link rel='stylesheet' href='/simplecms/style' type='text/css'>
            <script src='/simplecms/script' defer></script>
            ";
        }, ["is_safe" => ["html"], "needs_context" => true]));

        $this->twig->addFunction(new TwigFunction("foot", function() {
            $popupHtml = file_get_contents(__DIR__ . '/foot.html');
            return $popupHtml;
        }, ["is_safe" => ["html"]]));

        $this->app = AppFactory::create();
        $this->app->addBodyParsingMiddleware();

        // TODO: "Get settings from environment variables or some shit"
        $this->db = new Database(
            "localhost",
            "simplecms",
            "root",
            "root"
        );

        $this->createEditEndpoints();
    }

    private function findSnippet($context, $name, $defaultValue) {
        $snippets = $context['__snippets'];

        $value = $defaultValue;
        foreach ($snippets as $snippet) {
            if ($snippet['name'] === $name) {
                $value = $snippet['value'];
                break;
            }
        }
        return $value;
    }

    public function page($path, $pageFile) {
        $this->app->get($path, function (Request $request, Response $response, array $args) use ($pageFile) {
            $snippets = $this->db->select("SELECT * FROM snippets WHERE page = :page", [":page" => $pageFile]);

            $html = $this->render($pageFile, ["__pageFile" => $pageFile, "__snippets" => $snippets]);
            $response->getBody()->write($html);
            return $response;
        });
    }

    public function run() {
        $this->app->run();
    }

    private function createEditEndpoints() {
        $this->app->get("/simplecms/style", function (Request $request, Response $response, array $args) {
            $styleData = file_get_contents(__DIR__ . '/../dist/style.css');
            $response->getBody()->write($styleData);
            return $response->withHeader('Content-Type', 'text/css');
        });

        $this->app->get("/simplecms/script", function (Request $request, Response $response, array $args) {
            $styleData = file_get_contents(__DIR__ . '/../dist/app.js');
            $response->getBody()->write($styleData);
            return $response->withHeader('Content-Type', 'text/javascript');
        });

        $this->app->post("/simplecms/update", function (Request $request, Response $response, array $args) {
            $params = $request->getParsedBody();

            $this->updateOrCreateSnippet(
                $params['page'],
                $params['name'],
                $params['value']
            );

            $res = json_encode(["success" => true]);
            $response->getBody()->write($res);
            return $response->withHeader('Content-Type', 'application/json');
        })->add(new JsonBodyParserMiddleware());

        $this->app->post("/simplecms/upload", function (Request $request, Response $response, $args = []) {
            $params = $request->getParsedBody();

            $page = $params['page'];
            $name = $params['name'];
            $files = $request->getUploadedFiles();
            $uploadedFile = $files['file'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                 $filename = $this->moveUploadedFile($this->imageDirectory, $uploadedFile);

                $this->updateOrCreateSnippet(
                    $page,
                    $name,
                    $filename
                );

                return $this->JSON($response, ["fileName" => $filename, "url" => "/storage/$filename"]);
            }

//            return $this->JS

//            $res = json_encode($files['file']);
//            $response->getBody()->write($res);
//            return $response->withHeader('Content-Type', 'application/json');
        });
    }

    private function updateOrCreateSnippet(string $page, string $name, string $value) {
        $queryParams = [
            ":name" => $name,
            ":page" => $page
        ];
        $snippetExists = $this->db->exists("SELECT id FROM snippets WHERE name = :name AND page = :page LIMIT 1", $queryParams);

        $queryParams = [
            ":name" => $name,
            ":page" => $page,
            ":value" => $value
        ];
        $stmt2 = null;
        if (!$snippetExists) {
            $this->db->insert("INSERT INTO snippets (name, page, value) VALUES (:name, :page, :value)", $queryParams);
        } else {
            $this->db->update("UPDATE snippets SET value = :value WHERE name = :name AND page = :page", $queryParams);
        }
    }

    private function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        $basename = bin2hex(random_bytes(32));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $path = $directory . DIRECTORY_SEPARATOR . $filename;
        $uploadedFile->moveTo($path);

        return $filename;
    }

    private function render($page, $context = []) {
        return $this->twig->load($page)->render($context);
//        try {
//        } catch (LoaderError $e) {
//        } catch (RuntimeError $e) {
//        } catch (SyntaxError $e) {
//        }
    }

    private function JSON(Response $response, $data) {
        $res = json_encode($data);
        $response->getBody()->write($res);
        return $response->withHeader('Content-Type', 'application/json');
    }

}
