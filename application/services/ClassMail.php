<?php
require_once('class.phpmailer.php');

class Sos_Service_ClassMail {
	private $subject; 
	private $addressTo;
	private $addressName;
	private $altBody;
	private $body;
	private $attachment;
		
	function setSubject($value) {
		$this->subject = $value;
	}

	function getSubject() {
		return $this->subject;
	}
		
	function setAddressTo($value) {
		$this->addressTo = $value;
	}

	function getAddressTo() {
		return $this->addressTo;
	}
		
	function setAddressName($value) {
		$this->addressName = $value;
	}

	function getAddressName() {
		return $this->addressName;
	}
		
	function setAltBody($value) {
		$this->altBody = $value;
	}

	function getAltBody() {
		return $this->altBody;
	}
		
	function setBody($value) {
//		$body = file_get_contents('contents.html');
//		$body = eregi_replace("[\]",'',$body);
		$this->body = $value;
	}

	function getBody() {
		return $this->body;
	}
			
	function setAttachment($value) {
		$this->attachment = $value;
	}

	function getAttachment() {
		return $this->attachment;
	}
		
	
	function sendMail($fromEmail = '', $fromName = '', $isHtml = false) {
		$config 		  = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini', 'gmail');
		$username		  = $config->resources->email->account;
                if (!$fromEmail) {
                    $fromEmail = $username;
                }
		
		$senderName		  = $config->resources->email->name;	
                if (!$fromName) {
                    $fromName = $senderName;
                }
                
                $password		  = $config->resources->email->password;
                
		$mail             = new PHPMailer();
                if ($isHtml) {
                    $mail->IsHTML(true);
                }
                
		$mail->IsSMTP(); 							// telling the class to use SMTP
		$mail->Host       = $config->resources->email->server;	// SMTP server
		$mail->SMTPAuth   = true;                  	// enable SMTP authentication
		$mail->SMTPSecure = 'ssl';//$config->resources->email->SMTPSecure;                 	// sets the prefix to the servier
		$mail->Port       = $config->resources->email->port;                   	// set the SMTP port for the GMAIL server
		$mail->Username   = $username;  		   	// GMAIL username
		$mail->Password   = $password;             	// GMAIL password
		
		$mail->ValidateAddress($this->getAddressTo());
		
		$mail->SetFrom($fromEmail, $fromName);
		
		$mail->AddAddress($this->getAddressTo(), $this->getAddressName());
		$mail->Subject    = $this->getSubject();
		$mail->AltBody    = $this->getAltBody();
		$mail->MsgHTML($this->getBody());
		//$mail->AddAttachment("images/test.txt");      // attachment
		if (!empty($this->attachment)) {
                    $mail->AddAttachment($this->getAttachment());
                }

		$logger = Sos_Service_Logger::getLogger();
                $logger->log("SENDING EMAIL....", Zend_Log::INFO);
                $logger->log("-- TO           :" . $this->getAddressTo(), Zend_Log::INFO);
                $logger->log("-- Subject      :" . $this->getSubject(), Zend_Log::INFO);
                $logger->log("-- Body         :" . $this->getAltBody(), Zend_Log::INFO);
                $logger->log("-- HTML Body    :" . $this->getBody(), Zend_Log::INFO);
	    		  
		if(!$mail->Send()) {
			throw new Exception("Mailer Error: " . $mail->ErrorInfo);
		}
	}
}
?>
