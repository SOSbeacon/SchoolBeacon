<?
if($_CFG['debug']==1){
print '<div style="background: #FFFFFF; text-align: left;">';
//print phpinfo();

print '<BLOCKQUOTE><B>$_SESSION VARS</B><BLOCKQUOTE>';
print '<pre>';
print_r ($_SESSION);
print '</pre>';
print '<BLOCKQUOTE><B>VERIFY SESSION SQL</B><BLOCKQUOTE>';
print '<pre>';
print $session_error;
print '</pre>';



print '<BLOCKQUOTE><B>$_SERVER VARS</B><BLOCKQUOTE>';
print '<pre>';
print_r ($_SERVER);
print '</pre>';



print '<BLOCKQUOTE><B>$_REQUEST VARS</B><BLOCKQUOTE>';
print '<pre>';
print_r ($_REQUEST);
print '</pre>';

print '<BLOCKQUOTE><B>$_POST VARS</B><BLOCKQUOTE>';
print '<pre>';
print_r ($_POST);
print '</pre>';

print '<BLOCKQUOTE><B>$_GET VARS</B><BLOCKQUOTE>';
print '<pre>';
print_r ($_GET);
print '</pre>';


print '<BLOCKQUOTE><B>FORM ERRORS $error</B><BLOCKQUOTE>';
print '<pre>';
print_r ($error);
print '</pre>';

print '<BLOCKQUOTE><B>FORM ERRORS $reg_error</B><BLOCKQUOTE>';
print '<pre>';
print_r ($reg_error);
print '</pre>';

print '<BLOCKQUOTE><B>$_DISPLAY VARS</B><BLOCKQUOTE>';
print '<pre>';
print_r ($_DISPLAY);
print '</pre>';

print '</div>';
}

?>
