<?php
/*
* Author:       Eric F. Palmer
* Email:        epalmer [at] richmond [dot] edu
* Organization: University of Richmond
* Date:         July 11, 2010
* Description:  Singleton Class for instantiating a Zend Framwork Zend_Log Logging facility
*
*
* Copyright:    2010 by Eric F. Palmer, University of Richmond
* License:      LGPL http://www.gnu.org/copyleft/lesser.html
*
* If you find this useful send me an email please.  Thanks EFP
*/

class Sos_Service_Logger {
    private static $logger = null;
    private static $initialized = false;
    private function __construct() {
    // make constructor private so that no one can instantiate this class
    }
    
    private static function initialize()
    {
        if (self::$initialized) {
            return;
        }
        // else set up logger
        
        // set up a writer for writing to html output, bold the output and put a html break at the end of the line
        $logger = new Zend_Log();
        // log to http output/console
//        $writer = new Zend_Log_Writer_Stream('php://output');
//        $format = '<strong>%timestamp% %priorityName% (%priority%): %message% </strong><br/>' . PHP_EOL;
//        $formatter = new Zend_Log_Formatter_Simple($format);
//        $writer->setFormatter($formatter);
//        $logger->addWriter($writer);
        
        // set up a 2nd writer that logs to a file with a simple format
        /* if you run the test program from the web you will have to set up the log folder to
        * have permissions so that your web server can write to the files
        */
        $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../tmp/log.txt');
        $format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
        $formatter = new Zend_Log_Formatter_Simple($format);
        $writer->setFormatter($formatter);
        $logger->addWriter($writer);

        // log to database
        $db = Zend_Db_Table::getDefaultAdapter();
        $columnMapping = array('timestamp' => 'timestamp' , 'priority' => 'priority', 'message' => 'message');
        $writerDb = new Zend_Log_Writer_Db($db, 'log', $columnMapping);
        $writerDb->addFilter(Zend_Log::ERR);
        $logger->addWriter($writerDb);
    
        // set up a 3rd writer that sends an email when an EMERG log message is created
//        $mail = new Zend_Mail();
//        if (!$mail) {
//            $logger("Unable to instantiate Zend_Mail object", Zend_Log::INFO);
//        }
//        // put your email address in the form and to methods belo
//        $mail->setFrom('yourEmail@domain.com')->addTo('yourEmail@domain.com');
//        $writer = new Zend_Log_Writer_Mail($mail);
//        $writer->setSubjectPrependText('A Serious Error Has Occurred in testZendLog.php: ');
//        $writer->addFilter(Zend_Log::EMERG);
//        $logger->addWriter($writer);
    
        self::$logger = $logger;
        self::$initialized = true;
    }
    
    public static function getLogger()
    {
        // call this method to return a single instance object for the loger.
        self::initialize();
        return self::$logger;
    }
}
