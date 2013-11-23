<?php

include('../functions/data_connect_aux.inc');
$$file_label_csv = date("m/d/Y");
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



$contentx = '<tr><td>CONTACT FIRST NAME</td><td>CONTACT LAST NAME</td><td>CONTACT EMAIL</td><td>CONTACT MOBILE PHONE</td><td>CONTACT WORK PHONE</td><td>CONTACT HOME PHONE</td><td>CONTACT STATUS</td><td>CONTACT TYPE</td><td>CONTACT RELATIONSHIP</td><td>STUDENT FIRST NAME</td><td>STUDENT LAST NAME</td></tr>';

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


$contentx .=  '<tr><td>'.$records_first_name.'</td><td>'.$records_last_name.'</td><td>'.$records_email.'</td><td>'.$records_phone_mobile.'</td><td>'.$records_phone_work.'</td><td>'.$records_phone_home.'</td><td>'.$records_status.'</td><td>'.$contacts_type.'</td><td>'.$contacts_relationship.'</td><td>'.$students_first_name.'</td><td>'.$students_last_name.'</td></tr>';



}
$file_name_label = 'Contacts_'.$file_label_csv;





$html =
"
<table>$contentx</table>
";
  $file_label = "test";


require('html2fpdf.php');
$pdf=new HTML2FPDF();
$pdf->AddPage();

$strContent = $html;
$pdf->WriteHTML($strContent);
$pdf->Output("sample.pdf");



?>