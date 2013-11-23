<?php

class Sos_Service_ShortenUrl {
    
    public static $shortenUrlConfig;
    public static $shortenLogin;
    public static $shortenKey;
    
    public static function shortenYourls($longUrl) {
        $shortUrl = $longUrl;
        $longUrl = urlencode($longUrl);
        $requestUrl = "http://sos2.org/yourls-api.php?action=shorturl&url=$longUrl&format=json&username=yourlsadmin&password=admin123$56";
        try {
            $shortenResult = file_get_contents($requestUrl);
            $shortenJson = json_decode($shortenResult);
            if (isset($shortenJson->shorturl)) {
                $shortUrl = $shortenJson->shorturl;
            }
        } catch (Zend_Exception $e) {}
        return $shortUrl;
    }
    
    public static function shortenUrlTiny($longUrl) {
        $shortUrl = $longUrl;
        $tinyUrl = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode($longUrl));
        if (filter_var($tinyUrl, FILTER_VALIDATE_URL)) $shortUrl = $tinyUrl;
        return $shortUrl;
    }
    
    public static function shortenUrlBitly($longUrl) {
        if (!self::$shortenKey) {
            self::$shortenUrlConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/oauth.ini', 'bitly');
            self::$shortenLogin = self::$shortenUrlConfig->api->login;
            self::$shortenKey = self::$shortenUrlConfig->api->key;
        }
        $shortUrl = '';
        try {
            $longUrlEncode = urlencode($longUrl);
            $shortenRequest = "http://api.bitly.com/v3/shorten?login=" . self::$shortenLogin . "&apiKey=" . self::$shortenKey ."&longUrl=$longUrlEncode&format=json";
            $shortenResult = file_get_contents($shortenRequest);
            $shortenJson = json_decode($shortenResult);
            if ($shortenJson->status_txt == 'OK') {
                $shortUrl = $shortenJson->data->url;
            }
        } catch (Zend_Exception $e) {}
        if (!$shortUrl) $shortUrl = $longUrl; // if shorten url error then use long url
        $logger = Sos_Service_Logger::getLogger();
        $logger->log("SHORTEN URL, longUrl: $longUrl, shortUrl: $shortUrl", Zend_Log::INFO);
        return $shortUrl;
    }
    
    public static function shortenUrlGoogle($longUrl) {
        if (!self::$shortenKey) {
            self::$shortenUrlConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/oauth.ini', 'google');
            self::$shortenKey = self::$shortenUrlConfig->shortener->apiKey;
        }
        $shortUrl = $longUrl;
        try {
            $ch = curl_init('https://www.googleapis.com/urlshortener/v1/url?key=' . self::$shortenKey);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $requestArgs = array('longUrl' => $longUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestArgs));
            $result = curl_exec($ch);
            curl_close($ch);
            if ($result) {
                $json = json_decode($result);
                if (isset($json->id)) if (strlen($json->id)) $shortUrl = $json->id;
            }
        } catch (Zend_Exception $e) {}
        return $shortUrl;
    }
    
}
