<?php

namespace Iw\Controllers;

use Slim\Router; 
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\User;

class SignInController{

	protected $router;

	protected $user;

	protected $flash;

	public function __construct(Router $router, User $user, Messages $flash)
	{
		$this->router = $router;
		$this->user = $user;
		$this->flash = $flash;
	}
	
	public function index(Request $request, Response $response, Twig $view, Messages $flash)
	{

		return $view->render($response, 'signin/index.twig');
	}


	public function signin(Request $request, Response $response, Router $router, Messages $flash)
	{
		$username = $request->getParam('username');
		$password = $request->getParam('password');

		$user = $this->user->where('username',$username)->first();

		if($user->password == md5($password))
		{
			$hash_str = $username.$password;
			$_SESSION['signin-token'] = hash('sha256', $hash_str);

			return $response->withRedirect($router->pathFor('home'));
		}

		 $this->flash->addMessage('error','賬號或密碼錯誤，請重新嘗試!');

		 return $response->withRedirect($router->pathFor('signin.index')); 

	}
}