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
		FROM elcerrito_student_raw
		ORDER BY id ASC
		";
		
$exec_qry = mysql_query($qry, $db);


while($row=mysql_fetch_array($exec_qry))
{



$pointer_student_last_name = strrpos($row[Student], " ");
$pointer_student_first_name = strpos($row[Student], " ");

$first_name = substr($row[Student], 0, $pointer_student_first_name);
$last_name = substr($row[Student], $pointer_student_last_name);


$students_first_name_sql = addslashes(trim($first_name));
$students_last_name_sql = addslashes(trim($last_name));


//Parent 1
$pointer_p1_last_name = strrpos($row[Parent1], " ");
$pointer_p1_first_name = strpos($row[Parent1], " ");
$p1_first_name = substr($row[Parent1], 0, $pointer_p1_first_name);
$p1_last_name = substr($row[Parent1], $pointer_p1_last_name);

$p1_first_name_sql = addslashes(trim($p1_first_name));
$p1_last_name_sql = addslashes(trim($p1_last_name));

$p1_cell_sql = addslashes(trim($row[P1Cell]));
$p1_email_sql = addslashes(trim($row[P1Email]));
$p1_home_sql = addslashes(trim($row[HomePhone]));
$p1_work_sql = addslashes(trim($row[WorkNumber]));


//Parent 2
$pointer_p2_first_name = strpos($row[Parent2], " ");
$pointer_p2_last_name = strrpos($row[Parent2], " ");
$p2_first_name = substr($row[Parent2], 0, $pointer_p2_first_name);
$p2_last_name = substr($row[Parent2], $pointer_p2_last_name);
$p2_first_name_sql = addslashes(trim($p2_first_name));
$p2_last_name_sql = addslashes(trim($p2_last_name));

$p2_cell_sql = addslashes(trim($row[P2Cell]));
$p2_email_sql = addslashes(trim($row[P2Email]));
$p2_home_sql = addslashes(trim($row[HomePhone]));
$p2_work_sql = addslashes(trim($row[WorkNumber]));








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
		 '1',
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
`records_phone_work` ,
`records_date_created` ,
`records_status`
)
VALUES (
'', 
'1',   
'$p1_first_name_sql',  
'$p1_last_name_sql', 
'$p1_email_sql', 
'$p1_cell_sql', 
'$p1_home_sql', 
'$p1_work_sql', 
NOW(), 
'1'
)";
$exec_qryrecords = mysql_query($qry_insert_records, $db) or die (mysql_error());
$record_id1 = mysql_insert_id();



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
'$record_id1',
NOW(),
'P',  
'1'
)";

$exec_qry_contacts = mysql_query($qry_insert_contacts, $db) or die (mysql_error());


$qry_insert_records = "INSERT INTO  `softwebs_sosbeacon`.`sos_records` (
`id` ,
`records_school_id` ,
`records_first_name` ,
`records_last_name` ,
`records_email` ,
`records_phone_mobile` ,
`records_phone_home` ,
`records_phone_work` ,
`records_date_created` ,
`records_status`
)
VALUES (
'', 
'1',   
'$p2_first_name_sql',  
'$p2_last_name_sql', 
'$p2_email_sql', 
'$p2_cell_sql', 
'$p2_home_sql', 
'$p2_work_sql',
NOW(), 
'1'
)";

$exec_qryrecords = mysql_query($qry_insert_records, $db) or die (mysql_error());
$record_id2 = mysql_insert_id();

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
'$record_id2',
NOW(),
'S',  
'1'
)";

$exec_qry_contacts = mysql_query($qry_insert_contacts, $db) or die (mysql_error());





}




?>