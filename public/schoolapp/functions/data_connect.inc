<?
        
$mysql_server = $_CFG['dbHost'];
$user_name = $_CFG['dbUser'];
$password = $_CFG['dbPass'];
$database= $_CFG['dbName'];


$db = mysql_connect("$mysql_server","$user_name","$password");
@mysql_select_db($database, $db) or die(mysql_error());
?>