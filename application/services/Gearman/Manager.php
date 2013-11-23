<?php
class Sos_Service_Gearman_Manager
{
    private static $_gearmanServers = null;
    private static $_stdLogger = null;
 
    /**
      * Retrieves the current Gearman Servers
      *
      * @return array
      */
     public static function getServers()
     {
         if (self::$_gearmanServers === null) {
             $servers = "127.0.0.1:4730";
             self::$_gearmanServers = $servers;
         }
         return self::$_gearmanServers;
     }
 
     /**
      * Creates a GearmanClient instance and sets the job servers
      *
      * @return GearmanClient
      */
     public static function getClient()
     {
         $gmclient= new GearmanClient();
         $servers = self::getServers();
         $gmclient->addServers($servers);
 
         return $gmclient;
    }
 
    /**
     * Creates a GearmanWorker instance
     *
     * @return GearmanWorker
     */
    public static function getWorker()
    {
        $worker = new GearmanWorker();
        $servers = self::getServers();
        $worker->addServers($servers);
 
        return $worker;
    }
 
    /**
     * Given a worker name, it checks if it can be loaded. If it's possible,
     * it creates and returns a new instance.
     *
     * @param string $workerName
     * @param string $logFile
     * @return Model_Gearman_Worker
     */
    public static function runWorker($workerName, $task, $params) {
        try {
            $workerName .= 'Worker';
            $workerName = ucfirst($workerName);
            $workerFile = APPLICATION_PATH . '/workers/' . $workerName . '.php';

            self::getLogger()->log(">>>>> START runWorker: $workerFile", Zend_Log::INFO);

            if (!file_exists($workerFile)) {
                throw new InvalidArgumentException("Worker no exist: $workerFile");
            }

            require $workerFile;

            if (!class_exists($workerName)) {
                throw new InvalidArgumentException("Class $workerName no exist: $workerFile");
            }
            self::getLogger()->log(">>>>> WORKER: $workerName, TASK: $task, PARAMS: $params", Zend_Log::INFO);
            $workerObject = new $workerName();
            if ($task == 'email') {
                $workerObject->_sendEmail($params);
            }
            if ($task == 'sms') {
                $workerObject->_sendSms($params);
            }
            return $workerObject;
        } catch (Exception $e) {
            self::getLogger()->log('Error: ' . $e->getMessage(), Zend_Log::ERR);
        }
    }
 
    public static function getLogger($logFile = '')
    {
        if (self::$_stdLogger === null) {
            self::$_stdLogger = Sos_Service_Logger::getLogger();
        }
        return self::$_stdLogger;
    }
}