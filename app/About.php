<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    public function images() {
    	return $this->hasMany('App\AboutImage');
    }
}
