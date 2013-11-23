<?
$form_r = $_REQUEST[form_r];
$form = $_REQUEST[form];

if(isset($form_r))
{
//Required Fields
$array_keys = array_keys($form_r);			
for($x=0;$x<sizeof($array_keys);$x++)
{
//check array for empty strings	
if(empty($form_r[$array_keys[$x]]))
{
$error[$array_keys[$x]] = $form_r[$array_keys[$x]].'<br>';
}
//stripp tags from array	
for($z=0;$z<sizeof($array_keys); $z++)
{
$form_r[$array_keys[$z]] = strip_tags($form_r[$array_keys[$z]]);
}

}


if(isset($form))
{
$array_keys = array_keys($form);
for($z=0;$z<sizeof($array_keys); $z++)
{
$form[$array_keys[$z]] = strip_tags($form[$array_keys[$z]]);
}
}

//Compare Passwords
if(isset($form_r[password]) && ($form_r[password] != $form_r[password_confirm]))
{
$reg_error[password] = 'true';
}


if((!isset($reg_error[password]) && !isset($error)) && (strlen($form_r[password]) < 8)){
$reg_error[password_length_min] = 'true';
}

if((!isset($reg_error[password]) && !isset($error)) && (strlen($form_r[password]) > 16)){
$reg_error[password_length_max] = 'true';
}

if (is_array($error) || is_array($reg_error))
{
	
$reg_error[error] = 'true';
$content_fragment = './templates/'.$_SESSION[culture].'/form_account_update.inc';
}

else
{
include('form_password_update.inc');
}

}
?>