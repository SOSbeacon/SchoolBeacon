<?
if($_REQUEST['add']){
include('./actions/form_students_add_verify.inc');
}
elseif($_REQUEST['new']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_students_add.inc';
}
elseif($_REQUEST['update']){
include('./actions/form_students_update_verify.inc');
}
elseif($_REQUEST['add-contacts']){
include('./actions/form_contacts_add_verify.inc');
}
elseif($_REQUEST['edit']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_students_update.inc';
}
else{
$content_fragment = './templates/'.$_SESSION[culture].'/form_students_add.inc';
}
?>