<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\CouponType;

use Illuminate\Support\Facades\Log;

class CouponTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $final_resp = [];
        $couponTypes = CouponType::all();
        $coupon_type_array = json_decode($couponTypes, true);

        foreach ($coupon_type_array as $t) {
            Log::debug($t);
            Log::debug('xxxxxxxx');
            $id = $t['id'];
            $coupon_type_obj = CouponType::find($id);
            $resp = $t;           
            $resp['coupons'] = $coupon_type_obj->coupons()->get();

            array_push($final_resp, $resp);
        }

        return json_encode($final_resp);

       // return CouponType::select('id', 'type', 'description', 'sort_order')->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
