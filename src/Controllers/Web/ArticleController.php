<?php
namespace App\Controllers\Web;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GuzzleHttp\Exception\BadResponseException as GuzzleException;

class ArticleController extends \App\Controllers\BaseController
{
    public function showAll(Request $request, Response $response)
    {
        $client = $this->testing->request('GET',
                  $this->router->pathFor('api.get.all.article'));

        $content = json_decode($client->getBody()->getContents(),true);

        return $this->view->render($response, 'articles/list_article.twig',
            ['category' => $content['data']]);
    }

    public function getCreate(Request $request, Response $response)
    {
        try {
            $article = $this->testing->request('GET',
                        $this->router->pathFor('api.get.create.article'));

            $getArticle = json_decode($article->getBody()->getContents(), true);

            return $this->view->render($response, 'articles/add_article.twig',
                    ["category" => $getArticle['data']]);
        } catch (GuzzleException $e) {
            return $this->view->render($response, 'articles/list_article.twig');
        }
    }

    public function postCreate(Request $request, Response $response)
    {
        $body = $request->getParsedBody();

        try {
            $client = $this->testing->request('POST',
                  $this->router->pathFor('api.create.article'),['json' => $body]);

            $this->flash->addMessage('success', 'Create Article Success');

            return $response->withRedirect($this->router->pathFor('web.get.my.article'));
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

            $this->flash->addMessage('errors', 'Please Fill the Form');

            return $response->withRedirect($this->router->pathFor('web.get.create.article'));
        }
    }

    public function getArticleByUserId(Request $request, Response $response)
    {
        try {
            $article = $this->testing->request('GET',
                        $this->router->pathFor('api.get.my.article'));

            $getArticle = json_decode($article->getBody()->getContents(), true);

            return $this->view->render($response, 'articles/list_article.twig',
                    ["article" => $getArticle['data']]);
        } catch (GuzzleException $e) {
            return $this->view->render($response, 'articles/list_article.twig');
        }
    }

    public function getUpdate(Request $request, Response $response, $args)
    {
        $article = $this->testing->request('GET',
                    $this->router->pathFor('api.get.update.article',['slug' =>  $args['slug']]));
        $article = json_decode($article->getBody()->getContents(), true);

        return $this->view->render($response, 'articles/edit_article.twig',
                ["article" => $article['data']]);
    }

    public function postUpdate(Request $request, Response $response, $args)
    {
        $body = $request->getParams();

        try {
            $client = $this->testing->request('PUT',
                  $this->router->pathFor('api.put.update.article', ['slug' =>  $args['slug']]),['form_params' => $body]);

            $this->flash->addMessage('success', 'Edit Article Success');

            return $response->withRedirect($this->router->pathFor('web.get.my.article'));
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

            $this->flash->addMessage('errors', 'Please Fill the Form');

            return $response->withRedirect($this->router->pathFor('web.get.update.article', ['slug' => $args['slug']]));
        }
    }

    public function softDelete(Request $request, Response $response, $args)
    {
        $article = $this->testing->request('PUT',
                    $this->router->pathFor('api.put.soft.delete.article',['slug' =>  $args['slug']]));
        $article = json_decode($article->getBody()->getContents(), true);

        $this->flash->addMessage('warning', $article['message']);

        return $response->withRedirect($this->router->pathFor('web.get.my.article'));
    }

    public function hardDelete(Request $request, Response $response, $args)
    {
        $article = $this->testing->request('DELETE',
                    $this->router->pathFor('api.delete.hard.delete.article',['slug' =>  $args['slug']]));
        $article = json_decode($article->getBody()->getContents(), true);

        $this->flash->addMessage('warning', $article['message']);

        return $response->withRedirect($this->router->pathFor('web.get.my.article'));
    }

    public function showTrash(Request $request, Response $response)
    {
        $article = $this->testing->request('GET',
                    $this->router->pathFor('api.get.trash.article'));
        $article = json_decode($article->getBody()->getContents(), true);

        return $this->view->render($response, 'articles/list_article.twig', ['article' => $article['data']]);
    }

    public function showForUser(Request $request, Response $response)
    {
        $page['page'] = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;
        try {
            $article = $this->testing->request('GET',
                        $this->router->pathFor('api.article.show.for.user'), ['query' => $page]);

            $article = json_decode($article->getBody()->getContents(), true);
        } catch (GuzzleException $e) {

        }

        return $this->view->render($response, 'articles/index.twig', ['article' => $article['data']]);
    }

    public function searchByTitle(Request $request, Response $response)
    {
        $req = $request->getParam('query');
        try {
            $article = $this->testing->request('GET',
                        $this->router->pathFor('api.article.search'), ['json' => $req]);

            $article = json_decode($article->getBody()->getContents(), true);

            return $this->view->render($response, 'articles/index.twig', ['article' => $article['data']]);
        } catch (GuzzleException $e) {

        }
        return $this->view->render($response, 'articles/index.twig', ['article' => $article['data']]);
    }

    public function searchByCategory(Request $request, Response $response, $args)
    {
        $article = $this->testing->request('GET',
                    $this->router->pathFor('api.article.category', ['category' => $args['category']]));

        $article = json_decode($article->getBody()->getContents(), true);

        return $this->view->render($response, 'articles/index.twig', ['article' => $article['data']]);
    }

    public function detail(Request $request, Response $response, $args)
    {
        try {
            $article = $this->testing->request('GET',
                        $this->router->pathFor('api.article.slug', ['slug' => $args['slug']]));

            $article = json_decode($article->getBody()->getContents(), true);

            return $this->view->render($response, 'articles/detail.twig', ['article' => $article['data']]);
        } catch (GuzzleException $e) {
            return $this->view->render($response, 'articles/detail.twig');
        }
    }

}
