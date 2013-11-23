<?
$schools_label_sql = trim(addslashes($form_r[schools_label]));
$schools_type_sql = trim(addslashes($form_r[schools_type]));
$schools_status_sql = trim(addslashes($form_r[schools_status]));

$qry_schools_insert = "INSERT INTO `sos_schools` (
`id`, 
`schools_label`, 
`schools_type`,
`schools_status`
)

VALUES (
'NULL',
'$schools_label_sql', 
'$schools_type_sql',
'$schools_status_sql')
";

$exec_qry_schools_insert= mysql_query($qry_schools_insert, $db) or die(mysql_error());

$school_id_sql = mysql_insert_id();



$reg_insert['schools']=true;

$_REQUEST[id]=$school_id_sql;

$content_fragment = './templates/'.$_SESSION[culture].'/form_schools_update.inc';
?>