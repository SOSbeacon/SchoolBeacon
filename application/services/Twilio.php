
<?php
require_once "twilio_sdk.php";
define("API_VERSION", "2010-04-01");
define("ACCOUNT_ID", "AC6825c3d1d47edc3cabf866d484a1f356");
define("AUTH_TOKEN", "cc4ef8ad2e7cddc3d27e7e9f29451e18");

class Sos_Service_Twilio {

    // Twilio REST API version 
    public static function sendSMS($from, $to, $body, $userPhone = '') {
        // CNC development test only, to number start with "840000"
        $isCncTest = false;
        if (substr($to, 0, 6) == '840000') {
            $isCncTest = true;
        }
        // Instantiate a new Twilio Rest Client 
        $client = new TwilioRestClient(ACCOUNT_ID, AUTH_TOKEN);
        $data = array(
            "From" => $from, // Outgoing Caller ID
            "To" => $to, // The phone number you wish to dial
            "Body" => $body
        );

        $response = $client->request("/" . API_VERSION . "/Accounts/" . ACCOUNT_ID . "/SMS/Messages", "POST", $data);
        // log
        $smslogMapper = new Sos_Model_SmslogMapper();
        if ($userPhone) {
            $from = $userPhone; // set from user phone, if not, use SOS phone number
        }
        $smslog = new Sos_Model_Smslog();
        $smslog->setFrom($from);
        $smslog->setTo($to);
        $smslog->setMessage($body);
        $smslog->setCreatedDate(date("Y-m-d H:i:s"));
        $smslog->setStatus(1);
        // check response for success or error
        if ($response->IsError && !$isCncTest) {
            $smslog->setStatus(0);
            $smslog->save();
            throw new Zend_Exception($response->ErrorMessage);
        }
        $smslog->save();
    }

}