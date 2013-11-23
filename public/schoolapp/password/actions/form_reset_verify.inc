<?

$form_r = $_REQUEST[form_r];
$form = $_REQUEST[form];


if(isset($form_r))
{
//Required Fields
$array_keys = array_keys($form_r);			
for($x=0;$x<sizeof($array_keys);$x++)
{
//check array for empty strings	
if(empty($form_r[$array_keys[$x]]))
{
$error[$array_keys[$x]] = $form_r[$array_keys[$x]].'<br>';
}
//stripp tags from array	
for($z=0;$z<sizeof($array_keys); $z++)
{
$form_r[$array_keys[$z]] = strip_tags($form_r[$array_keys[$z]]);
}

}


//Check Email Address
$email_sql = trim(addslashes($form_r[email]));

$search ="SELECT passports_email
		  FROM sos_passports
		  WHERE passports_email = '$email_sql'
		  LIMIT 1
		  ";

$find_duplicate=mysql_query($search, $db) or die(mysql_error());


if (!isset($error[email]) && (mysql_num_rows($find_duplicate) !=1))
{
$reg_error[email] = 'true';
}


//Check Captacha
require_once('../recaptcha/recaptchalib.php');
$privatekey = "6LelzccSAAAAABSS6i_4nsVpvanKzOYflZ7knSwG";
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) { $reg_error[captcha] = $resp->error;}



if (is_array($error) || is_array($reg_error))
{
$reg_error[error] = 'true';
$content_fragment = './templates/'.$_SESSION[culture].'/form_reset.inc';
}

else
{
include('form_reset.inc');
}

}

?>