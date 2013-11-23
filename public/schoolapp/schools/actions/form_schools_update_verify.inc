<?
$form_r = $_REQUEST[form_r];
$form = $_REQUEST[form];


if(isset($form_r))
{
//Required Fields
$array_keys_r = array_keys($form_r);		
for($x=0;$x<sizeof($array_keys_r);$x++)
{
//check array for empty strings	
if(empty($form_r[$array_keys_r[$x]]))
{
$error[$array_keys_r[$x]] = $form_r[$array_keys_r[$x]].'<br>';
}
//stripp tags from array	
for($z=0;$z<sizeof($array_keys_r); $z++)
{
$form_r[$array_keys_r[$z]] = strip_tags($form_r[$array_keys_r[$z]]);
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

//Check Email Address
$schools_label_sql = trim(addslashes($form_r[schools_label]));

$school_id_sql = trim(addslashes($_REQUEST[id]));

$search ="SELECT *
		  FROM sos_schools
		  WHERE schools_label = '$schools_label_sql' AND schools_type = '$schools_type_sql' AND id != '$school_id_sql'
		  LIMIT 1
		  ";

$find_duplicate=mysql_query($search, $db) or die(mysql_error());


if (mysql_num_rows($find_duplicate) ==1)
{
$reg_error[schools_label] = 'true';
}




if (is_array($error) || is_array($reg_error))
{
$reg_error[error] = 'true';
$content_fragment = './templates/'.$_SESSION[culture].'/form_schools_update.inc';
}

else
{
include('form_schools_update.inc');
}



?>