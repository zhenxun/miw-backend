<?php

namespace Iw\Controllers;

use Slim\Router;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Http\UploadedFile;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\Subscribe;

class AttachmentController{

	public function __construct(Twig $view, Router $router, Subscribe $subscribe, Messages $flash)
	{
		$this->router = $router;
		$this->subscribe = $subscribe;
		$this->flash = $flash;
		$view->getEnvironment()->addGlobal('active',['attachment'=>true]);
	}
	
	public function index(Request $request, Response $response, Twig $view)
	{

		return $view->render($response, 'attachment/index.twig');

	}

	public function upload(Request $request, Response $response){

		$lang = $request->getParam('lang');

		$uploadedFiles = $request->getUploadedFiles();

		$name = $lang.'_file';

		$uploadFile = $uploadedFiles[$name];

		$filename = $uploadFile->getClientFilename();

		$file_extension = pathinfo($uploadFile->getClientFilename(), PATHINFO_EXTENSION);

		if($filename != '')
		{
			if ($uploadFile->getError() === UPLOAD_ERR_OK) {

				$save_filename = date('ymdHsi').'_'.hash('crc32', $filename).'.'.$file_extension;

				//window upload path
				/*$upload_dir_admin = env('WINUPLOADPATHADMIN');
				$upload_dir_client = env('WINUPLOADPATHCLIENT');*/

				//linux upload path
				$upload_dir_admin = env('LINUXUPLOADPATHADMIN');
				$upload_dir_client = env('LINUXUPLOADPATHCLIENT');

				$uploadFile->moveTo("$upload_dir_admin/subscribe/$save_filename");

				$srcfile = $upload_dir_admin.'/subscribe/'.$save_filename;
				$dstfile = $upload_dir_client.'/subscribe/'.$save_filename;
				copy($srcfile,$dstfile);


				$save_lang = ($lang == 'chn')? 0:1;

				$subscribe = $this->subscribe->create([
					'filename' => $save_filename,
					'language' => $save_lang
				]);


				$this->flash->addMessage('success', '文件上傳成功!');
				return $response->withRedirect($this->router->pathFor('attachment.index')); 
			}
		}
		else
		{
	    	$this->flash->addMessage('error', '文件不能空白!');
	    	return $response->withRedirect($this->router->pathFor('attachment.index')); 
		}

	}

	public function search(Request $request, Response $response)
	{
		$subscribes = $this->subscribe->orderBy('created_at', 'desc')->get();

		return json_encode($subscribes);
	}

	public function delete($id, Request $request, Response $response){

		$doDel = $this->subscribe->where('id',$id)->delete();

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