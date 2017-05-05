<?php

namespace App\Controllers\Api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class UserController extends \App\Controllers\BaseController
{
    public function register(Request $request, Response $response)
    {
        $user = new \App\Models\Users\User;

        $rule = [
            'required' => [
                ['name'],
                ['username'],
                ['email'],
                ['password'],
            ],
            'email' => [
                ['email'],
            ],
            'lengthMin' => [
                ['username', 6],
                ['password', 6],
            ],
        ];

        $this->validator->rules($rule);

        if ($this->validator->validate()) {
            $addUser = $user->register($request->getParsedBody());

            if (is_int($addUser)) {
                $find = $user->find('id', $addUser)->fetch();

                $role = new \App\Models\Users\UserRole;
                $role->createRole($find['id']);

                $this->mailer->send('templates/mailer/register.twig', ['user' => $find], function($message) use ($find) {
                        $message->to($find['email']);
                        $message->subject('Active Your Account');
                });
                $data = $this->responseDetail("Register Success", 201, $find);
            } else {
                $data = $this->responseDetail($addUser . " already used", 400);
            }
        }  else {
            $data = $this->responseDetail("Error", 400, $this->validator->errors());
        }

        return $data;
    }

    public function activeUser(Request $request, Response $response)
    {
        $user = new \App\Models\Users\User;

        $token = $request->getQueryParam('token');

        $findUser = $user->find('active_token', $token)->fetch();

        if ($findUser && $findUser['is_active'] == 0) {
            $update = ['is_active' => 1,];

            $user->update($update, 'id', $findUser['id']);

            $data = $this->responseDetail("Verification Success", 200);
        } elseif ($findUser && $findUser['is_active'] == 1) {
            $data = $this->responseDetail("You account is verified", 400);
        } else {
            $data = $this->responseDetail("Data Not Found", 404);
        }

        return $data;
    }

    public function login(Request $request, Response $response)
    {
        $user = new \App\Models\Users\User;

        $login = $user->find('username', $request->getParsedBody()['username'])->fetch();

        if (empty($login)) {
            $data = $this->responseDetail("Error", 401, "Username Not Registered");
        } else {
            $check = password_verify($request->getParsedBody()['password'], $login['password']);

            if ($check) {
                $token = new \App\Models\Users\UserToken;

                $getToken = $token->setToken($login['id']);

                $role = new \App\Models\Users\UserRole;
                $findRole = $role->find('user_id', $getToken['user_id'])->fetch();

                $key = [
                    'token' => $getToken,
                    'role'  => $findRole['role_id'],
                ];

                $data = $this->responseDetail("Login Success", 200, $login, $key);
            } else {
                $data = $this->responseDetail("Error", 401, "Wrong Password");
            }
        }

        return $data;
    }

    public function editProfile(Request $request, Response $response, $args)
    {
        $post = $request->getParsedBody();

        $rule = [
            'required' => [
                ['name'],
                ['email'],
            ],
            'email' => [
                ['email'],
            ],
        ];

        $this->validator->rules($rule);

        if ($this->validator->validate()) {
            $user = new \App\Models\Users\User;
            $findUser = $user->find('id', $args['id'])->fetch();

            if ($findUser['email'] === $request->getParam('email')) {
                unset($post['email']);
            }

            $update = $user->checkOrUpdate($post, 'id', $findUser['id']);

            if (is_array($update)) {
                $data = $this->responseDetail("Update Success", 200, $update);

                if ($findUser['email'] != $request->getParam('email')) {
                    $this->mailer->send('templates/mailer/update.twig', ['user' => $update], function($message) use ($findUser) {
                        $message->to($findUser['email']);
                        $message->subject('Profile Update');
                    });
                }
            } else {
                $data = $this->responseDetail($update . " already used", 400);
            }
        } else {
            $data = $this->responseDetail("Error", 400, $this->validator->errors());
        }

        return $data;
    }

    public function getEditProfile(Request $request, Response $response, $args)
    {
        $user = new \App\Models\Users\User;
        
        $findUser = $user->find('id', $args['id'])->fetch();

        if ($findUser) {
            $users['data'] = [
                'id'     => $findUser['id'],
                'name'   => $findUser['name'],
                'email'  => $findUser['email'],
            ];
            $data = $this->responseDetail("Data Profile", 200, $users);
        } else {
            $data = $this->responseDetail("Data Not Found", 404);
        }    
    }
}

?>
