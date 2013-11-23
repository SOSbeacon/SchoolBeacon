<?php

require_once 'BaseController.php';

class DataController extends BaseController {

    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('resources');
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', array('xml', 'json'))
            ->addActionContext('get', array('xml', 'json'))
            ->addActionContext('post', array('xml', 'json'))
            ->addActionContext('put', array('xml', 'json'))
            ->addActionContext('delete', array('xml', 'json'))
            ->initContext();
        $this->logger = Sos_Service_Logger::getLogger();
    }


    public function postAction() {
        $type = $this->_request->getParam('type', -1);
        $token = $this->_request->getParam('token', false);
        $phoneId = $this->_request->getParam('phoneId', false);
        $alertId = $this->_request->getParam('alertId', false);
        //Only for Check-in
        $alertlogType = $this->_request->getParam('alertlogType', false);
        $this->logger->log('Star receive alert data...........', Zend_Log::INFO);
        $resObj = array();
        $debug = "== Start recive file, time: " . date('Y-m-d H:i:s') . ", type:$type, token:$token, phoneId:$phoneId, alertId:$alertId";
        try {
            $this->authorizePhone($token, $phoneId);
            if ($type != Sos_Model_Alertdata::TYPE_AUDIO && $type != Sos_Model_Alertdata::TYPE_IMAGE) {
                throw new Zend_Exception('type param is incorrect');
            }
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->addValidator('Count', false, array('min' => 1, 'max' => 1))
                    //->addValidator('IsImage', false, 'jpeg')
                    ->addValidator('Size', false, array('max' => '5120kB'))
                    ->setDestination(APPLICATION_PATH . '/../tmp');
            $files = $upload->getFileInfo();
            
            $debug .= '== File info: ' . print_r($files, 1);
            if (!$upload->isValid()) {
                throw new Zend_Exception('Bad data: ' . implode(',', $upload->getMessages()));
            }
            foreach ($files as $file => $info) {
                $ext = pathinfo($info['name']);
                $this->logger->log('Uploaded file name       :' . $ext['basename'], Zend_Log::INFO);
                $upload->addFilter('Rename', array('target' => APPLICATION_PATH . '/../tmp/' . $phoneId . '_' . date('Ymdhis') . '.' . $ext['extension'],
                    'overwrite' => true));
                $upload->receive($file);
                $fileName = $upload->getFileName(null, false);
                $tmpPath = $upload->getFileName(null, true);
                $newPath = "";
                $url = "";
                if ($type == 0) {
                    $generatedName = Sos_Helper_File::generatePath(Sos_Helper_File::getAlertImagePath() . $fileName, false);
                    $newPath = Sos_Helper_File::getAlertImagePath()  . $generatedName;
                    $url = Sos_Helper_File::getAlertImageURL() . $generatedName;
                } else {
                    $fullPath = $upload->getFileName(null, true);
                    // if iphone, convert .caf to mp3
                    if ($ext['extension'] == 'caf') {
                        $mp3File = substr($fullPath, 0, strlen($fullPath) - 4) . ".mp3";
                        // convert to mp3
                        // 1. use sndfile-convert to convert CAF to AIF
                        // 2. use lame to conver AIF to MP3
                        $aifFile = substr($fullPath, 0, strlen($fullPath) - 4) . ".aif";
                        $output = array();
                        exec("sndfile-convert $fullPath $aifFile", $output, $returnVal1);
                        exec("lame $aifFile $mp3File", $output, $returnVal2);
                        if ($returnVal1 || $returnVal2) {
                            if (file_exists($fullPath))
                                unlink($fullPath);
                            if (file_exists($aifFile))
                                unlink($aifFile);
                            if (file_exists($mp3File))
                                unlink($mp3File);
                            throw new Zend_Exception('Error while converting audio file');
                        }
                        if (file_exists($aifFile))
                            unlink($aifFile);
                        if (file_exists($fullPath))
                            unlink($fullPath);
                    } else if ($ext['extension'] == 'amr') {
                        $mp3File = substr($fullPath, 0, strlen($fullPath) - 4) . ".mp3";
                        // convert to mp3
                        $output = array();
                        // http://packages.medibuntu.org/maverick/libavcodec-extra-52.html
                        exec("/usr/local/sbin/ffmpeg -i $fullPath -ar 8000 -ab 24000 $mp3File", $output, $returnVal3);
                        if (!file_exists($mp3File)) {
                            //   if (file_exists($fullPath)) unlink($fullPath);
                            
                            throw new Zend_Exception('Error while converting audio file');
                        }
                        if (file_exists($fullPath)) unlink($fullPath);
                    }
                    else {
                        $mp3File = substr($fullPath, 0, strlen($fullPath) - 4) . "." . $ext['extension'];
                    }
                    $ext = pathinfo($mp3File);
                    $generatedName = Sos_Helper_File::generatePath(Sos_Helper_File::getAlertAudioPath() . $ext['basename'], false);
                    $newPath = Sos_Helper_File::getAlertAudioPath() . $generatedName;
                    $url = Sos_Helper_File::getAlertAudioURL() . $generatedName;
                    $tmpPath = $mp3File;
                }
                $this->logger->log('File name : ' . $newPath, Zend_Log::INFO);
                if (!rename($tmpPath, $newPath)) {
                    throw new Zend_Exception('Error while removing temp file: ' . implode(',', $upload->getMessages()));
                }
                //Only for Check -in
                if ($alertlogType == 2) {
                    if (!$alertId) {   // TODO  will change after v1.27 submit to store
                        $alertlogMapper = new Sos_Model_AlertlogMapper();
                        //IF alert is Check-in, find last alertlog of phone type check-in
                        $alertlog = $alertlogMapper->getLastInCheckIn($phoneId);
                        if ($alertlog != null) { // IF Check-in Witness data in Session
                            //Set alertlogId to last check-in alertlog
                            $alertId = $alertlog->id;
                        } else {// IF Check-in Witness data NOT in Session
                            $alertId = null;
                        }
                    }
                }
                //End of Only for Check-in
                $mapper = new Sos_Model_AlertdataMapper();
                $alertData = new Sos_Model_Alertdata();
                $alertData->setType($type);
                $alertData->setPath($url);
                $alertData->setPhoneId($phoneId);
                $alertData->setAlertlogId($alertId);
                $alertData->setCreatedDate(date("Y-m-d H:i:s"));
                $mapper->save($alertData);
                // Clear alert page cache
                if ($alertId) {
                    $alertLog = new Sos_Model_Alertlog();
                    $alertLog->find($alertId);
                    if ($alertLog->getId()) {
                        $alertGroupId = $alertLog->getAlertloggroupId();
                        $alertGroup = new Sos_Model_Alertloggroup();
                        $alertGroup->find($alertGroupId);
                        if ($alertGroup->getId()) {
                            $alertToken = $alertGroup->getToken();
                            Sos_Service_Cache::clear('alert_' . $alertToken);
                        }
                    }
                }
                $resObj['success'] = "true";
                $resObj['id'] = $alertData->getId();
                //            $resObj['filename'] = $fileName;
                //            $resObj['filesize'] = $fileSize;
                $resObj['type'] = $type;
                $this->logger->log('Alert data sent successfully', Zend_Log::INFO);
                break; // 1 file / time only                                
            }
        } catch (Zend_Exception $ex) {
            $resObj['success'] = "false";
            $resObj['message'] = "error";
            $resObj['error'] = $ex->getMessage();
            $this->logger->log('ERROR: ' . $ex->getMessage(), Zend_Log::ERR);
        }
        $debug .= "== End recive file, time: " . date('Y-m-d H:i:s');
        $resObj['debug'] = $debug;
        $this->view->response = $resObj;
        
    }

    public function putAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }

    public function deleteAction() {
        $$this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }
    
    public function indexAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }

    public function getAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }
}

