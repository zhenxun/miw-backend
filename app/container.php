<?php

use Slim\Views\Twig;
use Slim\Flash\Messages;
use Interop\Container\ContainerInterface;
use Slim\Views\TwigExtension;
use function DI\get;
use Iw\Models\User;
use Iw\Models\Cover;
use Iw\Models\Subscribe;
use Iw\Models\Contact;
use Iw\Models\Footer;



return [
	
	'router' =>  DI\object(Slim\Router::class),

	Twig::class => function(ContainerInterface $c){

		$twig = new Twig(__DIR__. '/../resources/views',[
			'autoescape' => false,
			'cache' => false
		]);


		$twig->addExtension(new TwigExtension(
			$c->get('router'),
			$c->get('request')->getUri()
		));

		$twig->getEnvironment()->addGlobal('flash', $c->get(Messages::class));

		return $twig;
	},

	Messages::class => function(ContainerInterface $c){
		return new Messages();
	},

	User::class => function (ContainerInterface $c){
		 return new User;
	},

	Cover::class => function(ContainerInterface $c){
		return new Cover;
	},

	Subscribe::class => function(ContainerInterface $c){
		return new Subscribe;
	},

	Contact::class => function(ContainerInterface $c){
		return new Contact;
	},

	Footer::class => function(ContainerInterface $c){
		return new Footer;
	}

];