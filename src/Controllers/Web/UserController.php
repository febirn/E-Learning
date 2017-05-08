<?php

namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GuzzleHttp\Exception\BadResponseException as GuzzleException;

class UserController extends \App\Controllers\BaseController
{
    public function getRegister(Request $request, Response $response)
    {
        return $this->view->render($response,'user/register.twig');
    }

    public function postRegister(Request $request, Response $response)
    {
        $req = $request->getParsedBody();

        try {
            $client = $this->testing->request('POST',
                      $this->router->pathFor('api.user.register'),
                      ['json' => $req]);

            $this->flash->addMessage(
                'success','Success registered please check your email'
            );

            $resp = $response->withRedirect($this->router->pathFor(
                   'web.user.register'));
        } catch (GuzzleException $e) {
            $data = json_decode($e->getResponse()->getBody()->getContents(), true);

            $error = $data['data'] ? $data['data'] : $data['message'];

            if (is_array($error)) {
                foreach ($error as $key => $val) {
                    $_SESSION['errors'][$key] = $val;
                }
            } else {
                $errorArr = explode(' ', $error);

                $_SESSION['errors'][lcfirst($errorArr[0])][] = $error;
            }

            $this->flash->addMessage('errors', 'Failed check your data');

            return $response->withRedirect($this->router->pathFor('web.user.register'));
        }
    }

    public function activeUser(Request $request, Response $response)
    {
        $options = [
            'query' => [
                'token' => $request->getQueryParam('token'),
            ]
        ];

        try {
            $activation = $this->testing->request('GET', $this->router->pathFor('api.user.active'), $options);

            if ($activation->getStatusCode() == 200) {
                $this->flash->addMessage('success','success! your account activated');
                return $response->withRedirect(
                    $this->router->pathFor('web.home'));
            } else {
                $this->flash->addMessage('errors','Failed your account is not activated');
                return $response->withRedirect(
                    $this->router->pathFor('web.home'));
            }
        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(), true);

            $this->flash->addMessage('errors', $error['message']);

            return $response->withRedirect(
                $this->router->pathFor('web.home'));
        }
    }

    public function getLogin (Request $request, Response $response)
    {
        return $this->view->render($response, 'user/login.twig');
    }

    public function postLogin(Request $request, Response $response)
    {
        $body = $request->getParsedBody();

        try {
            $login = $this->testing->request('POST', $this->router->pathFor('api.user.login'),['json' => $body]);

            if ($login->getStatusCode() == 200) {
            $_SESSION['login'] = json_decode($login->getBody()->getContents())->data;

                $resp = $response->withRedirect($this->router->pathFor('web.home'));

            } else {
                $this->flash->addMessage('errors', 'Failed to login');

                $resp = $response->withRedirect($this->router->pathFor('web.user.login'));
            }
        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents())->data;

            $this->flash->addMessage('errors', $error);

            return $response->withRedirect($this->router->pathFor('web.user.login'));
        }
        return $resp;
    }

    public function getEditProfile (Request $request, Response $response, $args)
    {
        $id   = $args['id'];

        try {
            $client = $this->testing->request('GET', 
                      $this->router->pathFor('api.get.edit.profile.user', 
                      ['id' => $id]));
            var_dump($client);
            die();
            return $this->view->render($response, 'user/afterlogin/users/profile.twig', $client);
        } catch (GuzzleException $e) {
            $error = $e->getResponse()->getBody()->getContents();

        }
    }

    public function postEditProfile (Request $request, Response $response, $args)
    {
        $id   = $args['id'];
        $req = $request->getParsedBody();

        try {
            $client = $this->testing->request('PUT', 
                      $this->router->pathFor('api.user.edit.profile', 
                      ['id' => $id]),['json' => $req]);
            return $response->withRedirect($this->router->pathFor(
                   'web.user.edit.profile',
                   ['message' => 'success updated your profile']));
        } catch (GuzzleException $e) {
            $error = $e->getResponse()->getBody()->getContents();
            echo $error;
        }
        
    }

    public function logout(Request $request, Response $response)
    {
        unset($_SESSION['login']);
        return $response->withRedirect($this->router->pathFor('web.home'));
    }
}
