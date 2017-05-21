<?php

namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GuzzleHttp\Exception\BadResponseException as GuzzleException;

class CourseController extends \App\Controllers\BaseController
{
    public function showAll(Request $request, Response $response)
    {
        $client = $this->testing->request('GET', $this->router->pathFor('api.get.all.course'));

        $content = json_decode($client->getBody()->getContents(), true);

        return $this->view->render($response,'course/show_all.twig', ['data' => $content]);
    }

    public function showByIdUser(Request $request, Response $response)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.get.my.course'));

            $content = json_decode($client->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
        }
        
        return $this->view->render($response,'course/show_by_id_user.twig', ['data' => $content]);
    }

    public function showTrashByIdUser(Request $request, Response $response)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.get.trash.course'));

            $content = json_decode($client->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return $this->view->render($response,'course/trash.twig', ['course' => $content]);
    }

    public function getCreateCourse(Request $request, Response $response)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.get.create.course'));

            $content = json_decode($client->getBody()->getContents(),true);

        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return $this->view->render($response, 'course/add_course.twig', ['category' => $content['data']]);
    }

    public function postCreateCourse(Request $request, Response $response)
    {
        $body = $request->getParsedBody();

        try {
            $client = $this->testing->request('POST', $this->router->pathFor('api.post.create.course'), ['json' => $body]);

            $content = json_decode($client->getBody()->getContents(),true)['data'];
            
            return $response->withRedirect($this->router->pathFor('web.get.update.course', ['slug' => $content['title_slug']]));

        } catch (GuzzleException $e) {
            $data = json_encode($e->getResponse()->getBody()->getContents(), true);

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

            $this->flash->addMessage('errors', 'Please Fill the Form');

            return $response->withRedirect($this->router->pathFor('web.get.create.course'));
        }
    }

    public function getEditCourse(Request $request, Response $response, $args)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.get.edit.course', ['slug' => $args['slug']]));

            $content = json_decode($client->getBody()->getContents(),true);

        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return $this->view->render($response, 'course/edit_course.twig', ['data' => $content['data']]);
    }

    public function postEditCourse(Request $request, Response $response, $args)
    {
        $reqData = $request->getParams();

        try {
            $client = $this->testing->request('POST', $this->router->pathFor('api.post.edit.course', ['slug' => $args['slug']]), ['json' => $reqData]);

            $content = json_decode($client->getBody()->getContents(),true);

            $this->flash->addMessage('success', 'Data has been update');

        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return $response->withRedirect($this->router->pathFor('web.get.my.course'));
    }

    public function getCourse(Request $request, Response $response, $args)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.get.update.course', ['slug' => $args['slug']]));

            $content = json_decode($client->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return $this->view->render($response, 'course/add_course_content.twig', ['data' => $content]);
    }

    public function postAddCourseContent(Request $request, Response $response, $args)
    {
        $reqData = $request->getParams();
        $reqVideo = $request->getUploadedFiles()['url_video'];

        if ($reqVideo) {
            foreach ($reqVideo as $keyVideo => $valueVideo) {
                if (!($valueVideo->getClientFilename() == null)) {
                    $data['video'][] = [
                        'name'      =>  'url_video' . '[' .$keyVideo . ']',
                        'filename'  =>  $valueVideo->getClientFilename(),
                        'Mime-Type' =>  $valueVideo->getClientMediaType(),
                        'contents'  =>  fopen(realpath($valueVideo->file), 'rb'),
                    ];
                }
            }

            foreach ($reqData['title'] as $key => $value) {
                $data['title'][] = [
                    'name' => 'title' . '[' . $key . ']',
                    'contents' => $value,
                ];
            }

            $sendData = array_merge($data['title'], $data['video']);

        } else {
            foreach ($reqData as $keyName => $valueName) {
                foreach ($valueName as $key => $value) {
                    $sendData[] = [
                        'name' => $keyName . '[' .$key. ']',
                        'contents' => $value,
                    ];
                }
            }
        }

        try {
            $client = $this->testing->request('POST', $this->router->pathFor('api.put.update.course', ['slug' => $args['slug']]), ['multipart' => $sendData]);

            return $response->withRedirect($this->router->pathFor('web.get.my.course'));

        } catch (GuzzleException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(),true);

            $this->flash->addMessage('errors', $error['message']);

            return $response->withRedirect($this->router->pathFor('web.get.update.course', ['slug' => $args['slug']]));
        }
    }

    public function getAllCourseContent(Request $request, Response $response, $args)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.get.course.content', ['slug' => $args['slug']]));

            $content = json_decode($client->getBody(),true);

            return $this->view->render($response, 'course/show_all_course_content.twig', ['data' => $content, 'slug' => $args['slug']]);
        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody(),true);

            return $this->view->render($response, 'course/show_all_course_content.twig', ['data' => $content]);
        }
    }

    public function getCourseContent(Request $request, Response $response, $args)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.get.course.content.id', ['slug' => $args['slug'], 'id' => $args['id']]));

            $content = json_decode($client->getBody(),true);

            return $this->view->render($response, 'course/edit_course_content.twig', ['data' => $content]);
        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody(),true);

            return $this->view->render($response, 'course/edit_course_content.twig', ['data' => $content]);
        }
    }

    public function putCourseContent(Request $request, Response $response, $args)
    {
        $reqData = $request->getParams();
        $reqVideo = $request->getUploadedFiles()['url_video'];

        if ($reqVideo) {
            if (!($reqVideo->getClientFilename() == null)) {
                $sendData[] = [
                    'name'      =>  'url_video',
                    'filename'  =>  $reqVideo->getClientFilename(),
                    'Mime-Type' =>  $reqVideo->getClientMediaType(),
                    'contents'  =>  fopen(realpath($reqVideo->file), 'rb'),
                ];
            }

            foreach ($reqData as $key => $value) {
                $sendData[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }
        } else {
            foreach ($reqData as $keyName => $valueName) {
                $sendData[] = [
                    'name' => $keyName,
                    'contents' => $valueName,
                ];
            }
        }

        try {
            $client = $this->testing->request('POST', $this->router->pathFor('api.put.edit.course.content.id', ['slug' => $args['slug'], 'id' => $args['id']]), ['multipart' => $sendData]);

            $content = json_decode($client->getBody(),true);

            return $response->withRedirect($this->router->pathFor('web.get.course.content', ['slug' => $args['slug']]));
        } catch (Exception $e) {
            $content = json_decode($e->getResponse()->getBody(),true);

            return $this->view->render($response, 'course/show_all_course_content.twig', ['data' => $content]);
        }
    }

    public function softDelete(Request $request, Response $response, $args)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.get.soft.delete.course', ['slug' => $args['slug']]));
            
            $this->flash->addMessage('success', 'success delete');

            return $response->withRedirect($this->router->pathFor('web.get.my.course'));
        } catch (Exception $e) {
            $content = json_decode($e->getResponse()->getBody(),true);

            return $response->withRedirect($this->router->pathFor('web.get.my.course'));
        }
    }

    public function restore(Request $request, Response $response, $args)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.get.restore.course', ['slug' => $args['slug']]));
            
            $this->flash->addMessage('success', 'success restore');

            return $response->withRedirect($this->router->pathFor('web.get.trash.course'));
        } catch (Exception $e) {
            $content = json_decode($e->getResponse()->getBody(),true);

            return $response->withRedirect($this->router->pathFor('web.get.trash.course'));
        }
    }

    public function hardDelete(Request $request, Response $response, $args)
    {
        try {
            $client = $this->testing->request('DELETE', $this->router->pathFor('api.get.hard.delete.course', ['slug' => $args['slug']]));
            
            $this->flash->addMessage('success', 'success delete permanently');

            return $response->withRedirect($this->router->pathFor('web.get.my.course'));
        } catch (Exception $e) {
            $content = json_decode($e->getResponse()->getBody(),true);

            return $response->withRedirect($this->router->pathFor('web.get.my.course'));
        }
    }

    public function hardDeleteContent(Request $request, Response $response, $args)
    {
        try {
            $client = $this->testing->request('DELETE', $this->router->pathFor('api.get.hard.delete.course.content', ['slug' => $args['slug'], 'id' => $args['id']]));
            
            $this->flash->addMessage('success', 'success delete permanently');

            return $response->withRedirect($this->router->pathFor('web.get.course.content', ['slug' => $args['slug']]));
        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody(),true);

            return $response->withRedirect($this->router->pathFor('web.get.course.content'));
        }
    }

    public function searchBySlug(Request $request, Response $response, $args)
    {
        try {
            $client = $this->testing->request('GET', $this->router->pathFor('api.course.slug', ['slug' => $args['slug']]));

            $content = json_decode($client->getBody(),true);
        } catch (Exception $e) {
            $content = json_decode($e->getResponse()->getBody(),true);

            return $response->withRedirect($this->router->pathFor('web.get.my.course'));
        }

        return $this->view->render($response, 'course/show_course.twig', ['data' => $content]);
    }
}

?>