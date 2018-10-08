<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function updateOrCreate(Request $request)
    {
    	Log::debug($request);

    	$id = $request['id'];
    		
    	$api_token = $this->genToken($request['email']);

    	Log::debug(strlen($api_token));

    	$image_url = $request['image_url'];

    	if($request->hasFile('image')){
    		$file = $request->file('image');
    		$hashName = $file->hashName();
    		if($file->getMimeType() == 'image/jpeg')
                 $hashName = substr_replace($hashName, 'jp', -4, -1);

            $image_url = 'imgs/' . $hashName;
            $file->move(base_path('public/imgs'), $hashName);
    	}

    	$now = new \DateTime('now');
    	$last_login = $now->format('Y-m-d H:i:s');

    	$user = User::updateOrCreate(
    		['id' => $id], 
    		[
    			'id' => $id,
    			'name' => $request['username'],
    			'mobile' => $request['mobile'],
    			'email' => $request['email'],
    			'password' => $request['password'],
    			'api_token' => $api_token,
    			'image_url' => $image_url,
    			'last_login' => $last_login
    		]
    	);

    	return $user;
    }

    public function genToken($mobile) {
        $signer = new Sha256();
        
        $access_token = (new Builder())->setIssuer('http://venjong.com')
                                       ->setAudience($mobile)
                                       ->setIssuedAt(time())
                                       ->setExpiration(time() + 365 * 24 * 3600)
                                       ->sign($signer, 'Vj20182018')
                                       ->getToken();   

        return $access_token;    
    }

    public function showAll() {
    	return User::all();
    }

    public function destroyById($id)
    {
    	return User::where('id', $id)->where('name', '!=', 'admin')->delete();
    }

    public function login(Request $request)
    {
    	Log::debug($request);
    	$user = User::where('password', $request['password'])->get();

    	if($user && count($user) > 0) {
	    	$user_obj = User::find($user[0]->id);
	    	$now = new \DateTime('now');
	    	$user_obj->last_login = $now->format('Y-m-d H:i:s');
	    	$user_obj->save();
	    }

    	return $user;
    }

    public function updatePassword(Request $request)
    {
    	$user = User::find($request['user_id']);
    	if($user) {
    		$user->password = $request['password'];
    		$user->first_login = false;
    		$user->save();
    	}
    }

    public function checkEmailUnique($email)
    {
    	$result = User::where('email', $email)->get();

    	Log::debug($result);

    	if(count($result) > 0) return json_encode(['status' => 0]);
    	else return json_encode(['status' => 1]);
    }
}

