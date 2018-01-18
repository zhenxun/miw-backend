<?php

use Slim\Router;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Iw\Middleware\AuthMiddleware;

$app->group('', function(){

	$this->get('/',['Iw\Controllers\HomeController','index'])->setName('home');

	$this->get('/search',['Iw\Controllers\HomeController','search'])->setName('home.search');

	$this->delete('/search/{id}',['Iw\Controllers\HomeController','delete'])->setName('home.delete');

	$this->post('/',['Iw\Controllers\HomeController','upload'])->setName('home.upload');

	$this->get('/humanities/art',['Iw\Controllers\ArtController', 'index'])->setName('art.index');

	$this->post('/humanities/art',['Iw\Controllers\ArtController','create'])->setName('art.create');

	$this->post('/humanities/art/add',['Iw\Controllers\ArtController','add'])->setName('art.add');

	$this->post('/humanities/art/tag/add',['Iw\Controllers\ArtController','addTag'])->setName('art.add.tag');

	$this->get('/humanities/building',['Iw\Controllers\BuildingController', 'index'])->setName('building.index');

	$this->post('/humanities/building',['Iw\Controllers\BuildingController','create'])->setName('building.create');

	$this->post('/humanities/building/add',['Iw\Controllers\BuildingController','add'])->setName('building.add');

	$this->post('/humanities/building/tag/add',['Iw\Controllers\BuildingController','addTag'])->setName('building.add.tag');

	$this->get('/humanities/people',['Iw\Controllers\PeopleController', 'index'])->setName('people.index');

	$this->post('/humanities/people',['Iw\Controllers\PeopleController','create'])->setName('people.create');

	$this->post('/humanities/people/add',['Iw\Controllers\PeopleController','add'])->setName('people.add');

	$this->post('/humanities/people/tag/add',['Iw\Controllers\PeopleController','addTag'])->setName('people.add.tag');

	$this->get('/space/design',['Iw\Controllers\DesignController', 'index'])->setName('design.index');

	$this->post('/space/design',['Iw\Controllers\DesignController','create'])->setName('design.create');

	$this->post('/space/design/add',['Iw\Controllers\DesignController','add'])->setName('design.add');

	$this->post('/space/design/tag/add',['Iw\Controllers\DesignController','addTag'])->setName('design.add.tag');

	$this->get('/furniture/topic',['Iw\Controllers\TopicController', 'index'])->setName('topic.index');

	$this->post('/furniture/topic',['Iw\Controllers\TopicController','create'])->setName('topic.create');

	$this->post('/furniture/topic/tag/add',['Iw\Controllers\TopicController','addTag'])->setName('topic.add.tag');

	$this->get('/furniture/brand',['Iw\Controllers\BrandController', 'index'])->setName('brand.index');

	$this->post('/furniture/brand',['Iw\Controllers\BrandController', 'create'])->setName('brand.create');

	$this->post('/furniture/brand/tag/add',['Iw\Controllers\BrandController','addTag'])->setName('brand.add.tag');

	$this->get('/global',['Iw\Controllers\GlobalController', 'index'])->setName('global.index');

	$this->post('/global',['Iw\Controllers\GlobalController', 'create'])->setName('global.create');

	$this->post('/global/add',['Iw\Controllers\GlobalController', 'add'])->setName('global.add');

	$this->post('/global/tag/add',['Iw\Controllers\GlobalController','addTag'])->setName('global.add.tag');

	$this->get('/detail',['Iw\Controllers\DetailController', 'index'])->setName('detail.index');

	$this->get('/detail/annual/cover',['Iw\Controllers\DetailController', 'annualCover'])->setName('detail.annual.cover');

	$this->post('/detail/cover',['Iw\Controllers\DetailController','cover'])->setName('detail.cover');

	$this->post('/detail',['Iw\Controllers\DetailController', 'create'])->setName('detail.create');

	$this->post('/detail/add',['Iw\Controllers\DetailController', 'add'])->setName('detail.add');

	$this->post('/detail/tag/add',['Iw\Controllers\DetailController','addTag'])->setName('detail.add.tag');

	$this->get('/attachment',['Iw\Controllers\AttachmentController', 'index'])->setName('attachment.index');

	$this->get('/attachment/search',['Iw\Controllers\AttachmentController', 'search'])->setName('attachment.search');

	$this->post('/attachment',['Iw\Controllers\AttachmentController', 'upload'])->setName('attachment.upload');

	$this->delete('/attachment/{id}',['Iw\Controllers\AttachmentController', 'delete'])->setName('attachment.delete');

	$this->get('/contact',['Iw\Controllers\ContactController', 'index'])->setName('contact.index');

	$this->delete('/contact/{id}',['Iw\Controllers\ContactController', 'contactDelete'])->setName('contact.delete');

	$this->get('/setting',['Iw\Controllers\SettingController', 'index'])->setName('setting.index');

	$this->post('/setting',['Iw\Controllers\SettingController', 'changePassword'])->setName('changePassword');

	$this->post('/setting/reset',['Iw\Controllers\SettingController', 'resetEmail'])->setName('resetEmail');

	$this->get('/setting/footer',['Iw\Controllers\FooterController', 'index'])->setName('footer.index');

	$this->post('/setting/footer',['Iw\Controllers\FooterController', 'update'])->setName('footer.update');

	$this->get('/signout',['Iw\Controllers\SignOutController','signout'])->setName('signout');

	$this->get('/cover/{id}',['Iw\Controllers\SearchController','get'])->setName('cover.get');

	$this->get('/cover/check/{position}/{rank}',['Iw\Controllers\SearchController','checkOld'])->setName('cover.check');

	$this->delete('/cover/{id}',['Iw\Controllers\SearchController','deleteCover'])->setName('cover.delete');

	$this->get('/content/{id}',['Iw\Controllers\SearchController','content'])->setName('content.get');

	$this->get('/content/each/{id}',['Iw\Controllers\SearchController','eachContent'])->setName('content.each.get');

	$this->delete('/content/{id}',['Iw\Controllers\SearchController','deleteContent'])->setName('content.delete');

	$this->post('/summernote/upload',['Iw\Controllers\SearchController','summernoteUpload'])->setName('summernote.upload');

})->add(new AuthMiddleware($container->get(Twig::class), $container->get('router'), $container->get(Messages::class)));


$app->get('/signin',['Iw\Controllers\SignInController', 'index'])->setName('signin.index');

$app->post('/signin',['Iw\Controllers\SignInController', 'signin'])->setName('signin');

$app->get('/forgot',['Iw\Controllers\ForgotPasswordController', 'index'])->setName('forgot.index');

$app->post('/forgot',['Iw\Controllers\ForgotPasswordController','reset'])->setName('forgot.reset');



