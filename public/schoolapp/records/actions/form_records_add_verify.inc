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


//Check Student Records
$records_first_name_sql = trim(addslashes($form_r[records_first_name]));
$records_last_name_sql = trim(addslashes($form_r[records_last_name]));
$records_email_sql = trim(addslashes($form_r[records_email]));

$search ="SELECT *
		  FROM sos_records
		  WHERE records_first_name = '$records_first_name_sql' AND records_last_name = '$records_last_name_sql' AND records_email = '$records_email_sql'
		  LIMIT 1
		  ";

$find_duplicate=mysql_query($search, $db) or die(mysql_error());

if (mysql_num_rows($find_duplicate) ==1)
{
$reg_error[records_first_name] = 'true';
}


if(!valid_email($form_r[records_email])) 
{
$reg_error[records_email] = 'true';
}

if (is_array($error) || is_array($reg_error))
{
	//print_r ($error);
	
	//print_r ($reg_error());
	
	
$reg_error[error] = 'true';
$content_fragment = './templates/'.$_SESSION[culture].'/form_records_add.inc';
}

else
{
include('form_records_insert.inc');
}

}
?>