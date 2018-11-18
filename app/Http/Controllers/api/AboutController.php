<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\About;
use App\AboutImage;

use Illuminate\Support\Facades\Log;

class AboutController extends Controller
{
    public function show() 
    {
    	$about = About::find(1);

    	$resp = [];
    	if($about) {
    		$resp = $about->get();
    		$resp['images'] = $about->images()->get();
    	}

    	return $resp;
    }

    public function update(Request $request)
    {
    	Log::debug($request);
    	$num_top_images = $request['num_top_images'];

    	if($num_top_images > 0) {
    		// delete old images
    		AboutImage::truncate();

    		// insert new images
    		$sort_order = 10;
    		for($i = 0; $i < $num_top_images; $i++) {
    			$filename = 'top_image_' . $i;

    			if($request->hasFile($filename)) {
  					[$image_url, $size] = $this->processUploadedFile($request, $filename);

          			$image = new AboutImage();
          			$image->about_id = 1;
          			$image->image_url = $image_url;
          			$image->save();
    			}
    		}
    	}

    	$content = $request['content'];
    	if($content) {
    		$about = About::find(1);
    		$about->content = $content;
    		$about->save();
    	}    	
    }

    public function processUploadedFile($request, $filename)
    {
		$file = $request->file($filename);
		$hashName = $file->hashName();
		if($file->getMimeType() == 'image/jpeg')
     		$hashName = substr_replace($hashName, 'jp', -4, -1);
     	$size = getimagesize($file);
     	$image_url = 'imgs/' . $hashName;
		$file->move(base_path('public/imgs'), $hashName);   	

		return [$image_url, $size];
    }
}
