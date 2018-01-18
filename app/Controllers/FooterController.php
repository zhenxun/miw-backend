<?php

namespace Iw\Controllers;

use Slim\Router;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Http\UploadedFile;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\Footer;

class FooterController{

	protected $router;

	protected $flash;

	public function __construct(Twig $view, Router $router, Messages $flash, Footer $footer)
	{
		$this->router = $router;
		$this->flash = $flash;
		$this->footer = $footer;
		$view->getEnvironment()->addGlobal('active',['setting'=>true,'footer'=>true]);
	}
	
	public function index(Request $request, Response $response, Twig $view)
	{
		$logo = $this->footer->find(1);

		$content = $this->footer->find(2);

		return $view->render($response, 'setting/footer.twig', compact('logo','content'));

	}

	public function update(Request $request, Response $response){

		$content = htmlentities($request->getParam('editor1'));

		$uploadedFiles = $request->getUploadedFiles();

		$uploadedFile = $uploadedFiles['logo'];

		$filename = $uploadedFile->getClientFilename();

		$file_extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

		$type = explode('/',$uploadedFile->getClientMediaType());
		

		if($filename != '')
		{
			if($type[0] === 'image')
			{
				if ($uploadedFile->getError() === UPLOAD_ERR_OK) {

					$save_filename = date('ymd').'_'.hash('crc32', $filename).'.'.$file_extension;

					$upload_dir_admin = $this->getAdminUploadPath();
					$upload_dir_client = $this->getClientUploadPath();	

					$uploadedFile->moveTo("$upload_dir_admin/footer/$save_filename");

					$srcfile = $upload_dir_admin.'/footer/'.$save_filename;
					$dstfile = $upload_dir_client.'/footer/'.$save_filename;
					copy($srcfile,$dstfile);

		        	
		        	$footer = $this->footer->find(1);

		        	$footer->value = $save_filename;

		        	$footer->save();

		    	}
		    	else
		    	{
	    			$this->flash->addMessage('error', '圖片上傳失敗!');
	    			return $response->withRedirect($this->router->pathFor('footer.index')); 		    		
		    	}
		    }
		    else
		    {

	    		$this->flash->addMessage('error', '僅能上傳圖片!');
	    		return $response->withRedirect($this->router->pathFor('footer.index')); 		    	
		    }
	    }
	    else
	    {

	    	$this->flash->addMessage('error', '檔案不能空白!');
	    	return $response->withRedirect($this->router->pathFor('footer.index')); 		    	

	    }

	    $this->flash->addMessage('success', '儲存成功!');
	    return $response->withRedirect($this->router->pathFor('footer.index')); 

	}

	private function getAdminUploadPath(){
        
        switch (PHP_OS) {
            case 'WINNT':
                $upload_dir_admin = env('WINUPLOADPATHADMIN');
                break;
            case 'Linux':
                $upload_dir_admin = env('LINUXUPLOADPATHADMIN');
                break;
        }
        
        return $upload_dir_admin;
	}
	
	private function getClientUploadPath(){
        
        switch (PHP_OS) {
            case 'WINNT':
                $upload_dir_client = env('WINUPLOADPATHCLIENT');
                break;
            case 'Linux':
                $upload_dir_client = env('LINUXUPLOADPATHCLIENT');
                break;
        }
        
        return $upload_dir_client;
    }

}