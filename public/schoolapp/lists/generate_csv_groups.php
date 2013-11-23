<?
require_once("../config/config.inc");
$file_label_csv = date("m/d/Y");
$u_id = $_SESSION['u_id'];
$form = $_REQUEST[form];

$group_type = $form[group_type];
$group_name = $form[group_name];
$group_teacher = $form[group_teacher];
$contacts_type = $form[contacts_type];


$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = " WHERE sos_schools.id = '$data_user[user_school_id]' ";}

$group_type_sql = trim(addslashes($group_type));
$group_name_sql = trim(addslashes($group_name));
$group_teacher_sql = trim(addslashes($group_teacher));

$qry = "SELECT sos_groups.*, sos_groups_type_codec.groups_type_label, sos_schools.schools_label, sos_schools_type.schools_type_label, sos_group_lists.lists_relationship, sos_students.students_label_id, sos_students.students_first_name, sos_students.students_last_name, sos_students.students_dob, sos_students.students_gender, sos_records.records_first_name, sos_records.records_last_name, sos_records.records_email, sos_records.records_phone_mobile, sos_records.records_phone_work, sos_records.records_phone_home, sos_contacts.contacts_type
		FROM sos_groups
		JOIN sos_groups_type_codec ON sos_groups.group_type = sos_groups_type_codec.id
		JOIN sos_group_lists ON sos_group_lists.lists_group_id = sos_groups.id
		JOIN sos_students ON sos_students.id = sos_group_lists.lists_student_id
		JOIN sos_schools ON sos_schools.id = sos_groups.group_school_id
		JOIN sos_schools_type ON sos_schools_type.id = sos_schools.schools_type
		LEFT JOIN sos_contacts ON sos_contacts.contacts_student_id = sos_students.id 
		LEFT JOIN sos_records ON sos_contacts.contacts_record_id = sos_records.id
		$toggle";
if(!empty($group_type)){ $qry .= "AND sos_groups.group_type = '$group_type_sql'";}		
if(!empty($group_name)){ $qry .= "AND sos_groups.id = '$group_name_sql'";}	
if(!empty($group_teacher)){ $qry .= "AND sos_groups.group_teacher LIKE '%$group_teacher_sql%'";}	
if(!empty($contacts_type)){ $qry .= "AND sos_records.records_status= '$contacts_type_sql'";}
$qry .= "GROUP BY sos_contacts.id
		 ORDER BY sos_groups.group_name
		 ";

//print $qry;
$exec_qry = mysql_query($qry, $db) or die(mysql_error());


$contentx = '"GROUP NAME","GROUP TYPE","SCHOOL","GROUP TEACHER","NOTES","STUDENT FIRST NAME","STUDENT LAST NAME","STUDENT DOB","STUDENT GENDER","CONTACT FIRST NAME","CONTACT LAST NAME","CONTACT EMAIL","CONTACT MOBILE PHONE","CONTACT WORK PHONE","CONTACT HOME PHONE","CONTAC TYPE"'."\n";

while($row = mysql_fetch_array($exec_qry))
{
$id = str_replace("\r\n", "", $row['id']);
$group_school_id= str_replace("\r\n", "", $row['group_school_id']);
$group_name= str_replace("\r\n", "", $row['group_name']);
$group_type = str_replace("\r\n", "", $row['group_type']);
$group_teacher = str_replace("\r\n", "", $row['group_teacher']);
$group_date_created = str_replace("\r\n", "", $row['group_date_created']);
$group_date_created_user_id = str_replace("\r\n", "", $row['group_date_created_user_id']);
$group_date_updated = str_replace("\r\n", "", $row['group_date_updated']);
$group_date_updated_user_id = str_replace("\r\n", "", $row['group_date_updated_user_id']);
$group_status = str_replace("\r\n", "", $row['group_status']);
$group_notes = str_replace("\r\n", "", $row['group_notes']);
$groups_type_label = str_replace("\r\n", "", $row['groups_type_label']);
$schools_label = str_replace("\r\n", "",  $row['schools_label']);
$schools_type_label = str_replace("\r\n", "",  $row['schools_type_label']);
$lists_relationship = str_replace("\r\n", "",  $row['lists_relationship']);
$students_label_id = str_replace("\r\n", "",  $row['students_label_id']);
$students_first_name = str_replace("\r\n", "",  $row['students_first_name']);
$students_last_name = str_replace("\r\n", "",  $row['students_last_name']);
$students_dob = str_replace("\r\n", "",  $row['students_dob']);
$students_gender = str_replace("\r\n", "",  $row['students_gender']);
$records_first_name = str_replace("\r\n", "",  $row['records_first_name']);
$records_last_name = str_replace("\r\n", "",  $row['records_last_name']);
$records_email = str_replace("\r\n", "",  $row['records_email']);
$records_phone_mobile = str_replace("\r\n", "",  $row['records_phone_mobile']);
$records_phone_work = str_replace("\r\n", "",  $row['records_phone_work']);
$records_phone_home = str_replace("\r\n", "",  $row['records_phone_home']);
$contacts_type = str_replace("\r\n", "",  $row['contacts_type']);


// data gathering 
$contentx .=  '"'.$group_name.'","'.$groups_type_label.'","'.$schools_label.'","'.$group_teacher.'","'.$group_notes.'","'.$students_first_name.'","'.$students_last_name.'","'.$students_dob.'","'.$students_gender.'","'.$records_first_name.'","'.$records_last_name.'","'.$records_email.'","'.$records_phone_mobile.'","'.$records_phone_work.'","'.$records_phone_home.'","'.$contacts_type.'"'."\n";
}
$file_name_label = 'Groups_'.$file_label_csv;

// close file 
header("Content-type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=$file_name_label.csv"); 
echo("$contentx");
?>