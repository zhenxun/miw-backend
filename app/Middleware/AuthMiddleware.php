<?php

namespace Iw\Middleware;

use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;

class AuthMiddleware{

	protected $view;

	protected $router;

	public function __construct(Twig $view, Router $router, Messages $flash){

		$this->view = $view;
		$this->router = $router;
		$this->flash = $flash;

	}


	public function __invoke($request, $response, $next){

		if(isset($_SESSION['signin-token'])){

			$this->view->getEnvironment()->addGlobal('signin-token', $_SESSION['signin-token']);
			$response = $next($request, $response);

		}
		else
		{
			//$this->flash->addMessage('error','請先登入');
			$basepath = $request->getUri()->getBasePath();
			$uri = $basepath. '/signin';
			$response = $response->withRedirect($uri,403);
		}

		return $response;

	}
}