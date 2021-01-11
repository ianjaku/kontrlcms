<?php

namespace invacto\SimpleCMS;


use Dotenv\Dotenv;
use Illuminate\Database\QueryException;
use invacto\SimpleCMS\repos\SnippetRepo;
use invacto\SimpleCMS\repos\UserRepo;
use invacto\SimpleCMS\repos\UserTokenRepo;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Middleware\BodyParsingMiddleware;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class SimpleCMS {

    /**
     * @var BodyParsingMiddleware
     */
    private $bodyParser;

	/**
	 * @var AppContext
	 */
    private $appContext;

    public function __construct($appDir)
    {
		$dotenv = Dotenv::createImmutable($appDir);
		$dotenv->safeLoad();

		$this->appContext = new AppContext($appDir);

        $this->bodyParser = new BodyParsingMiddleware();

        if (isset($_ENV["BASE_URL"])) {
            $this->appContext->getTwig()->addGlobal("BASE_URL", $_ENV["BASE_URL"]);
        } else {
            $this->appContext->getTwig()->addGlobal("BASE_URL", "");
        }

        $this->appContext->getTwig()->addFunction(new TwigFunction('head', function($context) {
        	$content = "";

            if ($this->appContext->getAuthenticator()->hasUser()) {
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

            $pageContext = new PageContext($context);
            foreach ($this->appContext->getHooksFor("head") as $hook) {
				$content = $hook($content, $pageContext);
			}
//			foreach ($this->appContext->getPlugins() as $plugin) {
//            	if (!isset($plugin->hooks["head"])) continue;
//            	foreach ($plugin->hooks["head"] as $hook) {
//					$content = $hook($content, $pageContext);
//				}
//			}

            // TODO: Add base script & base style
//			<script>
//			   	const __KONTRL_BASE_PLUGINS = [];
//			   </script>
//			<link rel='stylesheet' href='/simplecms/base/style' type='text/css'>
//			<script src='/simplecms/base/script' defer></script>

            return $content;
        }, ["is_safe" => ["html"], "needs_context" => true]));

		$this->appContext->getTwig()->addFunction(new TwigFunction("foot", function() {
            if (!$this->appContext->getAuthenticator()->hasUser()) return "";

            $popupHtml = file_get_contents(__DIR__ . '/foot.html');
            return $popupHtml;
        }, ["is_safe" => ["html"]]));

        $this->appContext->getApp()->get("/simplecms/admin/script", function ($request, $response, $args) {
            $content = "";
            foreach ($this->appContext->getPlugins() as $plugin) {
                foreach ($plugin->adminScriptFunctions as $scriptFunction) {
                    $content .= "\n" . call_user_func($scriptFunction);
                }
            }
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/javascript');
        });

        $this->appContext->getApp()->get("/simplecms/base/script", function ($request, $response, $args) {
            $content = "";
            foreach ($this->appContext->getPlugins() as $plugin) {
                foreach ($plugin->scriptFunctions as $scriptFunction) {
                    $content .= "\n" . call_user_func($scriptFunction);
                }
            }
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/javascript');
        });

        $this->appContext->getApp()->get("/simplecms/admin/style", function ($request, $response, $args) {
            $content = "";
            foreach ($this->appContext->getPlugins() as $plugin) {
                foreach ($plugin->adminStyleFunctions as $styleFunction) {
                    $content .= "\n" . call_user_func($styleFunction);
                }
            }
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/css');
        });

        $this->appContext->getApp()->get("/simplecms/base/style", function ($request, $response, $args) {
            $content = "";
            foreach ($this->appContext->getPlugins() as $plugin) {
                foreach ($plugin->styleFunctions as $styleFunction) {
                    $content .= "\n" . call_user_func($styleFunction);
                }
            }
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/css');
        });

        $this->createEditEndpoints();
    }

    public function addPlugin($plugin) {
    	$this->appContext->addPlugin($plugin);
    }

    public function page($path, $pageFile, $settings = []) {
        $this->appContext->getApp()->get($path, function (Request $request, Response $response, array $args) use ($pageFile, $settings) {
        	$context = $this->appContext->fetchPageContext($pageFile);
			$html = $this->appContext->renderPage($pageFile, $context);
            $response->getBody()->write($html);
            return $response;
        });
    }

    public function redirect($from, $to) {
        $this->appContext->getApp()->get($from, function (Request $request, Response $response, array $args) use ($to) {
            return $this->sendRedirect($response, $to);
        });
    }

	public function handleRequest(string $type, string $path, $callback) {
    	$twig = $this->appContext->getTwig();

        $this->appContext->getApp()->map(
            [strtoupper($type)],
            $path,
            function (Request $request, Response $response, array $args = []) use ($callback, $twig) {
                $helpers = new RequestHelper($request, $response, $args, $twig);
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
        foreach ($this->appContext->getPlugins() as $plugin) {
            $plugin->appContext = $this->appContext;
            $plugin->setup();
//            $pluginFunctions = $plugin->functions;
//            foreach ($pluginFunctions as $pluginFunction) {
//                call_user_func($pluginFunction, $this->appContext->getTwig());
//            }
            foreach ($plugin->handledRequests as $handledRequest) {
            	$this->handleRequest($handledRequest[0], $handledRequest[1], $handledRequest[2]);
			}
        }

		$this->addGlobalFunction();
		$this->addErrorHandler();

        $this->appContext->getApp()->run();
    }

	public function renderErrorPage($statusCode, $errorMessage = "") {
		$page = $this->appContext->getErrorPage();
		if ($page == null) {
			return $statusCode . ":" . $errorMessage;
		} else {
			$pageData = $this->appContext->fetchPageContext($page, ["statusCode" => $statusCode, "errorMessage" => $errorMessage]);
			return $this->appContext->renderPage($page, $pageData);
		}
	}

	public function setTemplateVariable(string $name, $value) {
        $this->appContext->getTwig()->addGlobal($name, $value);
    }

    public function loadTemplate(string $templatePath, $context = []) {
        return $this->appContext->getTwig()->load($templatePath)->render($context);
    }

    public function errorPage($pageFile) {
    	$this->appContext->setErrorPage($pageFile);
	}

//    public function generateSiteMap() {
//        $this->appContext->getApp()->get("/robots.txt", function (Request $request, Response $response, $args = []) {
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
//        $this->appContext->getApp()->get("/sitemap.xml", function (Request $request, Response $response, $args = []) {
//
//        });
//    }

    private function createEditEndpoints() {
        $this->appContext->getApp()->get("/simplecms/style", function (Request $request, Response $response, array $args) {
            $styleData = file_get_contents(__DIR__ . '/../dist/style.css');
            $response->getBody()->write($styleData);
            return $response->withHeader('Content-Type', 'text/css');
        });

        $this->appContext->getApp()->get("/simplecms/script", function (Request $request, Response $response, array $args) {
            $styleData = file_get_contents(__DIR__ . '/../dist/app.js');
            $response->getBody()->write($styleData);
            return $response->withHeader('Content-Type', 'text/javascript');
        });

        $this->appContext->getApp()->post("/simplecms/update", function (Request $request, Response $response, array $args) {
            if (!$this->appContext->getAuthenticator()->hasUser()) return "";

            $params = $request->getParsedBody();

			SnippetRepo::updateOrCreate($params["name"], $params["page"], $params["value"]);

            $res = json_encode(["success" => true]);
            $response->getBody()->write($res);
            return $response->withHeader('Content-Type', 'application/json');
        })->add($this->bodyParser);

        $this->appContext->getApp()->get("/simplecms/snippets", function (Request $request, Response $response, array $args) {
        	$queryParams = $request->getQueryParams();
        	if (!isset($queryParams["snippets"])) {
				return $this->badRequest($response);
			}
        	$namesAndPages = json_decode($queryParams["snippets"], true);
			$snippets = SnippetRepo::allByNamesAndPages($namesAndPages);

        	return $this->JSON($response, $snippets);
		});

        $this->appContext->getApp()->post("/simplecms/upload", function (Request $request, Response $response, $args = []) {
            if (!$this->appContext->getAuthenticator()->hasUser()) return $this->unauthorized($response);

            $params = $request->getParsedBody();

            $page = $params['page'];
            $name = $params['name'];
            $files = $request->getUploadedFiles();
            $uploadedFile = $files['file'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($this->appContext->getImageDir(), $uploadedFile);

                $existingImageSnippet = SnippetRepo::single($name, $page);

                if ($existingImageSnippet == null) {
                	SnippetRepo::insert($name, $page, $filename);
				} else {
                	SnippetRepo::update($name, $page, $filename);

                	// Remove previous image
					$previousFileName = $existingImageSnippet->value;
					$totalPath = $this->appContext->getImageDir() . "/" . $previousFileName;
					if (file_exists($totalPath)) {
						unlink($totalPath);
					}
				}

//                SnippetRepo::updateOrCreate($name, $page, $filename);

//                $existingImageSnippet = $this->updateOrCreateSnippet(
//                    $page,
//                    $name,
//                    $filename
//                );

//                if ($existingImageSnippet !== null) {
//                    $previousFileName = $existingImageSnippet["value"];
//                    $totalPath = $this->appContext->getImageDir() . "/" . $previousFileName;
//                    if (file_exists($totalPath)) {
//                        unlink($totalPath);
//                    }
//                }

                return $this->JSON($response, ["fileName" => $filename, "url" => "/storage/$filename"]);
            }
            return $this->JSON($response, ["error" => "Failed to upload file"]);
        })->add($this->bodyParser);

        $this->appContext->getApp()->post("/simplecms/upload/{purpose}", function (Request $request, Response $response, $args = []) {
            if (!$this->appContext->getAuthenticator()->hasUser()) return $this->unauthorized($response);

            $files = $request->getUploadedFiles();
            $uploadedFile = $files['file'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($this->appContext->getImageDir(), $uploadedFile, $args['purpose']);

                return $this->JSON($response, ["fileName" => $filename, "url" => "/storage/$filename"]);
            }
            return $this->JSON($response, ["error" => "Failed to upload file"]);
        })->add($this->bodyParser);

        $this->appContext->getApp()->get("/simplecms/login", function (Request $request, Response $response, $args = []) {
            return $this->renderLibraryPage($response, "login.twig");
        });
        $this->appContext->getApp()->post("/simplecms/login", function (Request $request, Response $response, $args = []) {
            $params = $request->getParsedBody();

            $email = strtolower($params['email']);
            $password = $params['password'];

            $user = $this->appContext->getAuthenticator()->login($email, $password);

            if ($user === false) {
                return $this->renderLibraryPage($response, "login.twig", ["error" => "Invalid email or password"]);
            }

            // TODO: save authed user to cookie or something

            return $this->sendSeeOther($response, "/");
        })->add($this->bodyParser);

		$this->appContext->getApp()->get("/simplecms/setup", function (Request $request, Response $response, $args = []) {
			$tableExists = UserRepo::doesTableExist();
			if ($tableExists) {
				$existingUsersCount = UserRepo::countAll();
				if ($existingUsersCount !== 0) return $this->unauthorized($response);
			}
			return $this->renderLibraryPage($response, "setup.twig");
		})->add($this->bodyParser);

        $this->appContext->getApp()->post("/simplecms/setup", function (Request $request, Response $response, $args = []) {
            $params = $request->getParsedBody();

			$tableExists = UserRepo::doesTableExist();
			if ($tableExists) {
				$existingUsersCount = UserRepo::countAll();
				if ($existingUsersCount !== 0) return $this->unauthorized($response);
			} else {
				$this->appContext->getDB()->createTables();
			}

            $email = strtolower($params["admin_email"]);
            $this->appContext->getAuthenticator()->register($email, $params["admin_password"]);

            return $this->sendSeeOther($response, "/");
        })->add($this->bodyParser);

        $this->appContext->getApp()->get("/simplecms/logout", function (Request $request, Response $response, $args = []) {
            $this->appContext->getAuthenticator()->logout();
            return $this->sendSeeOther($response, "/");
        });

        $this->appContext->getApp()->get("/simplecms/user_tokens/new", function (Request $request, Response $response, $args = []) {
            if (!$this->appContext->getAuthenticator()->hasUser()) return $this->unauthorized($response);

            // Create user token
            $token = bin2hex(random_bytes(32));
			UserTokenRepo::create($token);

            return $this->renderLibraryPage($response, "user_token.twig", ["token" => $token]);
        });
        $this->appContext->getApp()->get("/simplecms/users/new/{token}", function (Request $request, Response $response, $args) {
            $token = $args["token"];
            if (!UserTokenRepo::tokenExists($token)) {
                return $this->unauthorized($response);
            }
            return $this->renderLibraryPage($response, "new_user.twig", ["token" => $token]);
        });

		$this->appContext->getApp()->post("/simplecms/users/new", function (Request $request, Response $response, $args) {
            $data = $request->getParsedBody();

            if ($data["password"] !== $data["re_password"]) {
                return $this->renderLibraryPage(
                    $response,
                    "new_user.twig",
                    ["token" => $data["token"], "error" => "Passwords do not match"]
                );
            }

            if (!UserTokenRepo::tokenExists($data["token"])) {
                return $this->unauthorized($response);
            }
			UserTokenRepo::removeToken($data["token"]);

			$this->appContext->getAuthenticator()->register($data["email"], $data["password"]);

            return $this->sendSeeOther($response, "/simplecms/login");
        })->add($this->bodyParser);

		$this->appContext->getApp()->get("/simplecms/login_token/new", function (Request $request, Response $response, $args) {
			if (!$this->appContext->getAuthenticator()->hasUser()) {
				return $this->unauthorized($response);
			}

			$users = UserRepo::all();
			return $this->renderLibraryPage($response, "login_link.twig", ["users" => $users]);
		});

		$this->appContext->getApp()->post("/simplecms/login_token/new", function (Request $request, Response $response, $args) {
			if (!$this->appContext->getAuthenticator()->hasUser()) {
				return $this->unauthorized($response);
			}

			$params = $request->getParsedBody();

			$token = $this->appContext->getAuthenticator()->createLoginTokenFor($params["user"]);
			return $this->renderLibraryPage($response, "login_link_show.twig", ["token" => $token]);
		})->add($this->bodyParser);

		$this->appContext->getApp()->get("/simplecms/login_token/authenticate/{token}", function (Request $request, Response $response, $args) {
			$this->appContext->getAuthenticator()->loginWithToken($args["token"]);
			return $this->sendSeeOther($response, "/");
		});
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
        return $this->appContext->getTwig()->load($page)->render($context);
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

    private function sendRedirect(Response $response, $to) {
        return $response
            ->withHeader('Location', $to)
            ->withStatus(302);
    }

    private function sendSeeOther(Response $response, $to) {
    	return $response
			->withHeader('Location', $to)
			->withStatus(303);
	}

    private function unauthorized(Response $response) {
        return $response
            ->withStatus(401);
    }

	private function badRequest(Response $response) {
		return $response
			->withStatus(400);
	}

	private function addErrorHandler() {
		$customErrorHandler = function(
			ServerRequestInterface $request,
			Throwable $exception,
			bool $displayErrorDetails,
			bool $logErrors,
			bool $logErrorDetails,
			?LoggerInterface $logger = null
		) {
			$response = $this->appContext->getApp()->getResponseFactory()->createResponse();
			$response->getBody()->write($this->renderErrorPage($exception->getCode(), $exception->getMessage()));
			return $response;
		};
		$errorMiddleware = $this->appContext->getApp()->addErrorMiddleware(true, true, true, $this->appContext->getLogger());
		$errorMiddleware->setDefaultErrorHandler($customErrorHandler);
	}

	private function addGlobalFunction() {
    	$this->appContext->getTwig()->addFunction(new TwigFunction("start_global", function () {
    		$this->appContext->setGlobalNames(true);
    		return "";
		}, ["is_safe" => ["html"]]));
		$this->appContext->getTwig()->addFunction(new TwigFunction("end_global", function () {
			$this->appContext->setGlobalNames(false);
			return "";
		}, ["is_safe" => ["html"]]));
	}

}
