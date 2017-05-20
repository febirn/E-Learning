<?php

namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GuzzleHttp\Exception\BadResponseException as GuzzleException;

class UserController extends \App\Controllers\BaseController
{
    public function getRegister(Request $request, Response $response)
    {
        return $this->view->render($response, 'users/register.twig');
    }

    public function postRegister(Request $request, Response $response)
    {
        $req = $request->getParsedBody();

        try {
            $client = $this->testing->request('POST',
                      $this->router->pathFor('api.user.register'),
                      ['json' => $req]);

            $this->flash->addMessage(
                'success', 'Register Success!, Check Your Email for Activate Your Account'
            );

            return $response->withRedirect($this->router->pathFor(
                   'web.user.login'));
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

            $_SESSION['old'] = $req;

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
                $this->flash->addMessage('success','Your Account Has Activated');

                return $response->withRedirect($this->router->pathFor('web.home'));
            } else {
                $this->flash->addMessage('errors','Failed your account is not activated, please check your email');

                return $response->withRedirect($this->router->pathFor('web.home'));
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
        if (empty($_SESSION['login'])) {
            return $this->view->render($response, 'users/login.twig');
        } else {
            return $response->withRedirect($this->router->pathFor('web.home'));
        }
    }

    public function postLogin(Request $request, Response $response)
    {
        $body = $request->getParsedBody();

        try {
            $login = $this->testing->request('POST', $this->router->pathFor('api.user.login'),['json' => $body]);

            if ($login->getStatusCode() == 200) {
                $contents = json_decode($login->getBody()->getContents(), true);

                $_SESSION['login'] = [
                    'data'  => $contents['data'],
                    'meta'  => $contents['meta'],
                ];
                
                $url = $_SESSION['url'] ? $_SESSION['url'] : $this->router->pathFor('web.home');

                return $response->withRedirect($url);
            } else {
                $this->flash->addMessage('errors', 'Failed to login');

                return $response->withRedirect($this->router->pathFor('web.user.login'));
            }
        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents())->data;

            $this->flash->addMessage('errors', $error);

            return $response->withRedirect($this->router->pathFor('web.user.login'));
        }
    }

    public function logout(Request $request, Response $response)
    {
        unset($_SESSION['login']);

        return $response->withRedirect($this->router->pathFor('web.user.login'));
    }

    public function getPasswordReset(Request $request, Response $response)
    {
        return $this->view->render($response, 'users/resetpassword.twig');
    }

    public function postPasswordReset(Request $request, Response $response)
    {
        $body = $request->getParsedBody();

        try {
            $reset = $this->testing->request('POST', $this->router->pathFor('api.user.password.reset'), ['json' => $body]);
            
            if ($reset->getStatusCode() == 201) {
                $this->flash->addMessage('success', 'Check your mail for reset password');

                return $response->withRedirect($this->router->pathFor('web.home'));
            }
        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents());

            if ($error->data) {
                $errors = $error->data->email[0];
            } else {
                $errors = $error->message;
            }

            $this->flash->addMessage('errors', $errors);

            return $response->withRedirect($this->router->pathFor('web.user.password.reset'));
        }
    }

    public function getReNewPassword(Request $request, Response $response)
    {
        $options = [
            'query' => [
                'token' => $request->getQueryParam('token'),
            ]
        ];

        try {
            $reNewPassword = $this->testing->request('GET', $this->router->pathFor('api.user.get.renew.password'), $options);

            if ($reNewPassword->getStatusCode() == 200) {
                return $this->view->render($response, 'users/newpassword.twig');
            }
        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(), true);

            $this->flash->addMessage('errors', $error['message']);

            return $response->withRedirect(
                $this->router->pathFor('web.user.password.reset'));
        }
    }

    public function postReNewPassword(Request $request, Response $response)
    {
        $body = $request->getParsedBody();

        $token = $request->getQueryParam('token');

        try {
            $reNewPassword = $this->testing->request('PUT', $this->router->pathFor('api.user.put.renew.password'), ['json' => $body]);

            if ($reNewPassword->getStatusCode() == 200) {
                $this->flash->addMessage('success', 'Password Has Been Change');

                return $this->response->withRedirect($this->router->pathFor('web.user.login')); 
            }
        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(), true);

            if (is_array($error['data'])) {
                foreach ($error['data'] as $key => $val) {
                    $_SESSION['errors'][$key] = $val;
                }
            } else {
                $errorArr = explode(' ', $error);
                $_SESSION['errors'][lcfirst($errorArr[0])][] = $error;
            }
            return $this->response->withRedirect($this->router->pathFor('web.user.renew.password')."?token=$token"); 
        }
    }

    public function getEditProfile (Request $request, Response $response)
    {
        $data = $_SESSION['login'];

        $client = $this->testing->request('GET', 
                  $this->router->pathFor('api.get.edit.profile.user', 
                  ['id' => $data['data']['id']]));

        return $this->view->render($response, 'users/edit_profile.twig', ['user' => $data['data']]);
    }

    public function postEditProfile (Request $request, Response $response)
    {
        $id = $_SESSION['login']['data']['id'];
        $reqData = $request->getParams();
        $reqPhoto = $request->getUploadedFiles()['photo'];
        
        $imageName = $reqPhoto->getClientFilename();
        $imageMimeType = $reqPhoto->getClientMediaType();

        if (!($imageName == null)) {   
            $data[] = [
                'name' => "photo",
                'filename' => $imageName,
                'Mime-Type'=> $imageMimeType,
                'contents' => fopen(realpath($reqPhoto->file), 'rb'),
            ];
        }

        foreach ($reqData as $key => $value) {
            $data[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }

        try {
            $client = $this->testing->request('POST', $this->router->pathFor('api.put.edit.profile.user', ['id' => $id]), [ 'multipart' => $data]);

            $this->flash->addMessage('success', 'Data has bean Update');

            $contents = json_decode($client->getBody()->getContents(), true);
            
            $_SESSION['login'] = [
                'data'  => $contents['data'],
                'meta'  => $_SESSION['login']['meta'],
            ];

            return $response->withRedirect($this->router->pathFor(
                   'web.user.edit_profile'));
        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(),true);

            $this->flash->addMessage('errors', $error[0]);

            return $response->withRedirect($this->router->pathFor(
                   'web.user.edit_profile'));
        }
    
    }

    public function getChangePassword(Request $request, Response $response)
    {
        return $this->view->render($response, 'users/change_password.twig');
    }

    public function postChangePassword(Request $request, Response $response)
    {
        $body = $request->getParsedBody();

        try {
           $client = $this->testing->request('PUT', $this->router->pathFor('api.user.password.change'), ['json' => $body]);

           $this->flash->addMessage('success', 'Change Password Success, please re-login');

           unset($_SESSION['login']);

           return $response->withRedirect($this->router->pathFor('web.user.login'));
        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(),true);
            
            if ($error['data']) {
                foreach ($error['data'] as $key => $value) {
                    $_SESSION['errors'][lcfirst($key)][] = $value[0];
                }
            } else {
                $this->flash->addMessage('errors', $error['message']);
            }

            return $response->withRedirect($this->router->pathFor('web.user.change.password'));
        }
    }

    public function otherAccount(Request $request, Response $response, $args)
    {
        $client = $this->testing->request('GET', $this->router->pathFor('api.user.other.account', ['username' => $args['username']]));

        if ($client->getStatusCode() == 200) {
            $contents = json_decode($login->getBody()->getContents(), true);

            return $this->view->render($response, 'users/overview.twig', ['user' => $contents['data']]);
        } else {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
    }

    public function myAccount(Request $request, Response $response)
    {
        $client = $this->testing->request('GET', $this->router->pathFor('api.user.other.account', ['username' => $_SESSION['login']['data']['username']]));
        $contents = json_decode($client->getBody()->getContents(), true);

        return $this->view->render($response, 'users/overview.twig', ['user' => $contents['data']]);
    }

    public function getPremium(Request $request, Response $response)
    {
        // $client = $this->testing->request('GET', $this->router->pathFor('api.user.premium'));
        // $contents = json_decode($client->getBody()->getContents(), true);

        return $this->view->render($response, 'users/upgrade_user.twig');
    }

    public function postPremium(Request $request, Response $response)
    {
        $req = $request->getParams();

        try {
            $client = $this->testing->request('POST', $this->router->pathFor('api.user.premium'), ['form_params' => $req]);

            $contents = json_decode($client->getBody()->getContents(), true);

            $this->flash->addMessage('success', $contents['message']);

            $_SESSION['login']['meta']['is_premium'] = 1;

            return $response->withRedirect($this->router->pathFor('web.home'));
        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(),true);

            $this->flash->addMessage('errors', $error['message']);

            return $response->withRedirect($this->router->pathFor('web.user.premium'));
        }
    }
}