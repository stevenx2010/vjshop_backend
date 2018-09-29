<?php
namespace App\Libraries\Payment;

use \XMLWriter;
use Illuminate\Support\Facades\Log;

class WechatUnifiedOrderRequest {
	public $appid;
	public $mch_id;
	public $device_info = 'WEB';
	public $nonce_str;
	public $sign;
	public $sign_type = 'MD5';
	public $body = '稳卓商城';
	public $detail;
	public $attach;
	public $out_trade_no;
	public $fee_type = 'CNY';
	public $total_fee;
	public $spbill_create_ip;
	public $time_start;
	public $time_expire;
	public $goods_tag;
	public $notify_url;
	public $trade_type = 'APP';
	public $limit_pay;
	public $scene_info;

	public $order = [];

	private function fillOrder() {
		$order = [];

		$order['appid'] = $this->appid;
		$order['mch_id'] = $this->mch_id;
		$order['device_info'] = $this->device_info;
		$order['nonce_str'] = $this->nonce_str;
		$order['sign'] = $this->sign;
		$order['sign_type'] = $this->sign_type;
		$order['body'] = $this->body;
		$order['detail'] = $this->detail;
		$order['attach'] = $this->attach;
		$order['out_trade_no'] = $this->out_trade_no;
		$order['fee_type'] = $this->fee_type;
		$order['total_fee'] = $this->total_fee;
		$order['spbill_create_ip'] = $this->spbill_create_ip;
		$order['time_start'] = $this->time_start;
		$order['time_expire'] = $this->time_expire;
		$order['goods_tag'] = $this->goods_tag;
		$order['notify_url'] = $this->notify_url;
		$order['trade_type'] = $this->trade_type;
		$order['limit_pay'] = $this->limit_pay;
		$order['scene_info'] = $this->scene_info;

		return $order;
	}

	public function getPreOrderRequest() {
		$this->order = [];
		$this->order = $this->genSign();

		$xw = new XMLWriter();
		$xw->openMemory();
		$xw->startElement('xml');

		foreach ($this->order as $key => $value) {
			if($value && $value != '' && $key != 'sign') {
				$xw->startElement($key);
				$xw->text($value);
				$xw->endElement();
			}
		}

		$xw->startElement('sign');
		$xw->text($this->order['sign']);
		$xw->endElement();

		$xw->endElement();

		return $xw->outputMemory();
	}

	private function genSign() {
		$order = [];
		$order = $this->fillOrder();

		ksort($order);
		$tempString = '';
		$isStart = true;
		foreach($this->order as $key => $value) {
			if($value && $value != '' && $key != 'sign') {
				if($isStart) {
					$tempString = "$key=$value";
					$isStart = false;
				} else {
					$tempString .= "&$key=$value";
				}
			}
		}

		$tempString = $tempString . '&key=' . env('WECHAT_APP_KEY');
		$order['sign'] = strtoupper(md5($tempString));

		return $order;
	}
}