<?php
class Sos_Form_UserLogin extends Sos_Form_Base {
    
    public function init() {
        $this->setMethod("post");

        // Change: use email or phone to login
        //$emailElement = $this->getTextField("Email", "email",true);
        //$emailElement->addValidator(new Zend_Validate_EmailAddress());
        $userElement = $this->getTextField("Email/Phone number", "user",true);
        $userElement->addValidator(new Zend_Validate_StringLength(6,50));

        $passwordElement = $this->getTextField("Passwod","password",true);
        $passwordElement->addValidator(new Zend_Validate_StringLength(6,20));

        $submitElement = $this->getSubmit("Login","login",false);
        
        $this->addElements(array($userElement,$passwordElement,$submitElement));
    }
    
}
