<?php
namespace App\Libraries\Payment;

use \XMLReader;

use Illuminate\Support\Facades\Log;

class WechatNotify {
	private $request = [];
	private $requestInXml;
	private $key;

	public function __construct($requestInXml) {
		$this->key = env('WECHAT_APP_KEY', '');
		$this->requestInXml = $requestInXml;

		$this->parseRequestInXml($this->requestInXml);		
	}

	private function parseRequestInXml($requestInXml) {
		$reader = new XMLReader();
		$reader->xml($requestInXml);
		while($reader->next()) {
			$reader->read();
			if($reader->nodeType == XMLReader::ELEMENT) {
				$this->request[$reader->name] = $reader->readString();
			}
		}
	}	

	public function getRequest() {
		return $this->request;
	}

	private function genSignature($request) {
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

		$signature = strtoupper(md5($tempString));

		Log::debug($signature);

		return $signature;
	}

	public function isSignatureCorrect() {
		$sign = $this->genSignature($this->request);

		return ($sign == $this->request['sign']);
	}
}