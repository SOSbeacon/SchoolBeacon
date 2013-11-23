<?

$group_id_sql = trim(addslashes($_REQUEST[id]));
$group_school_id_sql = trim(addslashes($form_r[group_school_id]));
$group_name_sql = trim(addslashes($form_r[group_name]));
$group_type_sql = trim(addslashes($form_r[group_type]));
$group_teacher_sql = trim(addslashes($form_r[group_teacher]));
$group_date_updated_sql = 'NOW()';
$group_date_updated_user_id_sql = trim(addslashes($_SESSION[u_id]));
$group_notes_sql = trim(addslashes($form[group_notes]));
$group_status_sql = trim(addslashes($form_r[group_status]));

$qry_group_update = "UPDATE sos_groups
					SET
					`group_school_id` = '$group_school_id_sql',
					`group_name` = '$group_name_sql',
					`group_type` = '$group_type_sql',
					`group_teacher` = '$group_teacher_sql',
					`group_date_updated` = $group_date_updated_sql,
					`group_date_updated_user_id` = '$group_date_updated_user_id_sql',
					`group_notes` = '$group_notes_sql',
					`group_status` ='$group_status_sql'
					WHERE id = '$group_id_sql'
					";

$exec_qry_group_update = mysql_query($qry_group_update, $db) or die (mysql_error());

$_REQUEST['id'] = $_REQUEST['id'];

$reg_update['group'] = 'true';

$content_fragment = './templates/'.$_SESSION[culture].'/form_groups_update.inc';
?>