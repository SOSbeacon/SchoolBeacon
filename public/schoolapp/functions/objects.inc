<?
class RequiredFields {
function RequiredFields($required_fields) {

$array_keys_r = array_keys($required_fields);


for($x=0;$x<sizeof($array_keys_r);$x++)
{
if(empty($required_fields[$array_keys_r[$x]]))
{
 $error[$array_keys_r[$x]] = $required_fields[$array_keys_r[$x]];
}

for($z=0;$z<sizeof($array_keys_r); $z++)
{
$required_fields[$array_keys_r[$z]] = strip_tags($required_fields[$array_keys_r[$z]]);
}

}







$this->form_r  = $required_fields;
$this->error  = $error;
}




	}
	
class NonRequiredFields {
function NonRequiredFields($nonrequired_fields) {

$array_keys = array_keys($nonrequired_fields);


for($x=0;$x<sizeof($array_keys);$x++)
{


for($z=0;$z<sizeof($array_keys); $z++)
{
$nonrequired_fields[$array_keys[$z]] = strip_tags($nonrequired_fields[$array_keys[$z]]);
}

}







$this->form  = $nonrequired_fields;

}




	}
?>