<?php

namespace Iw\Controllers;

use Slim\Router;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\Contact;

class ContactController{

	protected $router;

	public function __construct(Twig $view, Router $router, Contact $contact, Messages $flash)
	{
		$this->router = $router;
		$this->contact = $contact;
		$this->flash = $flash;
		$view->getEnvironment()->addGlobal('active',['contact'=>true]);
	}
	
	public function index(Request $request, Response $response, Twig $view)
	{

		$contacts = $this->contact->orderBy('created_at','desc')->get();

		return $view->render($response, 'contact/index.twig', compact('contacts'));

	}

	public function contactDelete($id, Request $request, Response $response){

		$doDel = $this->contact->where('id',$id)->delete();

		if($doDel)
		{
			echo 1;
		}
		else
		{
			echo 0;
		}

	}
}