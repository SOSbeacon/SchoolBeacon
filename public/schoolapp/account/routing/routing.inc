<?
if($_REQUEST['add']){
include('./actions/form_account_add_verify.inc');
}
elseif($_REQUEST['new']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_account_add.inc';
}
elseif($_REQUEST['update']){
include('./actions/form_account_update_verify.inc');
}
elseif($_REQUEST['edit']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_account_update.inc';
}
elseif($_REQUEST['password']){
include('./actions/form_password_update_verify.inc');
}
else{
$content_fragment = './templates/'.$_SESSION[culture].'/form_account_update.inc';
}
?>