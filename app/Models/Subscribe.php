<?php

namespace Iw\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscribe extends Model{

	use SoftDeletes;

	protected $fillable = ['filename', 'language', 'created_at', 'updated_at'];

	protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

}