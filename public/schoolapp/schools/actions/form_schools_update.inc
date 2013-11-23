<?
$schools_id_sql = trim(addslashes($_REQUEST[id]));
$schools_label_sql = trim(addslashes($form_r[schools_label]));
$schools_type_sql = trim(addslashes($form_r[schools_type]));
$schools_status_sql = trim(addslashes($form_r[schools_status]));

$qry_update = "UPDATE sos_schools
			   	SET 
			   	schools_label = '$schools_label_sql', 
			   	schools_type = '$schools_type_sql', 
			   	schools_status = '$schools_status_sql'
			   	WHERE id = '$schools_id_sql'
			   ";

$exec_qry_update = mysql_query($qry_update, $db) or die (mysql_error());

$_REQUEST['id'] = $_REQUEST['id'];
$reg_update['schools'] = 'true';

$content_fragment = './templates/'.$_SESSION[culture].'/form_schools_update.inc';
?>