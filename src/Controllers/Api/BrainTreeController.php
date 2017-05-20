<?php

namespace App\Controllers\Api;

use Braintree_ClientToken;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class BrainTreeController extends \App\Controllers\BaseController
{
	public function token(Request $request, Response $response)
	{
		return $response->withJson([
			'token'	=> Braintree_ClientToken::generate(),
		]);
	}
}