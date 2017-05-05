<?php
namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GuzzleHttp\Exception\BadResponseException as GuzzleException;

class HomeController extends \App\Controllers\BaseController
{
    public function index(Request $request, Response $response)
    {
        if (!empty($_SESSION['login'] && !is_null($_SESSION['login'] )))
            return $this->view->render($response, 'user/afterlogin/home.twig');
        else {
            return $this->view->render($response, 'user/beforelogin/home.twig');
        }
    }
}
