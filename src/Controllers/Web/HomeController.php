<?php

namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class HomeController extends \App\Controllers\BaseController
{
    public function index(Request $request, Response $response)
    {
        $data = $this->testing->request('GET', $this->router->pathFor('api.index'),['json' => $body]);

        return $data;
        // return $this->view->render($response,'home.twig', $data);
    }
}

?>