<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\HomePageImage;
use App\CouponForNewComer;
use App\Video;

use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function update(Request $request)
    {
    	Log::debug($request);
    	$num_top_images = $request['num_top_images'];
    	$num_bottom_images = $request['num_bottom_images'];

        $sort_order = 10;

    	if($num_top_images > 0) {
    		$position = 1;

    		// delete old images
    		$hpi = HomePageImage::where('position', $position);	//1: top
    		$hpi->delete();

    		// insert new images
    		$sort_order = 10;
    		for($i = 0; $i < $num_top_images; $i++) {
    			$filename = 'top_image_' . $i;

    			if($request->hasFile($filename)) {
  					[$image_url, $size] = $this->processUploadedFile($request, $filename);

            		$this->updateRecord($image_url, '', $position, $size[0], $size[1], $sort_order);
            		$sort_order += 10;
    			}
    		}
    	}

    	$newcomer_image = $request['newcomer_image'];	
    	if($newcomer_image) {
    		$position = 2;

    		// delete old images
    		$hpi = HomePageImage::where('position', $position);	// 2: newcomer
    		$hpi->delete();

    		$filename = 'newcomer_image';
    		if($request->hasFile($filename)) {
    			[$image_url, $size] = $this->processUploadedFile($request, $filename);

    			$this->updateRecord($image_url, '', $position, $size[0], $size[1], $sort_order);
    		}
    	}

    	$coupon_image = $request['coupon_image'];
    	if($coupon_image) {
    		$position = 3;

     		// delete old images
    		$hpi = HomePageImage::where('position', $position);	// 3: coupon image
    		$hpi->delete();

    		$filename = 'coupon_image';
    		if($request->hasFile($filename)) {
    			[$image_url, $size] = $this->processUploadedFile($request, $filename);

    			$this->updateRecord($image_url, '', $position, $size[0], $size[1], $sort_order);
    		}   		
    	}

    	if($num_bottom_images > 0) {
    		$position = 4;

    		// delete old images
    		$hpi = HomePageImage::where('position', $position);	//4: bottom
    		$hpi->delete();

    		// insert new images
    		$sort_order = 10;
    		for($i = 0; $i < $num_bottom_images; $i++) {
    			$filename = 'bottom_image_' . $i;

    			if($request->hasFile($filename)) {
  					[$image_url, $size] = $this->processUploadedFile($request, $filename);

            		$this->updateRecord($image_url, '', $position, $size[0], $size[1], $sort_order);
            		$sort_order += 10;
    			}
    		}
    	}

        $video_clip = $request['video_clip'];
        $poster_image = $request['poster_image'];

        if($video_clip) {
            $position = 5;
/*
            // delete old video file
            $video = Video::where('position', $position)->get();
            if($video && count($video) > 0) {
                Log::debug($video);
                $video_url = $video[0]->video_url;
                $video_file_path = public_path() . '/' . $video_url;
                if(file_exists($video_file_path)) unlink($video_file_path); 
            }
*/
            // store new video;
            $filename = 'video_clip';
            $filename_poster = 'poster_image';
            if($request->hasFile($filename) && $request->hasFile($filename_poster)) {
                $file = $request->file($filename);
                $hashName = $file->hashName();
                $video_url = 'videos/' . $hashName;
                $file->move(base_path('public/videos'), $hashName);

                $file_poster = $request->file($filename_poster);
                $hasName_poster = $file_poster->hashName();
                $poster_url = 'videos/' . $hasName_poster;
                $file_poster->move(base_path('public/videos'), $hasName_poster);

                Log::debug($video_url);
                $hpv = Video::updateOrCreate(
                    ['position' => $position],
                    ['video_url' => $video_url, 'poster_url' => $poster_url]
                );
            }
        }

    }

    public function updateRecord($url, $click_url, $position, $width, $height, $sort_order)
    {
    	$hpi = new HomePageImage();
    	$hpi->image_url = $url;
    	$hpi->click_to_url = $click_url;
    	$hpi->position = $position;
    	$hpi->width = $width;
    	$hpi->height = $height;
    	$hpi->sort_order = $sort_order;

    	$hpi->save();
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

    public function updateNewComer(Request $request)
    {
    	$description = $request['description'];
    	$num_of_images = $request['num_of_images'];

    	$first = CouponForNewComer::find(1);
    	$isEmpty = false;
    	if(!$first) $isEmpty = true;

    	if($description) {
    		if($isEmpty) {
    			$new = new CouponForNewComer();
    			$new->description = $description;
    			$new->image_url = '';
    			$new->save();
    		} else {
    			CouponForNewComer::where('description', 'like', '%')->update(['description' => $description]);
    		}
    	}

    	if($num_of_images > 0) {
    		// delete all data
    		CouponForNewComer::where('description', 'like', '%')->delete();

    		for($i = 0; $i < $num_of_images; $i++) {
    			$filename = 'image_file_' . $i;
    			if($request->hasFile($filename)) {
    				[$image_url, $size] = $this->processUploadedFile($request, $filename);

    				$newcomer = new CouponForNewComer();
    				$newcomer->description = $description;
    				$newcomer->image_url = $image_url;

    				$newcomer->save();
    			}
    		}
    	}

    }

    public function deleteVideo() {
        
        $position = 5;
        $video_obj = Video::where('position', $position);
        /*
        // delete the video file
        $video = $video_obj->get();
        if($video && count($video) > 0) {
            Log::debug($video);
            $video_url = $video[0]->video_url;
            $video_file_path = public_path() . '/' . $video_url;
            if(file_exists($video_file_path)) unlink($video_file_path); 
        }   */
        
        $video_obj->delete(); 
    }
}
