<?php
namespace App\Libraries\Payment;

use Illuminate\Support\Facades\Log;

class WechatPayRequest {
	private $appid;
	private $partnerid;
	private $prepayid;
	private $package = 'Sign=WXPay';
	private $noncestr;
	private $timestamp;
	private $sign;

	private $key;

	private $request= [];

	public function __construct($prepayId, $orderId) {
		$this->appid = env('WECHAT_PAY_APP_ID', '');
		$this->partnerid = env('WECHAT_MCH_ID', '');
		$this->key = env('WECHAT_APP_KEY', '');
		$this->prepayid = $prepayId;
		$this->noncestr = md5(mt_rand(10000, 99999) . $orderId . microtime());
		$this->timestamp = (explode(' ', microtime()))[1];
	}

	private function fillRequest() {
		$request = [];

		$request['appid'] = $this->appid;
		$request['partnerid'] = $this->partnerid;
		$request['prepayid'] = $this->prepayid;
		$request['package'] = $this->package;
		$request['noncestr'] = $this->noncestr;
		$request['timestamp'] = $this->timestamp;
		$request['sign'] = $this->sign;

		return $request;
	}

	private function genSign($request) {
		$tempString = '';
		$isStart = true;

		ksort($request);
		foreach ($request as $key => $value) {
			if($value && $key != 'sign') {
				if($isStart) {
					$tempString = $key . '=' . $value;
					$isStart = false;
				} else {
					$tempString .= '&' . $key . '=' . $value;
				}
			} 
		}

		$tempString = $tempString .  '&key=' . $this->key;
		Log::debug($tempString);

		$request['sign'] = strtoupper(md5($tempString));
		return $request;
	}

	public function getWechatPayRequest() {
		$request = $this->fillRequest();
		$signedReq = $this->genSign($request);

		return $signedReq;
	}
}