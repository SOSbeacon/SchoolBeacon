<?php

require_once 'BaseController.php';

class LocationController extends BaseController {

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

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it 
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction() {
        $token = $this->_request->getParam('token', false);
        $phoneId = $this->_request->getParam('phoneId', false);

        $latitude = $this->_request->getParam('latitude', false);
        $longtitude = $this->_request->getParam('longtitude', false);
        if (!$longtitude) {
            $longtitude = $this->_request->getParam('longitude', '');
        }

        $this->logger->log('Update location', Zend_Log::INFO);
        $this->logger->log('== Phone ID  : ' . $phoneId, Zend_Log::INFO);
        $this->logger->log('== Latitude  : ' . $latitude, Zend_Log::INFO);
        $this->logger->log('== Longtitude: ' . $longtitude, Zend_Log::INFO);

        $resObj = array();
        try {
            $this->authorizePhone($token, $phoneId);

            $mapper = new Sos_Model_LocationMapper;
            $location = new Sos_Model_Location();

            $location->setId($this->getLocationIdByPhoneId($phoneId));
            $location->setLatitude($latitude);
            $location->setLongtitude($longtitude);
            $location->setUpdatedDate(date("Y-m-d H:i:s"));

            $mapper->save($location);

            $resObj['success'] = "true";
            $resObj['id'] = $location->getId();
        } catch (Zend_Exception $ex) {
            $resObj['success'] = "false";
            $resObj['message'] = "error";
            $resObj['error'] = $ex->getMessage();
        }

        $this->view->response = $resObj;
    }

    //Get LocationId by phoneID
    private function getLocationIdByPhoneId($pid) {
        $phoneMap = new Sos_Model_PhoneMapper();
        $phone = new Sos_Model_Phone();
        $row = $phoneMap->findOneByField('id', $pid, $phone);

        return $row->getLocationId();
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

