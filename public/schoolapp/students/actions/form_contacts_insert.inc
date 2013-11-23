<?
$contacts_student_id_sql = trim(addslashes($_REQUEST[id]));
$contacts_record_id_sql = trim(addslashes($form_r[contacts_record_id]));
$contacts_date_created_sql = 'NOW()';
$contacts_date_created_user_id_sql = trim(addslashes($_SESSION[u_id]));
$contacts_date_updated_sql = 'NOW()';
$contacts_date_updated_user_id_sql = trim(addslashes($_SESSION[u_id]));
$contacts_relationship_sql = trim(addslashes($form_r[contacts_relationship]));
$contacts_type_sql = trim(addslashes($form_r[contacts_type]));
$contacts_status_sql = trim(addslashes($form_r[contacts_status]));

$qry_contacts_insert = "INSERT INTO sos_contacts (
`id` ,
`contacts_student_id` ,
`contacts_record_id` ,
`contacts_date_created` ,
`contacts_date_created_user_id` ,
`contacts_date_updated` ,
`contacts_date_updated_user_id` ,
`contacts_relationship` ,
`contacts_type` ,
`contacts_status`
)
VALUES (
NULL,
'$contacts_student_id_sql',
'$contacts_record_id_sql',
 $contacts_date_created_sql,
'$contacts_date_created_user_id_sql',
 $contacts_date_updated_sql,
'$contacts_date_updated_user_id_sql',
'$contacts_relationship_sql',
'$contacts_type_sql',
'$contacts_status_sql'
);
";

$exec_qry_contacts_insert= mysql_query($qry_contacts_insert, $db) or die(mysql_error());

$contact_id_sql = mysql_insert_id();


$reg_insert['record']=true;

$_REQUEST[id]=$_REQUEST[id];

$content_fragment = './templates/'.$_SESSION[culture].'/form_students_update.inc';
?>