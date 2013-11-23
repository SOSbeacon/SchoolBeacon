<?php

class Sos_Model_Mail extends Zend_Mail
{
	var $to;
	var $from;
	var $subject;
	var $bodyText;

	function setTo($value){
		$this->to = $value;
	}
	
	function getTo(){
		return $this->to;
	}

	function setFrom($value){
		$this->from = $value;
	}
	
	function getFrom(){
		return $this->from;
	}
	
	function setSubject($value){
		$this->subject = $value;
	}
	
	function getSubject(){
		return $this->subject;
	}
	
	function setBodyText($value){
		$this->bodyText = $value;
	}
	
	function getBodyText(){
		return $this->bodyText;
	}
		
	function Send() {
		$mail = new Zend_Mail();

		$mail->setBodyText($this->bodyText);
	   
	   	$mail->setFrom($this->from);
	   
	   	$mail->addTo($this->to);
	   
	   	$mail->setSubject($this->subject);
	      
	   	$mail->send();
	}
}
?>
