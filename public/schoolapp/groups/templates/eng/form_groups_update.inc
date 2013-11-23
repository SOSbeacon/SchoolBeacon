<?
$_REQUEST[id] = urldecode($_REQUEST[id]);
$group_id_sql = trim(addslashes(urldecode($_REQUEST[id])));

//Fetch Users
$group_lists_array = array_group_lists($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1', $_REQUEST[id]);
$array_group_lists_size = sizeof($group_lists_array);

group_data($db, $group_id_sql);

?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                
<div style="padding: 20px;">
<table width="100%" cellpadding="3">
<tr>
<td width="20%"><b>Student</b></td>
<td width="20%"><b>Gender</b></td>
<td width="20%"><b>DOB</b></td>
<td width="20%"><b>Teacher</b></td>
<td width="20%"><b>Status</b></td>
</tr>
</table>
<table width="100%" cellpadding="3">
<?
for($i=0; $i<$array_group_lists_size; $i++)
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
<td width="20%"><a href="<?=$_CFG['rootURL']?>students/?edit=true&id=<?=$group_lists_array[$i]['id']?>"><?=$group_lists_array[$i]['students_first_name']?> <?=$group_lists_array[$i]['students_last_name']?></a></td>
<td width="20%"><?=$group_lists_array[$i]['students_gender']?></td>
<td width="20%"><?=$group_lists_array[$i]['students_dob']?></td>
<td width="20%"><?=$group_lists_array[$i]['group_teacher']?></td>
<td width="20%"><?if($group_lists_array[$i]['lists_status']=='1'){?>Active<?}else{?>Inactive<?}?></td>
</tr>
<?
}if($array_group_lists_size==0)
{
?>
<tr>
<td width="100%" colspan="5">There are no student records for this group in the database</td>
</tr>
<?	
}
?>
</table>               
</div>

<div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="update" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Edit Group</h3></div>
                 <?if(isset($reg_update['group'])){?><div class="information">Record Updated</div><?}?>
                 <?if(isset($reg_insert['group'])){?><div class="information">Record Added</div><?}?>
                 
               	
                
                
                <div class="div1">
                <?if(isset($error[groups_school_id])){?><div class="error">Required Field</div><?}?>
                <div class="left">School:<span style="color:#ff0000;">*</span></div>
                <div class="right">
               <select name="form_r[group_school_id]" class="txt">
			    <?
				if(isset($form_r[group_school_id])){$school_id_toggle = $form_r['group_school_id'];}else{$school_id_toggle = $group_data[0]['group_school_id'];}
				options_schools($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $school_id_toggle);
				print_r ($options_schools);
				?>
				</select>
                </div>
                </div>
	
                
				<div class="div1">
                <?if(isset($error[group_name])){?><div class="error">Required Field</div><?}?>
                <?if(isset($reg_error[group_name])){?><div class="error">Duplicate Record</div><?}?>
                <div class="left">Group Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['group_name'])){print $form_r['group_name'];}else{print $group_data[0]['group_name'];}?>" id="user" name="form_r[group_name]">
                </div>
                </div>
                
                	
  				<div class="div1">
                <?if(isset($error[group_type])){?><div class="error">Required Field</div><?}?>
                <div class="left">Type:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='1'){?> checked<?}elseif(!isset($_REQUEST[add]) && ($group_data[0]['group_type']=='1')){?> checked<?}?>>&nbsp;School
                <input type="radio" value="2" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='2'){?> checked<?}elseif(!isset($_REQUEST[add]) && ($group_data[0]['group_type']=='2')){?> checked<?}?>>&nbsp;Class
                <input type="radio" value="3" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='3'){?> checked<?}elseif(!isset($_REQUEST[add]) && ($group_data[0]['group_type']=='3')){?> checked<?}?>>&nbsp;Sports
                <input type="radio" value="4" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='4'){?> checked<?}elseif(!isset($_REQUEST[add]) && ($group_data[0]['group_type']=='4')){?> checked<?}?>>&nbsp;Activity
                <input type="radio" value="5" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='5'){?> checked<?}elseif(!isset($_REQUEST[add]) && ($group_data[0]['group_type']=='5')){?> checked<?}?>>&nbsp;Other
                </div>
                </div>
                
                
                <div class="div1">
                <?if(isset($error[group_teacher])){?><div class="error">Required Field</div><?}?>
                <div class="left">Teacher:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['group_teacher'])){print $form_r['group_teacher'];}else{print $group_data[0]['group_teacher'];}?>" id="user" name="form_r[group_teacher]">
                </div>
                </div>
               
                <div class="div1">
                <div class="left">Comments</div>
                <div class="right">
                <textarea id="user" name="form[group_notes]" style="width: 225px; font-size: 13px !important;"><?if(isset($form[group_notes])){print str_replace("<br />","",nl2br($form[group_notes]));}else{ print str_replace("<br />","",nl2br($group_data[0][group_notes]));}?></textarea>
                </div>
                </div>
                 
                
                <div class="div1">
                <div class="left">Date Record Created:</div>
                <div class="right">
                <?=$group_data[0]['group_date_created']?>
                </div>
                </div>
                 
                <div class="div1">
                <div class="left">Record Author:</div>
                <div class="right">
                <? $created_by = user_data_name($db, $group_data[0]['group_date_created_user_id']);?>
                <?=$created_by[0]['user_f_name']?>  <?=$created_by[0]['user_l_name']?>
                </div>
                </div>
                

                <div class="div1">
                <div class="left">Date Record Updated:</div>
                <div class="right">
                <?=$group_data[0]['group_date_updated']?>
                </div>
                </div>
                 
                <div class="div1">
                <div class="left">Record Update Author:</div>
                <div class="right">
                <? $updated_by = user_data_name($db, $group_data[0]['group_date_updated_user_id']);?>
                <?=$updated_by[0]['user_f_name']?>  <?=$updated_by[0]['user_l_name']?>                
                </div>
                </div>
                
                              
                 <div class="div1">
                <?if(isset($error[students_status])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[group_status]"<?if($form_r[group_status]=='1'){?> checked<?}elseif(!isset($_REQUEST[add]) AND ($group_data[0]['group_status']=='1')){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[group_status]"<?if($form_r[group_status]=='2'){?> checked<?}elseif(!isset($_REQUEST[add]) AND ($group_data[0]['group_status']=='2')){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>
                                              
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="hidden" name="id" value="<?=$group_data[0]['id']?>"><input type="submit" value="Edit Group" style="float: left;" name="update"></div>
                </div>
                
                                                  
        </form>        

        
        
        
         
        
        
        <div style="clear: both;"></div>
        
        
        
        <form id="frmLogin" name="add-lists" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Add Student</h3></div>
                 <?if(isset($reg_update['lists'])){?><div class="information">Group Record Updated</div><?}?>
                 <?if(isset($reg_insert['lists'])){?><div class="information">Group Record Added</div><?}?>
                 

                 
                <div class="div1">
                <?if(isset($reg_error[duplicate_lists])){?><div class="error">Duplicate Record</div><?}?>
                <?if(isset($error[lists_student_id])){?><div class="error">Required Field</div><?}?>
                <div class="left">Student Record:<span style="color:#ff0000;">*</span></div>
                <div class="right">
               <select name="form_r[lists_student_id]" class="txt">
			    <?
				if(isset($form_r[lists_student_id])){$students_id_toggle = $form_r['lists_student_id'];}
				options_students($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $students_id_toggle);
				print_r ($options_students);
				?>
				</select>
                </div>
                </div>
                                
               <div class="div1">
                <?if(isset($error[lists_relationship])){?><div class="error">Required Field</div><?}?>
                <div class="left">Relationship:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form_r[lists_relationship]?>" name="form_r[lists_relationship]">
                </div>
                </div>
                
                             	
                <div class="div1">
                <?if(isset($error[lists_status])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[lists_status]"<?if($form_r[lists_status]=='1'){?> checked<?}elseif(!isset($form_r[lists_status])){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[lists_status]"<?if($form_r[lists_status]=='2'){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>              
                        
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="hidden" name="id" value="<?=$group_data[0]['id']?>"><input type="submit" value="Add Student To Group" style="float: left;" name="add-lists"></div>
                </div>
                
                                                 
        </form>  
        
        
                  
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
