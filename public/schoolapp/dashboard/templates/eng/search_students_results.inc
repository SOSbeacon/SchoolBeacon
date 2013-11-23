<?//Fetch Students
$form_r = $_REQUEST[form_r];
$form = $_REQUEST[form];

$students_first_name = $form[students_first_name];
$students_last_name = $form[students_last_name];
$students_label_id = $form[students_label_id];

$students_array = search_array_students($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'],  $students_label_id, $students_first_name, $students_last_name);
$array_students_size = sizeof($students_array);
?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">

<div class="login-home">
      
        
        <div class="login-panel">
                        
                 <form id="frmLogin" name="students" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Search Students</h3></div>
                

        <table width="100%" cellpadding="3" cellspacing="3">
        <tr>
        <td width="15%" align="right">Student ID:</td>
        <td width="15%"><input type="text" style="width: 100px;" class="txt" value="<?=$form[students_label_id]?>" id="" name="form[students_label_id]"></td>
        <td width="15%" align="right">First Name:</td>
        <td width="15%"> <input type="text" style="width: 100px;" class="txt" value="<?=$form[students_first_name]?>" id="" name="form[students_first_name]"></td>
        <td width="15%" align="right">Last Name:</td>
        <td width="15%"><input type="text" style="width: 100px;" class="txt" value="<?=$form[students_last_name]?>" id="" name="form[students_last_name]"></td>
        <td width="15%"><input type="submit" value="Search" style="float: left;" name="students"></td>
        </tr>
        </table>        
               
			
                                                                  
        </form>   
  </div>
  </div>
        
        
<div style="padding: 20px;">
<table width="100%" cellpadding="3">
<tr>
<td width="20%"><b>Student Name</b></td>
<td width="20%"><b>School Name</b></td>
<td width="20%"><b>DOB</b></td>
<td width="20%"><b>Gender</b></td>
<td width="20%"><b>Status</b></td>
</tr>
</table>
<table width="100%" cellpadding="3">
<?
for($i=0; $i<$array_students_size; $i++)
{
	
	$counter++;
	if($counter%2==0)
		{
			
			$bg_color = 'ffffff';
			
		}
		else {

			$bg_color = 'c0cde8';		
		}
		
		?>
<tr style="background: #<?=$bg_color?>;">
<td width="20%"><a href="<?=$_CFG['rootURL']?>students/?edit=true&id=<?=$students_array[$i]['id']?>"><?=$students_array[$i]['students_first_name']?> <?=$students_array[$i]['students_last_name']?></a></td>
<td width="20%"><?=$students_array[$i]['schools_label']?></td>
<td width="20%"><?=$students_array[$i]['students_dob']?></td>
<td width="20%"><?=$students_array[$i]['students_gender']?></td>
<td width="20%"><?if($students_array[$i]['students_status']=='1'){?>Active<?}else{?>Inactive<?}?></td>
</tr>
<?
}if($array_students_size==0)
{
?>
<tr>
<td width="100%" colspan="5">There are no students in the database</td>
</tr>
<?	
}
?>
</table>               
</div>

              </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
