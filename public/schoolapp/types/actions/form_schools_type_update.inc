<?

$schools_type_id_sql = trim(addslashes($_REQUEST[id]));
$schools_type_label_sql = trim(addslashes($form_r[schools_type_label]));
$schools_type_status_sql = trim(addslashes($form_r[schools_type_status]));

$qry_update = "UPDATE sos_schools_type
			   	SET 
			   	schools_type_label = '$schools_type_label_sql', 
			   	schools_type_status = '$schools_type_status_sql'
			   	WHERE id = '$schools_type_id_sql'
			   ";

$exec_qry_update = mysql_query($qry_update, $db) or die (mysql_error());

$_REQUEST['id'] = $_REQUEST['id'];
$reg_update['schools_type'] = 'true';

$content_fragment = './templates/'.$_SESSION[culture].'/form_schools_type_update.inc';
?>