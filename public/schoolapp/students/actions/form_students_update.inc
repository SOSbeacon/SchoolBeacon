<?
$mm = substr($form_r[students_dob],0,2);
$dd = substr($form_r[students_dob],3,2);
$yyyy = substr($form_r[students_dob],6,4);
$students_id_sql = trim(addslashes($_REQUEST[id]));
$students_school_id_sql = trim(addslashes($form_r[students_school_id]));
$students_label_id_sql = trim(addslashes($form_r[students_label_id]));
$students_first_name_sql = trim(addslashes($form_r[students_first_name]));
$students_last_name_sql = trim(addslashes($form_r[students_last_name]));
$students_dob_sql = trim(addslashes($yyyy.'-'.$mm.'-'.$dd));
$students_gender_sql = trim(addslashes($form_r[students_gender]));
$students_date_created_sql = 'NOW()';
$students_date_created_user_id_sql = trim(addslashes($_SESSION[u_id]));
$students_date_updated_sql = 'NOW()';
$students_date_updated_user_id_sql = trim(addslashes($_SESSION[u_id]));
$students_status_sql = trim(addslashes($form_r[students_status]));

$qry_user_update = "UPDATE sos_students
					SET
					`students_school_id` = '$students_school_id_sql',
					`students_label_id` = '$students_label_id_sql',
					`students_first_name` = '$students_first_name_sql',
					`students_last_name` = '$students_last_name_sql',
					`students_dob` = '$students_dob_sql',
					`students_gender` = '$students_gender_sql',
					`students_date_updated` = $students_date_updated_sql,
					`students_date_updated_user_id` = '$students_date_updated_user_id_sql',
					`students_status` ='$students_status_sql'
					WHERE id = '$students_id_sql'
					";

$exec_qry_user_update = mysql_query($qry_user_update, $db) or die (mysql_error());

$_REQUEST['id'] = $_REQUEST['id'];

$reg_update['student'] = 'true';

$content_fragment = './templates/'.$_SESSION[culture].'/form_students_update.inc';
?>