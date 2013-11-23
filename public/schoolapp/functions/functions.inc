<?
//////////////////////////Session Functions/////////////////
function session_admin($u_type='')
{
global $_CFG;

switch ($u_type) {
    case "0":
        $access =  "no";
        break;	
    case "1":
        $access =  "yes";
        break;
    case "2":
        $access =  "no";
        break;
    default:
       $access =  "no";
        break;
}

if($access=='no'){HEADER("LOCATION: $_CFG[rootURL]login/");}
}

function session_system_user($u_type='')
{
global $_CFG;

switch ($u_type) {
    case "0":
        $access =  "no";
        break;	
    case "1":
        $access =  "yes";
        break;
    case "2":
        $access =  "yes";
        break;
    default:
       $access =  "no";
        break;
}

if($access=='no'){HEADER("LOCATION: $_CFG[rootURL]login/");}
}

//////////////////////////Format Functions/////////////////
function valid_email ($email) {

if (eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$", $email, $check)) {

return TRUE;}

return FALSE;}

function checkDateFormat($date)
{
//match the format of the date
if (preg_match ("/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/", $date, $parts))
{
//check weather the date is valid of not

if(checkdate($parts[1],$parts[2],$parts[3]))
return true;
else
return false;
}
else
return false;
}

//////////////////////////Image Uploads/////////////////
function image_upload($userfile) 

{

global $_CFG;

global $new_file;

global $FileDst;

global $new_array;
global $_FILES;


//Get File Extension

$new_string = implode ("," , $_FILES[userfile]);

$new_array=explode(",", $new_string);



$FileDst=$new_array[0];

 $FileCutted = strrchr($new_array[0], '.');
//print ' - ';
 $FileDst=date('dmyHis').$FileCutted;

$new_file = '/home/globegno/public_html/assets/original/'.$FileDst;



if (!copy($userfile, $new_file)) echo "failed to copy file $userfile to $new_file\n"; 

return true; 

}

//////////////////////////Directory Data/////////////////
function explodePathInfo($server='') {

	global $url_array;
	global $default_page;

	$url_array = array();
	$num       = null;
	$array     = array();

	$array     = explode('/',$server);
	$num       = count($array);

	for ($i=1; $i<$num; $i++) {
		$url_array[$i] = $array[$i];
	}

	if (!$url_array[2]) {
		$url_array[2] = $default_page;
	}
	return $url_array;
}

//////////////////////////Utilities/////////////////
function generatePassword($length=9, $strength=0) {
    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEUY";
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }

    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $password;
}

function display_1($display_1)
{
switch ($display_1) {
    case 'main':
   $display_var = "Front Desk";
        break;
    case 'admin':
        $display_var = "Administration";
        break;
    case 'foo':
        $display_var = "Foo";
        break;
    default:
       $display_var = 'Hotel Net';
}

return $display_var;
}
	
function display_2($display_2)
{
switch ($display_2) {
    case 'dashboard':
   $display_var = "Dashboard";
        break;
    case 'students':
        $display_var = "Students";
        break;
    case 'records':
        $display_var = "Records";
        break;
    case 'groups':
        $display_var = "Groups";
        break;
    case 'print':
        $display_var = "Print";
        break; 
    case 'lists':
        $display_var = "Group Lists";
        break;
    case 'account':
        $display_var = "My Account";
        break;
    case 'login':
        $display_var = "User Login";
        break;                
    case 'password':
        $display_var = "Reset Password";
        break;                 
    default:
       $display_var = 'Dashboard';
}
return $display_var;
}	

////////////ADMIN DATA BY PRIMARY KEY
function user_data($db='', $id='')

{

 global $user_data;

 $qry_property_admin_data = "SELECT sos_users.*, sos_schools.schools_label, sos_passports.passports_name, sos_passports.passports_email, sos_passports.passports_type
				FROM sos_users
				JOIN sos_schools ON sos_schools.id = sos_users.user_school_id
				JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
				WHERE sos_users.id = '$id'
				LIMIT 1
					  ";

$qry_property_admin_data = mysql_query($qry_property_admin_data, $db) or die(mysql_error());

$property_admin_data = mysql_fetch_array($qry_property_admin_data);

$user_data[0]['id'] = $property_admin_data['id'];

$user_data[0]['user_f_name'] = $property_admin_data['user_f_name'];

$user_data[0]['user_l_name'] = $property_admin_data['user_l_name'];

$user_data[0]['user_title'] = $property_admin_data['user_title'];

$user_data[0]['user_school_id'] = $property_admin_data['user_school_id'];

$user_data[0]['user_phone_primary']	= $property_admin_data['user_phone_primary'];

$user_data[0]['user_fax'] = $property_admin_data['user_fax'];

$user_data[0]['user_email'] = $property_admin_data['user_email'];

$user_data[0]['user_address'] = $property_admin_data['user_address'];

$user_data[0]['user_city'] = $property_admin_data['user_city'];

$user_data[0]['user_state'] = $property_admin_data['user_state'];

$user_data[0]['user_zip'] = $property_admin_data['user_zip'];

$user_data[0]['user_comments'] = $property_admin_data['user_comments'];

$user_data[0]['user_active'] = $property_admin_data['user_active'];

$user_data[0]['schools_label'] = $property_admin_data['schools_label'];

$user_data[0]['passports_name'] = $property_admin_data['passports_name'];

$user_data[0]['passports_email'] = $property_admin_data['passports_email'];

$user_data[0]['passports_type'] = $property_admin_data['passports_type'];
}

function school_data($db='', $id='')

{

 global $school_data;

$qry_schools_data = "SELECT sos_schools.*, sos_schools_type.schools_type_label
				FROM sos_schools
				JOIN sos_schools_type ON sos_schools_type.id = sos_schools.schools_type
				WHERE sos_schools.id = '$id'
				LIMIT 1
					  ";

$exec_qry_schools_data = mysql_query($qry_schools_data, $db) or die(mysql_error());

$school_data_fetch = mysql_fetch_array($exec_qry_schools_data);

$school_data[0]['id'] = $school_data_fetch['id'];

$school_data[0]['schools_label'] = $school_data_fetch['schools_label'];

$school_data[0]['schools_type_label'] = $school_data_fetch['schools_type_label'];

$school_data[0]['schools_type'] = $school_data_fetch['schools_type'];

$school_data[0]['schools_status'] = $school_data_fetch['schools_status'];

}

function school_type_data($db='', $id='')

{

 global $school_type_data;

$qry_schools_type_data = "SELECT sos_schools_type.*
				FROM sos_schools_type
				WHERE sos_schools_type.id = '$id'
				LIMIT 1
					  ";

$exec_qry_schools_type_data = mysql_query($qry_schools_type_data, $db) or die(mysql_error());

$school_type_data_fetch = mysql_fetch_array($exec_qry_schools_type_data);

$school_type_data[0]['id'] = $school_type_data_fetch['id'];

$school_type_data[0]['schools_type_label'] = $school_type_data_fetch['schools_type_label'];

$school_type_data[0]['schools_type_status'] = $school_type_data_fetch['schools_type_status'];

}

////////////SYSTEM USER DATA BY PRIMARY KEY
function student_data($db='', $id='')

{

 global $student_data;

 $qry_student_data = "SELECT sos_students.*, DATE_FORMAT(students_dob, '%m/%d/%Y') AS students_dob, sos_schools.schools_label, sos_schools_type.schools_type_label
				FROM sos_students
				JOIN sos_schools ON sos_schools.id = sos_students.students_school_id
				JOIN sos_schools_type ON sos_schools_type.id = sos_schools.schools_type
				WHERE sos_students.id = '$id'
				LIMIT 1
					  ";

$exec_qry_student_data = mysql_query($qry_student_data, $db) or die(mysql_error());

$qry_student_data = mysql_fetch_array($exec_qry_student_data);

$student_data[0]['id'] = $qry_student_data['id'];

$student_data[0]['students_school_id'] = $qry_student_data['students_school_id'];
$student_data[0]['students_label_id'] = $qry_student_data['students_label_id'];
$student_data[0]['students_first_name'] = $qry_student_data['students_first_name'];
$student_data[0]['students_last_name'] = $qry_student_data['students_last_name'];
$student_data[0]['students_dob'] = $qry_student_data['students_dob'];
$student_data[0]['students_gender'] = $qry_student_data['students_gender'];
$student_data[0]['students_date_created'] = $qry_student_data['students_date_created'];
$student_data[0]['students_date_created_user_id'] = $qry_student_data['students_date_created_user_id'];
$student_data[0]['students_date_updated'] = $qry_student_data['students_date_updated'];
$student_data[0]['students_date_updated_user_id'] = $qry_student_data['students_date_updated_user_id'];
$student_data[0]['students_status'] = $qry_student_data['students_status'];
$student_data[0]['schools_label'] = $qry_student_data['schools_label'];
$student_data[0]['schools_type_label'] = $qry_student_data['schools_type_label'];
}

function user_data_name($db='', $id='')

{



 $qry_property_admin_data = "SELECT sos_users.*, sos_schools.schools_label, sos_passports.passports_name, sos_passports.passports_email, sos_passports.passports_type
				FROM sos_users
				JOIN sos_schools ON sos_schools.id = sos_users.user_school_id
				JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
				WHERE sos_users.id = '$id'
				LIMIT 1
					  ";

$exec_qry_property_admin_data = mysql_query($qry_property_admin_data, $db) or die(mysql_error());

$property_admin_data = mysql_fetch_array($exec_qry_property_admin_data);

$user_data_name[0]['id'] = $property_admin_data['id'];

$user_data_name[0]['user_f_name'] = $property_admin_data['user_f_name'];

$user_data_name[0]['user_l_name'] = $property_admin_data['user_l_name'];

$user_data_name[0]['user_title'] = $property_admin_data['user_title'];

$user_data_name[0]['user_school_id'] = $property_admin_data['user_school_id'];

$user_data_name[0]['user_phone_primary']	= $property_admin_data['user_phone_primary'];

$user_data_name[0]['user_fax'] = $property_admin_data['user_fax'];

$user_data_name[0]['user_email'] = $property_admin_data['user_email'];

$user_data_name[0]['user_address'] = $property_admin_data['user_address'];

$user_data_name[0]['user_city'] = $property_admin_data['user_city'];

$user_data_name[0]['user_state'] = $property_admin_data['user_state'];

$user_data_name[0]['user_zip'] = $property_admin_data['user_zip'];

$user_data_name[0]['user_comments'] = $property_admin_data['user_comments'];

$user_data_name[0]['user_active'] = $property_admin_data['user_active'];

$user_data_name[0]['schools_label'] = $property_admin_data['schools_label'];

$user_data_name[0]['passports_name'] = $property_admin_data['passports_name'];

$user_data_name[0]['passports_email'] = $property_admin_data['passports_email'];

$user_data_name[0]['passports_type'] = $property_admin_data['passports_type'];

return $user_data_name;

}

function record_data($db='', $id='')
{

 global $record_data;

 $qry_record_data = "SELECT sos_records.*
					 FROM sos_records
					 WHERE sos_records.id = '$id'
					 LIMIT 1
					 ";

$exec_qry_record_data = mysql_query($qry_record_data, $db) or die(mysql_error());

$qry_record_data = mysql_fetch_array($exec_qry_record_data);

$record_data[0]['id'] = $qry_record_data['id'];
$record_data[0]['records_school_id'] = $qry_record_data['records_school_id'];
$record_data[0]['records_first_name'] = $qry_record_data['records_first_name'];
$record_data[0]['records_last_name'] = $qry_record_data['records_last_name'];
$record_data[0]['records_email'] = $qry_record_data['records_email'];
$record_data[0]['records_phone_mobile'] = $qry_record_data['records_phone_mobile'];
$record_data[0]['records_phone_work'] = $qry_record_data['records_phone_work'];
$record_data[0]['records_phone_home'] = $qry_record_data['records_phone_home'];
$record_data[0]['records_date_created'] = $qry_record_data['records_date_created'];
$record_data[0]['records_date_created_user_id'] = $qry_record_data['records_date_created_user_id'];
$record_data[0]['records_date_updated'] = $qry_record_data['records_date_updated'];
$record_data[0]['records_date_updated_user_id'] = $qry_record_data['records_date_updated_user_id'];
$record_data[0]['records_status'] = $qry_record_data['records_status'];
}

function group_data($db='', $id='')

{

 global $group_data;

 $qry_group =   "SELECT sos_groups.*, sos_schools.schools_label, sos_schools_type.schools_type_label
						FROM sos_groups
						JOIN sos_schools ON sos_schools.id = sos_groups.group_school_id
						JOIN sos_schools_type ON sos_schools_type.id = sos_schools.schools_type
						WHERE sos_groups.id = '$id'
						LIMIT 1
					   ";

$exec_qry_group = mysql_query($qry_group, $db) or die(mysql_error());

$qry_group_data = mysql_fetch_array($exec_qry_group);

$group_data[0]['id'] = $qry_group_data['id'];
$group_data[0]['group_school_id'] = $qry_group_data['group_school_id'];
$group_data[0]['group_name'] = $qry_group_data['group_name'];
$group_data[0]['group_type'] = $qry_group_data['group_type'];
$group_data[0]['group_teacher'] = $qry_group_data['group_teacher'];
$group_data[0]['group_date_created'] = $qry_group_data['group_date_created'];
$group_data[0]['group_date_created_user_id'] = $qry_group_data['group_date_created_user_id'];
$group_data[0]['group_date_updated'] = $qry_group_data['group_date_updated'];
$group_data[0]['group_date_updated_user_id'] = $qry_group_data['group_date_updated_user_id'];
$group_data[0]['group_status'] = $qry_group_data['group_status'];
$group_data[0]['group_notes'] = $qry_group_data['group_notes'];
$group_data[0]['schools_label'] = $qry_group_data['schools_label'];
$group_data[0]['schools_type_label'] = $qry_group_data['schools_type_label'];


}

////ADMIN ARRAYS
function array_users($db='', $culture='', $u_id='', $u_type, $permissions='', $active='')
{



$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = "AND users_school_id = '$data_user[users_property_id]'";}



$qry = "SELECT sos_users.*, sos_schools.schools_label, sos_passports.passports_name, sos_passports.passports_email
		FROM sos_users
		JOIN sos_schools ON sos_schools.id = sos_users.user_school_id
		JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
		WHERE sos_passports.passports_type NOT IN ('1') $toggle 
		ORDER BY sos_users.id
		";
 
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_users[$i]['id'] = $row['id'];
$array_users[$i]['user_f_name'] = $row['user_f_name'];
$array_users[$i]['user_l_name'] = $row['user_l_name']; 
$array_users[$i]['user_title'] = $row['user_title']; 
$array_users[$i]['user_school_id'] = $row['user_school_id']; 
$array_users[$i]['user_phone_primary'] = $row['user_phone_primary']; 	
$array_users[$i]['user_fax'] = $row['user_fax']; 	
$array_users[$i]['user_email'] = $row['user_email']; 	
$array_users[$i]['user_address'] = $row['user_address']; 	
$array_users[$i]['user_city'] = $row['user_city']; 	
$array_users[$i]['user_state'] = $row['user_state']; 	
$array_users[$i]['user_zip'] = $row['user_zip']; 	
$array_users[$i]['user_comments'] = $row['user_comments']; 	
$array_users[$i]['user_active'] = $row['user_active']; 	
$array_users[$i]['schools_label'] = $row['schools_label']; 	
$array_users[$i]['passports_name'] = $row['passports_name']; 
$array_users[$i]['passports_email'] = $row['passports_email']; 
$i++;
}

return $array_users;
}

function array_schools($db='', $culture='', $u_id='', $u_type, $permissions='', $active='')
{



$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = "AND users_property_id = '$data_user[users_property_id]'";}



$qry = "SELECT sos_schools.*, sos_schools_type.schools_type_label 
		FROM sos_schools
		JOIN sos_schools_type  ON sos_schools.schools_type = sos_schools_type.id
		ORDER BY sos_schools_type.schools_type_label 
		";
 
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_schools[$i]['id'] = $row['id'];
$array_schools[$i]['schools_label'] = $row['schools_label'];
$array_schools[$i]['schools_type'] = $row['schools_type']; 
$array_schools[$i]['schools_status'] = $row['schools_status'];
$array_schools[$i]['schools_type_label'] = $row['schools_type_label'];
$i++;
}

return $array_schools;
}

function array_schools_type($db='', $culture='', $u_id='', $u_type, $permissions='', $active='')
{



$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = "AND users_property_id = '$data_user[users_property_id]'";}



$qry = "SELECT sos_schools_type.*
		FROM sos_schools_type
		ORDER BY sos_schools_type.schools_type_label
		";
 
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_schools_type[$i]['id'] = $row['id'];
$array_schools_type[$i]['schools_type_label'] = $row['schools_type_label'];
$array_schools_type[$i]['schools_type_status'] = $row['schools_type_status']; 
$i++;
}

return $array_schools_type;
}

////////////SYSTEM USER ARRAYS
function array_students($db='', $culture='', $u_id='', $u_type, $permissions='', $active='')
{



$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = "WHERE students_school_id = '$data_user[user_school_id]'";}



$qry = "SELECT sos_students.*, DATE_FORMAT(students_dob, '%m/%d/%Y') AS students_dob, sos_schools.schools_label, sos_schools_type.schools_type_label
		FROM sos_students
		JOIN sos_schools ON sos_schools.id = sos_students.students_school_id
		JOIN sos_schools_type ON sos_schools_type.id = sos_schools.schools_type
		$toggle 
		ORDER BY sos_students.students_last_name, sos_students.students_first_name
		";
 
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_students[$i]['id'] = $row['id'];
$array_students[$i]['students_school_id'] = $row['students_school_id'];
$array_students[$i]['students_label_id'] = $row['students_label_id'];
$array_students[$i]['students_first_name'] = $row['students_first_name'];
$array_students[$i]['students_last_name'] = $row['students_last_name'];
$array_students[$i]['students_dob'] = $row['students_dob'];
$array_students[$i]['students_gender'] = $row['students_gender'];
$array_students[$i]['students_date_created'] = $row['students_date_created'];
$array_students[$i]['students_date_created_user_id'] = $row['students_date_created_user_id'];
$array_students[$i]['students_date_updated'] = $row['students_date_updated'];
$array_students[$i]['students_date_updated_user_id'] = $row['students_date_updated_user_id'];
$array_students[$i]['students_status'] = $row['students_status'];
$array_students[$i]['schools_label'] = $row['schools_label'];
$array_students[$i]['schools_type_label'] = $row['schools_type_label'];
$i++;
}

return $array_students;
}

function array_records($db='', $culture='', $u_id='', $u_type, $permissions='', $active='')
{



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



$qry = "SELECT sos_records.*, DATE_FORMAT(records_date_created, '%m/%d/%Y') AS records_date_created
		FROM sos_records
		$toggle 
		GROUP By sos_records.id
		ORDER BY sos_records.records_last_name, sos_records.records_first_name
		
		";
 
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_records[$i]['id'] = $row['id'];
$array_records[$i]['records_school_id'] = $row['records_school_id'];
$array_records[$i]['records_first_name'] = $row['records_first_name'];
$array_records[$i]['records_last_name'] = $row['records_last_name'];
$array_records[$i]['records_email'] = $row['records_email'];
$array_records[$i]['records_phone_mobile'] = $row['records_phone_mobile'];
$array_records[$i]['records_phone_work'] = $row['records_phone_work'];
$array_records[$i]['records_phone_home'] = $row['records_phone_home'];
$array_records[$i]['records_date_created'] = $row['records_date_created'];
$array_records[$i]['records_date_created_user_id'] = $row['records_date_created_user_id'];
$array_records[$i]['records_date_updated'] = $row['records_date_updated'];
$array_records[$i]['records_date_updated_user_id'] = $row['records_date_updated_user_id'];
$array_records[$i]['records_status'] = $row['records_status'];
$i++;
}

return $array_records;
}

function array_contacts($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $student_id='')
{



$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = " AND students_school_id = '$data_user[user_school_id]'";}

$qry = "SELECT sos_contacts.*, sos_records.id AS records_id, sos_records.records_first_name, sos_records.records_last_name, sos_records.records_email, sos_records.records_phone_mobile, sos_records.records_phone_work, sos_records.records_phone_home, sos_records.records_date_created_user_id, sos_records.records_date_updated, sos_records.records_date_updated_user_id, sos_records.records_status
		FROM sos_contacts
		JOIN sos_records ON sos_contacts.contacts_record_id = sos_records.id
		JOIN sos_students ON sos_students.id = sos_contacts.contacts_student_id
		WHERE sos_contacts.contacts_student_id = '$student_id' $toggle
		ORDER BY sos_records.records_last_name, sos_records.records_first_name
		";
 

/*$qry = "SELECT sos_contacts.*
		FROM sos_contacts
		JOIN sos_students ON sos_students.id = sos_contacts.contacts_student_id
		WHERE sos_contacts.contacts_student_id = '$student_id' $toggle
		ORDER BY sos_records.records_last_name, sos_records.records_first_name
		";
*/
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_contacts[$i]['id'] = $row['id'];

$array_contacts[$i]['contacts_student_id'] = $row['contacts_student_id'];
$array_contacts[$i]['contacts_record_id'] = $row['contacts_record_id'];
$array_contacts[$i]['contacts_date_created'] = $row['contacts_date_created'];
$array_contacts[$i]['contacts_date_created_user_id'] = $row['contacts_date_created_user_id'];
$array_contacts[$i]['contacts_date_updated'] = $row['contacts_date_updated'];
$array_contacts[$i]['contacts_date_updated_user_id'] = $row['contacts_date_updated_user_id'];
$array_contacts[$i]['contacts_relationship'] = $row['contacts_relationship'];
$array_contacts[$i]['contacts_type'] = $row['contacts_type'];
$array_contacts[$i]['contacts_status'] = $row['contacts_status'];

$array_contacts[$i]['records_id'] = $row['records_id'];
$array_contacts[$i]['records_first_name'] = $row['records_first_name'];
$array_contacts[$i]['records_last_name'] = $row['records_last_name'];
$array_contacts[$i]['records_email'] = $row['records_email'];
$array_contacts[$i]['records_phone_mobile'] = $row['records_phone_mobile'];
$array_contacts[$i]['records_phone_work'] = $row['records_phone_work'];
$array_contacts[$i]['records_phone_home'] = $row['records_phone_home'];
$array_contacts[$i]['records_date_created'] = $row['records_date_created'];
$array_contacts[$i]['records_date_created_user_id'] = $row['records_date_created_user_id'];
$array_contacts[$i]['records_date_updated'] = $row['records_date_updated'];
$array_contacts[$i]['records_date_updated_user_id'] = $row['records_date_updated_user_id'];
$array_contacts[$i]['records_status'] = $row['records_status'];
$i++;
}

return $array_contacts;
}

function array_groups($db='', $culture='', $u_id='', $u_type, $permissions='', $active='')
{



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



$qry = "SELECT sos_groups.*, sos_schools.schools_label, sos_schools_type.schools_type_label
		FROM sos_groups
		JOIN sos_schools ON sos_schools.id = sos_groups.group_school_id
		JOIN sos_schools_type ON sos_schools_type.id = sos_schools.schools_type
		$toggle
		ORDER BY sos_groups.group_name
		";
 
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_groups[$i]['id'] = $row['id'];
$array_groups[$i]['group_school_id'] = $row['group_school_id'];
$array_groups[$i]['group_name'] = $row['group_name'];
$array_groups[$i]['group_type'] = $row['group_type'];
$array_groups[$i]['group_teacher'] = $row['group_teacher'];
$array_groups[$i]['group_date_created'] = $row['group_date_created'];
$array_groups[$i]['group_date_created_user_id'] = $row['group_date_created_user_id'];
$array_groups[$i]['group_date_updated'] = $row['group_date_updated'];
$array_groups[$i]['group_date_updated_user_id'] = $row['group_date_updated_user_id'];
$array_groups[$i]['group_status'] = $row['group_status'];
$array_groups[$i]['group_notes'] = $row['group_notes'];
$array_groups[$i]['schools_label'] = $row['schools_label'];
$array_groups[$i]['schools_type_label'] = $row['schools_type_label'];
$i++;
}

return $array_groups;
}

function array_group_lists($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $group_id='')
{



$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = " AND students_school_id = '$data_user[user_school_id]'";}

$qry = "SELECT sos_students.*,
		DATE_FORMAT(students_dob, '%m/%d/%Y') AS students_dob,
		sos_group_lists.id AS group_lists_id,
		sos_group_lists.lists_date_created,
		sos_group_lists.lists_date_created_user_id,
		sos_group_lists.lists_date_updated,
		sos_group_lists.lists_date_updated_user_id,
		sos_group_lists.lists_relationship,
		sos_group_lists.lists_status,
		sos_groups.group_name,
		sos_groups.group_teacher
		FROM sos_students
		JOIN sos_group_lists ON sos_students.id = sos_group_lists.lists_student_id
		JOIN sos_groups ON sos_groups.id = sos_group_lists.lists_group_id
		WHERE sos_group_lists.lists_group_id  = '$group_id' $toggle
		ORDER BY sos_students.students_first_name, sos_students.students_last_name
	  ";
 
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_lists[$i]['id'] = $row['id'];
$array_lists[$i]['students_school_id'] = $row['students_school_id'];
$array_lists[$i]['students_label_id'] = $row['students_label_id'];
$array_lists[$i]['students_first_name'] = $row['students_first_name'];
$array_lists[$i]['students_last_name'] = $row['students_last_name'];
$array_lists[$i]['students_dob'] = $row['students_dob'];
$array_lists[$i]['students_gender'] = $row['students_gender'];
$array_lists[$i]['students_date_created'] = $row['students_date_created'];
$array_lists[$i]['students_date_created_user_id'] = $row['students_date_created_user_id'];
$array_lists[$i]['students_date_updated'] = $row['students_date_updated'];
$array_lists[$i]['students_date_updated_user_id'] = $row['students_date_updated_user_id'];
$array_lists[$i]['students_status'] = $row['students_status'];

$array_lists[$i]['group_name'] = $row['group_name'];
$array_lists[$i]['group_teacher'] = $row['group_teacher'];

$array_lists[$i]['group_lists_id'] = $row['group_lists_id'];
$array_lists[$i]['lists_date_created'] = $row['lists_date_created'];
$array_lists[$i]['lists_date_created_user_id'] = $row['lists_date_created_user_id'];
$array_lists[$i]['lists_date_updated'] = $row['lists_date_updated'];
$array_lists[$i]['lists_date_updated_user_id'] = $row['lists_date_updated_user_id'];
$array_lists[$i]['lists_relationship'] = $row['lists_relationship'];
$array_lists[$i]['lists_status'] = $row['lists_status'];

$i++;
}

return $array_lists;
}


/////SEARCH RESULT ARRAYS
function search_array_students($db='', $culture='', $u_id='', $u_type, $students_label_id='', $students_first_name='', $students_last_name='')
{



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

$student_id_sql = trim(addslashes($students_label_id));
$students_first_name_sql = trim(addslashes($students_first_name));
$students_last_name_sql = trim(addslashes($students_last_name));

 $qry = "SELECT sos_students.*, DATE_FORMAT(students_dob, '%m/%d/%Y') AS students_dob, sos_schools.schools_label, sos_schools_type.schools_type_label
		FROM sos_students
		JOIN sos_schools ON sos_schools.id = sos_students.students_school_id
		JOIN sos_schools_type ON sos_schools_type.id = sos_schools.schools_type
		$toggle ";
if(!empty($students_label_id)){ $qry .= "AND sos_students.students_label_id = '$student_id_sql'";}		
if(!empty($students_first_name)){ $qry .= "AND students_first_name LIKE '%$students_first_name_sql%'";}	
if(!empty($students_last_name)){ $qry .= "AND students_last_name LIKE '%$students_last_name_sql%'";}	
$qry .= "ORDER BY sos_students.students_last_name, sos_students.students_first_name
		";
 
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_students[$i]['id'] = $row['id'];
$array_students[$i]['students_school_id'] = $row['students_school_id'];
$array_students[$i]['students_label_id'] = $row['students_label_id'];
$array_students[$i]['students_first_name'] = $row['students_first_name'];
$array_students[$i]['students_last_name'] = $row['students_last_name'];
$array_students[$i]['students_dob'] = $row['students_dob'];
$array_students[$i]['students_gender'] = $row['students_gender'];
$array_students[$i]['students_date_created'] = $row['students_date_created'];
$array_students[$i]['students_date_created_user_id'] = $row['students_date_created_user_id'];
$array_students[$i]['students_date_updated'] = $row['students_date_updated'];
$array_students[$i]['students_date_updated_user_id'] = $row['students_date_updated_user_id'];
$array_students[$i]['students_status'] = $row['students_status'];
$array_students[$i]['schools_label'] = $row['schools_label'];
$array_students[$i]['schools_type_label'] = $row['schools_type_label'];
$i++;
}

return $array_students;
}

function search_array_groups($db='', $culture='', $u_id='', $u_type,  $group_type='', $group_name='', $group_teacher='', $contacts_type='', $contacts_status='')
{



$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = " WHERE sos_schools.id != '0' ";}
elseif($data_user['passports_type']=='2'){$toggle = " WHERE sos_schools.id = '$data_user[user_school_id]' ";}

$group_type_sql = trim(addslashes($group_type));
$group_name_sql = trim(addslashes($group_name));
$group_teacher_sql = trim(addslashes($group_teacher));
$contacts_type_sql = trim(addslashes($contacts_type));
$contacts_status_sql = trim(addslashes($contacts_status));
$qry = "SELECT sos_groups.*, sos_groups_type_codec.groups_type_label, sos_schools.schools_label, sos_schools_type.schools_type_label, sos_group_lists.lists_relationship, sos_students.students_label_id, sos_students.students_first_name, sos_students.students_last_name, sos_students.students_dob, sos_students.students_gender, sos_records.records_first_name, sos_records.records_last_name, sos_records.records_email, sos_records.records_phone_mobile, sos_records.records_phone_work, sos_records.records_phone_home, sos_contacts.contacts_type, sos_contacts.contacts_status
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
if(!empty($contacts_type)){ $qry .= "AND sos_contacts.contacts_type = '$contacts_type_sql'";}
if(!empty($contacts_status)){ $qry .= "AND sos_contacts.contacts_status = '$contacts_status_sql'";}
$qry .= "GROUP BY sos_contacts.id
		 ORDER BY sos_groups.group_name
		 ";

//print $qry;
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_groups[$i]['id'] = $row['id'];
$array_groups[$i]['group_school_id'] = $row['group_school_id'];
$array_groups[$i]['group_name'] = $row['group_name'];
$array_groups[$i]['group_type'] = $row['group_type'];
$array_groups[$i]['group_teacher'] = $row['group_teacher'];
$array_groups[$i]['group_date_created'] = $row['group_date_created'];
$array_groups[$i]['group_date_created_user_id'] = $row['group_date_created_user_id'];
$array_groups[$i]['group_date_updated'] = $row['group_date_updated'];
$array_groups[$i]['group_date_updated_user_id'] = $row['group_date_updated_user_id'];
$array_groups[$i]['group_status'] = $row['group_status'];
$array_groups[$i]['group_notes'] = $row['group_notes'];
$array_groups[$i]['schools_label'] = $row['schools_label'];
$array_groups[$i]['schools_type_label'] = $row['schools_type_label'];
$array_groups[$i]['students_first_name'] = $row['students_first_name'];
$array_groups[$i]['students_last_name'] = $row['students_last_name'];

$array_groups[$i]['contacts_type'] = $row['contacts_type'];
$array_groups[$i]['contacts_status'] = $row['contacts_status'];

$array_groups[$i]['records_first_name'] = $row['records_first_name'];
$array_groups[$i]['records_last_name'] = $row['records_last_name'];
$array_groups[$i]['records_email'] = $row['records_email'];
$array_groups[$i]['records_phone_mobile'] = $row['records_phone_mobile'];
$array_groups[$i]['records_phone_work'] = $row['records_phone_work'];
$array_groups[$i]['records_phone_home'] = $row['records_phone_home'];
$i++;
}

return $array_groups;
}

function search_array_records($db='', $culture='', $u_id='', $u_type, $records_first_name='', $records_last_name='',  $contacts_type='', $contacts_status='')
{



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
$qry .= "GROUP By sos_records.id
		ORDER BY sos_records.records_last_name, sos_records.records_first_name
		
		";
 //print $qry;
$exec_qry = mysql_query($qry, $db) or die(mysql_error());
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_records[$i]['id'] = $row['id'];
$array_records[$i]['records_school_id'] = $row['records_school_id'];
$array_records[$i]['records_first_name'] = $row['records_first_name'];
$array_records[$i]['records_last_name'] = $row['records_last_name'];
$array_records[$i]['records_email'] = $row['records_email'];
$array_records[$i]['records_phone_mobile'] = $row['records_phone_mobile'];
$array_records[$i]['records_phone_work'] = $row['records_phone_work'];
$array_records[$i]['records_phone_home'] = $row['records_phone_home'];
$array_records[$i]['records_date_created'] = $row['records_date_created'];
$array_records[$i]['records_date_created_user_id'] = $row['records_date_created_user_id'];
$array_records[$i]['records_date_updated'] = $row['records_date_updated'];
$array_records[$i]['records_date_updated_user_id'] = $row['records_date_updated_user_id'];
$array_records[$i]['records_status'] = $row['records_status'];

$array_records[$i]['contact_id'] = $row['contact_id'];
$array_records[$i]['contacts_type'] = $row['contacts_type'];
$array_records[$i]['contacts_relationship'] = $row['contacts_relationship'];

$array_records[$i]['students_first_name'] = $row['students_first_name'];
$array_records[$i]['students_last_name'] = $row['students_last_name'];
$i++;
}

return $array_records;
}

////PRINT RESULTS ARRAY
function print_array_students($db='', $culture='', $u_id='', $u_type, $students_gender='', $students_status='')
{



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
$i=0;
while($row = mysql_fetch_array($exec_qry))
{
$array_students[$i]['id'] = $row['id'];
$array_students[$i]['students_school_id'] = $row['students_school_id'];
$array_students[$i]['students_label_id'] = $row['students_label_id'];
$array_students[$i]['students_first_name'] = $row['students_first_name'];
$array_students[$i]['students_last_name'] = $row['students_last_name'];
$array_students[$i]['students_dob'] = $row['students_dob'];
$array_students[$i]['students_gender'] = $row['students_gender'];
$array_students[$i]['students_date_created'] = $row['students_date_created'];
$array_students[$i]['students_date_created_user_id'] = $row['students_date_created_user_id'];
$array_students[$i]['students_date_updated'] = $row['students_date_updated'];
$array_students[$i]['students_date_updated_user_id'] = $row['students_date_updated_user_id'];
$array_students[$i]['students_status'] = $row['students_status'];
$array_students[$i]['schools_label'] = $row['schools_label'];
$array_students[$i]['schools_type_label'] = $row['schools_type_label'];
$i++;
}

return $array_students;
}

////SELECT ELEMENTS
function options_schools($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $school_id_toggle='')
{

$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = " WHERE schools_status = '1'";}
elseif($data_user['passports_type']=='2'){$toggle = " WHERE id = '$data_user[user_school_id]' AND schools_status = '1'";}



global $options_schools;

 $qry = "SELECT *
		FROM sos_schools
		 $toggle
		ORDER BY schools_label
		
		";

$exec_qry = mysql_query($qry, $db) or die(mysql_error());

$options_schools[] = '<option value="">Select A School</option>';
$i=0;
while($data = mysql_fetch_array($exec_qry))
{
if($school_id_toggle==$data['id']){$selected_marker = ' SELECTED';}else{$selected_marker='';}
if($culture == 'eng'){$sql_culture = 'eng';}elseif($culture == 'vn'){ $sql_culture = 'vn';}
$options_schools[] = '<option value="'.$data['id'].'"'.$selected_marker.'>'.$data['schools_label'].'</option>';
$i++;
}	
}

function options_schools_type($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $school_type_id_toggle='')
{
	
global $options_schools_type;

 $qry = "SELECT *
		FROM sos_schools_type
		WHERE schools_type_status = '1'
		ORDER BY schools_type_label
		";

$exec_qry = mysql_query($qry, $db) or die(mysql_error());

$options_schools_type[] = '<option value="">Select A School Type</option>';
$i=0;
while($data = mysql_fetch_array($exec_qry))
{
if($school_type_id_toggle==$data['id']){$selected_marker = ' SELECTED';}else{$selected_marker='';}
if($culture == 'eng'){$sql_culture = 'eng';}elseif($culture == 'vn'){ $sql_culture = 'vn';}
$options_schools_type[] = '<option value="'.$data['id'].'"'.$selected_marker.'>'.$data['schools_type_label'].'</option>';
$i++;
}	
}

function options_records($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $records_id_toggle='')
{

	$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);

if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = " AND records_school_id = '$data_user[user_school_id]'";}



global $options_records;

 $qry = "SELECT *
		FROM sos_records
		WHERE records_status = '1' $toggle
		ORDER BY records_last_name, records_first_name
		
		";

$exec_qry = mysql_query($qry, $db) or die(mysql_error());

$options_records[] = '<option value="">Select Contact Record</option>';
$i=0;
while($data = mysql_fetch_array($exec_qry))
{
if($records_id_toggle==$data['id']){$selected_marker = ' SELECTED';}else{$selected_marker='';}
if($culture == 'eng'){$sql_culture = 'eng';}elseif($culture == 'vn'){ $sql_culture = 'vn';}
$options_records[] = '<option value="'.$data['id'].'"'.$selected_marker.'>'.$data['records_last_name'].', '.$data['records_first_name'].'</option>';
$i++;
}	
}

function options_students($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $students_id_toggle='')
{

	$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = " AND students_school_id = '$data_user[user_school_id]'";}



global $options_students;

 $qry = "SELECT *
		FROM sos_students
		WHERE students_status = '1' $toggle
		ORDER BY students_first_name, students_last_name
		
		";

$exec_qry = mysql_query($qry, $db) or die(mysql_error());

$options_students[] = '<option value="">Select Student Record</option>';
$i=0;
while($data = mysql_fetch_array($exec_qry))
{
if($students_id_toggle==$data['id']){$selected_marker = ' SELECTED';}else{$selected_marker='';}
if($culture == 'eng'){$sql_culture = 'eng';}elseif($culture == 'vn'){ $sql_culture = 'vn';}
$options_students[] = '<option value="'.$data['id'].'"'.$selected_marker.'>'.$data['students_first_name'].', '.$data['students_last_name'].'</option>';
$i++;
}	
}

function options_groups($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $groups_id_toggle='')
{
	
		$qry_user = "SELECT sos_users.*, sos_passports.passports_type
			 FROM sos_users
			 JOIN sos_passports ON sos_passports.passports_user_id = sos_users.id
			 WHERE sos_users.id = '$u_id'
			 LIMIT 1
			 ";

$exec_qry_user = mysql_query($qry_user, $db) or die(mysql_error());

$data_user = mysql_fetch_array($exec_qry_user);


if($data_user['passports_type']=='1'){$toggle = '';}
elseif($data_user['passports_type']=='2'){$toggle = " WHERE group_school_id = '$data_user[user_school_id]'";}




	
global $options_groups;

 $qry = "SELECT *
		FROM sos_groups
		$toggle
		ORDER BY group_name
		";

$exec_qry = mysql_query($qry, $db) or die(mysql_error());

$options_groups[] = '<option value="">Select Group</option>';

$i=0;
while($data = mysql_fetch_array($exec_qry))
{
if($groups_id_toggle==$data['id']){$selected_marker = ' SELECTED';}else{$selected_marker='';}
if($culture == 'eng'){$sql_culture = 'eng';}elseif($culture == 'vn'){ $sql_culture = 'vn';}
$options_groups[] = '<option value="'.$data['id'].'"'.$selected_marker.'>'.$data['group_name'].'</option>';
$i++;
}	
}

function options_groups_type_codec($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $groups_type_id_toggle='')
{
	
global $options_groups_type_codec;

 $qry = "SELECT *
		FROM sos_groups_type_codec
		ORDER BY id
		";

$exec_qry = mysql_query($qry, $db) or die(mysql_error());

$options_groups_type_codec[] = '<option value=""></option>';

$i=0;
while($data = mysql_fetch_array($exec_qry))
{
if($groups_type_id_toggle==$data['id']){$selected_marker = ' SELECTED';}else{$selected_marker='';}
if($culture == 'eng'){$sql_culture = 'eng';}elseif($culture == 'vn'){ $sql_culture = 'vn';}
$options_groups_type_codec[] = '<option value="'.$data['id'].'"'.$selected_marker.'>'.$data['groups_type_label'].'</option>';
$i++;
}	
}

function options_codec_gender($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $codec_gender_id_toggle='')
{
	
global $options_codec_gender;

 $qry = "SELECT *
		FROM sos_codec_gender
		ORDER BY id
		";

$exec_qry = mysql_query($qry, $db) or die(mysql_error());

$options_codec_gender[] = '<option value="">Select Gender</option>';
$i=0;
while($data = mysql_fetch_array($exec_qry))
{
if($codec_gender_id_toggle==$data['gender_value']){$selected_marker = ' SELECTED';}else{$selected_marker='';}
if($culture == 'eng'){$sql_culture = 'eng';}elseif($culture == 'vn'){ $sql_culture = 'vn';}
$options_codec_gender[] = '<option value="'.$data['gender_value'].'"'.$selected_marker.'>'.$data['gender_label'].'</option>';
$i++;
}	
}

function options_codec_primary($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $codec_primary_id_toggle='')
{
	
global $options_codec_primary;

 $qry = "SELECT *
		FROM sos_codec_primary
		ORDER BY id
		";

$exec_qry = mysql_query($qry, $db) or die(mysql_error());

$options_codec_primary[] = '<option value="">Select Type</option>';
$i=0;
while($data = mysql_fetch_array($exec_qry))
{
if($codec_primary_id_toggle==$data['primary_value']){$selected_marker = ' SELECTED';}else{$selected_marker='';}
if($culture == 'eng'){$sql_culture = 'eng';}elseif($culture == 'vn'){ $sql_culture = 'vn';}
$options_codec_primary[] = '<option value="'.$data['primary_value'].'"'.$selected_marker.'>'.$data['primary_label'].'</option>';
$i++;
}	
}

function options_codec_status($db='', $culture='', $u_id='', $u_type, $permissions='', $active='', $codec_status_id_toggle='')
{
	
global $options_codec_status;

 $qry = "SELECT *
		FROM sos_codec_status
		ORDER BY id
		";

$exec_qry = mysql_query($qry, $db) or die(mysql_error());

$options_codec_status[] = '<option value="">Select Status</option>';
$i=0;
while($data = mysql_fetch_array($exec_qry))
{
if($codec_status_id_toggle==$data['status_value']){$selected_marker = ' SELECTED';}else{$selected_marker='';}
if($culture == 'eng'){$sql_culture = 'eng';}elseif($culture == 'vn'){ $sql_culture = 'vn';}
$options_codec_status[] = '<option value="'.$data['status_value'].'"'.$selected_marker.'>'.$data['status_label'].'</option>';
$i++;
}	
}
?>