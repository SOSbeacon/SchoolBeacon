<?php
class Sos_Helper_Xml
{
    public static function getXML ($obj)
    {
        $doc = new DOMDocument();
        $doc->formatOutput = true;
        $root_element = $doc->createElement("response");
        $doc->appendChild($root_element);
        foreach ($obj as $var => $value) {
            $statusElement = $doc->createElement($var);
            if (! is_array($value)) {
                $statusElement->appendChild($doc->createTextNode($value));
                $root_element->appendChild($statusElement);
            } else {
                self::_xmlHelper($doc, $root_element, $statusElement, $value);
            }
        }
        print $doc->saveXML();
    }
    
    public static function _xmlHelper (&$doc, &$root_element, &$statusElement, &$value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                if (is_array($val)) {
                    self::_xmlHelper($doc, $root_element, $statusElement, $val);
                } else 
                    if (is_object($val)) {
                        $se = $doc->createElement(str_replace('object', '', get_class($val)));
                        $arr = get_object_vars($val);
                        self::_xmlHelper($doc, $root_element, $se, $arr);
                        $root_element->appendChild($se);
                    } else {
                        //print $key . " => " . $val . "\n";
                        $se = $doc->createElement($key);
                        $se->appendChild($doc->createTextNode($val));
                        $statusElement->appendChild($se);
                    }
            }
            //print_r($value);
        } else {
            $statusElement->appendChild($doc->createTextNode($value));
            $root_element->appendChild($statusElement);
        }
    }
}