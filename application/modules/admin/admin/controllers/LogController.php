<?php
class Admin_LogController extends Zend_Controller_Action
{
    public function indexAction()
    {
         if(!Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->hasIdentity()) {
        	$this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        } else {
            $message = '';
            $log = new Sos_Model_Log();
            $paginator  = $log->fetchListToPaginator(null, 'id DESC');
            $paginator->setItemCountPerPage(40);

            $page = $this->_request->getParam('page', 1);
            $paginator->setCurrentPageNumber($page);
            $paginator->setPageRange(10);
            $this->view->paginator = $paginator;
            $this->view->message = $message;
        }
    }
    
}
