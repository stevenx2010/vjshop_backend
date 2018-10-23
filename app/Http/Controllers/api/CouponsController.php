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

    public function showCoupons(Request $request) {
        Log::debug($request);
        $coupon_type_id = $request['coupon_type_id'];
        $keyword = $request['keyword'];

        if($coupon_type_id == 0) {
            if($keyword == null || $keyword == '*')
                return Coupon::all();
            else
                return Coupon::where('name', 'like', '%' . $keyword . '%')->get();
        } else {
             if($keyword == null || $keyword == '*')
                return Coupon::where('coupon_type_id', $coupon_type_id)->get();
            else
                return Coupon::where('name', 'like', '%' . $keyword . '%')->where('coupon_type_id', $coupon_type_id)->get();
        }
    
    }

    public function showCouponById($couponId) {
        return Coupon::where('id', $couponId)->get();
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

    public function updateOrCreateCoupon(Request $request)
    {
        Log::debug($request);

        $date1 = new \DateTime($request['expire_date']);
        $date2 = new \DateTime("now");

        Log::debug($date1->diff($date2)->format('%a'));
        $expired = $date1->diff($date2)->format('%a') > 0 ? false : true;

        // 1. process image file
        $image_url = $request['image_url'];
        if($request->hasFile('image_file')) {
            $file =$request->file('image_file');
            $hashName = $file->hashName();
            if($file->getMimeType() == 'image/jpeg')
                 $hashName = substr_replace($hashName, 'jp', -4, -1);

            $image_url = 'imgs/' . $hashName;
            $file->move(base_path('public/imgs'), $hashName);
        }

        // 2. fill the db
        $coupon = Coupon::updateOrCreate(
            ['id' => $request['id']],
            [
                'name' => $request['name'],
                'description' => $request['description'],
                'coupon_type_id' => $request['coupon_type_id'],
                'expire_date' => $request['expire_date'],
                'expired' => $expired,
                'discount_method' => $request['discount_method'],
                'discount_percentage' => $request['discount_percentage'],
                'discount_value' => $request['discount_value'],
                'quantity_initial' => $request['quantity_initial'],
                'quantity_available' => $request['quantity_initial'],
                'for_new_comer' => $for_new_comer,
                'image_url' => $image_url
            ]
        );

        return $coupon;
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
