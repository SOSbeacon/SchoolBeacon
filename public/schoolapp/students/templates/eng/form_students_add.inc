<script>
	$(function() {
		$( "#datepicker" ).datepicker({
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 4,
			showButtonPanel: true,
			yearRange: '1950:2011'

		});
	});
	</script>

<?
//Fetch Users
$students_array = array_students($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_students_size = sizeof($students_array);
?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                
<div style="padding: 20px; height: 270px; overflow-y: auto;">
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
		         

        <form id="frmLogin" name="add" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Add A New Student</h3></div>
                

                
                
    			<div class="div1">
                <?if(isset($error[students_school_id])){?><div class="error">Required Field</div><?}?>
                <div class="left">School:<span style="color:#ff0000;">*</span></div>
                <div class="right">
               <select name="form_r[students_school_id]" class="txt">
			    <?
				if(isset($form_r[students_school_id])){$school_id_toggle = $form_r['students_school_id'];}
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
                <input type="text" class="txt" value="<?=$form_r[students_label_id]?>" id="" name="form_r[students_label_id]">
                </div>
                </div>
                
                	
				<div class="div1">
                <?if(isset($error[students_first_name])){?><div class="error">Required Field</div><?}?>
                <div class="left">First Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form_r[students_first_name]?>" id="" name="form_r[students_first_name]">
                </div>
                </div>
                
                
            
                
                <div class="div1">
                <?if(isset($error[students_last_name])){?><div class="error">Required Field</div><?}?>
                <div class="left">Last Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form_r[students_last_name]?>" id="" name="form_r[students_last_name]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($error[students_dob])){?><div class="error">Required Field</div><?}?>
                <div class="left">Date of Birth:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" id="datepicker" class="txt" value="<?if(!empty($form_r[students_dob])){?><?=$form_r[students_dob]?><?}?>" name="form_r[students_dob]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($error[students_gender])){?><div class="error">Required Field</div><?}?>
                <div class="left">Gender:<span style="color:#ff0000;">*</span></div>
                <div class="right">
 				<input type="radio" value="M" id="active" name="form_r[students_gender]"<?if($form_r[students_gender]=='M'){?> checked<?}?>>&nbsp;Male
                <input type="radio" value="F" id="active" name="form_r[students_gender]"<?if($form_r[students_gender]=='F'){?> checked<?}?>>&nbsp;Female
                </div>
                </div>
                
               

                <div class="div1">
                <?if(isset($error[students_status])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[students_status]"<?if($form_r[students_status]=='1'){?> checked<?}elseif(!isset($form_r[students_status])){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[students_status]"<?if($form_r[students_status]=='2'){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>
                                               
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="submit" value="Add Student" style="float: left;" name="add"></div>
                </div>
                
                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
