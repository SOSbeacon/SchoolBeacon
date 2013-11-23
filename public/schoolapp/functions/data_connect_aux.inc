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
?>