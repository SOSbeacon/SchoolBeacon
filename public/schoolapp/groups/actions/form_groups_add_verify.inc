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
$group_name_sql = trim(addslashes($form_r[group_name]));
$group_type_sql = trim(addslashes($form_r[group_type]));
$group_teacher_sql = trim(addslashes($form_r[group_teacher]));

$search ="SELECT *
		  FROM sos_groups
		  WHERE group_name = '$group_name_sql' AND group_type = '$group_type_sql' AND group_teacher = '$group_teacher_sql'
		  LIMIT 1
		  ";

$find_duplicate=mysql_query($search, $db) or die(mysql_error());

if (mysql_num_rows($find_duplicate) ==1)
{
$reg_error[group_name] = 'true';
}


if(!isset($form_r[group_type]) || empty($form_r[group_type]))
{
$error[group_type] = true;
}

if (is_array($error) || is_array($reg_error))
{
	//print_r ($error);
	
	//print_r ($reg_error());
	
	
$reg_error[error] = 'true';
$content_fragment = './templates/'.$_SESSION[culture].'/form_groups_add.inc';
}

else
{
include('form_groups_insert.inc');
}

}
?>