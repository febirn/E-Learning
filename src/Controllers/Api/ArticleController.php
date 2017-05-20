<?php

namespace App\Controllers\Api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ArticleController extends \App\Controllers\BaseController
{
    public function showAll(Request $request, Response $response)
    {
        $article = new \App\Models\Articles\Article;
        $allArticle = $article->getAllJoin();

        if (!$allArticle) {
            return $this->responseDetail("Article is empty", 404);
        }

        return $this->responseDetail("Data Available", 200, $allArticle);
    }

    public function showByIdUser(Request $request, Response $response)
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = new \App\Models\Users\UserToken;
        $userId = $userToken->find('token', $token)->fetch()['user_id'];

        $article = new \App\Models\Articles\Article;
        $findArticle = $article->getArticleByUserId($userId);

        if (!$findArticle) {
            return $this->responseDetail("You not have articles", 404);
        }

        return $this->responseDetail("Data Available", 200, $findArticle);
    }

    public function showTrashByIdUser(Request $request, Response $response)
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = new \App\Models\Users\UserToken;
        $userId = $userToken->find('token', $token)->fetch()['user_id'];

        $article = new \App\Models\Articles\Article;
        $findArticle = $article->getTrashByUserId($userId);

        if (!$findArticle) {
            return $this->responseDetail("You not have trash", 404);
        }

        return $this->responseDetail("Data Available", 200, $findArticle);
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
		$post = $request->getParams();

		$token = $request->getHeader('Authorization')[0];

		$userToken = new \App\Models\Users\UserToken;

		$post['user_id'] = $userToken->find('token', $token)->fetch()['user_id'];

		$rule = [
            'required' => [
                ['title'],
                ['content'],
                ['category'],
            ],
            'lengthMin' => [
                ['content', 10],
            ],
        ];

        $this->validator->rules($rule);

        if ($this->validator->validate()) {
            $article = new \App\Models\Articles\Article;

            $create = $article->add($post);

            if (!is_int($create)) {
                return $this->responseDetail("Title have already used", 400);
            }

            $categories = $request->getParam('category');
            $category = new \App\Models\Categories\Category;
            $createCategory = $category->add($categories);

            $articleCategory = new \App\Models\Articles\ArticleCategory;
            $articleCategory->add($create, $createCategory);

            return $this->responseDetail("Article Created", 201);
        } else {
            return $this->responseDetail("Error", 400, $this->validator->errors());
        }
	}

    private function validateUser($token, $article)
    {
        $userToken = new \App\Models\Users\UserToken;
        $userId = $userToken->find('token', $token)->fetch()['user_id'];

        $role = new \App\Models\Users\UserRole;
        $roleUser = $role->find('user_id', $userId)->fetch()['role_id'];

        if ($userId != $article['user_id'] && $roleUser > 1) {
            return false;
        }

        return true;
    }

    private function checkArticle($article)
    {
        if (!$article) {
            return false;
        }

        return true;
    }

    public function getUpdate(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $article = new \App\Models\Articles\Article;
        $category = new \App\Models\Categories\Category;

        $getArticle['article'] = $article->getEdit($args['slug']);
        $getArticle['category'] = $category->getAll()->fetchAll();

        $validateUser = $this->validateUser($token, $getArticle);

        if (!$this->checkArticle($getArticle)) {
            return $this->responseDetail("Data Not Found", 404);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this article", 401);
        }

        return $this->responseDetail("Data Available", 200, $getArticle);
    }

    public function putUpdate(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $article = new \App\Models\Articles\Article;
        $getArticle = $article->getEdit($args['slug']);

        $validateUser = $this->validateUser($token, $getArticle);

        if (!$this->checkArticle($getArticle)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this article", 401);
        }

        $post = $request->getParams();

        $rule = [
            'required' => [
                ['title'],
                ['content'],
                ['category'],
            ],
            'lengthMin' => [
                ['content', 10],
            ],
        ];

        $this->validator->rules($rule);

        if ($this->validator->validate()) {
            $article = new \App\Models\Articles\Article;

            $update = $article->edit($post, $args['slug']);

            if (!is_array($update)) {
                return $this->responseDetail("Title already used", 400);
            }

            $categories = $request->getParam('category');
            $category = new \App\Models\Categories\Category;
            $updateCategory = $category->add($categories);

            $articleCategory = new \App\Models\Articles\ArticleCategory;
            $articleCategory->edit($update['id'], $updateCategory);

            return $this->responseDetail("Article has updated", 200);
        } else {
            return $this->responseDetail("Error", 400, $this->validator->errors());
        }
    }

    public function softDelete(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $article = new \App\Models\Articles\Article;
        $findArticle = $article->find('title_slug', $args['slug'])->withoutDelete()->fetch();

        $validateUser = $this->validateUser($token, $findArticle);

        if (!$this->checkArticle($findArticle)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this article", 401);
        }

        $article->softDelete('id', $findArticle['id']);

        return $this->responseDetail($findArticle['title']. 'is set to trash', 200);
    }

    public function restore(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $article = new \App\Models\Articles\Article;
        $findArticle = $article->find('title_slug', $args['slug'])->fetch();

        $validateUser = $this->validateUser($token, $findArticle);

        if (!$this->checkArticle($findArticle)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this article", 401);
        }

        $article->restore('id', $findArticle['id']);

        return $this->responseDetail($findArticle['title'] .'is restored', 200);
    }

    public function hardDelete(Request $request, Response $response, $args)
    {
        $token = $request->getHeader('Authorization')[0];

        $article = new \App\Models\Articles\Article;
        $findArticle = $article->find('title_slug', $args['slug'])->fetch();

        $validateUser = $this->validateUser($token, $findArticle);

        if (!$this->checkArticle($findArticle)) {
            return $this->responseDetail("Data Not Found", 400);
        } elseif (!$validateUser) {
            return $this->responseDetail("You have not Authorized to edit this article", 401);
        }

        $article->hardDelete('id', $findArticle['id']);

        return $this->responseDetail($findArticle['title']. 'is permanently removed', 200);
    }

    public function showForUser(Request $request, Response $response)
    {
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;
        $article = new \App\Models\Articles\Article;
        $category = new \App\Models\Categories\Category;

        $allArticle['content'] = $article->showForUser($page, 5);
        $allArticle['category'] = $category->getAll()->fetchAll();

        if (!$allArticle['content']) {
            return $this->responseDetail("Article is empty", 200, $allArticle);
        }

        return $this->responseDetail("Data Available", 200, $allArticle);

    }

    public function searchByCategory(Request $request, Response $response, $args)
    {
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;
        $article = new \App\Models\Articles\Article;
        $category = new \App\Models\Categories\Category;

        $allArticle['content'] = $article->showByCategory($args['category'], $page, 5);
        $allArticle['category'] = $category->getAll()->fetchAll();

        if (!$allArticle) {
            return $this->responseDetail("Articles Not Found", 404);
        }

        return $this->responseDetail("Data Available", 200, $allArticle);
    }

    public function searchByTitle(Request $request, Response $response)
    {
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;
        $article = new \App\Models\Articles\Article;

        $allArticle = $article->search($request->getQueryParam('q'), $page, 5);

        if (!$allArticle) {
            return $this->responseDetail("Articles Not Found", 404);
        }

        return $this->responseDetail("Data Available", 200, $allArticle);
    }

    public function searchBySlug(Request $request, Response $response, $args)
    {
        $article = new \App\Models\Articles\Article;

        $allArticle = $article->getArticleBySlug($args['slug']);

        if (!$allArticle) {
            return $this->responseDetail("Article Not Found", 404);
        }

        return $this->responseDetail("Data Available", 200, $allArticle);
    }
}
