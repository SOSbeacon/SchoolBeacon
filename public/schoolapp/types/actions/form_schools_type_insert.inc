<?
$schools_type_label_sql = trim(addslashes($form_r[schools_type_label]));
$schools_type_status_sql = trim(addslashes($form_r[schools_type_status]));

$qry_schools_insert = "INSERT INTO `sos_schools_type` (
`id`, 
`schools_type_label`, 
`schools_type_status`
)

VALUES (
'NULL',
'$schools_type_label_sql', 
'$schools_type_status_sql')
";

$exec_qry_schools_insert= mysql_query($qry_schools_insert, $db) or die(mysql_error());

$school_type_id_sql = mysql_insert_id();



$reg_insert['schools_type']=true;

$_REQUEST[id]=$school_type_id_sql;

$content_fragment = './templates/'.$_SESSION[culture].'/form_schools_type_update.inc';
?>