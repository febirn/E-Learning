<?php

namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CourseController extends \App\Controllers\BaseController
{
    public function showAll(Request $request, Response $response)
    {
        $data = $this->testing->request('GET', $this->router->pathFor('api.get.all.course'),['json' => $body]);

        return $data;
    }
}

?>