<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AppVersion;

class AppVersionController extends Controller
{
    public function show() {
    	return AppVersion::select('latest_version')->where('id', 1)->get();
    }
}
