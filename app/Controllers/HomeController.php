<?php

namespace Iw\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\Cover;

class HomeController{

	protected $router;

	protected $cover;

	protected $flash;

	public function __construct(Twig $view, Router $router, Cover $cover, Messages $flash)
	{
		$this->router = $router;
		$this->cover = $cover;
		$this->flash = $flash;
		$view->getEnvironment()->addGlobal('active',['home'=>true]);
	}
	
	public function index(Request $request, Response $response, Twig $view, Router $router)
	{

			$cover1 = $this->cover->where('position', 0)->where('rank','1')->orderBy('created_at','desc')->first();
			$cover2 = $this->cover->where('position', 0)->where('rank','2')->orderBy('created_at','desc')->first();

			return $view->render($response, 'home.twig',compact('cover1','cover2'));
	}


	public function upload(Request $request, Response $response)
	{
		$position = $request->getParam('position');

		$uploadedFiles = $request->getUploadedFiles();

		$each_file = $uploadedFiles['file'];

		$num_file = count($each_file);

		for($a=0; $a<$num_file; $a++)
		{
			$filename = $each_file[$a]->getClientFilename();
			$fileExt = pathinfo($each_file[$a]->getClientFilename(), PATHINFO_EXTENSION);

			if($each_file[$a]->getError() === UPLOAD_ERR_OK)
			{
				$rank = $a + 1;
				$save_filename = date('ymd').'_'.hash('crc32', $filename).'.'.$fileExt;

				$upload_dir_admin = $this->getAdminUploadPath();
				$upload_dir_client = $this->getClientUploadPath();			

				$each_file[$a]->moveTo("$upload_dir_admin/covers/$save_filename");
				
				$coversrcfile = $upload_dir_admin.'/covers/'.$save_filename;
				$coverdstfile = $upload_dir_client.'/covers/'.$save_filename;
				copy($coversrcfile,$coverdstfile);					

				$cover = $this->cover->create([
					'filename' => $save_filename,
					'position' => $position,
					'rank' => $rank
				]);
			}
			else
			{
				$this->flash->addMessage('error', '文件上傳失敗!'.$each_file[$a]->getError());
				return $response->withRedirect($this->router->pathFor('home')); 
			}
		}

		$this->flash->addMessage('success', '文件上傳成功!');
		return $response->withRedirect($this->router->pathFor('home')); 

	}


	public function search(Request $request, Response $response)
	{
		$covers = $this->cover->where('position',0)->orderBy('created_at','desc')->orderBy('rank','asc')->take(2)->get();

		return json_encode($covers);
	}


	public function delete($id, Request $request, Response $response){

		$doDel = $this->cover->where('id',$id)->delete();

		$status = ($doDel)? 1:0;

		echo $status;
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