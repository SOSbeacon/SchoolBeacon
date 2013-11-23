<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    function _initAutoLoad() {
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APPLICATION_PATH,
            'namespace' => 'Sos',
            'resourceTypes' => array(
                'form' => array(
                    'path' => 'forms',
                    'namespace' => 'Form_'),
                'validate' => array(
                    'path' => 'views/validation',
                    'namespace' => 'Validate_'),
                'plugin' => array(
                    'path' => 'plugins',
                    'namespace' => 'Plugin_'),
                'service' => array(
                    'path' => 'services',
                    'namespace' => 'Service_'),
                'model' => array(
                    'path' => 'models',
                    'namespace' => 'Model_'),
                'helper' => array(
                    'path' => 'helpers',
                    'namespace' => 'Helper_'),
            ),
        ));
        $autoLoader->setFallbackAutoloader(true);
        //Call Zend_Acl plugin
        $zcf = Zend_Controller_Front::getInstance();
        //Call Plugin_layout
        $zcf->registerPlugin(new Sos_Plugin_Modularlayout());
        return $autoLoader;
    }

//    protected function _initGearmanWorker() {
//       $options = $this->getOptions();
//        $gearmanworker = new GearmanWorker();
//        if (isset($options['gearmanworker']) && isset($options['gearmanworker']['servers'])) {
//            $gearmanworker->addServers($options['gearmanworker']['servers']);
//        } else {
//            $gearmanworker->addServer();
//        }
//        return $gearmanworker;
//    }
    
    protected function _initRestRoute() {
        $frontController = Zend_Controller_Front::getInstance();
        $dedaultRoute = new Zend_Controller_Router_Route(
            ':module/:controller/:action/*',
            array('module' => 'default')
        );
        $webRoute = new Zend_Controller_Router_Route(
            'web/:controller/:action/*',
            array('module' => 'web', 'action' => 'index')
        );
        $webappRoute = new Zend_Controller_Router_Route(
            'webapp/:controller/:action/*',
            array('module' => 'webapp', 'controller' => 'index', 'action' => 'index')
        );
        $adminRoute = new Zend_Controller_Router_Route(
            'admin/:controller/:action/*',
            array('module' => 'admin', 'controller' => 'index', 'action' => 'index')
        );
        $alertlistRoute = new Zend_Controller_Router_Route(
            'web/alert/list/id//:id',
            array()
        );
        // Old alert router
        $reviewAlertRouteV1 = new Zend_Controller_Router_Route(
            '/r/:id',
            array('module' => 'web', 'controller' => 'alert', 'action' => 'list')
        );
        $reviewAlertRoute = new Zend_Controller_Router_Route(
            '/r/:contactId/:id',
            array('module' => 'web', 'controller' => 'alert', 'action' => 'list')
        );
        $activeRoute = new Zend_Controller_Router_Route(
            '/a/:code',
            array('module' => 'web', 'controller' => 'phone', 'action' => 'doactive')
        );
        $this->bootstrap('Request');
        $front = $this->getResource('FrontController');
        $restRoute = new Zend_Rest_Route($front, array(), array(
            'default' => array('users', 'rest', 'groups', 'join-group', 'contacts', 'phones', 'data', 'alert', 'setting', 'location', 'mail', 'test')
        ));
        $front->getRouter()->addRoute('default', $dedaultRoute);
        $front->getRouter()->addRoute('listAlert', $alertlistRoute);
        $front->getRouter()->addRoute('web', $webRoute);
        $front->getRouter()->addRoute('webapp', $webappRoute);
        $front->getRouter()->addRoute('admin', $adminRoute);
        $front->getRouter()->addRoute('rest', $restRoute);
        $front->getRouter()->addRoute('reviewV1', $reviewAlertRouteV1);
        $front->getRouter()->addRoute('review', $reviewAlertRoute);
        $front->getRouter()->addRoute('activate', $activeRoute);
    }

    protected function _initRequest() {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $request = $front->getRequest();
        if (null === $front->getRequest()) {
            $request = new Zend_Controller_Request_Http();
            $front->setRequest($request);
        }
        return $request;
    }
    
    protected function _initTimeZone() {
        date_default_timezone_set('Europe/Dublin');
    }
    
    public function run() {
        global $argv;
        if (isset($argv[1])) { // Run send alert dispatch
            $logger = Sos_Service_Logger::getLogger();
            $logger->log('>>>>> RUN, argv: ' . print_r($argv, 1), Zend_Log::INFO);
            if ($argv[1] == 'alert' && ($argv[2] == 'email' || $argv[2] == 'sms') && $argv[3]) {
                Sos_Service_Gearman_Manager::runWorker($argv[1], $argv[2], $argv[3]);
            }
        } else { // Run web script
            $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $front   = $this->getResource('FrontController');
            $default = $front->getDefaultModule();
            if (null === $front->getControllerDirectory($default)) {
                throw new Zend_Application_Bootstrap_Exception(
                    'No default controller directory registered with front controller'
                );
            }
            $front->setParam('bootstrap', $this);
            $cache = Sos_Service_Cache::cacheFactoryPage();
            $auth = Sos_Service_Functions::webappAuth(false);
            // catche for not login user and not bitlybot
            if (!$auth->getId() && $agent != 'bitlybot') $cache->start(); 
            $response = $front->dispatch();
            if ($front->returnResponse()) {
                return $response;
            }
        }
    }
    
    public function runAlertWorker() {
        Sos_Service_Gearman_Manager::runWorker('alert');
    }
}