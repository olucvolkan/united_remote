<?php

namespace Core\Http\Request;

class Request
{
    private array $queryParams = [];
    private array $bodyParams = [];
    private array $headers = [];

    public function __construct()
    {
        $this->queryParams = $_GET;

        $this->bodyParams = json_decode(file_get_contents('php://input'), true) ?? [];

        $this->headers = getallheaders();
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function getBodyParams()
    {
        return $this->bodyParams;
    }

    public function getBodyParam($key, $default = null)
    {
        return $this->bodyParams[$key] ?? $default;
    }

    public function getHeaders(): bool|array
    {
        return $this->headers;
    }

    public function getHeader($key, $default = null)
    {
        return $this->headers[$key] ?? $default;
    }
}