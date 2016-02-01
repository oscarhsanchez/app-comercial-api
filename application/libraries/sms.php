<?php

class sms {
	
	private $sms; 

	public function __construct()
	{
		require_once 'SMSSend.inc';

		$this->sms=new smsItem;
		$this->sms->setAccount(SMS_ACCOUNT);
		$this->sms->setPwd(SMS_PASS);  

	}

	public function sendSms($phone, $text) {		
		$this->sms->setTo($phone);
		$this->sms->setText($text);
		$this->sms->setFrom("WIKKING");
		$resultado = $this->sms->Send();

		return $resultado;
	}

}

?>