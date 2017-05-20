<?php 

namespace App\Middlewares\Api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AdminMiddleware extends \App\Middlewares\BaseMiddleware
{
	public function __invoke(Request $request, Response $response, $next)
	{
		$blackList = ['api/admin/course/add_admin_course', 'api/admin/article/all'];

		$token = $request->getHeader('Authorization')[0];

		$userToken = new \App\Models\Users\UserToken;
		$findUser = $userToken->find('token', $token)->fetch();

		$userRole = new \App\Models\Users\UserRole;
		$findRole = $userRole->find('user_id', $findUser['user_id'])->fetch();

		if (in_array($request->getUri()->getPath(), $blackList)) {
			if ($findRole['role_id'] > 1) {
				$data['status'] = 401;
				$data['message'] = 'Not Authorized';

				return $response->withHeader('Content-type', 'application/json')->withJson($data, $data['status']);
			}
		}

		if ($findRole['role_id'] == 3) {
			$data['status'] = 401;
			$data['message'] = 'Not Authorized';

			return $response->withHeader('Content-type', 'application/json')->withJson($data, $data['status']);
		}
		
		$response = $next($request, $response);
		
		return $response;
	}
}