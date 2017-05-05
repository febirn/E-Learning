<?php 

namespace App\Middlewares\Api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthToken extends \App\Middlewares\BaseMiddleware
{

	public function __invoke(Request $request, Response $response, $next)
	{
		$whiteList = ['/', 'api/register', 'api/login'];

		if (!in_array($request->getUri()->getPath(), $whiteList)) {
			$token = $request->getHeader('Authorization')[0];

			$userToken = new \App\Models\Users\UserToken;

			$findUser = $userToken->find('token', $token)->fetch();

			$now = date('Y-m-d H:i:s');

			if (!findUser || $findUser['expire_at'] < $now ) {
					$data['status'] = 401;
					$data['message'] = 'Not Authorized';

					return $response->withHeader('Content-type', 'application/json')->withJson($data, $data['status']);
			}
		}
		$response = $next($request, $response);

		return $response;
	}

}