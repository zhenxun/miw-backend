<?php

namespace Iw\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ForgotPasswordController{

	protected $router;

	protected $flash;

	protected $user;

	public function __construct(Router $router, Messages $flash, User $user)
	{
		$this->router = $router;
		$this->flash = $flash;
		$this->user = $user;
	}
	
	public function index(Request $request, Response $response, Twig $view){

		return $view->render($response, 'signin/forgot.twig');

	}

	public function reset(Request $request, Response $response){

		$sendEmail = $request->getParam('sendEmail');

		$user = $this->user->where('email',$sendEmail)->count();

		if($user > 0)
		{
			$mail = new PHPMailer(true);

			try {

				$mail->SMTPDebug = 2;
				$mail->isSMTP();
				$mail->Host = "smtp.gmail.com";
				$mail->SMTPAuth = true;
				$mail->Username = 'zhenxun9119@gmail.com';
				$mail->Password = 'wzx910619015018';
				$mail->SMTPSecure = 'ssl';
				$mail->Port = 465;
				$mail->CharSet = 'utf-8';
				$mail->isHTML(true);

				$mail->setFrom('no-reply@com.tw','iw傢飾系統管理者');
				$mail->addAddress($sendEmail);

				$mail->Subject = 'iw傢飾管理平台密碼重置';

				$str = date("ymdHsi").$sendEmail.rand(1000,9999);

				$resetPassword = crypt($str, 'se');

				$body  = '<html><body><p>系統已將舊密碼重置，新密碼如下:</p><p>新密碼: '. $resetPassword .'</p>';
				$body .= '<p>登入後可至 <strong>系統設定->變更密碼</strong> 更改密碼，網址連結: <a href="http://www.iw.com.tw/admin/public/setting">http://www.iw.com.tw/admin/public/setting </a> </p>';
				$body .= '</body></html>';

				$mail->Body = $body;

				if($mail->send())
				{
					$encryptPassword = md5($resetPassword);

					$user = $this->user->where('email',$sendEmail)->update([
						'password' => $encryptPassword
					]);

					$this->flash->addMessage('success','郵件寄送成功。');
					return $response->withRedirect($this->router->pathFor('forgot.index')); 
				}
				else
				{
					$this->flash->addMessage('error','郵件寄送失敗。請重新嘗試!');
					return $response->withRedirect($this->router->pathFor('forgot.index')); 					
				}


			} catch(Exception $e){
				$this->flash->addMessage('error','郵件寄送失敗。請重新嘗試!');
				return $response->withRedirect($this->router->pathFor('forgot.index')); 
			}
		}
		else
		{
			$this->flash->addMessage('error','找不到符合之電子郵件!');
		 	return $response->withRedirect($this->router->pathFor('forgot.index')); 
		}

	}
}