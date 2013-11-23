<?
if($_REQUEST['add']){
include('./actions/form_add_verify.inc');
}
elseif($_REQUEST['new']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_add.inc';
}
elseif($_REQUEST['update']){
include('./actions/form_update_verify.inc');
}
elseif($_REQUEST['edit']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_update.inc';
}
else{
 $content_fragment = './templates/'.$_SESSION[culture].'/default.inc';
}
?>