<?
$lists_group_id_sql = trim(addslashes($_REQUEST[id]));
$lists_student_id_sql = trim(addslashes($form_r[lists_student_id]));
$lists_date_created_sql = 'NOW()';
$lists_date_created_user_id_sql = trim(addslashes($_SESSION[u_id]));
$lists_date_updated_sql = 'NOW()';
$lists_date_updated_user_id_sql = trim(addslashes($_SESSION[u_id]));
$lists_relationship_sql = trim(addslashes($form_r[lists_relationship]));
$lists_status_sql = trim(addslashes($form_r[lists_status]));

$qry_lists_insert = "INSERT INTO sos_group_lists (
`id` ,
`lists_group_id` ,
`lists_student_id` ,
`lists_date_created` ,
`lists_date_created_user_id` ,
`lists_date_updated` ,
`lists_date_updated_user_id` ,
`lists_relationship` ,
`lists_status`
)
VALUES (
NULL,
'$lists_group_id_sql',
'$lists_student_id_sql',
 $lists_date_created_sql,
'$lists_date_created_user_id_sql',
 $lists_date_updated_sql,
'$lists_date_updated_user_id_sql',
'$lists_relationship_sql',
'$lists_status_sql'
);
";

$exec_qry_lists_insert= mysql_query($qry_lists_insert, $db) or die(mysql_error());

$contact_id_sql = mysql_insert_id();


$reg_insert['lists']=true;

$_REQUEST[id]=$_REQUEST[id];

$content_fragment = './templates/'.$_SESSION[culture].'/form_groups_update.inc';
?>