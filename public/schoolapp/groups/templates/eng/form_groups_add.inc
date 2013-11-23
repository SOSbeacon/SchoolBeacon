<?//Fetch Users
$groups_array = array_groups($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_groups_size = sizeof($groups_array);
?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                
<div style="padding: 20px;">
<table width="100%" cellpadding="3">
<tr>
<td width="20%"><b>Group Name</b></td>
<td width="20%"><b>School Name</b></td>
<td width="20%"><b>Type</b></td>
<td width="20%"><b>Teacher</b></td>
<td width="20%"><b>Status</b></td>
</tr>
</table>
<table width="100%" cellpadding="3">
<?
for($i=0; $i<$array_groups_size; $i++)
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
<td width="20%"><a href="?edit=true&id=<?=$groups_array[$i]['id']?>"><?=$groups_array[$i]['group_name']?></a></td>
<td width="20%"><?=$groups_array[$i]['schools_label']?></td>
<td width="20%"><? if($groups_array[$i]['group_type']=='1'){?>School<?}elseif($groups_array[$i]['group_type']=='2'){?>Class<?}elseif($groups_array[$i]['group_type']=='3'){?>Sports<?}elseif($groups_array[$i]['group_type']=='4'){?>Activity<?}elseif($groups_array[$i]['group_type']=='5'){?>Other<?}?></td>
<td width="20%"><?=$groups_array[$i]['group_teacher']?></td>
<td width="20%"><?if($groups_array[$i]['group_status']=='1'){?>Active<?}else{?>Inactive<?}?></td>
</tr>
<?
}if($array_groups_size==0)
{
?>
<tr>
<td width="100%" colspan="5">There are no groups in the database</td>
</tr>
<?	
}
?>
</table>               
</div>

<div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="add" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Add A New Group</h3></div>
                

                
                
    			<div class="div1">
                <?if(isset($error[group_school_id])){?><div class="error">Required Field</div><?}?>
                <div class="left">School:<span style="color:#ff0000;">*</span></div>
                <div class="right">
               <select name="form_r[group_school_id]" class="txt">
			    <?
				if(isset($form_r[group_school_id])){$school_id_toggle = $form_r['group_school_id'];}
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
                <input type="text" class="txt" value="<?=$form_r[group_name]?>" id="user" name="form_r[group_name]">
                </div>
                </div>
                
                	
  				<div class="div1">
                <?if(isset($error[group_type])){?><div class="error">Required Field</div><?}?>
                <div class="left">Type:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='1'){?> checked<?}?>>&nbsp;School
                <input type="radio" value="2" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='2'){?> checked<?}?>>&nbsp;Class
                <input type="radio" value="3" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='3'){?> checked<?}?>>&nbsp;Sports
                <input type="radio" value="4" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='4'){?> checked<?}?>>&nbsp;Activity
                <input type="radio" value="5" id="active" name="form_r[group_type]"<?if($form_r[group_type]=='5'){?> checked<?}?>>&nbsp;Other
                </div>
                </div>
                
                
                <div class="div1">
                <?if(isset($error[group_teacher])){?><div class="error">Required Field</div><?}?>
                <div class="left">Teacher:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form_r[group_teacher]?>" id="user" name="form_r[group_teacher]">
                </div>
                </div>
               
                <div class="div1">
                <div class="left">Comments</div>
                <div class="right">
                <textarea id="user" name="form[group_notes]" style="width: 225px; font-size: 13px !important;"><?=str_replace("<br />","",nl2br($form[group_notes]))?></textarea>
                </div>
                </div>
                               
                <div class="div1">
                <?if(isset($error[group_status])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[group_status]"<?if($form_r[group_status]=='1'){?> checked<?}elseif(!isset($form_r[group_status])){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[group_status]"<?if($form_r[group_status]=='2'){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>
                                               
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="submit" value="Add Group" style="float: left;" name="add"></div>
                </div>
                
                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
