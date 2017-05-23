<?php 

namespace App\Middlewares\Web;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AdminMiddleware extends \App\Middlewares\BaseMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $blackList = ['admin/course/add_admin_course'];

        if (in_array($request->getUri()->getPath(), $blackList)) {
            if ($_SESSION['login']['meta']['role'] > 1) {

                $this->flash->addMessage('warning', "You Haven't Authorized");

                return $response->withRedirect($this->router->pathFor('web.home'));
            }
        }

        if ($_SESSION['login']['meta']['role'] == 3) {
            $this->flash->addMessage('warning', "You Haven't Authorized");

            return $response->withRedirect($this->router->pathFor('web.home'));
        }

        
        $response = $next($request, $response);
        
        return $response;
    }
}