<?php

namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GuzzleHttp\Exception\BadResponseException as GuzzleException;

class AdminController extends \App\Controllers\BaseController
{
    public function index(Request $request, Response $response)
    {
        return $this->view->render($response, 'admin/dashboard.twig');
    }
}
