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
}

if(isset($form))
{
$array_keys = array_keys($form);
for($z=0;$z<sizeof($array_keys); $z++)
{
$form[$array_keys[$z]] = strip_tags($form[$array_keys[$z]]);
}
}



if (is_array($error) || is_array($reg_error))
{
$reg_error[error] = 'true';
$content_fragment = './templates/'.$_SESSION[culture].'/default_system_user.inc';
}

else
{
$content_fragment = './templates/'.$_SESSION[culture].'/search_records_results.inc';
}


?>