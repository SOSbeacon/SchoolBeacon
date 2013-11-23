<?
if($_REQUEST['students']){
include('./actions/search_students_verify.inc');
}
elseif($_REQUEST['records']){
include('./actions/search_records_verify.inc');
}
elseif($_REQUEST['groups']){
include('./actions/search_groups_verify.inc');
}
elseif($_REQUEST['edit']){
$content_fragment = './templates/'.$_SESSION[culture].'/form_update.inc';
}
else{
	
	if($_SESSION[u_type]=='1'){
 $content_fragment = './templates/'.$_SESSION[culture].'/default_root.inc';
}
else {
	 $content_fragment = './templates/'.$_SESSION[culture].'/default_system_user.inc';
}
}
?>