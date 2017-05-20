<?php

namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GuzzleHttp\Exception\BadResponseException as GuzzleException;

class CourseController extends \App\Controllers\BaseController
{
    public function showAll(Request $request, Response $response)
    {
        $client = $this->testing->request('GET', $this->router->pathFor('api.get.all.course'));

        $content = json_decode($client->getBody()->getContents());

        // return $this->view->render($response,'admin.show.all.content.twig', $content);

        var_dump($content);
    }

    public function showByIdUser(Request $request, Response $response)
    {
        $client = $this->testing->request('GET', $this->router->pathFor('api.get.all.course'));

        $content = json_decode($client->getBody()->getContents());
    }

}

?>