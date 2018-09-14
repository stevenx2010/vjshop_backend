<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Coupon;
use App\CouponForNewComer;
use App\Customer;

use Illuminate\Support\Facades\Log;

class CouponsController extends Controller
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
    public function show($id)
    {
        return Coupon::select('id', 'name', 'description', 'coupon_type_id', 'expire_date', 'expired', 'discount_method', 'discount_percentage', 'discount_value', 'quantity_initial', 'quantity_available', 'image_url')->where('coupon_type_id', $id)->where('expired', false)->get();
    }

    public function showNewComer() {
        return CouponForNewComer::select('description', 'image_url')->get();
    }

    public function showByMobile($mobile)
    {
        $id = Customer::select('id')->where('mobile', $mobile)->get();
        $user = Customer::find($id[0]->id);
        return $user->coupons()->get();
    }

    public function showCouponsFiltered($mobile)
    {
        $id = Customer::select('id')->where('mobile', $mobile)->get();

        return \DB::select('select id, name, description, coupon_type_id, expire_date, expired, discount_method, discount_percentage, discount_value, quantity_initial, quantity_available, image_url from coupons c left join (select * from coupon_customer where customer_id = ?) d on c.id=d.coupon_id where d.coupon_id is null', [$id[0]->id]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($tyeId)
    {
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

    public function updateExpireStatus(Request $request) {

        $coupon = Coupon::find($request['id']);
        $coupon->expired = $request['expired'];
        $coupon->save();
    }

    public function updateCouponCustomerRelation(Request $request) {
        Log::debug($request);
        $id = Customer::select('id')->where('mobile', $request['mobile'])->get();
        Log::debug($id[0]->id);
        $user = Customer::find($id[0]->id);

        $coupon_id = $request['id'];
        $coupon = Coupon::find($coupon_id);

        if($user->coupons->contains($coupon)) {
            return response('duplicate', 200);
        } else {
            $user->coupons()->attach([$coupon_id => ['quantity' => 1]]);
        }

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
