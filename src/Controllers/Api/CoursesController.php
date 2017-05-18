<?php

namespace App\Controllers\Api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CoursesController extends \App\Controllers\BaseController
{
    public function showAll(Request $request, Response $response)
    {
        $token = $request->getHeader('Authorization')[0];

        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');

        $course = new \App\Models\Courses\Course;
        $allCourse = $course->getAllJoin($page, 6);

        if (!$allCourse) {
            return $this->responseDetail("Course is empty", 404);
        }

        return $this->responseDetail("Data Available", 200, $allCourse);
    }

    public function showByIdUser(Request $request, Response $response)
    {
        $token = $request->getHeader('Authorization')[0];

        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');

        $userToken = new \App\Models\Users\UserToken;
        $userId = $userToken->find('token', $token)->fetch()['user_id'];

        $course = new \App\Models\Courses\Course;

        $findCourse = $course->getCourseByUserId($userId, $page, 5);

        if (!$findCourse || $findCourse['data'] == null) {
            return $this->responseDetail("You not have courses", 404);
        }

        return $this->responseDetail("Data Available", 200, $findCourse);
    }

    public function create(Request $request, Response $response)
    {
        $rule = [
            'required' => [
                ['title'],
            ],
        ];

        $post = $request->getParams();

        $token = $request->getHeader('Authorization')[0];
        $userToken = new \App\Models\Users\UserToken;
        $post['user_id'] = $userToken->find('token', $token)->fetch()['user_id'];

        $this->validator->rules($rule);

        if ($this->validator->validate()) {
            $courses = new \App\Models\Courses\Course;
            $createCourse = $courses->add($post);

            if (!is_int($createCourse)) {
                return $this->responseDetail('Title have already used', 400);
            }

            $categories = $request->getParams()['category'];
            $category = new \App\Models\Categories\Category;
            $createCategory = $category->add($categories);

            $courseCategory = new \App\Models\Courses\CourseCategory;
            $courseCategory->add($createCourse, $createCategory);

            return $this->responseDetail('Courses Create', 201);
        } else {
            return $this->responseDetail('Error', 400, $this->validator->errors());
        }
    }

    public function getCourse(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = new \App\Models\Users\UserToken;
        $userId = $userToken->find('token', $token)->fetch()['user_id'];

        $courses = new \App\Models\Courses\Course;
        $getCourse = $courses->getCourse($args['slug']);

        if ($userId != $getCourse['user_id']) {
            return $this->responseDetail('You have not Authorized to edit this course', 401);
        }

        return $this->responseDetail('Data Available', 200, $getCourse);
    }

    public function addCourseContent(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];
        $userToken = new \App\Models\Users\UserToken;
        $userId = $userToken->find('token', $token)->fetch()['user_id'];

        $courses = new \App\Models\Courses\Course;
        $getCourse = $courses->getCourse($args['slug']);

        $courseContent = new \App\Models\Courses\CourseContent;

        if ($userId != $getCourse['user_id']) {
            return $this->responseDetail('You have not Authorized to edit this course', 401);
        }

        $upload = $request->getUploadedFiles();
        $reqData = $request->getParams();

        foreach ($reqData['title'] as $titleKey => $value) {
            $title[$titleKey] = $value;
        }
        
        $this->validator->rule('required', 'title.*.' . $titleKey);
        
        $array_temp = [];

        foreach($title as $key => $val) {
            if (!in_array($val, $array_temp)) {
                $array_temp[] = $val;
            } else {
                return $this->responseDetail('Title cannot be same', 400);
            }
        }
        
        if (!$upload) {
            $this->validator->rule('required', 'url_video.*.' . $titleKey);
            if ($this->validator->validate()) {
                $dataVideo = $this->mergeArray($reqData['title'], $reqData['url_video']);

                $courseAdd = $courseContent->add($getCourse['id'], $dataVideo);

                if (!is_int($courseAdd)) {
                    return $this->responseDetail('Title already used', 400);
                }
                
                return $this->responseDetail('Success', 200);

            } else {
                return $this->responseDetail('Errors', 400, $this->validator->errors());
            }
        } else {
            if ($this->validator->validate()) {
                $storage = new \Upload\Storage\FileSystem('upload/video/');
                
                // Setting URL
                $baseUrl = $request->getUri();
                $scheme = $baseUrl->getScheme();
                $host = $baseUrl->getHost();
                $port = ($baseUrl->getPort() != null) ? $baseUrl->getPort() : null;
                $basePath = $baseUrl->getBasePath();

                $file = new \Upload\File('url_video', $storage);

                foreach ($reqData['title'] as $key => $values) {
                    foreach ($upload['url_video'] as $valData) {
                        $file[$key]->setName(uniqid());
                        
                        $fileName = $file[$key]->getNameWithExtension();
                        
                        $url = $scheme . '://' . $host . ':' . $port . $basePath . '/upload/video/' . $fileName;

                        $urlVideo[$values] = $url;
                    }
                    $titleVideo[$values] = $values;
                }

                $file->addValidations([
                    new \Upload\Validation\Mimetype(['video/mp4', 'video/3gp', 'video/webm']),
                    new \Upload\Validation\Size('128M')
                ]);

                $dataVideo = array_merge_recursive($titleVideo, $urlVideo);

                $courseAdd = $courseContent->add($getCourse['id'], $dataVideo);

                if (!is_int($courseAdd)) {
                    return $this->responseDetail('Title already used', 400);
                }

                try {
                    $file->upload();
                } catch (\Exception $errors) {
                    $errors = $file->getErrors();
                    return $this->responseDetail($errors, 400);
                }
                
                return $this->responseDetail('Upload File Success', 201);
            } else {
                return $this->responseDetail('Errors', 400, $this->validator->errors());
            }
        }
    }

    public function showTrashByIdUser(Request $request, Response $response)
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = new \App\Models\Users\UserToken;
        $userId = $userToken->find('token', $token)->fetch()['user_id'];
        
        $course = new \App\Models\Courses\Course;
        
        $findCourse = $course->getTrashByUserId($userId);
        
        if (!$findCourse) {
            return $this->responseDetail("You not have trash", 404);
        }
        
        return $this->responseDetail("Data Available", 200, $findCourse);
    }

    public function softDelete(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $course = new \App\Models\Courses\Course;
        $findCourse = $course->find('title_slug', $args['slug'])->withoutDelete()->fetch();
        
        $validateUser = $this->validateUser($token, $findCourse);

        if (!$this->checkCourse($findCourse)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        }

        $course->softDelete('id', $findCourse['id']);

        return $this->responseDetail($findCourse['title']. ' is set to trash', 200);
    }

    public function restore(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $course = new \App\Models\Courses\Course;
        $findCourse = $course->find('title_slug', $args['slug'])->fetch();
        
        $validateUser = $this->validateUser($token, $findCourse);
        
        if (!$this->checkCourse($findCourse)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        }

        $course->restore('id', $findCourse['id']);
        
        return $this->responseDetail($findCourse['title'] .' is restored', 200);
    }

    public function hardDelete(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $course = new \App\Models\Courses\Course;
        $findCourse = $course->find('title_slug', $args['slug'])->fetch();
        
        $validateUser = $this->validateUser($token, $findCourse);

        if (!$this->checkCourse($findCourse)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        }

        $course->hardDelete('id', $findCourse['id']);

        return $this->responseDetail($findCourse['title']. ' is permanently removed', 200);
    }

    private function validateUser($token, $course = null)
    {
        $userToken = new \App\Models\Users\UserToken;
        $userId = $userToken->find('token', $token)->fetch()['user_id'];
        
        $role = new \App\Models\Users\UserRole;
        $roleUser = $role->find('user_id', $userId)->fetch()['role_id'];
        
        if (($userId != $course['user_id'] && $roleUser > 1) || $roleUser > 1)  {
            return false;
        }
        
        return true;
    }

    private function checkCourse($course)
    {
        if (!$course) {
            return false;
        }
        return true;
    }


    /**
     * Give Description About Response
     * @param  array      $firstData     must Array
     * @param  array      $secondData     must Array
     * @return array_merge_recursive($firtsResult, $secondResult)
     */
    public function mergeArray(array $firstData, array $secondData)
    {
        foreach ($firstData as $valueData) {
            foreach ($secondData as $value) {
                $firstResult[$valueData] = $valueData;
            }
            $secondResult[$valueData] = $valueData;
        }

        return array_merge_recursive($firstResult, $secondResult);
    }
}

?>