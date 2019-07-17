<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

        $default_address =  ShippingAddress::select('id', 'username', 'mobile', 'tel', 'city', 'street', 'customer_id', 'default_address')->where('default_address', true)->where('customer_id', $userId)->get();

        // found the default address
        if(count($default_address) > 0) return $default_address;
        
        // otherwise, check if there're addresses but no default address, select the first address & set it as default
        $addresses = ShippingAddress::where('mobile', $mobile)->get();

        // if no addresses, return empty
        if(count($addresses) < 1) return [];
      
        // otherwise set the first one as default
        ShippingAddress::where('id', $addresses[0]['id'])->update(['default_address' => 1]);           
        $address = $addresses[0];

        Log::debug('-----------------------ShippingAddress-------------------------');
        Log::debug($address);

        return [0 => $address];
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


    public function updateAddressAsDefault($addressId)
    {
        // get the customer id
        $address = ShippingAddress::find($addressId);
        $customer_id = $address->customer_id;

        // select all default addresses of this customer if there's any
        $addresses = ShippingAddress::where('customer_id', $customer_id)->where('default_address', 1)->get();
        Log::debug($address);
        Log::debug($customer_id);
        Log::debug($addresses);

        // check if this address is default address already
        if($address[0]['default_address'] && count($addresses) < 1) return;
        else {
            DB::transaction(function() use ($customer_id, $address) {
                ShippingAddress::where('customer_id', $customer_id)->where('id', '<>', $address->id)->update(['default_address' => 0]);
                $address->default_address = 1;
                $address->save();
            }, 5);
            
            return response(json_encode(['done']), 200);
        }

        return response(json_encode(['not found']), 404);
    }

    public function updateAddress(Request $request)
    {
        $resp = [
                "status" => 0,
                "address" => ''
        ];

        try {
            DB::transaction(function() use ($request) {
                $userId = $request['user_id'];

                // check if user id is null, if so, find userid by mobile
                if(!$userId) {
                $user = Customer::where('mobile', $request['mobile'])->get();
                    $userId = (json_decode($user, true))[0]['id'];
                }

                $default_address_array = ShippingAddress::where('customer_id', $userId)->where('default_address', 1)->get();
                if($request['default_address'] && count($default_address_array) > 0) {
                    ShippingAddress::where('customer_id', $userId)->update(['default_address' => 0]);
                }

                $default_address = $request['default_address'];
                if(!$request['default_address'] && count($default_address_array) < 1) {
                    $default_address = 1;
                }

                $address = ShippingAddress::updateOrCreate(
                  ['city' => $request['city'], 'street' => $request['street'], 'customer_id' => $userId],
                  [
                      'customer_id' => $userId,
                      'username' => $request['username'],
                      'mobile' => $request['mobile'],
                      'city' => $request['city'],
                      'street' => $request['street'],
                      'tel' => $request['tel'],
                      'default_address' => $default_address, //$request['default_address'],
                  ]
                );      
            }, 5);
        } catch (\Exception $e) {
            $resp = [
                "status" => 0,
                "address" => 'Error Create Address'
            ];
            return response(json_encode($resp), 200)->header('Content-type', 'application/json');
        }

        $resp = [
            "status" => 1,
            "address" => 'Address Created Successfully'
        ];
        return response(json_encode($resp), 200)->header('Content-type', 'application/json');
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
