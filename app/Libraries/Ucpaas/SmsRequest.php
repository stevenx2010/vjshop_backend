<?php

namespace App\Libraries\Ucpaas;

class SmsRequest 
{
	private $app_id;
	private $sid;
	private $auth_token;
	private $template_id;
	private $param;
	private $mobile;
	private $uid;

	public function __construct($mobile, $param) {
		$this->app_id = env('APP_ID', '');
		$this->sid = env('SID', '');
		$this->auth_token = env('AUTH_TOKEN', '');
		$this->template_id = env('TEMPLATE_ID', '');
		$this->param = $param;
		$this->mobile = $mobile;
		$this->uid = '';
	}

	public function send() {
		$options['accountsid'] = $this->sid;
		$options['token'] = $this->auth_token;

		$ucpaas = new Ucpaas($options);

		return  $ucpaas->SendSms($this->app_id, $this->template_id, $this->param, $this->mobile, $this->uid);
	}
}