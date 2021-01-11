<?php

namespace invacto\KontrlCMS;


use Exception;
use invacto\KontrlCMS\repos\SnippetRepo;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Twig\Environment;

class RequestHelper
{
    /**
     * @var Request $request
     */
    private $request;

    /**
     * @var Response $response
     */
    private $response;

	/**
	 * @var array url args
	 */
    private $args;

	/**
	 * @var Environment twig environment
	 */
    private $twig;

    public function __construct(Request $request, Response $response, $args, Environment $twig)
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $this->twig = $twig;
    }

    public function json($data) {
        $res = json_encode($data);
        $this->response->getBody()->write($res);
        return $this->response->withHeader('Content-Type', 'application/json');
    }

    public function redirect($to) {
        return $this->response
            ->withHeader('Location', $to)
            ->withStatus(302);
    }

    public function seeOther($to) {
    	return $this->response
			->withHeader('Location', $to)
			->withStatus(303);
	}

    public function text($text) {
        $this->response->getBody()->write($text);
        return $this->response;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getUrlParameters() {
    	return $this->args;
	}

	public function notFound() {
		throw new HttpNotFoundException($this->request);
		return null;
	}
}