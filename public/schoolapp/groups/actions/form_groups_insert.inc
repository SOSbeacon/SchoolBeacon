<?
$group_school_id_sql = trim(addslashes($form_r[group_school_id]));
$group_name_sql = trim(addslashes($form_r[group_name]));
$group_type_sql = trim(addslashes($form_r[group_type]));
$group_teacher_sql = trim(addslashes($form_r[group_teacher]));
$group_date_created_sql = 'NOW()';
$group_date_created_user_id_sql = trim(addslashes($_SESSION[u_id]));
$group_date_updated_sql = 'NOW()';
$group_date_updated_user_id_sql = trim(addslashes($_SESSION[u_id]));
$group_notes_sql = trim(addslashes($form[group_notes]));
$group_status_sql = trim(addslashes($form_r[group_status]));

$qry_group_insert = "INSERT INTO sos_groups (
`id` ,
`group_school_id` ,
`group_name` ,
`group_type` ,
`group_teacher` ,
`group_date_created` ,
`group_date_created_user_id` ,
`group_date_updated` ,
`group_date_updated_user_id` ,
`group_status`,
`group_notes`
)
VALUES (
NULL,
'$group_school_id_sql',
'$group_name_sql',
'$group_type_sql',
'$group_teacher_sql',
 $group_date_created_sql,
'$group_date_created_user_id_sql',
 $group_date_updated_sql,
'$group_date_updated_user_id_sql',
'$group_status_sql',
'$group_notes_sql'
);
";

$exec_qry_group_insert= mysql_query($qry_group_insert, $db) or die(mysql_error());

$group_id_sql = mysql_insert_id();


$reg_insert['group']=true;

$_REQUEST[id]=$group_id_sql;

$content_fragment = './templates/'.$_SESSION[culture].'/form_groups_update.inc';
?>