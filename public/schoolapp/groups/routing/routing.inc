<?
if($_REQUEST['add']){
include('./actions/form_groups_add_verify.inc');
}
elseif($_REQUEST['new']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_groups_add.inc';
}
elseif($_REQUEST['update']){
include('./actions/form_groups_update_verify.inc');
}
elseif($_REQUEST['add-lists']){
include('./actions/form_lists_add_verify.inc');
}
elseif($_REQUEST['edit']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_groups_update.inc';
}
else{
$content_fragment = './templates/'.$_SESSION[culture].'/form_groups_add.inc';
}
?>