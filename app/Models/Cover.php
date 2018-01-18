<?php

namespace Iw\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Iw\Models\Content;

class Cover extends Model{
	
	use SoftDeletes;

	protected $fillable = ['ori_filename','filename', 'position', 'rank', 'created_at'];

	protected $guarded = ['id'];	

    protected $dates = ['deleted_at'];


    public function hasManyContent(){

    	return $this->hasMany(Content::class);
    }

}