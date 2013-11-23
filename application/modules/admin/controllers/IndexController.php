<?php

class Admin_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
    	if(!Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->hasIdentity()){
        	$this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        }
        $phoneNumber 		=  trim($this->_request->getParam("phone_number", ''));
        $viewType 		=  trim($this->_request->getParam("view_type", 'history'));
        $this->view->phoneNumber = htmlspecialchars($phoneNumber);
        $this->view->message = '';
        if ($phoneNumber) {
            $phoneModel = new Sos_Model_Phone();
            $phoneMapper = new Sos_Model_PhoneMapper();
            // get phone id by number
            $phoneIds = $phoneModel->fetchListToArray('number="'. $phoneNumber .'"');
            if ($phoneIds && count($phoneIds) > 0) {
                $phoneId = $phoneIds[0]['id'];
                $phoneToken = $phoneIds[0]['token'];
                if ($viewType == 'history') {
                    $phone = $phoneMapper->getPhoneByToken($phoneToken, $phoneModel);
                    $alertToken = $this->_request->getParam("token", '');
                    if (!$alertToken) {
                        // get last token
                        if($phone != null) {
                            // find the latest alert id
                            $query = "select id, token from alertloggroup
                                                    where id = (select max(id) as id from alertloggroup
                                                                            where phone_id = ".$phone->getId().")";
                            $result = $phoneMapper->getDbTable()->getAdapter()->query($query)->fetch();
                            $alertToken = $result['token'];
                        }
                    }
                    $alertDataRows = '';
                    if($alertToken) {
                        //$alertloggroupId	= $this->getAlertloggroupIdByToken($alertToken);
                        $alertloggroup	= new Sos_Model_Alertloggroup();
                        $alertloggroupMap = new Sos_Model_AlertloggroupMapper();
                        $alertloggroupMap->findOneByField('token', $alertToken, $alertloggroup);
                        $alertloggroupId = $alertloggroup->getId();
                        if(isset($alertloggroupId)) {
                            //alertdata
                            $alertData		= new Sos_Model_Alertdata();
                            $alertMapper	= new Sos_Model_AlertdataMapper();
                            $alertDataRows	= $alertMapper->findAllByAlertloggroup($alertloggroupId);
                            $phone			= new Sos_Model_Phone();
                            $phoneMap		= new Sos_Model_PhoneMapper();
                            $alertlog		= new Sos_Model_Alertlog();
                            $alertlogMap 	= new Sos_Model_AlertlogMapper();
                            $alertlogRows	= $alertlogMap->getAllAlertlogDataByAlertloggroup($alertloggroupId);
                             // Get Map9516192858
                            $maps = array();
                            foreach ($alertlogRows as $row) {
                                $alrid = $row->alertlog_id;
                                $location = new Sos_Model_Location();
                                $map  = $location->fetchListToArray("alertlog_id = $alrid", 'id');
                                if($map) {
                                    $maps[$alrid] = $map[0];
                                }
                            }
                            $phoneMap->getPhoneByAlertloggroupId($phone, $alertloggroupId);
                            $phoneNums = null;
                            $this->view->alertDataRows	= $alertDataRows;
                            $this->view->maps	= $maps;
                            $this->view->phoneRows		= $phone;
                            $this->view->alertlogRows 	= $alertlogRows;
                            $this->view->alertloggroupId = $alertloggroupId;
                            // alert list
                            $alertlogRowsList = $alertloggroupMap->fetchList("phone_id = $phoneId", "id DESC");
                            $this->view->alertlogRowsList 		= $alertlogRowsList;
                            $this->view->alertlogRowsList 		= $alertlogRowsList;
                        }
                        $this->view->token			= $alertToken;
                    }
                    if (!$alertDataRows) {
                        $this->view->message = 'No record found.';
                    }
                } else if ($viewType == 'infor') {

                }
                $this->view->phoneId = $phoneId;
            } else {
                $this->view->message = 'This phone number does not exist.';
            }
        }
        $this->view->viewType = $viewType;
        // get time zone to display
        $time_gmt =  trim($this->_request->getParam("time_gmt", ''));
        $time_gmt_value = $time_gmt ? strtotime($time_gmt) : time();
        $time_display = 'San Francisco: ' . gmdate('M d Y, h:i A', $time_gmt_value - (8 * 60 * 60)) . ' |
                         Hanoi: ' . gmdate('M d Y, h:i A', $time_gmt_value + (7 * 60 * 60)) ;
        $this->view->time_display = $time_display;
        $this->view->time_gmt =  gmdate('d-m-Y, H:i', $time_gmt_value);
    }


}
