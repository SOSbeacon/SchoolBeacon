<?php

set_time_limit(0);
 
 
if (!isset($argv[1])) {
    // $argv[1] = 'alert';
    throw new InvalidArgumentException('Must be passed in as a parameter');
}
 
//if (isset($argv[2])) {
//    define('APPLICATION_ENV', $argv[2]);
//} else {
    define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
//}
 
// Define path to application directory
//defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

 defined('APPLICATION_PATH') || define('APPLICATION_PATH',  '/home/sosbeacon/sosbeacon-api-v2/application');
 
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    '/home/sosbeacon/sosbeacon-api-v2/library',
    '/home/sosbeacon/sosbeacon/library',
)));
 
require_once 'Zend/Application.php';
$app = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$app->bootstrap()->run();