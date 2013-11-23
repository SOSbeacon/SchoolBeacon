<?
require_once("../config/config.inc");
$file_label_csv = date("m/d/Y");
$u_id = $_SESSION['u_id'];
$form = $_REQUEST[form];

$students_gender = $form[students_gender];
$students_status = $form[students_status];

$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = "WHERE sos_students.id !='' ";}
elseif($data_user['passports_type']=='2'){$toggle = "WHERE students_school_id = '$data_user[user_school_id]'";}

$student_gender_sql = trim(addslashes($students_gender));
$students_status_sql = trim(addslashes($students_status));


 $qry = "SELECT sos_students.*, DATE_FORMAT(students_dob, '%m/%d/%Y') AS students_dob, sos_schools.schools_label, sos_schools_type.schools_type_label
		FROM sos_students
		JOIN sos_schools ON sos_schools.id = sos_students.students_school_id
		JOIN sos_schools_type ON sos_schools_type.id = sos_schools.schools_type
		$toggle ";
		
		
if(!empty($students_gender)){ $qry .= " AND sos_students.students_gender = '$student_gender_sql'";}		
if(!empty($students_status)){ $qry .= " AND sos_students.students_status = '$students_status_sql'";}	
$qry .= "ORDER BY sos_students.students_last_name, sos_students.students_first_name
		";
//print $qry;
 
$exec_qry = mysql_query($qry, $db) or die(mysql_error());


$contentx = '"students_school","students_id","students_first_name","students_last_name","students_dob","students_gender","students_date_created","students_date_created_user_id","students_date_updated","students_date_updated_user_id","students_status"'."\n";

while($row = mysql_fetch_array($exec_qry))
{
$id = str_replace("\r\n", "", $row['id']);
$students_school_id = str_replace("\r\n", "", $row['schools_label']);
$students_label_id = str_replace("\r\n", "", $row['students_label_id']);
$students_first_name = str_replace("\r\n", "", $row['students_first_name']);
$students_last_name = str_replace("\r\n", "", $row['students_last_name']);
$students_dob = str_replace("\r\n", "", $row['students_dob']);
$students_gender = str_replace("\r\n", "", $row['students_gender']);
$students_date_created = str_replace("\r\n", "", $row['students_date_created']);
$students_date_created_user_id = str_replace("\r\n", "", $row['students_date_created_user_id']);
$students_date_updated = str_replace("\r\n", "", $row['students_date_updated']);
$students_date_updated_user_id = str_replace("\r\n", "", $row['students_date_updated_user_id']);
$students_status = str_replace("\r\n", "", $row['students_status']);
$created_by = user_data_name($db, $row['students_date_created_user_id']);
$updated_by = user_data_name($db, $row['students_date_updated_user_id']);
// data gathering 
$contentx .=  '"'.$students_school_id.'","'.$students_label_id.'","'.$students_first_name.'","'.$students_last_name.'","'.$students_dob.'","'.$students_gender.'","'.$students_date_created.'","'.$created_by[0][user_f_name].' '.$created_by[0][user_l_name].'","'.$students_date_updated.'","'.$updated_by[0][user_f_name].' '.$updated_by[0][user_l_name].'","'.$students_status.'"'."\n";
}
$file_name_label = 'Students_'.$file_label_csv;

// close file 
header("Content-type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=$file_name_label.csv"); 
echo("$contentx");
?>