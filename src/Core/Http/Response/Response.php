<?php

namespace Core\Http\Response;

class Response
{
    public function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function error($message, int $statusCode = 400): void
    {
        $this->json(['error' => $message], $statusCode);
    }
}