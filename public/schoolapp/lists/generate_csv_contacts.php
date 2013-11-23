<?
require_once("../config/config.inc");
$file_label_csv = date("m/d/Y");
$u_id = $_SESSION['u_id'];
$form = $_REQUEST[form];

$contacts_type = $form[contacts_type];
$contacts_status = $form[contacts_status];

$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = " WHERE records_school_id = '$data_user[user_school_id]'";}

$records_first_name_sql = trim(addslashes($records_first_name));
$records_last_name_sql = trim(addslashes($records_last_name));
$contacts_type_sql = trim(addslashes($contacts_type));
$contacts_status_sql = trim(addslashes($contacts_status));

$qry = "SELECT sos_records.*, DATE_FORMAT(records_date_created, '%m/%d/%Y') AS records_date_created, sos_contacts.id AS contact_id, sos_contacts.contacts_type, sos_contacts.contacts_relationship, sos_students.students_first_name, sos_students.students_last_name
		FROM sos_records
		JOIN sos_contacts ON sos_contacts.contacts_record_id = sos_records.id
		LEFT JOIN sos_students ON sos_students.id = sos_contacts.contacts_student_id
		$toggle";
if(!empty($records_first_name)){ $qry .= "AND sos_records.records_first_name LIKE '%$records_first_name_sql%'";}		
if(!empty($records_last_name)){ $qry .= "AND sos_records.records_last_name LIKE '%$records_last_name_sql%'";}	
if(!empty($contacts_type)){ $qry .= "AND sos_contacts.contacts_type = '$contacts_type_sql'";}	
if(!empty($contacts_status)){ $qry .= "AND sos_contacts.contacts_status = '$contacts_status_sql'";}	
//$qry .= "GROUP By sos_contacts.id
$qry .= "GROUP By sos_records.records_last_name, sos_records.records_first_name

		ORDER BY sos_records.records_last_name, sos_records.records_first_name
		
		";
 //print $qry;
$exec_qry = mysql_query($qry, $db) or die(mysql_error());



$contentx = '"CONTACT FIRST NAME","CONTACT LAST NAME","CONTACT EMAIL","CONTACT MOBILE PHONE","CONTACT WORK PHONE","CONTACT HOME PHONE","CONTACT STATUS","CONTACT TYPE","CONTACT RELATIONSHIP","STUDENT FIRST NAME","STUDENT LAST NAME"'."\n";

while($row = mysql_fetch_array($exec_qry))
{

$records_first_name = str_replace("\r\n", "", $row['records_first_name']);
$records_last_name = str_replace("\r\n", "", $row['records_last_name']);
$records_email = str_replace("\r\n", "", $row['records_email']);
$records_phone_mobile = str_replace("\r\n", "", $row['records_phone_mobile']);
$records_phone_work = str_replace("\r\n", "", $row['records_phone_work']);
$records_phone_home = str_replace("\r\n", "", $row['records_phone_home']);
$records_status = str_replace("\r\n", "", $row['records_status']);

$contact_id = str_replace("\r\n", "", $row['contact_id']);
$contacts_type = str_replace("\r\n", "", $row['contacts_type']);
$contacts_relationship = str_replace("\r\n", "", $row['contacts_relationship']);

$students_first_name = str_replace("\r\n", "",$row['students_first_name']);
$students_last_name = str_replace("\r\n", "",$row['students_last_name']);


// data gathering 
$contentx .=  '"'.$records_first_name.'","'.$records_last_name.'","'.$records_email.'","'.$records_phone_mobile.'","'.$records_phone_work.'","'.$records_phone_home.'","'.$records_status.'","'.$contacts_type.'","'.$contacts_relationship.'","'.$students_first_name.'","'.$students_last_name.'"'."\n";
}
$file_name_label = 'Contacts_'.$file_label_csv;

// close file 
header("Content-type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=$file_name_label.csv"); 
echo("$contentx");
?>