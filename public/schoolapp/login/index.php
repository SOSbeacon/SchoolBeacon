<?
//Config Controller
require_once("../config/config.inc");
//Route Requests
include('routing/routing.inc');
include($_CFG['fragmentsDir'].'html_html_open.inc');    
include($_CFG['fragmentsDir'].'html_content_wrapper_open.inc');
include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_header.inc');
//Display View
include($content_fragment);	
include($_CFG['fragmentsDir'].'html_content_wrapper_close.inc');
include($_CFG['fragmentsDir'].'html_html_close.inc'); 
?>