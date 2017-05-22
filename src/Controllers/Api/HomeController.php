<?php

namespace App\Controllers\Api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class HomeController extends \App\Controllers\BaseController
{
    public function index(Request $request, Response $response)
    {

        $course = new \App\Models\Courses\Course;
        $allCourse = $course->showForHome(6);

        if (!$allCourse) {
            return $this->responseDetail("Course is empty", 404);
        }

        return $this->responseDetail("Data Available", 200, $allCourse);
    }
}

?>