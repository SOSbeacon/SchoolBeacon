<?
$_REQUEST[id] = urldecode($_REQUEST[id]);
$student_id_sql = trim(addslashes(urldecode($_REQUEST[id])));

//Fetch Users
$students_array = array_students($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_students_size = sizeof($students_array);

student_data($db, $student_id_sql);

?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                
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
<td width="20%"><a href="?edit=true&id=<?=$students_array[$i]['id']?>"><?=$students_array[$i]['students_first_name']?> <?=$students_array[$i]['students_last_name']?></a></td>
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

<div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="update" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Edit Student</h3></div>
                 <?if(isset($reg_update['student'])){?><div class="information">Record Updated</div><?}?>
                 <?if(isset($reg_insert['student'])){?><div class="information">Record Added</div><?}?>
                 

                 
                 	<div class="div1">
                <?if(isset($error[students_school_id])){?><div class="error">Required Field</div><?}?>
                <div class="left">School:<span style="color:#ff0000;">*</span></div>
                <div class="right">
               <select name="form_r[students_school_id]" class="txt">
			    <?
				if(isset($form_r[students_school_id])){$school_id_toggle = $form_r['students_school_id'];}else{$school_id_toggle = $student_data[0]['students_school_id'];}
				options_schools($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $school_id_toggle);
				print_r ($options_schools);
				?>
				</select>
                </div>
                </div>
                 
                 
               <div class="div1">
                <?if(isset($error[students_label_id])){?><div class="error">Required Field</div><?}?>
                <div class="left">Student ID:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['students_label_id'])){print $form_r['students_label_id'];}else{print $student_data[0]['students_label_id'];}?>" id="user" name="form_r[students_label_id]">
                </div>
                </div>
                
                	
				<div class="div1">
                <?if(isset($error[students_first_name])){?><div class="error">Required Field</div><?}?>
                <div class="left">First Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['students_first_name'])){print $form_r['students_first_name'];}else{print $student_data[0]['students_first_name'];}?>" id="user" name="form_r[students_first_name]">
                </div>
                </div>
                
                
            
                
                <div class="div1">
                <?if(isset($error[students_last_name])){?><div class="error">Required Field</div><?}?>
                <div class="left">Last Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['students_last_name'])){print $form_r['students_last_name'];}else{print $student_data[0]['students_last_name'];}?>" id="user" name="form_r[students_last_name]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($error[students_dob])){?><div class="error">Required Field</div><?}?>
                <div class="left">Date of Birth:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['students_dob'])){print $form_r['students_dob'];}else{print $student_data[0]['students_dob'];}?>" id="user" name="form_r[students_dob]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($error[students_gender])){?><div class="error">Required Field</div><?}?>
                <div class="left">Gender:</div>
                <div class="right">
 				<input type="radio" value="M" id="active" name="form_r[students_gender]"<?if($form_r[students_gender]=='M'){?> checked<?}elseif(!isset($_REQUEST[add]) && ($student_data[0]['students_gender']=='M')){?> checked<?}?>>&nbsp;Male
                <input type="radio" value="F" id="active" name="form_r[students_gender]"<?if($form_r[students_gender]=='F'){?> checked<?}elseif(!isset($_REQUEST[add]) && ($student_data[0]['students_gender']=='F')){?> checked<?}?>>&nbsp;Female
                </div>
                </div>
                

                <div class="div1">
                <div class="left">Date Record Created:</div>
                <div class="right">
                <?=$student_data[0]['students_date_created']?>
                </div>
                </div>
                 
                <div class="div1">
                <div class="left">Record Author:</div>
                <div class="right">
                <? $created_by = user_data_name($db, $student_data[0]['students_date_created_user_id']);?>
                <?=$created_by[0]['user_f_name']?>  <?=$created_by[0]['user_l_name']?>
                </div>
                </div>
                

                <div class="div1">
                <div class="left">Date Record Updated:</div>
                <div class="right">
                <?=$student_data[0]['students_date_updated']?>
                </div>
                </div>
                 
                <div class="div1">
                <div class="left">Record Update Author:</div>
                <div class="right">
                <? $updated_by = user_data_name($db, $student_data[0]['students_date_updated_user_id']);?>
                <?=$updated_by[0]['user_f_name']?>  <?=$updated_by[0]['user_l_name']?>                
                </div>
                </div>
                                
                <div class="div1">
                <?if(isset($error[students_status])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[students_status]"<?if($form_r[students_status]=='1'){?> checked<?}elseif(!isset($_REQUEST[add]) AND ($student_data[0]['students_status']=='1')){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[students_status]"<?if($form_r[students_status]=='2'){?> checked<?}elseif(!isset($_REQUEST[add]) AND ($student_data[0]['students_status']=='2')){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>
                

                               
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="hidden" name="id" value="<?=$student_data[0]['id']?>"><input type="submit" value="Edit Student" style="float: left;" name="update"></div>
                </div>
                
                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
