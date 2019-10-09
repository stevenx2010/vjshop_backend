<?php
namespace App\Libraries\Payment;

use \XMLReader;

use Illuminate\Support\Facades\Log;

class WechatPay {
	private $request;
	private $request_url;
	private $response = [];
	private $responseRaw;

	public function __construct($request, $request_url) {
		$this->request = $request;
		$this->request_url = $request_url;
	}

	public function sendUnifiedOrderRequest() {
		Log::debug($this->request_url);
		Log::debug($this->request);

		$ch = curl_init($this->request_url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);

		if(curl_errno($ch)) {
			Log::debug(curl_errno($ch));
			Log::debug(curl_error($ch));
		} else {
			$result = curl_exec($ch);
			Log::debug($result);
			
			$this->responseRaw = $result;

			$this->parseResponse($result);
			curl_close($ch);
		}	

		if($this->response['return_code'] == 'FAIL' || $this->response['result_code'] == 'FAIL') {
			//Log::debug($this->response['err_code']);
			Log::debug($this->response['return_msg']);
			return false;
		}
		else 
			return $this->response['prepay_id'] ? true : false;	
	}

	private function parseResponse($resp) {
		$reader = new XMLReader();
		$reader->xml($resp);
		while($reader->next()) {
			$reader->read();
			if($reader->nodeType == XMLReader::ELEMENT) {
				$this->response[$reader->name] = $reader->readString();
			}
		}
	}

	public function getResponse() {
		return $this->response;
	}

	public function getPrepayId() {
		return $this->response['prepay_id'];
	}

	public function getRequest() {
		return $this->request;
	}

	public function getResponseRaw() {
		return $this->responseRaw;
	}
}