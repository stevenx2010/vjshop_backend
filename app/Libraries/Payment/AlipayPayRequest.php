<?php
namespace App\Libraries\Payment;

use App\Libraries\Payment\AlipayInterfaces;

class AlipayPayRequest {
	private $app_id;
	private $method;
	private $format = 'JSON';
	private $charset = 'utf-8';
	private $sign_type ='RSA2';
	private $sign;
	private $timestamp;
	private $version ='1.0';
	private $notify_url;
	private $biz_content;

	private $alipay_public_key;

	private $my_public_key;
	private $my_private_key;

	public function __construct($order_info, $method = AlipayInterfaces::APP_PAY)
	{
		$this->app_id = env('ALIPAY_APP_ID', '');
		$this->notify_url = env('ALIPAY_NOTIFY_URL','');
		$this->method = $method;
		$this->biz_content = $order_info;
		$this->timestamp = date("Y-m-d H:i:s");

		$this->alipay_public_key = "-----BEGIN PUBLIC KEY-----\n" . wordwrap(env('ALIPAY_PUBLIC_KEY', ''), 64, "\n", true) . "\n-----END PUBLIC KEY-----";

		$this->my_public_key = "-----BEGIN PUBLIC KEY-----\n" . wordwrap(env('MY_PUBLIC_KEY', ''), 64, "\n", true) . "\n-----END PUBLIC KEY-----";

		$this->my_private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap(env('MY_PRIVATE_KEY', ''), 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";

	}

	public function getRequest() {
		$request_string = 'app_id=' . $this->app_id . '&';
		$request_string .= 'biz_content=' . $this->biz_content . '&';
		$request_string .= 'charset=' . $this->charset . '&';
		$request_string .= 'format=' . $this->format . '&';
		$request_string .= 'method=' . $this->method . '&';
		$request_string .= 'notify_url=' . $this->notify_url . '&';
		$request_string .= 'sign_type=' . $this->sign_type . '&';
		$request_string .= 'timestamp=' . $this->timestamp . '&';
		$request_string .= 'version=' . $this->version;

		$my_priv_key_id = openssl_pkey_get_private($this->my_private_key);
		$signaure = '';

		$result = openssl_sign($request_string, $signature, $my_priv_key_id, OPENSSL_ALGO_SHA256);

		if($result) {
			$this->sign = base64_encode($signature);
			$request_string = $request_string . '&sign=' . $this->sign;

			return $request_string;
		}
		
		return '';
	}
}