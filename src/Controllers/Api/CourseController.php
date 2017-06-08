<?php

namespace App\Controllers\Api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CourseController extends \App\Controllers\BaseController
{
	public function showAll(Request $request, Response $response)
	{
		$course = new \App\Models\Courses\Course;
	    $allCourse = $course->getAllJoin();

	    if (!$allCourse) {
	        return $this->responseDetail("Course is empty", 404);
	    }

	    return $this->responseDetail("Data Available", 200, $allCourse);
	}
	
	public function showByIdUser(Request $request, Response $response)
    {
        $userToken = $this->findToken();
        $userId = $userToken['user_id'];

        $course = new \App\Models\Courses\Course;
        $findCourse = $course->getCourseByUserId($userId);

        if (!$findCourse) {
            return $this->responseDetail("You not have Courses", 404);
        }

        return $this->responseDetail("Data Available", 200, $findCourse);
    }

    public function showAllContent(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = $this->findToken();
        $userId = $userToken['user_id'];

        $courses = new \App\Models\Courses\Course;
        $getCourse = $courses->getCourse($args['slug']);

        $courseContent = new \App\Models\Courses\CourseContent;
        $getCourseContent = $courseContent->find('course_id', $getCourse['id'])->fetchAll();

        $validateUser = $this->validateUser($token, $getCourse);

        if (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        } elseif (!$getCourse) {
            return $this->responseDetail("Course Content is empty", 404);
        }

        return $this->responseDetail('Data Available', 200, $getCourseContent);

    }

    public function showContent(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = $this->findToken();
        $userId = $userToken['user_id'];

        $courses = new \App\Models\Courses\Course;
        $getCourse = $courses->getCourse($args['slug']);

        $courseContent = new \App\Models\Courses\CourseContent;
        $getCourseContent = $courseContent->find('id', $args['id'])->fetch();

        $validateUser = $this->validateUser($token, $getCourse);

        if (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        } elseif (!$getCourse || !$getCourseContent) {
            return $this->responseDetail("Course Content is empty", 404);
        }

        return $this->responseDetail('Data Available', 200, $getCourseContent);
    }

    public function getCreate(Request $request, Response $response)
    {
        $category = new \App\Models\Categories\Category;

        $find = $category->getAll()->fetchAll();
        
        if ($find) {
            return $this->responseDetail("Category Available", 200, $find);
        } else {
            return $this->responseDetail("Category Not Available", 200);
        }
    }

    public function create(Request $request, Response $response)
    {
        $rule = [
            'required' => [
                ['title'],
                ['category'],
                ['url_source_code'],
                ['description'],
            ],
        ];

        $post = $request->getParams();

        $userToken = $this->findToken();
        $userId = $userToken['user_id'];

		$post['user_id'] = $userId;

        $this->validator->rules($rule);

        if ($this->validator->validate()) {
            $courses = new \App\Models\Courses\Course;

            if ($request->getUploadedFiles()) {
                $upload = new \Upload\Storage\FileSystem('upload/images');
                $file = new \Upload\File('cover', $upload);

                $file->setName(uniqid());

                $file->addValidations(array(
                    new \Upload\Validation\Mimetype(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg')), new \Upload\Validation\Size('5M')));

                $cover = $file->getNameWithExtension();

                try {
                    $file->upload();
                } catch (\Exception $e) {
                    $errors = $file->getErrors();
                    return $this->responseDetail("Error", 400, $errors);
                }
            }

            $createCourse = $courses->add($post, $cover);

            if (!is_int($createCourse)) {
                return $this->responseDetail('Title have already used', 400);
            }

            $categories = $request->getParams()['category'];
            $category = new \App\Models\Categories\Category;
            $createCategory = $category->add($categories);

            $courseCategory = new \App\Models\Courses\CourseCategory;
            $courseCategory->add($createCourse, $createCategory);

            $findCourse = $courses->find('id', $createCourse)->fetch();

            return $this->responseDetail('Courses Create', 201, $findCourse);
        } else {
            return $this->responseDetail('Error', 400, $this->validator->errors());
        }
    }

    public function editCourse(Request $request, Response $response, $args)
    {
        $post = $request->getParams();

        $rule = [
            'required' => [
                ['title'],
                ['category'],
                ['url_source_code'],
                ['description'],
            ],
        ];

        $token = $request->getHeader('Authorization')[0];
        $course = new \App\Models\Courses\Course;
        $getCourse = $course->getCourse($args['slug']);

        $validateUser = $this->validateUser($token, $getCourse);

        if (!$this->checkCourse($getCourse)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        }
        
        $this->validator->rules($rule);

        if ($this->validator->validate()) {
            if ($request->getUploadedFiles()) {
                $upload = new \Upload\Storage\FileSystem('upload/images');
                $file = new \Upload\File('cover', $upload);

                $file->setName(uniqid());

                $file->addValidations(array(
                    new \Upload\Validation\Mimetype(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg')), new \Upload\Validation\Size('5M')));

                $cover = $file->getNameWithExtension();

                try {
                    $file->upload();

                    if ($getCourse['cover'] != 'default_cover.jpg') {
                        unlink('upload/images/' . $getCourse['cover']);
                    } 
                } catch (\Exception $e) {
                    $errors = $file->getErrors();
                    return $this->responseDetail("Error", 400, $errors);
                }
            }


            $update = $course->edit($post, $args['slug'], $cover);

            if (!is_array($update)) {
                return $this->responseDetail("Title already used", 400);
            }

            $categories = $request->getParam('category');
            $category = new \App\Models\Categories\Category;
            $updateCategory = $category->add($categories);

            $courseCategory = new \App\Models\Courses\CourseCategory;
            $courseCategory->edit($update['id'], $updateCategory);
            
            return $this->responseDetail("Course has updated", 200);
        } else {
            return $this->responseDetail("Error", 400, $this->validator->errors());
        }
    }

    public function getCourse(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $courses = new \App\Models\Courses\Course;
        $getCourse = $courses->getCourse($args['slug']);

        $category = new \App\Models\Categories\Category;
        $find = $category->getAll()->fetchAll();

        $data['course'] = $getCourse;
        $data['category'] = $find;

        $validateUser = $this->validateUser($token, $getCourse);
     
        if (!$this->checkCourse($getCourse)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        }

        return $this->responseDetail('Data Available', 200, $data);
    }

    public function addCourseContent(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];
        
        $userToken = $this->findToken();
        $userId = $userToken['user_id'];

        $courses = new \App\Models\Courses\Course;
        $getCourse = $courses->getCourse($args['slug']);

        $courseContent = new \App\Models\Courses\CourseContent;

        if ($userId != $getCourse['user_id']) {
            return $this->responseDetail('You have not Authorized to edit this course', 401);
        }

        $upload = $request->getUploadedFiles();
        $reqData = $request->getParams();

        // var_dump($upload);
        // var_dump($reqData);
        // die();
        
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

        if ($upload && $reqData['url_video']) {
            $this->validator->rule('required', 'url_video.*.' . $titleKey);
            if ($this->validator->validate()) {
                $storage = new \Upload\Storage\FileSystem('upload/video/');
                
                // Setting URL
                $baseUrl = $request->getUri();
                $scheme = $baseUrl->getScheme();
                $host = $baseUrl->getHost();
                $port = ($baseUrl->getPort() != null) ? $baseUrl->getPort() : null;
                $basePath = $baseUrl->getBasePath();

                $file = new \Upload\File('url_video', $storage);

                $file->addValidations([
                    new \Upload\Validation\Mimetype(['video/mp4', 'video/3gp', 'video/webm']),
                    new \Upload\Validation\Size('128M')
                ]);

                foreach ($upload['url_video'] as $key => $valData) {
                    $file[$key]->setName(uniqid());
                    
                    $fileName = $file[$key]->getNameWithExtension();
                    
                    $url = $scheme . '://' . $host . ':' . $port . $basePath . '/upload/video/' . $fileName;

                    $urlVideo[$key] = $url;
                }

                foreach ($reqData['title'] as $titleKey => $titleValue) {
                    $var[] = $titleValue;
                    $dataTitle[$titleValue] = $titleValue;
                }

                foreach ($reqData['url_video'] as $videoKey => $videoValue) {
                    $video[$videoKey] = $videoValue;
                }

                $mergeData = array_merge($video, $urlVideo);
                $corrected = $this->flipped($var, $mergeData);
                // var_dump($upload['url_video']);
                // var_dump($reqData['url_video']);
                // var_dump($mergeData);
                // var_dump($video);
                
                $dataVideo = array_merge_recursive($dataTitle, $corrected);

                try {
                    $file->upload();
                } catch (\Exception $errors) {
                    $errors = $file->getErrors();
                    return $this->responseDetail($errors, 400);
                }

                $courseAdd = $courseContent->add($getCourse['id'], $dataVideo);
                
                return $this->responseDetail('Success', 200);
            } else {
                return $this->responseDetail('Errors', 400, $this->validator->errors());
            }
        } elseif (!$upload) {
            $this->validator->rule('required', 'url_video.*.' . $titleKey);
            if ($this->validator->validate()) {

                foreach ($reqData['title'] as $key => $values) {
                    $var[] = $values;
                    $titleVideo[$values] = $values;
                }

                foreach ($reqData['url_video'] as $keys => $valData) {
                    $urlVideo[$keys] = $valData;
                }

                $array_temp = [];

                foreach($urlVideo as $key => $val) {
                    if (!in_array($val, $array_temp)) {
                        $array_temp[] = $val;
                    } else {
                        return $this->responseDetail('Video cannot be same', 400);
                    }
                }

                $flipped = $this->flipped($var, $urlVideo);
                
                $dataVideo = array_merge_recursive($titleVideo, $flipped);

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

                $file->addValidations([
                    new \Upload\Validation\Mimetype(['video/mp4', 'video/3gp', 'video/webm']),
                    new \Upload\Validation\Size('128M')
                ]);

                foreach ($reqData['title'] as $key => $values) {
                    foreach ($upload['url_video'] as $valData) {
                        $file[$key]->setName(uniqid());
                        
                        $fileName = $file[$key]->getNameWithExtension();
                        
                        $url = $scheme . '://' . $host . ':' . $port . $basePath . '/upload/video/' . $fileName;

                        $urlVideo[$values] = $url;
                    }
                    $titleVideo[$values] = $values;
                }

                try {
                    $file->upload();
                } catch (\Exception $errors) {
                    $errors = $file->getErrors();
                    return $this->responseDetail($errors, 400);
                }

                $dataVideo = array_merge_recursive($titleVideo, $urlVideo);
                
                $courseAdd = $courseContent->add($getCourse['id'], $dataVideo);

                if (!is_int($courseAdd)) {
                    return $this->responseDetail('Title already used', 400);
                }
                
                return $this->responseDetail('Upload File Success', 201);
            } else {
                return $this->responseDetail('Errors', 400, $this->validator->errors());
            }
        }
    }

    public function putCourseContent(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];
        $course = new \App\Models\Courses\Course;
        $getCourse = $course->getCourse($args['slug']);
        
        $validateUser = $this->validateUser($token, $getCourse);

        if (!$this->checkCourse($getCourse)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        }

        $this->validator->rule('required', 'title');

        $upload = $request->getUploadedFiles();
        $reqData = $request->getParams();

        $courseContent = new \App\Models\Courses\CourseContent;

        if (!$upload) {
            $this->validator->rule('required', 'url_video');
            if ($this->validator->validate()) {
                $courseEdit = $courseContent->edit($reqData, $args['id'], $reqData['url_video']);
            
                return $this->responseDetail('Update Data Success', 200);
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

                $file->setName(uniqid());

                $fileName = $file->getNameWithExtension();
                
                $url = $scheme . '://' . $host . ':' . $port . $basePath . '/upload/video/' . $fileName;

                $file->addValidations([
                    new \Upload\Validation\Mimetype(['video/mp4', 'video/3gp', 'video/webm']),
                    new \Upload\Validation\Size('128M')
                ]);

                $findCourseContent = $courseContent->find('id', $args['id'])->fetch()['url_video'];

                $findFile = end(explode('/', $findCourseContent));
                
                try {
                    $file->upload();

                    if ($findFile != 'sample.mp4') {
                        unlink('upload/video/' . $findFile);
                    }
                    
                } catch (\Exception $errors) {
                    $errors = $file->getErrors();
                    return $this->responseDetail($errors, 400);
                }

                $dataVideo = [
                    'id'        =>  $args['id'],
                    'title'     =>  $reqData['title'],
                ];

                $courseEdit = $courseContent->edit($dataVideo, $args['id'], $url);

                return $this->responseDetail('Update Data Success', 200);
            } else {
                return $this->responseDetail('Errors', 400, $this->validator->errors());
            }
        }
    }


    public function showTrashByIdUser(Request $request, Response $response)
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = $this->findToken();
        $userId = $userToken['user_id'];
        
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

        $courseContent = new \App\Models\Courses\CourseContent;
        $findCourseContent = $courseContent->find('course_id', $findCourse['id'])->fetchAll();
        
        $validateUser = $this->validateUser($token, $findCourse, true);

        if (!$this->checkCourse($findCourse)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        }

        foreach ($findCourseContent as $key => $value) {
            $findFile = end(explode('/', $value['url_video']));

            unlink('upload/video/' . $findFile);
        }

        $course->hardDelete('id', $findCourse['id']);

        return $this->responseDetail($findCourse['title']. ' is permanently removed', 200);
    }

    public function hardDeleteContent(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $course = new \App\Models\Courses\Course;
        $findCourse = $course->find('title_slug', $args['slug'])->fetch();

        $courseContent = new \App\Models\Courses\CourseContent;
        $findCourseContent = $courseContent->find('id', $args['id'])->fetch();

        $findFile = end(explode('/', $findCourseContent['url_video']));

        $validateUser = $this->validateUser($token, $findCourse);

        if (!$this->checkCourse($findCourse)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this Course", 401);
        }

        $deleteContent = $courseContent->hardDelete('id', $findCourseContent['id']);

        if ($deleteContent) {
            unlink('upload/video/'.$findFile);
        }

        return $this->responseDetail($findCourseContent['title']. ' is permanently removed', 200);
    }

    public function searchByCategory(Request $request, Response $response, $args)
    {
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;
        $course = new \App\Models\Courses\Course;
        $category = new \App\Models\Categories\Category;

        $allCourse['content'] = $course->showByCategory($args['category'], $page, 5);
        $allCourse['category'] = $category->getAll()->fetchAll();

        if (!$allCourse) {
            return $this->responseDetail("Courses Not Found", 404);
        }

        return $this->responseDetail("Data Available", 200, $allCourse);
    }

    public function searchByTitle(Request $request, Response $response)
    {
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;

        $course = new \App\Models\Courses\Course;
        $category = new \App\Models\Categories\Category;

        $allCourse['content'] = $course->search($request->getQueryParam('query'), $page, 5);
        $allCourse['category'] = $category->getAll()->fetchAll();

        if (!$allCourse) {
            return $this->responseDetail("Courses Not Found", 404);
        }

        return $this->responseDetail("Data Available", 200, $allCourse);
    }

    public function searchBySlug(Request $request, Response $response, $args)
    {
        $course = new \App\Models\Courses\Course;
        $allCourse = $course->getCourseBySlug($args['slug']);

        if (!$allCourse) {
            return $this->responseDetail("Course Not Found", 404);
        }

        return $this->responseDetail("Data Available", 200, $allCourse);
    }

    private function checkCourse($course)
    {
        if (!$course) {
            return false;
        }

        return true;
    }

    public function showForUser(Request $request, Response $response)
    {
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;
        $course = new \App\Models\Courses\Course;
        $category = new \App\Models\Categories\Category;

        $allCourse['content'] = $course->showForUser($page, 5);
        $allCourse['category'] = $category->getAll()->fetchAll();

        if (!$allCourse['content']) {
            return $this->responseDetail("Course is empty", 200, $allCourse);
        }

        return $this->responseDetail("Data Available", 200, $allCourse);
    }

    public function viewVideo(Request $request, Response $response, $args)
    {
        $course = new \App\Models\Courses\Course;
        $getCourse = $course->find('title_slug', $args['slug'])->fetch();
        $getVideo = $course->getVideo($args['slug'], $args['id']);

        if (!$getVideo) {
            return $this->responseDetail("Video Not Found", 404);
        } elseif (!$getCourse) {
            return $this->responseDetail("Course Not Found", 404);
        }

        return $this->responseDetail("Data Available", 200, $getVideo);
    }

    public function flipped(array $key, array $data)
    {
        $flipped = array_flip($data);
        foreach ($flipped as $keyFlip => $valueFlip) {
            $flipped[$keyFlip] = ($valueFlip === $valueFlip ? $key[$valueFlip] : $data);
        }
        
        return $corrected = array_flip($flipped);
    }

    public function searchByType(Request $request, Response $response, $args)
    {
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;

        $course = new \App\Models\Courses\Course;
        $category = new \App\Models\Categories\Category;

        if ($args['type'] === 'premium') {
            $args['type'] = 1;
        } elseif ($args['type'] === 'free') {
            $args['type'] = 0;
        } else {
            $args['type'] = null;
        }

        $allCourse['content'] = $course->showByType($args['type'], $page, 10);
        $allCourse['category'] = $category->getAll()->fetchAll();

        if (!$allCourse) {
            return $this->responseDetail("Courses Not Found", 404);
        }

        return $this->responseDetail("Data Available", 200, $allCourse);
    }

}