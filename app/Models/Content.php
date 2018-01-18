<?php

namespace Iw\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Iw\Models\Cover;

class Content extends Model{
	
	use SoftDeletes;

	protected $fillable = ['cover_id', 'ori_filename' ,'filename', 'rank', 'type', 'is_tag', 'tag_content', 'created_at'];

	protected $guarded = ['id'];

    protected $dates = ['deleted_at'];


    public function hasOneCover(){

    	return $this->hasOne(Cover::class);
    }


}