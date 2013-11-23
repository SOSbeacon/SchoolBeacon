<?php
class ObjectToXML
{
    private $dom;
    public function __construct ($obj)
    {
        $this->dom = new DOMDocument("1.0", "UTF-8");
        $root = $this->dom->createElement('response');
        foreach ($obj as $key => $value) {
            $node = $this->createNode($key, $value);
            if ($node != NULL)
                $root->appendChild($node);
        }
        $this->dom->appendChild($root);
    }
    private function createNode ($key, $value)
    {
        $key =  is_int($key) ? 'item' : $key;
        try {
            $node = NULL;
            if (is_string($value) || is_numeric($value) || is_bool($value) || $value == NULL) {
                if ($value == NULL)
                    $node = $this->dom->createElement($key);
                else
                    $node = $this->dom->createElement($key, (string) $value);
            } else {
                $node = $this->dom->createElement($key);
                if ($value != NULL) {
                    foreach ($value as $key => $value) {
                        $sub = $this->createNode($key, $value);
                        if ($sub != NULL)
                            $node->appendChild($sub);
                    }
                }
            }
            return $node;
        } catch (Exception $ex) {
             
        }

        return null;
    }
    public function __toString ()
    {
        return $this->dom->saveXML();
    }
}
