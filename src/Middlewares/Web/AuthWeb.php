<?php 

namespace App\Middlewares\Web;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthWeb extends \App\Middlewares\BaseMiddleware
{

    public function __invoke(Request $request, Response $response, $next)
    {
        $whiteList = ['/', 'register', 'login', 'active'];

        if (!in_array($request->getUri()->getPath(), $whiteList)) {
            $token = $_SESSION['login']['meta']['token']['token'];

            if (!$_SESSION['login'] ) {
                $this->flash->addMessage('errors', 'You must login before doing that');
                return $response->withRedirect($this->router->pathFor('web.user.login'));
            }
            // $request = $request->withAddedHeader('Authorization', $token);
        }
        
        $response = $next($request, $response);
        
        return $response;
    }

}