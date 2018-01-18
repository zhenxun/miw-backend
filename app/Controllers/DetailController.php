<?php

namespace Iw\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\Cover;
use Iw\Models\Content;

class DetailController{

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
		$view->getEnvironment()->addGlobal('active',['detail'=>true]);
	}
	
	public function index(Request $request, Response $response, Twig $view)
	{
		$grid_num = $this->getGridNum();
		$target_post_route = 'detail.create';
		$target_add_content = 'detail.add';
		$target_add_tag = 'detail.add.tag';
		return $view->render($response, 'detail/index.twig',compact('target_post_route','target_add_content','target_add_tag','grid_num'));

	}

	public function create(Request $request, Response $response){

		$pathFor = 'detail.index';
		$coverRank = $request->getParam('rank');
		$coverPosition = ($request->getParam('position') == 'detail')? '8':'';

		$uploadedFiles = $request->getUploadedFiles();

		$cover_file = $uploadedFiles['coverFile'];

		$cover_filename = $cover_file->getClientFilename();
		$cover_fileExt = pathinfo($cover_file->getClientFilename(), PATHINFO_EXTENSION);

		if($cover_file->getError() === UPLOAD_ERR_OK)
		{
			$cover_save_filename = date('ymdhsi').'_'.hash('crc32', $cover_filename).'.'.$cover_fileExt;

			$upload_dir_admin = $this->getAdminUploadPath();
			$upload_dir_client = $this->getClientUploadPath();			

			$cover_file->moveTo("$upload_dir_admin/covers/$cover_save_filename");
			
			$coversrcfile = $upload_dir_admin.'/covers/'.$cover_save_filename;
			$coverdstfile = $upload_dir_client.'/covers/'.$cover_save_filename;
			copy($coversrcfile,$coverdstfile);	

			$cover = $this->cover->create([
				'ori_filename' => $cover_filename,
				'filename' => $cover_save_filename,
				'position' => $coverPosition,
				'rank' => $coverRank	
			]);

			$content_files = $uploadedFiles['contentFile'];
			$count_content_files = count($content_files);

			if($count_content_files > 1)
			{
				for($a=0; $a < $count_content_files; $a++){

					$content_filename = $content_files[$a]->getClientFilename();
					$content_fileExt = pathinfo($content_files[$a]->getClientFilename(), PATHINFO_EXTENSION);

					if($content_files[$a]->getError() === UPLOAD_ERR_OK)
					{
						$rank = $a + 1;
						$content_type_param_name = 'type'.$rank;
						$content_type = $request->getParam($content_type_param_name);
						$content_save_filename = date('ymd').'_'.hash('crc32', $content_filename).'.'.$content_fileExt;
						$content_files[$a]->moveTo("$upload_dir_admin/contents/$content_save_filename");
						
						$cotentsrcfile = $upload_dir_admin.'/contents/'.$content_save_filename;
						$cotentdstfile = $upload_dir_client.'/contents/'.$content_save_filename;
						copy($cotentsrcfile,$cotentdstfile);

						$content = $cover->hasManyContent()->create([
							'ori_filename' => $content_filename,
							'filename' => $content_save_filename,
							'rank'=> $rank,
							'type' => $content_type
						]);
					}
					else
					{
						$this->flash->addMessage('error', '文件上傳失敗!'.$content_files[$a]->getError());
						return $response->withRedirect($this->router->pathFor($pathFor)); 
					}
				}
			}
		}
		else
		{
			$this->flash->addMessage('error','封面文件上傳失敗('.$cover_file->getError().')');
			return $response->withRedirect($this->router->pathFor($pathFor)); 
		}
		

		$this->flash->addMessage('success','文件上傳成功');
		return $response->withRedirect($this->router->pathFor($pathFor));

	}


	public function add(Request $request, Response $response){

		$coverid = $request->getParam('coverid');

		$getLastData = $this->content->where('cover_id',$coverid)->count();

		$getLastRank = $getLastData;

		$uploadedFiles = $request->getUploadedFiles();

		$each_file = $uploadedFiles['contentFile'];

		$count_file = count($each_file);


		for($i=0; $i<$count_file; $i++)
		{

			$filename = $each_file[$i]->getClientFilename();
			$fileExt = pathinfo($each_file[$i]->getClientFilename(), PATHINFO_EXTENSION);

			if($each_file[$i]->getError() === UPLOAD_ERR_OK)
			{
				$content_type_param_name = 'type'.($i+1);
				$content_type = $request->getParam($content_type_param_name);
				$content_save_filename = date('ymdHsi').'_'.hash('crc32', $filename).'.'.$fileExt;

				$upload_dir_admin = $this->getAdminUploadPath();
				$upload_dir_client = $this->getClientUploadPath();

				$each_file[$i]->moveTo("$upload_dir_admin/contents/$content_save_filename");

				$srcfile = $upload_dir_admin.'/contents/'.$content_save_filename;
				$dstfile = $upload_dir_client.'/contents/'.$content_save_filename;
				copy($srcfile,$dstfile);

				$getLastRank = $getLastRank + 1;

				$content = $this->content->create([
					'cover_id' => $coverid,
					'ori_filename' => $filename,
					'filename' => $content_save_filename,
					'rank'=> $getLastRank,
					'type' => $content_type
				]);
			}
			else
			{
				$this->flash->addMessage('error', '文件上傳失敗!'.$each_file[$i]->getError());
				return $response->withRedirect($this->router->pathFor('detail.index')); 
			}
		}

		$this->flash->addMessage('success','文件上傳成功');
		return $response->withRedirect($this->router->pathFor('detail.index'));

	}


	public function cover(Request $request, Response $response){

		$pathFor = 'detail.index';

		$checkRank = $this->cover->where('position',9)->get()->count();

		$coverRank = $checkRank;

		$uploadedFiles = $request->getUploadedFiles();

		$cover_file = $uploadedFiles['annualCover'];

		$cover_filename = $cover_file->getClientFilename();
		$cover_fileExt = pathinfo($cover_file->getClientFilename(), PATHINFO_EXTENSION);

		if($cover_file->getError() === UPLOAD_ERR_OK)
		{
			$cover_save_filename = date('ymdhsi').'_'.hash('crc32', $cover_filename).'.'.$cover_fileExt;
			
			$upload_dir_admin = $this->getAdminUploadPath();
			$upload_dir_client = $this->getClientUploadPath();			

			$cover_file->moveTo("$upload_dir_admin/covers/$cover_save_filename");
			
			$coversrcfile = $upload_dir_admin.'/covers/'.$cover_save_filename;
			$coverdstfile = $upload_dir_client.'/covers/'.$cover_save_filename;
			copy($coversrcfile,$coverdstfile);	

			$cover = $this->cover->create([
				'ori_filename' => $cover_filename,
				'filename' => $cover_save_filename,
				'position' => 9,
				'rank' => $coverRank + 1,	
			]);

			$this->flash->addMessage('success','文件上傳成功');
			return $response->withRedirect($this->router->pathFor($pathFor));

		}
		else
		{
			$this->flash->addMessage('error','封面文件上傳失敗('.$cover_file->getError().')');
			return $response->withRedirect($this->router->pathFor($pathFor)); 			
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
			return $response->withRedirect($this->router->pathFor('detail.index'));			
		}
		else
		{
			$this->flash->addMessage('error', '標籤內容儲存失敗!');
			return $response->withRedirect($this->router->pathFor('detail.index')); 			
		}

	}

	public function annualCover(Request $request, Response $response){

		$annual_cover = $this->cover->where('position','9')->orderBy('created_at','desc')->first();

		return json_encode($annual_cover);
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

	private function getGridNum(){
		//$grid_num = $this->cover->where('position','8')->count();
		$last_cover_rank = $this->cover->where('position','8')->orderBy('rank','desc')->first();
		$grid_num = $last_cover_rank->rank;

		if($grid_num<=3)
		{
			$gridNum = 3;
		}
		else if($grid_num <= 6)
		{
			$gridNum = 6;
		}
		else if($grid_num <= 9)
		{
			$gridNum = 9;
		}
		else
		{
			$gridNum = 12;
		}

		return $gridNum;
	}

}