<?php

namespace Iw\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SignOutController{

	protected $router;

	public function __construct(Twig $view, Router $router)
	{
		$this->router = $router;
	}
	
	public function signout(Request $request, Response $response, Router $router)
	{
		if(isset($_SESSION['signin-token']))
		{
			session_unset(); 
			session_destroy();
		}

		return $response->withRedirect($router->pathFor('signin.index'));

	}
}