<?php

namespace App\Controllers;

use Slim\Container;

abstract class BaseController
{
	protected $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function __get($property)
	{
		if ($this->container->{$property}) {
			return $this->container->{$property};
		}
	}

	/**
	 * Give Description About Response
	 * @param  int|200    $status   HTTP status code
	 * @param  string     $message
	 * @param  array      $data     [description]
	 * @param  array|null $meta     additional data
	 * @return $this->response->withHeader('Content-type', 'application/json')->withJson($response, $response['status']);
	 */
	protected function responseDetail($message, $status = 200, $data = null, array $meta = null)
	{
		$response = [
			'status'	=> $status,
			'message'	=> $message,
			'data'		=> $data,
			'meta'		=> $meta,
		];

		if (is_null($data) && is_null($meta)) {
			array_pop($response);
		} if (!$meta) {
			array_pop($response);
		}

		return $this->response->withHeader('Content-type', 'application/json')->withJson($response, $response['status']);
	}

	protected function findToken()
	{
		$token = new \App\Models\Users\UserToken;
		$getToken = $this->request->getHeader('HTTP_AUTHORIZATION')[0];
		$findToken = $token->find('token', $getToken)->fetch();

		if ($findToken) {
			return $findToken;
		}

		return false;
	}
}