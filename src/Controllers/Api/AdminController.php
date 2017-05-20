<?php

namespace App\Controllers\Api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AdminController extends \App\Controllers\BaseController
{
    public function getAddAdminCourse(Request $request, Response $response)
    {
        $user = new \App\Models\Users\User;

        $find = $user->joinUserAndRole();

        if ($find) {
            $data = $this->responseDetail('Data Available', 200, $find);
        } else {
            $data = $this->responseDetail('Data Not Found', 404);
        }

        return $data;
    }

    public function putAddAdminCourse(Request $request, Response $response, $args)
    {
        foreach ($request->getParam('user_id') as $key => $value) {
            $user = new \App\Models\Users\UserRole;
            $update = ['role_id' => 2];
            $setRole = $user->update($update, 'user_id', $value);
        }

        return $this->responseDetail('Success Add Admin Course', 200);
    }
}