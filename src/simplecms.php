<?php

namespace invacto\SimpleCMS;


use Dotenv\Dotenv;
use invacto\SimpleCMS\auth\Authenticator;
use Psr\Http\Message\UploadedFileInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;
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

    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * @var string
     */
    private $appDir;

    public function __construct($appDir)
    {
        $this->appDir = $appDir;

        $dotenv = Dotenv::createImmutable($appDir);
        $dotenv->safeLoad();

        $this->imageDirectory = $appDir . "/public/storage";
        $fileSystemLoader = new FilesystemLoader($appDir . "/views");
        $this->twig = new Environment($fileSystemLoader, []);

        $textFunction = new TwigFunction('text', function($context, $name, $defaultText = "") {
            $text = $this->findSnippet($context, $name, $defaultText);
            if ($this->authenticator->hasUser()) {
                return '<span class="simplecms__editable" data-name="' . $name . '"  spellcheck="false">' . $text . '</span>';
            } else {
                return $text;
            }
        }, ["is_safe" => ["html"], "needs_context" => true]);
        $this->twig->addFunction($textFunction);

        $this->twig->addFunction(new TwigFunction('img', function($context, $name, $defaultValue = "", $alt = "", $other = "") {
            $src = $this->findSnippet($context, $name, null);
            if ($src == null) {
                $src = $defaultValue;
            } else {
                $src = '/storage/' . $src;
            }
            if ($this->authenticator->hasUser()) {
                return '<img src="'.$src.'" alt="'.$alt.'" data-simplecms-img="'.$name.'" '.$other.'>';
            } else {
                return '<img src="'.$src.'" alt="'.$alt.'"">';
            }
        }, ["is_safe" => ["html"], "needs_context" => true]));

        $this->twig->addFunction(new TwigFunction('bgImg', function($context, $name, $defaultValue = "") {
            $src = $this->findSnippet($context, $name, null);
            if ($src == null) {
                $src = $defaultValue;
            } else {
                $src = '/storage/' . $src;
            }

            if ($this->authenticator->hasUser()) {
                return 'style="background-image: url(\''.$src.'\')" data-simplecms-bg-image="'.$name.'"';
            } else {
                return 'style="background-image: url(\''.$src.'\')"';
            }
        }, ["is_safe" => ["html"], "needs_context" => true]));

        $this->twig->addFunction(new TwigFunction('wysiwyg', function ($context, $name, $defaultValue = "<h1>Your text</h1>") {
            $text = $this->findSnippet($context, $name, $defaultValue);
            if ($this->authenticator->hasUser()) {
                return '<div class="simplecms__editor" data-simplecms-name="'.$name.'">'.$text.'</div>';
            } else {
                return $text;
            }
        }, ["is_safe" => ["html"], "needs_context" => true]));

        $this->twig->addFunction(new TwigFunction('head', function($context) {
            if (!$this->authenticator->hasUser()) return "";

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
            if (!$this->authenticator->hasUser()) return "";

            $popupHtml = file_get_contents(__DIR__ . '/foot.html');
            return $popupHtml;
        }, ["is_safe" => ["html"]]));

        $this->app = AppFactory::create();
        $this->app->addBodyParsingMiddleware();

        // TODO: "Get settings from environment variables or some shit"
        // TODO: throw error when environment variables are not set
        $this->db = new Database(
            $_ENV["DB_HOST"] . ":" . $_ENV["DB_PORT"],
            $_ENV["DB_NAME"],
            $_ENV["DB_USER"],
            $_ENV["DB_PASS"]
        );

        // TODO: Get a secret key from environment variables or $cms->setSecret("...")
        $this->authenticator = new Authenticator("SecretKey", $this->db);

        $this->createEditEndpoints();
    }

    public function page($path, $pageFile) {
        $this->app->get($path, function (Request $request, Response $response, array $args) use ($pageFile) {
            $snippets = $this->db->select("SELECT * FROM snippets WHERE page = :page", [":page" => $pageFile]);

            $html = $this->render($pageFile, ["__pageFile" => $pageFile, "__snippets" => $snippets]);
            $response->getBody()->write($html);
            return $response;
        });
    }

    public function redirect($from, $to) {
        $this->app->get($from, function (Request $request, Response $response, array $args) use ($to) {
            return $this->sendRedirect($response, $to);
        });
    }

    public function run() {
        $this->app->run();
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
            if (!$this->authenticator->hasUser()) return "";

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
            if (!$this->authenticator->hasUser()) return $this->unauthorized($response);

            $params = $request->getParsedBody();

            $page = $params['page'];
            $name = $params['name'];
            $files = $request->getUploadedFiles();
            $uploadedFile = $files['file'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($this->imageDirectory, $uploadedFile);

                $existingImageSnippet = $this->updateOrCreateSnippet(
                    $page,
                    $name,
                    $filename
                );

                if ($existingImageSnippet !== null) {
                    $previousFileName = $existingImageSnippet["value"];
                    $totalPath = $this->imageDirectory . "/" . $previousFileName;
                    if (file_exists($totalPath)) {
                        unlink($totalPath);
                    }
                }

                return $this->JSON($response, ["fileName" => $filename, "url" => "/storage/$filename"]);
            }
            return $this->JSON($response, ["error" => "Failed to upload file"]);
        });
        $this->app->post("/simplecms/upload/{purpose}", function (Request $request, Response $response, $args = []) {
            if (!$this->authenticator->hasUser()) return $this->unauthorized($response);

            $files = $request->getUploadedFiles();
            $uploadedFile = $files['file'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($this->imageDirectory, $uploadedFile, $args['purpose']);

                return $this->JSON($response, ["fileName" => $filename, "url" => "/storage/$filename"]);
            }
            return $this->JSON($response, ["error" => "Failed to upload file"]);
        });

        $this->app->get("/simplecms/login", function (Request $request, Response $response, $args = []) {
            return $this->renderLibraryPage($response, "login.twig");
        });
        $this->app->post("/simplecms/login", function (Request $request, Response $response, $args = []) {
            $params = $request->getParsedBody();

            $email = $params['email'];
            $password = $params['password'];

            $user = $this->authenticator->login($email, $password);

            if ($user === false) {
                return $this->renderLibraryPage($response, "login.twig", ["error" => "Invalid email or password"]);
            }

            // TODO: save authed user to cookie or something

            return $this->sendRedirect($response, "/");
        });

        $this->app->get("/simplecms/setup", function (Request $request, Response $response, $args = []) {
            try {
                $existingUsers = $this->db->select("SELECT COUNT(*) as count FROM users");
                $existingUsersCount = $existingUsers[0]["count"];

                if ((int)$existingUsersCount !== 0) {
                    $response->getBody()->write("Page unavailable");
                    return $response;
                }
            } catch (\PDOException $e) {
            }

            return $this->renderLibraryPage($response, "setup.twig");
        });
        $this->app->post("/simplecms/setup", function (Request $request, Response $response, $args = []) {
            $params = $request->getParsedBody();

            try {
                $existingUsers = $this->db->select("SELECT COUNT(*) as count FROM users");
                $existingUsersCount = $existingUsers[0]["count"];

                if ((int)$existingUsersCount !== 0) {
                    $response->getBody()->write("Page unavailable");
                    return $response;
                }
            } catch (\PDOException $e) {
                $this->db->setup();
            }
            $this->authenticator->register($params["admin_email"], $params["admin_password"]);

            return $this->sendRedirect($response, "/");
        });
        $this->app->get("/simplecms/logout", function (Request $request, Response $response, $args = []) {
            $this->authenticator->logout();
            return $this->sendRedirect($response, "/");
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
            return null;
        } else {
            $previousValues = $this->db->select(
            "SELECT * FROM snippets where name = :name AND page = :page",
                [":name" => $name, ":page" => $page]
            );
            $this->db->update("UPDATE snippets SET value = :value WHERE name = :name AND page = :page", $queryParams);
            if (sizeof($previousValues) === 0) return null;
            return $previousValues[0];
        }
    }

    private function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile, string $prefix = null)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        $basename = bin2hex(random_bytes(32));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        if ($prefix != null) {
            $filename = $prefix . "-" . $filename;
        }

        $path = $directory . DIRECTORY_SEPARATOR . $filename;
        $uploadedFile->moveTo($path);

        return $filename;
    }

    private function render($page, $context = []) {
        return $this->twig->load($page)->render($context);
    }

    private function renderLibraryPage(Response $response, $page, $context = []) {
        $fileSystemLoader = new FilesystemLoader(__DIR__ . "/pages");
        $twig = new Environment($fileSystemLoader, []);
        $pageData = $twig->load($page)->render($context);
        $response->getBody()->write($pageData);
        return $response;
    }

    private function JSON(Response $response, $data) {
        $res = json_encode($data);
        $response->getBody()->write($res);
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function renderSimplecmsPage(Response $response, $path, $type = "text/html") {
        $page = file_get_contents($path);
        $response->getBody()->write($page);
        return $response->withHeader('Content-Type', $type);
    }

    private function sendRedirect($response, $to) {
        return $response
            ->withHeader('Location', $to)
            ->withStatus(302);
    }

    private function unauthorized(Response $response) {
        return $response
            ->withStatus(401);
    }

}
