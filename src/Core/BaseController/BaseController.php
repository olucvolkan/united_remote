<?php

namespace Core\BaseController;

use Core\Http\Request\Request;
use Core\Http\Response\Response;

class BaseController
{
    protected Request $request;
    protected Response $response;
    public function __construct(Request $request, Response $response)
    {
        $this->request = new Request();
        $this->response = new Response();
    }


}