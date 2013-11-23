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
$lists_group_id_sql = trim(addslashes($_REQUEST[id]));
$lists_student_id_sql = trim(addslashes($form_r[lists_student_id]));

$search ="SELECT *
		  FROM sos_group_lists
		  WHERE lists_student_id = '$lists_student_id_sql' AND lists_group_id = '$lists_group_id_sql'
		  LIMIT 1
		  ";

$find_duplicate=mysql_query($search, $db) or die(mysql_error());

if (mysql_num_rows($find_duplicate) ==1)
{
$reg_error[duplicate_lists] = 'true';
}



if (is_array($error) || is_array($reg_error))
{
$reg_error[error] = 'true';
$content_fragment = './templates/'.$_SESSION[culture].'/form_groups_update.inc';
}

else
{
include('form_lists_insert.inc');
}

}
?>