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
                return $this->responseDetail("Register Success", 201, $find);
            } else {
                return $this->responseDetail($addUser . " already used", 400);
            }
        }  else {
            return $this->responseDetail("Error", 400, $this->validator->errors());
        }
    }

    public function activeUser(Request $request, Response $response)
    {
        $user = new \App\Models\Users\User;

        $token = $request->getQueryParam('token');

        $findUser = $user->find('active_token', $token)->fetch();

        if ($findUser && $findUser['is_active'] == 0) {
            $update = ['is_active' => 1,];

            $user->update($update, 'id', $findUser['id']);

            return $this->responseDetail("Verification Success", 200);
        } elseif ($findUser && $findUser['is_active'] == 1) {
            return $this->responseDetail("You account is verified", 400);
        } else {
            return $this->responseDetail("Data Not Found", 404);
        }
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

                if (is_int($getToken)) {
                    $getToken = $token->find('id', $getToken)->fetch();
                }

                $role = new \App\Models\Users\UserRole;
                $findRole = $role->find('user_id', $getToken['user_id'])->fetch();

                $premium = new \App\Models\Users\PremiumUser;
                $findPremi = $premium->find('user_id', $login['id'])->fetch();

                $key = [
                    'token'     => $getToken,
                    'role'      => $findRole['role_id'],
                    'is_premium'=> $findPremi ? 1 : 0,
                ];

                return $this->responseDetail("Login Success", 200, $login, $key);
            } else {
                return $this->responseDetail("Error", 401, "Wrong Password");
            }
        }
    }

    public function passwordReset(Request $request, Response $response)
    {
        $user = new \App\Models\Users\User;

        $rule = [
            'required' => [
                ['email'],
            ],
            'email' => [
                ['email'],
            ],
        ];

        $this->validator->rules($rule);

        if ($this->validator->validate()) {
            $find = $user->find('email', $request->getParam('email'))->fetch();

            if (!$find) {
                $data = $this->responseDetail("Email not registered", 400);
            } else {
                $passwordReset = new \App\Models\Users\PasswordReset;
                $setToken = $passwordReset->setToken($find['id']);

                if (is_int($setToken)) {
                    $findToken = $passwordReset->find('id', $setToken)->fetch();
                } else {
                    $findToken = $setToken;
                }

                $this->mailer->send('templates/mailer/password_reset.twig', ['token' => $findToken], function($message) use ($find) {
                        $message->to($find['email']);
                        $message->subject('Reset your password');
                });

                return $this->responseDetail("Check your email for reset password", 201);
            }
        } else {
            return $this->responseDetail("Error", 400, $this->validator->errors());
        }
    }

    public function getReNewPassword(Request $request, Response $response)
    {
        $pass = new \App\Models\Users\PasswordReset;

        $token = $request->getQueryParam('token');

        $find = $pass->find('token', $token)->fetch();

        if ($find) {
            return $this->responseDetail("Success Get Token", 200);
        } else {
            return $this->responseDetail("Data Not Found", 404);
        }
    }

    public function putReNewPassword(Request $request, Response $response)
    {
        $user = new \App\Models\Users\User;

        $rule = [
            'required' => [
                ['password'],
                ['retype_password'],
            ],
            'lengthMin' => [
                ['password', 6],
                ['retype_password', 6],
            ],
            'equals' => [
                ['retype_password', 'password']
            ],
        ];

        $this->validator->rules($rule);
        if ($this->validator->validate()) {
            $passwordToken = new \App\Models\Users\PasswordReset;

            $token = $request->getQueryParam('token');

            $findUserId = $passwordToken->find('token', $token)->fetch();
            
            $req = $request->getParsedBody();
            
            $updatePassword = $user->resetPassword($req, 'id', $findUserId['user_id']);

            $passwordToken->hardDelete('token', $token);

            return $this->responseDetail('Success Update Password', 200);
        } else {
            return $this->responseDetail('Error', 400, $this->validator->errors());
        }
    }

    public function getEditProfile(Request $request, Response $response, $args)
    {
        $user = new \App\Models\Users\User;

        $findUser = $user->find('id', $args['id'])->fetch();

        if (!$findUser) {
            return $this->responseDetail("Data Not Found", 404);
        }

        $data = [
            'name'  => $findUser['name'],
            'email' => $findUser['email'],
            'photo' => $findUser['photo'],
        ];

        return $this->responseDetail("Data Available", 200, $data);
    }

    public function putEditProfile(Request $request, Response $response, $args)
    {
        $post = $request->getParams();

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

            if (!$findUser) {
                return $this->responseDetail("Data Not Found", 404);
            }

            if ($findUser['email'] === $request->getParam('email')) {
                unset($post['email']);
            }

            if ($request->getUploadedFiles()) {
                $file = new \Upload\File('photo', $this->upload);

                $file->setName(uniqid());

                $file->addValidations(array(
                    new \Upload\Validation\Mimetype(array('image/png', 'image/gif',
                        'image/jpg', 'image/jpeg')),
                        new \Upload\Validation\Size('5M')));

                $photo = $file->getNameWithExtension();

                try {

                    $file->upload();

                    if ($findUser['photo'] != 'default_user.png') {
                        unlink('upload/'.$findUser['photo']);
                    }

                } catch (\Exception $e) {
                    $errors = $file->getErrors();

                    return $this->responseDetail("Error", 400, $errors);
                }
            }

            $update = $user->updateProfile($post, $findUser['id'], $photo);

            if (is_array($update)) {

                return $this->responseDetail("Update Success", 200, $update);

                if ($findUser['email'] != $request->getParam('email')) {
                    $this->mailer->send('templates/mailer/update.twig', ['user' => $update], function($message) use ($findUser) {
                        $message->to($findUser['email']);
                        $message->subject('Profile Update');
                    });
                }
            } else {
                return $this->responseDetail($update . " already used", 400);
            }
        } else {
            return $this->responseDetail("Error", 400, $this->validator->errors());
        }
    }

    public function changePassword(Request $request, Response $response)
    {
        $auth = $this->findToken();

        $users = new \App\Models\Users\User;
        $user = $users->find('id', $auth['user_id'])->fetch();

        $rule = [
            'required' => [
                ['old_password'],
                ['new_password'],
                ['retype_password'],
            ],
            'lengthMin' => [
                ['old_password', 6],
                ['new_password', 6],
                ['retype_password', 6],
            ],
            'equals' => [
                ['retype_password', 'new_password']
            ],
        ];

        $this->validator->rules($rule);

        if ($this->validator->validate()) {
            if (password_verify($request->getParam('old_password'), $user['password'])) {
                $update = ['password' => $request->getParam('new_password')];
                $users->update($update, 'id', $user['id']);

                return $this->responseDetail("Change password success", 200);
            } else {
                return $this->responseDetail("Your Old Password is Wrong", 400);
            }
        } else {
            return $this->responseDetail("Error", 400, $this->validator->errors());
        }
    }

    public function otherAccount(Request $request, Response $response, $args)
    {
        $users = new \App\Models\Users\User;
        $findUser = $users->find('username', $args['username'])->fetch();

        if (!$findUser) {
            return $this->responseDetail("Data Not Found", 404);
        }

        return $this->responseDetail("Data Available", 200, $findUser);
    }
}