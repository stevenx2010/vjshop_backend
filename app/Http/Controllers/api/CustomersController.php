<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;

use App\Customer;
use App\ShippingAddress;

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
        // step 1: send SMS to sms_api & get status
        $smsSendStatus = 1;

        // step 2: if succeeded, store the the sms in memcached temporarily 

        // step 3: send SMS sending status back to user
        if($smsSendStatus == SEND_SMS_CODE_SUCCESS) {
            // response result to customer
            return response(json_encode(['status'=> $smsSendStatus, 'mobile'=> $request['mobile']]), 200)->header('Content-type', 'application/json');
        } else {
            return response(json_encode(['status' => 503, 'text' => 'SMS Code Sening Service is Unavailable Temporarily, Pls try again later']), 503)->header('Content-type', 'application/json');
        }
    }

    public function authCustomer($request) {
        $access_token = '';
        $response = [
            'status' => CONFIRM_SMS_CODE_FAILURE,
            'mobile'=> $request['mobile'],
            'access_token' => '',
            'address_check' => 0
        ];
        // step 1: Get sms code from memcached to check if code matches & expired
        /******************** code here */
        $smsCode = '123456';

        // step 2: verify if the sms code received matches with the one in Memcached
        if(trim($request['sms_code']) === $smsCode) {
            // step 3: sms code matches then 
            // step 3-1: remove the sms code from Memcached
            /************************ code here */

            // step 3-2:  check if he's or she's a new comer
            $user = Customer::select('id')->where('mobile', trim($request['mobile']))->get();
  //          Log::debug(json_decode($user[0], true));
            Log::debug(count($user));
            Log::debug(sizeof($user));

            // step 3-3: if it's new user then generate access token & save it in db
            if(count($user) == 0) {
                // generate access token
                $access_token = $this->genToken($request['mobile']);
                $response['access_token'] = $access_token;

                // then insert it into db
                $newUser = new Customer;
                $newUser->username = ''; // Currently have no username, will update after creating shipping address
                $newUser->mobile = $request['mobile'];
                $newUser->access_token = '' . $access_token;
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
                } 

                // check if user info (name, shipping address) is valid
                $username = Customer::select('username')->where('mobile', $request['mobile'])->get();
                if((json_decode($username[0], true))['username'] == '') {
                    $response['address_check'] = SHIPPING_ADDRESS_CHECK_FAILURE;
                } else {
                    $response['address_check'] = SHIPPING_ADDRESS_CHECK_SUCCESS;
                }

            }

            $response['status'] = CONFIRM_SMS_CODE_SUCCESS;
        } 

        //return response('You are not authorized to access the resources! Incorrect SMS verification code', 403)
                    //->content(json_encode($response));
        return response(json_encode($response), 200)->header('Content-type', 'application/json');
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
       
    public function createShippingAddress($request) {
        // get the id of the user with this
        $userIdArray = Customer::select('id')->where('mobile', $request['mobile'])->get();
        $userId = (json_decode($userIdArray[0], true))['id'];
        $user = Customer::find($userId);
        $user->username = $request['username'];
        $user->save();

        $address = ShippingAddress::updateOrCreate(
            ['city' => $request['city'], 'street' => $request['street']],
            [
                'customer_id' => $userId,
                'city' => $request['city'],
                'street' => $request['street'],
                'tel' => $request['tel'],
                'default_address' => $request['default_address']
            ]
        );


          return response('{"status": 1}', 200)->header('Content-type', 'application/json');
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
