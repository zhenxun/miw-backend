<?php

namespace Iw\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Iw\Models\Cover;
use Iw\Models\Content;

class SearchController{

	protected $cover;

	public function __construct(Cover $cover, Content $content){

		$this->content = $content;
		$this->cover = $cover;
	}

	public function get($id, Request $request, Response $response){

		$position_str = array(
			'art'=> '1',
			'building' => '2',
			'people' => '3',
			'design' => '4',
			'topic' => '5',
			'brand' => '6',
			'global' => '7',
			'detail' => '8'
		);

		$position = $position_str[$id];

		$num_cover = $this->cover->where('position',$position)->count();

		if($num_cover > 0)
		{
			//$covers = $this->cover->where('position', $position)->orderBy('created_at','desc')->get();
			$last_cover = $this->cover->where('position', $position)->orderBy('rank','desc')->first();
			$covers = $this->cover->where('position', $position)->orderBy('rank','asc')->get();

			$last_rank = $last_cover->rank;

			for($a=0; $a<$last_rank; $a++)
			{
				$isExsit_covers = $this->cover->where('position', $position)->where('rank', ($a+1))->count();
				if($isExsit_covers > 0)
				{
					$cover = $this->cover->where('position', $position)->where('rank', ($a+1))->first();

					$data[] = array(
						'id' => $cover->id,
						'ori_filename' => $cover->ori_filename,
						'filename' => $cover->filename,
						'position' => $position,
						'rank' => $cover->rank,
						'created_at' => $cover->created_at
					); 
				}
				else
				{
					$data[] = array(
						'id' => null,
						'filename' => null,
						'position' => $position,
						'rank' => ($a+1),
						'created_at' =>null
					); 
				}
			}
		}
		else
		{
			$data = 'nl';
		}
		
		return json_encode($data);

	}

	public function content($id, Request $request, Response $response){

		$contents = $this->content->where('cover_id', $id)->orderBy('rank','asc')->get();

		echo $contents;

	}

	public function eachContent($id, Request $request, Response $response){

		$contents = $this->content->where('id',$id)->first();

		$tag_content = html_entity_decode($contents->tag_content);

		$content = array(
			'id' => $contents->id,
			'cover_id' => $contents->cover_id,
			'is_tag' => $contents->is_tag,
			'tag_content' => $tag_content
		);

		return json_encode($content);

	}

	public function deleteContent($id, Request $request, Response $response){

		$get_cover_id = $this->content->where('id', $id)->first();

		$coverid = $get_cover_id->cover_id;

		$deDel = $this->content->where('id',$id)->delete();

		if($deDel)
		{
			$contents = $this->content->where('cover_id', $coverid)->orderBy('rank','asc')->get();
			$update_rank = 1;
			foreach ($contents as $content) {

				$id = $content->id; 
				$content_rank = $this->content->where('id', $id)->update([
					'rank' => $update_rank
				]);

				$update_rank = $update_rank + 1;
			}

			echo 1;
		}
		else
		{
			echo 0;
		}

	}

	public function deleteCover($id, Request $request, Response $response){

		$cover= $this->cover->where('id',$id)->first();

		$coverPosition = $cover->position;

		$doDelContent = $this->content->where('cover_id',$id)->delete();
		$doDelCover = $this->cover->where('id',$id)->delete();

		if($doDelContent && $doDelCover)
		{
			// $positionCover = $this->cover->where('position',$coverPosition)->orderBy('created_at','desc')->orderBy('filename','desc')->get();
			// $rank = 1;
			// foreach ($positionCover as $cover) {
				
			// 	$rearrange = $this->cover->where('id',$cover->id)->update([
			// 		'rank' => $rank
			// 	]);

			// 	$rank = $rank + 1;
			// }

			echo 1;
		}
		else
		{
			echo 0;
		}

	}

	public function checkOld($position, $rank, Request $request, Response $response){

		$position_str = array(
			'art'=> '1',
			'building' => '2',
			'people' => '3',
			'design' => '4',
			'global' => '7',
			'detail' => '8'
		);	

		$checkOld = $this->cover->where('position', $position_str[$position])->where('id', $rank)->first();

		echo json_encode($checkOld);
	}

	public function summernoteUpload(Request $request, Response $response){

		$uploadedFiles = $request->getUploadedFiles();

		$uploadedFile = $uploadedFiles['file'];

		if(count($uploadedFile)){

			$filename = $uploadedFile->getClientFilename();
			$fileExt = explode('.', $filename);

			if($uploadedFile->getError() === UPLOAD_ERR_OK){

				$save_filename = date('ymdHsi').'_'.hash('crc32', $filename).'.'.$fileExt[1];

				$upload_dir_admin = $this->getAdminUploadPath();
				$upload_dir_client = $this->getClientUploadPath();
			
				$uploadedFile->moveTo("$upload_dir_admin/summernote/$save_filename");
	
				$srcfile = $upload_dir_admin.'/summernote/'.$save_filename;
				$dstfile = $upload_dir_client.'/summernote/'.$save_filename;
				copy($srcfile,$dstfile);
				
				$resp = array(
					'status' => true,
					'msg' => 'success',
					'hash_filename' => $save_filename
				);

			}
			else
			{
				$resp = array(
					'status' => false,
					'msg' => '文件上傳失敗!'
				);				
			}

		}
		else
		{
			$resp = array(
				'status' => false,
				'msg' => '文件不能為空!'
			);
		}

		return json_encode($resp);

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