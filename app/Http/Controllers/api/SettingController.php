<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Setting;
use App\Libraries\Utilities\SettingType;

use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function showShippingAll()
    {
    	return Setting::where('type', SettingType::SHIPPING_FEE_FORMULA)->get();
    }

    public function updateOrCreateShipping(Request $request)
    {
    	Log::debug($request);

    	$setting = Setting::updateOrCreate(
    		['id' => $request['id']],
    		[
    			'type' => $request['type'],
    			'description' => $request['description'],
    			'setting_name' => $request['setting_name'],
    			'setting_value' => $request['setting_value'],
    			'setting_value_postfix' => $request['setting_value_postfix'],
    			'parameter1' => $request['parameter1'],
    			'parameter2' => $request['parameter2'],
    			'condition1' => $request['condition1'],
    			'condition2' => $request['condition2']
    		]
    	);

    	return $setting;
    }

    public function destroyById($id)
    {
    	return Setting::where('id', $id)->delete();
    }

    public function showFormula($weight)
    {
    	return Setting::where('condition1', '<=', $weight)->where('condition2', '>', $weight)->get();
    }
}
