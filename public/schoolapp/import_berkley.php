<?
        $_CFG['dbType']             =   "mysql";           // view the abstraction layer for more possibilities
        $_CFG['dbHost']             =   "localhost";
        $_CFG['dbUser']             =   "softwebs_sos";
        $_CFG['dbPass']             =   "sos8252";
        $_CFG['dbName']             =   "softwebs_sosbeacon";
        $_CFG['dbPort']             =   "3306";


$mysql_server = $_CFG['dbHost'];
$user_name = $_CFG['dbUser'];
$password = $_CFG['dbPass'];
$database= $_CFG['dbName'];


$db = mysql_connect("$mysql_server","$user_name","$password");
@mysql_select_db($database, $db) or die(mysql_error());




$qry = "SELECT *
		FROM berkely_student_raw
		ORDER BY id ASC
		";
		
$exec_qry = mysql_query($qry, $db);


while($row=mysql_fetch_array($exec_qry))
{


$students_first_name_sql = addslashes(trim($row[StudentLastName]));
$students_last_name_sql = addslashes(trim($row[StudentFirstName]));

$pointer_last_name = strrpos($row[ParentName], " ");
$pointer_first_name = strpos($row[ParentName], " ");

$first_name = substr($row[ParentName], 0, $pointer_first_name);
$last_name = substr($row[ParentName], $pointer_last_name);

$records_first_name_sql = addslashes(trim($first_name));
$records_last_name_sql = addslashes(trim($last_name));
$records_phone_home_sql = addslashes(trim($row[HomePhoneNumber]));
$records_phone_mobile_sql = addslashes(trim($row[CellPhoneNumber]));
$records_email_sql = addslashes(trim($row[Email]));





if(!empty($row[StudentFirstName])){

$qry_insert_students = "INSERT 
		INTO sos_students
		(
		`id`, 
		`students_school_id`, 
		`students_first_name`, 
		`students_last_name`,  
		`students_date_created`, 
		`students_status`
		) 
		VALUES 
		(
		 '',
		 '3',
		 '$students_first_name_sql', 
		 '$students_last_name_sql',
		  NOW(),
		 '1'
		 )";
		 
		 
$exec_qry_students = mysql_query($qry_insert_students, $db) or die (mysql_error());
	
$student_id = mysql_insert_id();
			 
$qry_insert_records = "INSERT INTO  `softwebs_sosbeacon`.`sos_records` (
`id` ,
`records_school_id` ,
`records_first_name` ,
`records_last_name` ,
`records_email` ,
`records_phone_mobile` ,
`records_phone_home` ,
`records_date_created` ,
`records_status`
)
VALUES (
'', 
'3',   
'$records_first_name_sql',  
'$records_last_name_sql', 
'$records_email_sql', 
'$records_phone_mobile_sql', 
'$records_phone_home_sql', 
NOW(), 
'1'
)";
$exec_qryrecords = mysql_query($qry_insert_records, $db) or die (mysql_error());
$record_id = mysql_insert_id();

$qry_insert_contacts = "INSERT INTO  `sos_contacts` (
`id` ,
`contacts_student_id` ,
`contacts_record_id` ,
`contacts_date_created` ,
`contacts_type` ,
`contacts_status`
)
VALUES (
NULL ,  
'$student_id',  
'$record_id',
NOW(),
'P',  
'1'
)";

$exec_qry_contacts = mysql_query($qry_insert_contacts, $db) or die (mysql_error());
}else
{

$qry_insert_records = "INSERT INTO  `softwebs_sosbeacon`.`sos_records` (
`id` ,
`records_school_id` ,
`records_first_name` ,
`records_last_name` ,
`records_email` ,
`records_phone_mobile` ,
`records_phone_home` ,
`records_date_created` ,
`records_status`
)
VALUES (
'', 
'3',   
'$records_first_name_sql',  
'$records_last_name_sql', 
'$records_email_sql', 
'$records_phone_mobile_sql', 
'$records_phone_home_sql', 
NOW(), 
'1'
)";

$exec_qryrecords = mysql_query($qry_insert_records, $db) or die (mysql_error());
$record_id = mysql_insert_id();

$qry_insert_contacts = "INSERT INTO  `sos_contacts` (
`id` ,
`contacts_student_id` ,
`contacts_record_id` ,
`contacts_date_created` ,
`contacts_type` ,
`contacts_status`
)
VALUES (
NULL ,  
'$student_id',  
'$record_id',
NOW(),
'S',  
'1'
)";

$exec_qry_contacts = mysql_query($qry_insert_contacts, $db) or die (mysql_error());



}

}




?>