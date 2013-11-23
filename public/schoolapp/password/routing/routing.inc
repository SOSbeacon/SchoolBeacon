<?
if($_REQUEST['reset']){
include('./actions/form_reset_verify.inc');
}
else{
 $content_fragment = './templates/'.$_SESSION[culture].'/form_reset.inc';
}
?>