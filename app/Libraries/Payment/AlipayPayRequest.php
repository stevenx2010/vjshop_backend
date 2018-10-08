<?php
namespace App\Libraries\Payment;

use App\Libraries\Payment\AlipayInterfaces;

use Illuminate\Support\Facades\Log;

class AlipayPayRequest {
	private $app_id;
	private $method;
	private $format = 'json';
	private $charset;
	private $sign_type ='RSA2';
	private $sign;
	private $timestamp;
	private $version ='1.0';
	private $notify_url;
	private $biz_content;

	private $alipay_public_key;

	private $my_public_key;
	private $my_private_key;

	private $params = [];

	public function __construct($order_info, $charset='utf-8', $method = AlipayInterfaces::APP_PAY)
	{
		$this->app_id = trim(env('ALIPAY_APP_ID', ''));
		$this->notify_url = trim(env('ALIPAY_NOTIFY_URL',''));
		$this->method = $method;
		$this->charset = $charset;
		$this->biz_content = $order_info;
		$this->timestamp = date("Y-m-d H:i:s");
		$this->timeout_express = '15m';

		$pk_in_file = file_get_contents(__DIR__  . '/../Keys/private_key_2048.txt');

		$pk_in_file = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($pk_in_file, 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";

		Log::debug($pk_in_file);
		$this->my_private_key = openssl_pkey_get_private($pk_in_file);

	}

	public function getRequest() {
		// Step 1: fill in parameters
		$this->fillinParameters();
		Log::debug($this->params);

		// Step 2: genenrate request contents
		Log::debug('--------------------------------');

		$signingContent = $this->prepareSigningContent();
		Log::debug($signingContent);

		// Step 3: digital sign the request string
		$my_priv_key_id = openssl_pkey_get_private($this->my_private_key);

		$signaure = '';
		$result = openssl_sign($signingContent, $signature, $my_priv_key_id, OPENSSL_ALGO_SHA256);

		Log::debug('----------test---------');
		$signed = '';
		$test = 'a=123';
		openssl_sign($test, $signed, $my_priv_key_id, OPENSSL_ALGO_SHA256);		
		Log::debug(base64_encode($signed));

		openssl_pkey_free($my_priv_key_id);

		if($result) {
			$this->sign = base64_encode($signature);

			$request_string = $this->urlEncodingRequestContent();
			 //Step 5: replace '+' to %20%20
			$request_string = str_replace('+', '%20', $request_string);

			// Step 4: encoding the request content into charset
			$request_string  .=  '&sign=' . urlencode($this->sign);

			Log::debug($request_string);

			return $request_string;
		}

		return '';
	}

	private function fillinParameters() {
		$this->params['app_id'] = $this->app_id;
		$this->params['biz_content'] = $this->biz_content;
		$this->params['format'] = $this->format;
		$this->params['charset'] = $this->charset;
		$this->params['method'] = $this->method;
		$this->params['notify_url'] = $this->notify_url;
		$this->params['sign_type'] = $this->sign_type;
		$this->params['timestamp'] = $this->timestamp;
		$this->params['version'] = $this->version;
	}

	private function prepareSigningContent() {
		
		$isStart = true;
		
		ksort($this->params);

		$requestString = '';
		foreach ($this->params as $key => $value) {

			if($value == '' || $key =='sign') continue;
			if($isStart) {
				$requestString = "$key" . '=' . mb_convert_encoding($value, $this->charset);
				$isStart = false;
			} else {
				$requestString = $requestString . '&' . "$key" . '=' . mb_convert_encoding($value, $this->charset);
			}
		}

		unset($key, $value);

		return $requestString;
	}

	private function urlEncodingRequestContent() {
		$request_string = '';
		$isStart = true;
		ksort($this->params);
		foreach ($this->params as $key => $value) {
			//$value = urlencode(mb_convert_encoding($value, $this->charset));
			$value = urlencode($value);
			if($isStart) {
				$request_string = "$key" . '=' . $value;
				$isStart = false;
			} else {
				$request_string .= '&' . "$key" . '=' . $value;
			}
		}

		Log::debug($request_string);
		return $request_string;
	}
}