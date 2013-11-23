<?php

class Sos_Service_SendGrid {

    public static function sendEmails($toEmails, $toNames, $subject, $bodyHtml = '', $bodyText = '', 
            $fromName = '', $fromEmail = '', $to = '', $category = 'SOSbeacon') {
        $response = false;
        try {
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini', 'sendgrid');
            $url = $config->mail->url;
            $user = $config->mail->user;
            $pass = $config->mail->password;
            $fromEmail = $fromEmail ? $fromEmail : $config->mail->from;
            $fromName = $fromName ? $fromName : $config->mail->fromName;
            $json_string = array(
                'to' => $toEmails,
                'category' => $category
            );
            $params = array(
                'api_user' => $user,
                'api_key' => $pass,
                'to' => $to,
                'subject' => $subject,
                'html' => $bodyHtml,
                'text' => $bodyText,
                'from' => $fromEmail,
                'fromname' => $fromName
            );
            if (count($toEmails) > 0) {
                $params['x-smtpapi'] = json_encode($json_string);
            }
            $request = $url . 'api/mail.send.json';
            // Generate curl request
            $session = curl_init($request);
            // Tell curl to use HTTP POST
            curl_setopt($session, CURLOPT_POST, true);
            // Tell curl that this is the body of the POST
            curl_setopt($session, CURLOPT_POSTFIELDS, $params);
            // Tell curl not to return headers, but do return the response
            curl_setopt($session, CURLOPT_HEADER, false);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            // obtain response
            $responseJson = curl_exec($session);
            curl_close($session);
            $response = json_decode($responseJson);
        } catch (Exception $e) {
            $response = new stdClass();
            $response->message = 'error';
            $response->errors = array($e->getMessage());
        }
        return $response;
    }
}