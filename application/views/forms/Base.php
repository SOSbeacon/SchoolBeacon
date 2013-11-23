<?php

abstract class Wom_Form_Base extends Zend_Form {

    protected function getTextField($label=null, $name=null, $require=false)
    {
        $element = new Zend_Form_Element_Text($name);
        $element->setLabel($label);
        $element->setRequired($require);

        //Add Filter
        $element->addFilter(new Zend_Filter_StripTags());
        $element->addFilter(new Zend_Filter_HtmlEntities());

        return $element;
    }

    protected function getSelectField($label=null, $name=null, $require=false)
    {
        $element = new Zend_Form_Element_Select($name);
        $element->setLabel($label);
        $element->setRequired($require);

        //Add Filter
        $element->addFilter(new Zend_Filter_StripTags());
        $element->addFilter(new Zend_Filter_HtmlEntities());

        return $element;
    }

    protected function getCheckBoxField($label=null, $name=null, $require=false)
    {
        $element = new Zend_Form_Element_Checkbox($name);
        $element->setLabel($label);
        $element->setRequired($require);

        //Add Filter
        $element->addFilter(new Zend_Filter_StripTags());
        $element->addFilter(new Zend_Filter_HtmlEntities());

        return $element;
    }
    
    protected function getHiddenField($name=null, $require=false)
    {
        $element = new Zend_Form_Element_Hidden($name);
        $element->setRequired($require);
        return $element;
    }

     
}

