<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quanlity extends Model
{
    public function product() {
    	return $this->hasMany('App\Product');
    }
}
