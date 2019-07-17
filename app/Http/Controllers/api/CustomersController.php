<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use \Cache;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;

use App\Customer;
use App\CustomerProfile;
use App\ShippingAddress;
use App\Coupon;

use App\Libraries\Ucpaas\SmsRequest;
use App\Libraries\Ucpaas\SmsResponse;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function customerLogin(Request $request) {
      
        Log::debug($request);
        Log::debug($request->header('authorization'));
        $API_TOKEN = 'asdfasdf';

        // Check api_token
        $api_token = $request->header('authorization');
        if($api_token != $API_TOKEN) {
    //        return response('You are not authorized to access the resources!', 403);
        }



        $req = $request->json()->all();
        Log::debug($req);

        switch($req['command']) {
            case GET_SMS_CODE:
                return $this->sendSMSCode($req);
                break;
            case CONFIRM_SMS_CODE: 
                return $this->authCustomer($req);
                break;
            case CREATE_SHIPPING_ADDRESS:
                return $this->createShippingAddress($req);
                break;

        }
    }

    // Helpers
    
    public function sendSMSCode($request) {
      // step 0: Check if he/she is a new user
      $user = Customer::select('id')->where('mobile', trim($request['mobile']))->get();
      $isNewUser = false;

      if($user && count($user) == 0) {
        $isNewUser = true;
      }

      // step 1: Generate a random number, then send SMS to sms_api & get status
      $rand_number = mt_rand(100000, 999999);
      /********************for debug ********** remove it afterwards ***********/
      $rand_number = 123456;
      $smsRequest = new SmsRequest($request['mobile'], $rand_number);

      $smsResponse = new SmsResponse();
      $smsResponse = $smsRequest->send();

      Log::debug(json_decode($smsResponse, true));
    
      $resp =json_decode($smsResponse, true);

      if($resp['code'] ==='000000') $smsSendStatus = SEND_SMS_CODE_SUCCESS;
      else $smsSendStatus = SEND_SMS_CODE_FAILURE;

      // step 3: send SMS sending status back to user
      if($smsSendStatus == SEND_SMS_CODE_SUCCESS) {
          // store the the sms in memcached temporarily 
          Cache::put($request['mobile'], $rand_number, 1);  // expires after 1 minute;
          // response result to customer
          return response(json_encode([
            'status'=> $smsSendStatus, 
            'mobile'=> $request['mobile'],
            'newuser' => $isNewUser
          ]), 200)->header('Content-type', 'application/json');
      } else {
          return response(json_encode([
            'status' => $resp['code'], 
            'text' => $resp['msg'],
            'newuser' => $isNewUser
          ]), 200)->header('Content-type', 'application/json');
      }
    }

    public function authCustomer($request) {
        Log::debug($request);
        $access_token = '';
        $response = [
            'status' => CONFIRM_SMS_CODE_FAILURE,
            'text' => '',
            'mobile'=> $request['mobile'],
            'access_token' => '',
            'address_check' => 0,
            'new_user' => false
        ];

        // step 1: Get sms code from memcached to check if code matches & expired
        /******************** code here */
        $stored_sms_code = Cache::get($request['mobile']);
        Log::debug($stored_sms_code);

        if(!$stored_sms_code) {
          $response['text'] = 'SMS Code expired';

          return response(json_encode($response), 404)->header('Content-type', 'application/json');
        } 

        // step 2: verify if the sms code received matches with the one in Memcached
        if(trim($request['sms_code']) == $stored_sms_code) {
            // step 3: sms code matches then 
            // step 3-1: remove the sms code from Memcached
            /************************ code here */
            Cache::forget($request['mobile']);

            // step 3-2:  check if he's or she's a new comer
            $user = Customer::select('id')->where('mobile', trim($request['mobile']))->get();
  //          Log::debug(json_decode($user[0], true));
            Log::debug(count($user));
            Log::debug(sizeof($user));

            // step 3-3: if it's new user then generate access token & save it in db
            if($user && count($user) == 0) {
                // generate access token
                $access_token = $this->genToken($request['mobile']);
                $response['access_token'] = $access_token;

                // then insert it into db
                $newUser = new Customer;
                //  $newUser->username = ''; // Currently have no username, will update after creating shipping address
                $newUser->mobile = $request['mobile'];
                $newUser->access_token = '' . $access_token;  // convert to string
                $newUser->new_user =true;
                //$newUser->save();

                // store user profile
                $user_profile = new CustomerProfile;
                $user_profile->customer_id = $newUser->id;
                $user_profile->register_location = $request['location'];
                //$user_profile->save();


                DB::transaction(function() use ($newUser, $user_profile) {
                  $newUser->save();
                  $user_profile->customer_id = $newUser->id;
                  $user_profile->save();
                }, 5);

                $response['new_user'] = true;
                $response['text'] = 'New user: logged in, user info saved';

                // associate coupons for newuser to this user
                // step 1: check unlimited coupons
                $coupons = Coupon::where('for_new_comer', 1)->where('expired', 0)->where('quantity_available', -1)->get();
                if($coupons && count($coupons) > 0) {
                  foreach ($coupons as $coupon) {
                    $coupon_id = $coupon['id'];
                    $newUser->coupons()->attach(
                      [$coupon_id => ['quantity' => 1]]
                    );
                  }
                }

                // step 2: check coupon whose quantity > 0
                $coupons = Coupon::where('for_new_comer', 1)->where('expired', 0)->Where('quantity_available', '>', 0)->get();
                if($coupons && count($coupons) > 0) {
                  foreach ($coupons as $coupon) {
                    $coupon_id = $coupon['id'];
                    $newUser->coupons()->attach(
                      [$coupon_id => ['quantity' => 1]]
                    );

                    // decrease this coupon's quantity
                    $coupon_obj = Coupon::find($coupon_id);
                    $coupon_obj->quantity_available -= 1;
                    $coupon_obj->save();
                  }
                }

                // set the user as old user
                $newUser->new_user = false;
                $newUser->save();
            } else {
                // old user, check if access token expired
                if(!($this->checkToken($request['mobile']))) { 
                    // access token expired, then regenerate it
                    $access_token = $this->genToken($request['mobile']);

                    // update it in db
                    $userId = (json_decode($user[0], true))['id'];
                    $oldUser = Customer::find($userId);
                    $oldUser->access_token = $access_token;
                    $oldUser->save();

                    $response['text'] = 'Old user: access token updated';

                } 

                // check if user info (name, shipping address) is valid/has created valid shipping address
                $username = ShippingAddress::select('username')->where('mobile', $request['mobile'])->get();
                $username_array = json_decode($username);
                Log::debug($username_array);
                if(sizeof($username_array) < 1) {
                    $response['address_check'] = SHIPPING_ADDRESS_CHECK_FAILURE;
                    $response['text'] ="invalid shipping address";
                } else {
                    $response['address_check'] = SHIPPING_ADDRESS_CHECK_SUCCESS;
                    $response['text'] = 'valid shipping address';
                }

            }

            $response['status'] = CONFIRM_SMS_CODE_SUCCESS;

            Log::debug('xxxxxxxxxxxxxxxxx');
            Log::debug($response);

            return response(json_encode($response), 200)->header('Content-type', 'application/json');
          }

        $response['text'] ='Forbidden: not authorized to access';
        return response(json_encode($response), 403)->header('Content-type', 'application/json');
    }

    public function genToken($mobile) {
        $signer = new Sha256();
        
        $access_token = (new Builder())->setIssuer('http://venjong.com')
                                       ->setAudience($mobile)
                                       ->setIssuedAt(time())
                                       ->setExpiration(time() + 30 * 24 * 3600)
                                       ->sign($signer, 'Vj20182018')
                                       ->getToken();   

        return $access_token;    
    }

    public function checkToken($mobile) {
        $access_token = (json_decode((Customer::select('access_token')->where('mobile', $mobile)->get())[0], true))['access_token'];

        $data = new ValidationData();
        $data->setIssuer('http://venjong.com');
        $data->setAudience($mobile);

        $token = (new Parser())->parse((string)$access_token);

        return $token->validate($data);
    }
       
    public function createShippingAddress(Request $request) {
      $userId = $request['user_id'];

      // check if user id is null, if so, find userid by mobile
      if(!$userId) {
        $user = Customer::where('mobile', $request['mobile'])->get();
        $userId = (json_decode($user, true))[0]['id'];
      }

      // select default address id
      $default_address_id = ShippingAddress::where('default_address', 1)->get();
      $default_address = ($default_address_id[0]['id'] == $userId) ? 1 : 0;


      //If this address is default address, then clear all DEFAULT mark of the records in db
      if($request['default_address']){
        ShippingAddress::where('customer_id', $request['user_id'])->update(['default_address' => 0]);
      } 

      Log::debug($request);
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

      $resp = [
        "status" => 1,
        "address" => json_decode($address, true)
      ];

      Log::debug($address);
      return response(json_encode($resp), 200)->header('Content-type', 'application/json');
    }



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
        //
    }

    public function showProfile(Request $request)
    {
      \Log::debug($request);
      $mobile = $request['mobile'];
      $query_by_date = $request['query_by_date'];
      $date1 = $request['date1'];
      $date2 = $request['date2'];

      if(!$mobile && !$query_by_date) {
        return DB::table('customers')->join('customer_profiles', 'customers.id', '=', 'customer_profiles.customer_id')
                                     ->select('customers.id', 'customers.mobile', 'customers.created_at as register_date', 'customer_profiles.register_location')
                                     ->get();
      } else if($mobile && !$query_by_date) {
        return DB::table('customers')->join('customer_profiles', 'customers.id', '=', 'customer_profiles.customer_id')
                                     ->select('customers.id', 'customers.mobile', 'customers.created_at as register_date', 'customer_profiles.register_location')
                                     ->where('customers.mobile', $mobile)
                                     ->get();  
      } else if($query_by_date && !$mobile) {
        return DB::table('customers')->join('customer_profiles', 'customers.id', '=', 'customer_profiles.customer_id')
                                     ->select('customers.id', 'customers.mobile', 'customers.created_at as register_date', 'customer_profiles.register_location')
                                     ->where('customers.created_at', '>=', $date1)
                                     ->where('customers.created_at', '<=', $date2)
                                     ->get();  
      } else if($query_by_date && $mobile) {
        return DB::table('customers')->join('customer_profiles', 'customers.id', '=', 'customer_profiles.customer_id')
                                     ->select('customers.id', 'customers.mobile', 'customers.created_at as register_date', 'customer_profiles.register_location')
                                     ->where('customers.created_at', '>=', $date1)
                                     ->where('customers.created_at', '<=', $date2)
                                     ->where('customers.mobile', $mobile)
                                     ->get();  
      }

      return response(json_encode([]), 200);
    }

    public function showExist($mobile) 
    {
        $user = Customer::select('id')->where('mobile', $mobile)->get();

        if(count($user) > 0) {
          return response(json_encode(['status' => true]), 200);
        } else {
          return response(json_encode(['status' => false]), 200);
        }
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
