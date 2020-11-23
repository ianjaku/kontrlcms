<?php

namespace invacto\SimpleCMS;


use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
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
}