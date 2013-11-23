<?php

class Sos_Service_Cache {
    
    public static function cacheFactory($frontend = 'Output', $backend = 'File', $lifeTime = null) {
        if (!$lifeTime) $lifeTime = 60*60*24*2;
        $frontendOptions = array('lifetime' => $lifeTime, 'automatic_serialization' => true);
        $backendOptions = array('file_name_prefix' => 'view', 'cache_dir' => APPLICATION_PATH . '/../tmp/cache');
        $cache = Zend_Cache::factory($frontend, $backend, $frontendOptions, $backendOptions);
        return $cache;
    }
    
    public static function cacheFactoryPage($namespace = '') {
        $isMobile = Sos_Service_Functions::isMobileAccess();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $frontendOptions = array(
            'lifetime' => 60*60*24*2,
            'regexps' => array(
                '^/' => array('cache' => false),
                '^/$' => array('cache' => true), // homepage
                '^/web/alert/list' => array(
                    'cache' => false,
                    'tags' => array('alert_page_r_' . $request->getParam('id', ''))
                ),
                '^/web/alert/alertlist' => array('cache' => true),
                '^/r/' => array(
                    'cache' => false,
                    'tags' => array(
                        isset($_SERVER['REDIRECT_URL']) ? ('alert_page' . str_replace('/', '_', $_SERVER['REDIRECT_URL'])) : ''
                    )
                ),
            ),
            'default_options' => array(
                'cache' => true,
                'cache_with_get_variables' => true,
                'cache_with_post_variables' => true,
                'cache_with_session_variables' => true,
                'cache_with_files_variables' => true,
                'cache_with_cookie_variables' => true,
                'make_id_with_get_variables' => true,
                'make_id_with_post_variables' => false,
                'make_id_with_session_variables' => false,
                'make_id_with_cookie_variables' => false,
            ),
            'automatic_serialization' => true
        );
        $backendOptions = array('file_name_prefix' => 'page', 'cache_dir' => APPLICATION_PATH . '/../tmp/cache');
        if ($isMobile || $namespace == 'mobile') $backendOptions['file_name_prefix'] = 'page_mobile';
        $cache = Zend_Cache::factory('Page', 'File', $frontendOptions, $backendOptions);
        return $cache;
    }
    
    public static function clear($tag) {
        try {
            /*$alertPageCacheTag = 'alert_page_r_' . $this->_alertToken;
            $cache = Sos_Service_Functions::cacheFactoryPage();
            $cache->clean('matchingTag', array($alertPageCacheTag));
            $cache = Sos_Service_Functions::cacheFactoryPage('mobile');
            $cache->clean('matchingTag', array($alertPageCacheTag)); */
            $cache = self::cacheFactory();
            $cache->clean('matchingTag', array($tag));
        } catch (Zend_Exception $e) {}
    }
    
    public static function clearAlertCache($alertToken) {
        try {
            $alertPageCacheTag = 'alert_page_r_' . $alertToken;
            $cache = self::cacheFactoryPage();
            $cache->clean('matchingTag', array($alertPageCacheTag));
            $cache = self::cacheFactoryPage('mobile');
            $cache->clean('matchingTag', array($alertPageCacheTag));
            //$cache = self::cacheFactory();
            //$cache->clean('matchingTag', array($tag));
        } catch (Zend_Exception $e) {}
    }
}
