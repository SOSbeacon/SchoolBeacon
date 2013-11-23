<?php
class Sos_Form_User extends Sos_Form_Base {
    
    public function init() {
        $this->setMethod("post");

        $emailElement = $this->getTextField("Email", "email",true);
        $emailElement->addValidator(new Zend_Validate_EmailAddress());

        $cemailElement = $this->getTextField("Confirm Email", "cemail",true);
        $cemailElement->addValidator(new Zend_Validate_EmailAddress());
        
        //$countryElement = $this->getTextField("Country Code", "countryCode",true);
        //$countryElement->addValidator(new Zend_Validate_Alnum());

        $phoneElement = $this->getTextField("Phone Number", "phoneNumber",true);
        $phoneElement->addValidator(new Zend_Validate_Alnum());
                
        $passwordElement = $this->getTextField("Passwod","password",true);
        $passwordElement->addValidator(new Zend_Validate_StringLength(6,20));

        $cpasswordElement = $this->getTextField("Confirm Passwod","cpassword",true);
        $cpasswordElement->addValidator(new Zend_Validate_StringLength(6,20));
        
        $nameElement = $this->getTextField("Name","name",true);
        $nameElement->addValidator(new Zend_Validate_StringLength(1,50));

        $addressElement = $this->getTextField("Address", "address", false);
        $addressElement->addValidator(new Zend_Validate_StringLength(1,200));

        $submitElement = $this->getSubmit("Submit","submit",false);
        
        $this->addElements(array($emailElement, $cemailElement, $phoneElement, $passwordElement,$cpasswordElement,$nameElement,$addressElement,$submitElement));
    }
    
}
