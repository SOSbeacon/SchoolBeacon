<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../schoolbeacon/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

defined('CONTROLLER_PATH')
    || define('CONTROLLER_PATH',APPLICATION_PATH . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR);

defined('MODEL_PATH')
    || define('MODEL_PATH',APPLICATION_PATH . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR);

defined('VIEW_PATH')
    || define('VIEW_PATH',APPLICATION_PATH . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR);

defined('FORM_PATH')
    || define('FORM_PATH',APPLICATION_PATH . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR);
        
    
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../../sosbeacon/library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
