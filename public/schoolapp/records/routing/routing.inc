<?
if($_REQUEST['add']){
include('./actions/form_records_add_verify.inc');
}
elseif($_REQUEST['new']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_records_add.inc';
}
elseif($_REQUEST['update']){
include('./actions/form_records_update_verify.inc');
}
elseif($_REQUEST['edit']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_records_update.inc';
}
else{
$content_fragment = './templates/'.$_SESSION[culture].'/form_records_add.inc';
}
?>