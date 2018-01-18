<?php

namespace Iw\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\Cover;
use Iw\Models\Content;

class BrandController{

	protected $router;

	protected $flash;

	protected $cover;

	protected $content;

	public function __construct(Twig $view, Router $router, Messages $flash, Cover $cover, Content $content)
	{
		$this->router = $router;
		$this->flash = $flash;
		$this->cover = $cover;
		$this->content = $content;
		$view->getEnvironment()->addGlobal('active',['furniture'=>true,'brand'=>true]);
	}

	
	public function index(Request $request, Response $response, Twig $view)
	{

		$cover = $this->cover->where('position',6)->first();
		$contents = $this->content->where('cover_id',$cover->id)->orderBy('rank','asc')->get();

		$target_post_route = 'brand.create';
		$target_add_tag = 'brand.add.tag';
		return $view->render($response, 'furniture/brand.twig', compact('target_post_route','target_add_tag','contents'));

	}

	public function create(Request $request, Response $response){

		$position = $request->getParam('current-place');

		$uploadedFiles = $request->getUploadedFiles();

		$each_file = $uploadedFiles['furniture'];

		$count_file = count($each_file);

		$num_cover = $this->cover->where('position',6)->count();

		if($num_cover > 0)
		{
			$cover = $this->cover->where('position',6)->first();
			$num_content = $this->content->where('cover_id',$cover->id)->count();
			$rank = $num_content;
		}
		else
		{
			$rank = 0;
		}

		if($count_file > 0)
		{

			if($num_cover <= 0)
			{
				$cover = $this->cover->create([
					'filename' => 'furniture-brand',
					'position' => $position,
					'rank' => 1	
				]);
			}


			for($a=0; $a < $count_file; $a++)
			{
				$filename = $each_file[$a]->getClientFilename();
				$fileExt = pathinfo($each_file[$a]->getClientFilename(), PATHINFO_EXTENSION);

				$rank = $rank + 1;

				if($each_file[$a]->getError() === UPLOAD_ERR_OK)
				{
					$content_type_param_name = 'type'.($a+1);
					$content_type = $request->getParam($content_type_param_name);
					$content_save_filename = date('ymdHsi').'_'.hash('crc32', $filename).'.'.$fileExt;
					
					$upload_dir_admin = $this->getAdminUploadPath();
					$upload_dir_client = $this->getClientUploadPath();

					$each_file[$a]->moveTo("$upload_dir_admin/contents/$content_save_filename");

					$srcfile = $upload_dir_admin.'/contents/'.$content_save_filename;
					$dstfile = $upload_dir_client.'/contents/'.$content_save_filename;
					copy($srcfile,$dstfile);

					$content = $cover->hasManyContent()->create([
						'ori_filename' => $filename,
						'filename' => $content_save_filename,
						'rank'=> $rank,
						'type' => $content_type 
					]);
				}
				else
				{
					$this->flash->addMessage('error', '文件上傳失敗!'.$each_file[$i]->getError());
					return $response->withRedirect($this->router->pathFor('brand.index')); 
				}

			}

			$this->flash->addMessage('success','文件上傳成功');
			return $response->withRedirect($this->router->pathFor('brand.index'));
		}
	} 

	public function addTag(Request $request, Response $response){

		$is_tag = ($request->getParam('tag-content-onoffswitch') == '1')? 1:0;
		$content_id = $request->getParam('content-id');
		$content_tag = htmlentities($request->getParam('content-tag'));

		$save_tag = $this->content->where('id',$content_id)->update([
			'is_tag' => $is_tag,
			'tag_content' => $content_tag
		]);

		if($save_tag){
			$this->flash->addMessage('success','標籤內容儲存成功');
			return $response->withRedirect($this->router->pathFor('brand.index'));			
		}
		else
		{
			$this->flash->addMessage('error', '標籤內容儲存失敗!');
			return $response->withRedirect($this->router->pathFor('brand.index')); 			
		}

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