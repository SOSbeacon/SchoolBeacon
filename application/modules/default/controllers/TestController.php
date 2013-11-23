<?php
/**
 * UsersController
 * 
 * @author
 * @version 
 */

require_once 'BaseController.php';
class TestController extends BaseController
{
    public function init()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
		
		$options = $bootstrap->getOption('resources');
	
		$contextSwitch = $this->_helper->getHelper('contextSwitch');
		$contextSwitch->addActionContext('index', array('xml','json'))
		                ->addActionContext('get', array('xml','json'))
		                ->addActionContext('post', array('xml','json'))
		                ->addActionContext('put', array('xml','json'))
		                ->addActionContext('delete', array('xml','json'))
		                ->initContext();		

		$this->logger = Sos_Service_Logger::getLogger();         		                	
	}
    	
     	/**
         * The index action handles index/list requests; it should respond with a
         * list of the requested resources.
     */ 
    public function indexAction()
    {        
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';        
	}	
    
    /**
     * The get action handles GET requests and receives an 'id' parameter; it 
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */ 
    public function getAction()
    {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';        
	}
    	
    
    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */  
    public function postAction() {   
        $resObj = array();
        try {
            
            
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->addValidator('Count', false, array('min' =>1, 'max' => 1))
                   //->addValidator('IsImage', false, 'jpeg')
                   ->addValidator('Size', false, array('max' => '1024kB'))
                   ->setDestination(APPLICATION_PATH . '/../tmp');
                   
     
            if (!$upload->isValid()) 
            {
            //    throw new Zend_Exception('Bad data: '.implode(',', $upload->getMessages()));
            }
            
            
            $files = $upload->getFileInfo();
            foreach ($files as $file => $info) {
                $ext = pathinfo($info['name']);
                $this->logger->log('Uploaded file name       :' . $ext['basename'], Zend_Log::INFO); 
                                
                $upload->addFilter('Rename', array('target' =>   APPLICATION_PATH . '/../tmp/ID_'.date('Ymdhis'). '.'.  $ext['extension'],
                								 'overwrite' => true));
                $upload->receive($file);
                
                $fileName = $upload->getFileName(null, false);

                $tmpPath = $upload->getFileName(null, true);
                $newPath = "";        
                $url = "";        
                            
                //if ($type==0) {
                	$generatedName = Sos_Helper_File::generatePath(Sos_Helper_File::getAlertImagePath() . 'test/' . $fileName, false);
                	
                    $newPath = Sos_Helper_File::getAlertImagePath() . 'test/' . $generatedName;
                    $url 	 = Sos_Helper_File::getAlertImageURL () . 'test/' . $generatedName;
                //}
				
                $this->logger->log('File name : ' . $newPath, Zend_Log::INFO); 
                
                //if (!rename($tmpPath, $newPath)) {
                //    throw new Zend_Exception('Error while removing temp file: ' . implode(',', $upload->getMessages()));
                //}
            
                //Only for Check -in
                
                $resObj['success'] 	= "true";
                if (!rename($tmpPath, $newPath)) {
                    throw new Zend_Exception('Error while removing temp file: ' . implode(',', $upload->getMessages()));
                }
                $this->logger->log('Alert data succesfully', Zend_Log::INFO); 
                break; // 1 file / time only                                
                    
            }
           
        } catch (Zend_Exception $ex) {
            $resObj['success'] = "false";
            $resObj['message'] = "error";   
            $resObj['error']   = $ex->getMessage();
            
            $this->logger->log('ERROR: ' . $ex->getMessage(), Zend_Log::ERR);
            
        }
    
         $this->view->response = $resObj;        
    }
   
    /**
     * The put action handles PUT requests and receives an 'id' parameter; it 
     * should update the server resource state of the resource identified by 
     * the 'id' value.
     */  
    public function putAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';        
    }
    
    /**
     * The delete action handles DELETE requests and receives an 'id' 
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */  
    public function deleteAction() {
        $$this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';        
    }
    
    
        
}    

