<?php

namespace invacto\SimpleCMS;


use Dotenv\Dotenv;
use invacto\SimpleCMS\auth\Authenticator;
use invacto\SimpleCMS\plugins\Plugin;
use Psr\Http\Message\UploadedFileInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Middleware\BodyParsingMiddleware;
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

    /**
     * @var BodyParsingMiddleware
     */
    private $bodyParser;

    /**
     * @var Plugin[]
     */
    private $plugins = [];

    public function __construct($appDir)
    {
        $this->appDir = $appDir;

        $dotenv = Dotenv::createImmutable($appDir);
        $dotenv->safeLoad();

        $this->imageDirectory = $appDir . "/public/storage";
        $fileSystemLoader = new FilesystemLoader($appDir . "/views");
        $this->twig = new Environment($fileSystemLoader, []);

        $this->bodyParser = new BodyParsingMiddleware();

//        $this->twig->addFunction(new TwigFunction('text', function($context, $name, $defaultText = "") {
//            $text = $this->findSnippet($context, $name, $defaultText);
//            if ($this->authenticator->hasUser()) {
//                return '<span class="simplecms__editable" data-name="' . $name . '"  spellcheck="false">' . $text . '</span>';
//            } else {
//                return $text;
//            }
//        }, ["is_safe" => ["html"], "needs_context" => true]));

        if (isset($_ENV["BASE_URL"])) {
            $this->twig->addGlobal("BASE_URL", $_ENV["BASE_URL"]);
        } else {
            $this->twig->addGlobal("BASE_URL", "");
        }

//        $this->twig->addFunction(new TwigFunction('img', function($context, $name, $defaultValue = "", $alt = "", $other = "") {
//            $src = $this->findSnippet($context, $name, null);
//            if ($src == null) {
//                $src = $defaultValue;
//            } else {
//                $src = '/storage/' . $src;
//            }
//            if ($this->authenticator->hasUser()) {
//                return '<img src="'.$src.'" alt="'.$alt.'" data-simplecms-img="'.$name.'" '.$other.'>';
//            } else {
//                return '<img src="'.$src.'" alt="'.$alt.'"">';
//            }
//        }, ["is_safe" => ["html"], "needs_context" => true]));

//        $this->twig->addFunction(new TwigFunction('bgImg', function($context, $name, $defaultValue = "") {
//            $src = $this->findSnippet($context, $name, null);
//            if ($src == null) {
//                $src = $defaultValue;
//            } else {
//                $src = '/storage/' . $src;
//            }
//
//            if ($this->authenticator->hasUser()) {
//                return 'style="background-image: url(\''.$src.'\')" data-simplecms-bg-image="'.$name.'"';
//            } else {
//                return 'style="background-image: url(\''.$src.'\')"';
//            }
//        }, ["is_safe" => ["html"], "needs_context" => true]));

//        $this->twig->addFunction(new TwigFunction('wysiwyg', function ($context, $name, $defaultValue = "<h1>Your text</h1>") {
//            $text = $this->findSnippet($context, $name, $defaultValue);
//            if ($this->authenticator->hasUser()) {
//                return '<div class="simplecms__editor" data-simplecms-name="'.$name.'">'.$text.'</div>';
//            } else {
//                return $text;
//            }
//        }, ["is_safe" => ["html"], "needs_context" => true]));


		$this->twig->addTokenParser(new TestTokenparser());


        $this->twig->addFunction(new TwigFunction('head', function($context) {
        	$content = "";

            if ($this->authenticator->hasUser()) {
				$pageFile = $context['__pageFile'];
				$content .= "
					<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\">
					<link href=\"https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;700&display=swap\" rel=\"stylesheet\"> 
					<script lang='js'>
						const PAGE_NAME = '$pageFile';
						const __KONTRL_BASE_PLUGINS = [];
						const __KONTRL_ADMIN_PLUGINS = [];
					</script>
					<link rel='stylesheet' href='/simplecms/style' type='text/css'>
					<link rel='stylesheet' href='/simplecms/admin/style' type='text/css'>
					<script src='/simplecms/admin/script' defer></script>
					<script src='/simplecms/script' defer></script>
				";
			}

            $content .= "
            ";
//			<script>
//			   	const __KONTRL_BASE_PLUGINS = [];
//			   </script>
//			<link rel='stylesheet' href='/simplecms/base/style' type='text/css'>
//			<script src='/simplecms/base/script' defer></script>

            return $content;
        }, ["is_safe" => ["html"], "needs_context" => true]));

		$this->twig->addFunction(new TwigFunction("foot", function() {
            if (!$this->authenticator->hasUser()) return "";

            $popupHtml = file_get_contents(__DIR__ . '/foot.html');
            return $popupHtml;
        }, ["is_safe" => ["html"]]));

        $this->app = AppFactory::create();

        // TODO: throw error when environment variables are not set
        $this->db = new Database(
            $_ENV["DB_HOST"] . ":" . $_ENV["DB_PORT"],
            $_ENV["DB_NAME"],
            $_ENV["DB_USER"],
            $_ENV["DB_PASS"]
        );

        $this->app->get("/simplecms/admin/script", function ($request, $response, $args) {
            $content = "";
            foreach ($this->plugins as $plugin) {
                foreach ($plugin->adminScriptFunctions as $scriptFunction) {
                    $content .= "\n" . call_user_func($scriptFunction);
                }
            }
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/javascript');
        });

        $this->app->get("/simplecms/base/script", function ($request, $response, $args) {
            $content = "";
            foreach ($this->plugins as $plugin) {
                foreach ($plugin->scriptFunctions as $scriptFunction) {
                    $content .= "\n" . call_user_func($scriptFunction);
                }
            }
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/javascript');
        });

        $this->app->get("/simplecms/admin/style", function ($request, $response, $args) {
            $content = "";
            foreach ($this->plugins as $plugin) {
                foreach ($plugin->adminStyleFunctions as $styleFunction) {
                    $content .= "\n" . call_user_func($styleFunction);
                }
            }
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/css');
        });

        $this->app->get("/simplecms/base/style", function ($request, $response, $args) {
            $content = "";
            foreach ($this->plugins as $plugin) {
                foreach ($plugin->styleFunctions as $styleFunction) {
                    $content .= "\n" . call_user_func($styleFunction);
                }
            }
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/css');
        });

        // TODO: throw error when environment variables are not set
        $this->authenticator = new Authenticator($_ENV["SECRET_KEY"], $this->db);

        $this->createEditEndpoints();
    }

    public function addPlugin($plugin) {
        $this->plugins[] = $plugin;
    }

    public function page($path, $pageFile) {
        $this->app->get($path, function (Request $request, Response $response, array $args) use ($pageFile) {
            $snippets = $this->db->select("SELECT * FROM snippets WHERE page = :page OR page = '__global__'", [":page" => $pageFile]);

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

	public function handleRequest(string $type, string $path, $callback) {
        $this->app->map(
            [strtoupper($type)],
            $path,
            function (Request $request, Response $response, array $args = []) use ($callback) {
                $helpers = new RequestHelper($request, $response);
                $res = $callback($helpers);
                if ($res instanceof Response) {
                    return $res;
                }
                $response->getBody()->write($res);
                return $response;
            }
        )->add($this->bodyParser);
    }

    public function run() {

        // Run all plugins
        foreach ($this->plugins as $plugin) {
            $plugin->authenticator = $this->authenticator;
            $plugin->setup();
            $pluginFunctions = $plugin->functions;
            foreach ($pluginFunctions as $pluginFunction) {
                call_user_func($pluginFunction, $this->twig);
            }

        }

        $this->app->run();
    }

    public function setTemplateVariable(string $name, $value) {
        $this->twig->addGlobal($name, $value);
    }

    public function loadTemplate(string $templatePath, $context = []) {
        return $this->twig->load($templatePath)->render($context);
    }

//    public function generateSiteMap() {
//        $this->app->get("/robots.txt", function (Request $request, Response $response, $args = []) {
//            $sitemapUrl = $_ENV["BASE_URL"] . "/sitemap.xml";
//            $response->getBody()->write("
//# Auth
//User-agent: *
//Disallow: /simplecms/
//
//Sitemap: $sitemapUrl
//            ");
//        });
//
//        $this->app->get("/sitemap.xml", function (Request $request, Response $response, $args = []) {
//
//        });
//    }

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
        })->add($this->bodyParser);
//        })->add(new JsonBodyParserMiddleware());

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
        })->add($this->bodyParser);
        $this->app->post("/simplecms/upload/{purpose}", function (Request $request, Response $response, $args = []) {
            if (!$this->authenticator->hasUser()) return $this->unauthorized($response);

            $files = $request->getUploadedFiles();
            $uploadedFile = $files['file'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($this->imageDirectory, $uploadedFile, $args['purpose']);

                return $this->JSON($response, ["fileName" => $filename, "url" => "/storage/$filename"]);
            }
            return $this->JSON($response, ["error" => "Failed to upload file"]);
        })->add($this->bodyParser);

        $this->app->get("/simplecms/login", function (Request $request, Response $response, $args = []) {
            return $this->renderLibraryPage($response, "login.twig");
        });
        $this->app->post("/simplecms/login", function (Request $request, Response $response, $args = []) {
            $params = $request->getParsedBody();

            $email = strtolower($params['email']);
            $password = $params['password'];

            $user = $this->authenticator->login($email, $password);

            if ($user === false) {
                return $this->renderLibraryPage($response, "login.twig", ["error" => "Invalid email or password"]);
            }

            // TODO: save authed user to cookie or something

            return $this->sendSeeOther($response, "/");
        })->add($this->bodyParser);

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
        })->add($this->bodyParser);
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
            $email = strtolower($params["admin_email"]);
            $this->authenticator->register($email, $params["admin_password"]);

            return $this->sendSeeOther($response, "/");
        })->add($this->bodyParser);
        $this->app->get("/simplecms/logout", function (Request $request, Response $response, $args = []) {
            $this->authenticator->logout();
            return $this->sendSeeOther($response, "/");
        });

        $this->app->get("/simplecms/user_tokens/new", function (Request $request, Response $response, $args = []) {
            if (!$this->authenticator->hasUser()) return $this->unauthorized($response);

            // Create user token
            $token = bin2hex(random_bytes(32));
            $params = [":token" => $token, ":inserted_at" => (new \DateTime())->format("Y-m-d\TH:i:s.u")];
            $this->db->insert("INSERT INTO user_tokens (token, inserted_at) VALUES (:token, :inserted_at)", $params);

            return $this->renderLibraryPage($response, "user_token.twig", ["token" => $token]);
        });
        $this->app->get("/simplecms/users/new/{token}", function (Request $request, Response $response, $args) {
            // Check if token exists
            $token = $args["token"];

            $params = [":token" => $token];
            $exists = $this->db->exists("SELECT * FROM user_tokens WHERE token = :token", $params);

            if (!$exists) {
                return $this->unauthorized($response);
            }

            return $this->renderLibraryPage($response, "new_user.twig", ["token" => $token]);
        });

		$this->app->post("/simplecms/users/new", function (Request $request, Response $response, $args) {
            $data = $request->getParsedBody();

            if ($data["password"] !== $data["re_password"]) {
                return $this->renderLibraryPage(
                    $response,
                    "new_user.twig",
                    ["token" => $data["token"], "error" => "Passwords do not match"]
                );
            }

            echo $data["token"];

            $params = [":token" => $data["token"]];
            $tokenExists = $this->db->exists("SELECT * FROM user_tokens WHERE token = :token", $params);
            if (!$tokenExists) {
                return $this->unauthorized($response);
            }

			$this->db->query("DELETE FROM user_tokens WHERE token = :token", $params);

			$this->authenticator->register($data["email"], $data["password"]);

            return $this->sendSeeOther($response, "/simplecms/login");
        })->add($this->bodyParser);

		$this->app->get("/simplecms/login_token/new", function (Request $request, Response $response, $args) {
			if (!$this->authenticator->hasUser()) {
				return $this->unauthorized($response);
			}

			$users = $this->db->select("SELECT * FROM users");
			return $this->renderLibraryPage($response, "login_link.twig", ["users" => $users]);
		});

		$this->app->post("/simplecms/login_token/new", function (Request $request, Response $response, $args) {
			if (!$this->authenticator->hasUser()) {
				return $this->unauthorized($response);
			}

			$params = $request->getParsedBody();

			$token = $this->authenticator->createLoginTokenFor($params["user"]);
			return $this->renderLibraryPage($response, "login_link_show.twig", ["token" => $token]);
		})->add($this->bodyParser);

		$this->app->get("/simplecms/login_token/authenticate/{token}", function (Request $request, Response $response, $args) {
			$result = $this->authenticator->loginWithToken($args["token"]);
//			return $this->renderLibraryPage($response, "login_link_show.twig", ["token" => "test"]);
//			return $this->JSON($response, ["test" => $result]);
			return $this->sendSeeOther($response, "/");
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

    private function sendSeeOther($response, $to) {
    	return $response
			->withHeader('Location', $to)
			->withStatus(303);
	}

    private function unauthorized(Response $response) {
        return $response
            ->withStatus(401);
    }
}
