<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Video;

use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{

    public function showByPosition($position)
    {
    	Log::debug($position);
    	return Video::where('position', $position)->get();
    }
}
