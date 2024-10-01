<?php

namespace Core\BaseController;

use Core\Database\Database;
use Core\Http\Request\Request;
use Core\Http\Response\Response;

class BaseController
{
    protected Request $request;
    protected Response $response;
    protected $database;
    public function __construct(Request $request, Response $response, $database)
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->database = $database;
    }


}