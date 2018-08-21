<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Comment;

class Order extends Model
{
    public function comments() {
    	return $this->hasMany('App\Comment');
    }
}
