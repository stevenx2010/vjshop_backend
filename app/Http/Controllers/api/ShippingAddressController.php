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
        $user = Customer::where('mobile', $mobile)->get();
        $userId = (json_decode($user, true))[0]['id'];

        return ShippingAddress::select('id', 'username', 'mobile', 'tel', 'city', 'street', 'customer_id', 'default_address')->where('default_address', true)->where('customer_id', $userId)->get();
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

    public function showShippingAddressById($addressId) 
    {
        return ShippingAddress::where('id', $addressId)->get();
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
        //check if the address is default_address
        $address = ShippingAddress::find($id);
        Log::debug($address);
        $default_address = (json_decode($address, true))['default_address'];
        $userId = (json_decode($address, true))['customer_id']; 

        if($default_address) {
            Log::debug($userId);
            ShippingAddress::destroy($id);
            $user = Customer::find($userId);
            $addresses = $user->addresses()->get();
            $address_array = json_decode($addresses, true);
            $address_id = $address_array[0]['id'];

            $first_found_address = ShippingAddress::find($address_id);
            $first_found_address->default_address = 1;
            $first_found_address->save();

        } else {
            ShippingAddress::destroy($id);
        }
    }
}
