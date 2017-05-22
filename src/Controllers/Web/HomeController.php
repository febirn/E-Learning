<?php

namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GuzzleHttp\Exception\BadResponseException as GuzzleException;

class HomeController extends \App\Controllers\BaseController
{
    public function index(Request $request, Response $response)
    {
    	try {
    		$client = $this->testing->request('GET', $this->router->pathFor('api.index'));

        	$content = json_decode($client->getBody()->getContents());	
    	} catch (GuzzleException $e) {
    		
    	}

        return $this->view->render($response, 'home.twig', $content['data']);
    }
}

?>