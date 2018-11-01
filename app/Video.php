<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['video_url', 'position', 'width', 'height', 'poster_url', 'sort_order'];
}
