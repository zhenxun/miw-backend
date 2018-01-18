<?php

namespace Iw\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\User;

class SettingController{

	protected $router;

	protected $user;

	protected $messages;

	public function __construct(Twig $view, Router $router, User $user, Messages $flash)
	{
		$view->getEnvironment()->addGlobal('active',['setting'=>true]);
		$this->router = $router;
		$this->user = $user;
		$this->flash = $flash;
	}
	
	public function index(Request $request, Response $response, Twig $view)
	{

		return $view->render($response, 'setting/index.twig');

	}


	public function changePassword(Request $request, Response $response)
	{

		$newPassword = $request->getParam('newpwd');
		$confirmPassword = $request->getParam('confirmpwd');

		if($newPassword != $confirmPassword)
		{
			$this->flash->addMessage('error','確認密碼錯誤!');

			return $response->withRedirect($this->router->pathFor('setting.index')); 
		}		

		if($newPassword === $confirmPassword)
		{
			$encryptPassword = md5($newPassword);

			$affectedRows = $this->user->where('username','admin')->update([
				'password' => $encryptPassword
			]);

			if($affectedRows)
			{
				$this->flash->addMessage('success','密碼更新成功!');

				return $response->withRedirect($this->router->pathFor('setting.index'));
			}
		}
	}

	public function resetEmail(Request $request, Response $response){

		$email = $request->getParam('resetEmail');

		if($email != '')
		{
			$resetEmail = $this->user->where('username','admin')->update([
				'email' => $email
			]);

			if($resetEmail)
			{
				$this->flash->addMessage('success','重置密碼電子郵件設定成功!');
				return $response->withRedirect($this->router->pathFor('setting.index'));				
			}
			else
			{
				$this->flash->addMessage('error','重置密碼電子郵件設定失敗!');
				return $response->withRedirect($this->router->pathFor('setting.index'));				
			}

		}
		else
		{
			$this->flash->addMessage('error','電子郵件欄位不能空白!');
			return $response->withRedirect($this->router->pathFor('setting.index'));
		}

	}
}