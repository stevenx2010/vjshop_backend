<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coating extends Model
{
    public function product() {
    	return $this->hasManage('App\Product');
    }
}
