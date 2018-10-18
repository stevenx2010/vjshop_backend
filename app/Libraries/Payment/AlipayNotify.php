<?php
namespace App\Libraries\Payment;


use Illuminate\Support\Facades\Log;

class AlipayNotify {
	private $request = [];
	private $ali_pub_key;

	public function __construct($request) {
		$this->request = $request->all();

		// Get Alipay's public key
		$pk_in_file = file_get_contents(__DIR__  . '/../Keys/alipay_public_key.txt');
		$pk_in_file = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($pk_in_file, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
		$this->ali_pub_key = openssl_pkey_get_public($pk_in_file);
	}

	private function prepareSigningContent($request) {
		Log::debug($request);
		$isStart = true;
		
		ksort($request);

		$requestString = '';
		foreach ($request as $key => $value) {

			if($value == '' || $key =='sign' || $key == 'sign_type') continue;
			if($isStart) {
				$requestString = "$key" . '=' . urldecode($value);
				$isStart = false;
			} else {
				$requestString = $requestString . '&' . "$key" . '=' . urldecode($value);
			}
		}

		unset($key, $value);

		return $requestString;
	}

	public function isSignVerified() {
		// step 1: Prepare Signing cotent
		$signingContent = $this->prepareSigningContent($this->request);
		Log::debug($signingContent);

		// step 2: get Alipay public key id
		$ali_pub_key_id = openssl_pkey_get_public($this->ali_pub_key);

		// step 3: decode base64 sign
		$signature = base64_decode($this->request['sign']);

		// step 4: verify the signature
		$result = openssl_verify($signingContent, $signature, $ali_pub_key_id, OPENSSL_ALGO_SHA256);

		return $result;
	}
}