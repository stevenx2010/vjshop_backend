<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;

use App\ShippingAddress;
use App\Customer;

class ShippingAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
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
    public function show($mobile)
    {
        $users =  Customer::where('mobile', $mobile)->get();
        return Customer::find((json_decode($users[0], true))['id'])->addresses()->get();
    }

    public function showDefault($mobile)
    {
        Log::debug($mobile);
        return ShippingAddress::select('id', 'username', 'mobile', 'tel', 'city', 'street', 'customer_id', 'default_address')->where('default_address', true)->where('mobile', $mobile)->get();
    }

   public function showUserId($mobile)
    {
        $userId = -1;
        $addresses = 0;

        $users =  Customer::where('mobile', $mobile)->get();
        Log::debug(json_decode(gettype($users), true));

        if(!empty(json_decode($users, true))) { 
            $userId = (json_decode($users[0], true))['id'];
            $addresses = Customer::find($userId)->addresses()->get();
            return json_encode(['user_id' => $userId, 'has_number_of_addresses' => count($addresses)]);
        } 

        return json_encode(['user_id' => $userId, 'has_number_of_address' => $addresses]);
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
