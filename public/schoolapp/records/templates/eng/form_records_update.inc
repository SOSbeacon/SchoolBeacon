<?
$_REQUEST[id] = urldecode($_REQUEST[id]);
$record_id_sql = trim(addslashes(urldecode($_REQUEST[id])));

//Fetch Users
$records_array = array_records($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_records_size = sizeof($records_array);

record_data($db, $record_id_sql);

?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                
<div style="padding: 20px; height: 270px; overflow-y: auto;">
<table width="100%" cellpadding="3">
<tr>
<td width="20%"><b>Contact Name</b></td>
<td width="20%"><b>Email Address</b></td>
<td width="15%"><b>Cell Phone</b></td>
<td width="15%"><b>Work Phone</b></td>
<td width="15%"><b>Home Phone</b></td>
<td width="15%"><b>Status</b></td>
</tr>
</table>
<table width="100%" cellpadding="3">
<?
for($i=0; $i<$array_records_size; $i++)
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
<td width="20%"><a href="?edit=true&id=<?=$records_array[$i]['id']?>"><?=$records_array[$i]['records_first_name']?> <?=$records_array[$i]['records_last_name']?></a></td>
<td width="20%"><?=$records_array[$i]['records_email']?></td>
<td width="15%"><?=$records_array[$i]['records_phone_mobile']?></td>
<td width="15%"><?=$records_array[$i]['records_phone_work']?></td>
<td width="15%"><?=$records_array[$i]['records_phone_home']?></td>
<td width="15%"><?if($records_array[$i]['records_status']=='1'){?>Active<?}else{?>Inactive<?}?></td>
</tr>
<?
}if($array_records_size==0)
{
?>
<tr>
<td width="100%" colspan="6">There are no contact records in the database</td>
</tr>
<?	
}
?>
</table>               
</div>

<div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="update" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Edit Contact Record</h3></div>
                 <?if(isset($reg_update['record'])){?><div class="information">Record Updated</div><?}?>
                 <?if(isset($reg_insert['record'])){?><div class="information">Record Added</div><?}?>
                 
                 
           		<div class="div1">
                <?if(isset($error[records_school_id])){?><div class="error">Required Field</div><?}?>
                <div class="left">School:<span style="color:#ff0000;">*</span></div>
                <div class="right">
               <select name="form_r[records_school_id]" class="txt">
			    <?
				if(isset($form_r[records_school_id])){$school_id_toggle = $form_r['records_school_id'];}else{$school_id_toggle = $record_data[0]['records_school_id'];}
				options_schools($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $school_id_toggle);
				print_r ($options_schools);
				?>
				</select>
                </div>
                </div>
                
                	

                 	<div class="div1">
                <?if(isset($error[records_first_name])){?><div class="error">Required Field</div><?}?>
                <?if(isset($reg_error[records_first_name])){?><div class="error">Duplicate Record</div><?}?>                    
                <div class="left">First Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['records_first_name'])){print $form_r['records_first_name'];}else{print $record_data[0]['records_first_name'];}?>" id="user" name="form_r[records_first_name]">
                </div>
                </div>
                
                
            
                
                <div class="div1">
                <?if(isset($error[records_last_name])){?><div class="error">Required Field</div><?}?>
                <div class="left">Last Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['records_last_name'])){print $form_r['records_last_name'];}else{print $record_data[0]['records_last_name'];}?>" id="user" name="form_r[records_last_name]">
                </div>
                </div>
                
               
                
                <div class="div1">
                <?if(isset($error[records_email])){?><div class="error">Required Field</div><?}?>
                <?if(isset($reg_error[duplicate_records_email])){?><div class="error">Duplicate Email Address</div><?}?>
                <?if(isset($reg_error[records_email]) && !isset($error[records_email])){?><div class="error">Invalid Email Address</div><?}?>
                <div class="left">Email Address:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['records_email'])){print $form_r['records_email'];}else{print $record_data[0]['records_email'];}?>" id="user" name="form_r[records_email]">
                </div>
                </div>
                
                               

 
                <div class="div1">
                <?if(isset($error[records_phone_mobile])){?><div class="error">Required Field</div><?}?>
                <div class="left">Mobile Phone:</div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form['records_phone_mobile'])){print $form['records_phone_mobile'];}else{print $record_data[0]['records_phone_mobile'];}?>" id="user" name="form[records_phone_mobile]">
                </div>
                </div>


                <div class="div1">
                <?if(isset($error[records_phone_work])){?><div class="error">Required Field</div><?}?>
                <div class="left">Work Phone:</div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form['records_phone_work'])){print $form['records_phone_work'];}else{print $record_data[0]['records_phone_work'];}?>" id="user" name="form[records_phone_work]">
                </div>
                </div>

                <div class="div1">
                <?if(isset($error[records_phone_home])){?><div class="error">Required Field</div><?}?>
                <div class="left">Home Phone:</div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form['records_phone_home'])){print $form['records_phone_home'];}else{print $record_data[0]['records_phone_home'];}?>" id="user" name="form[records_phone_home]">
                </div>
                </div>
                
                
                
             <div class="div1">
                <div class="left">Date Record Created:</div>
                <div class="right">
                <?=$record_data[0]['records_date_created']?>
                </div>
                </div>
                 
                <div class="div1">
                <div class="left">Record Author:</div>
                <div class="right">
                <? $created_by = user_data_name($db, $record_data[0]['records_date_created_user_id']);?>
                <?=$created_by[0]['user_f_name']?>  <?=$created_by[0]['user_l_name']?>
                </div>
                </div>
                

                <div class="div1">
                <div class="left">Date Record Updated:</div>
                <div class="right">
                <?=$record_data[0]['records_date_updated']?>
                </div>
                </div>
                 
                <div class="div1">
                <div class="left">Record Update Author:</div>
                <div class="right">
                <? $updated_by = user_data_name($db, $record_data[0]['records_date_updated_user_id']);?>
                <?=$updated_by[0]['user_f_name']?>  <?=$updated_by[0]['user_l_name']?>                
                </div>
                </div>
             
                                
                <div class="div1">
                <?if(isset($error[records_status])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[records_status]"<?if($form_r[records_status]=='1'){?> checked<?}elseif(!isset($_REQUEST[add]) AND ($record_data[0]['records_status']=='1')){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[records_status]"<?if($form_r[records_status]=='2'){?> checked<?}elseif(!isset($_REQUEST[add]) AND ($record_data[0]['records_status']=='2')){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>
                

                               
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="hidden" name="id" value="<?=$record_data[0]['id']?>"><input type="submit" value="Edit Contact Record" style="float: left;" name="update"></div>
                </div>
                
                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
