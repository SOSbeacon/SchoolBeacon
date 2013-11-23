<?//Fetch Users
$records_array = array_records($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_records_size = sizeof($records_array);
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
		         

        <form id="frmLogin" name="add" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Add A New Contact Record</h3></div>
                
    			<div class="div1">
                <?if(isset($error[records_school_id])){?><div class="error">Required Field</div><?}?>
                <div class="left">School:<span style="color:#ff0000;">*</span></div>
                <div class="right">
               <select name="form_r[records_school_id]" class="txt">
			    <?
				if(isset($form_r[records_school_id])){$school_id_toggle = $form_r['records_school_id'];}
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
                <input type="text" class="txt" value="<?=$form_r[records_first_name]?>" id="user" name="form_r[records_first_name]">
                </div>
                </div>
                
                
            
                
                <div class="div1">
                <?if(isset($error[records_last_name])){?><div class="error">Required Field</div><?}?>
                <div class="left">Last Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form_r[records_last_name]?>" id="user" name="form_r[records_last_name]">
                </div>
                </div>
                
               
                
                <div class="div1">
                <?if(isset($error[records_email])){?><div class="error">Required Field</div><?}?>
                <?if(isset($reg_error[duplicate_records_email])){?><div class="error">Duplicate Email Address</div><?}?>
                <?if(isset($reg_error[records_email]) && !isset($error[records_email])){?><div class="error">Invalid Email Address</div><?}?>
                <div class="left">Email Address:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form_r[records_email]?>" id="user" name="form_r[records_email]">
                </div>
                </div>
                
                               

              

                <div class="div1">
                <?if(isset($error[records_phone_mobile])){?><div class="error">Required Field</div><?}?>
                <div class="left">Mobile Phone:</div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form[records_phone_mobile]?>" id="user" name="form[records_phone_mobile]">
                </div>
                </div>


                <div class="div1">
                <?if(isset($error[records_phone_work])){?><div class="error">Required Field</div><?}?>
                <div class="left">Work Phone:</div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form[records_phone_work]?>" id="user" name="form[records_phone_work]">
                </div>
                </div>

                <div class="div1">
                <?if(isset($error[records_phone_home])){?><div class="error">Required Field</div><?}?>
                <div class="left">Home Phone:</div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form[records_phone_home]?>" id="user" name="form[records_phone_home]">
                </div>
                </div>
                                               
           
                <div class="div1">
                <?if(isset($error[records_status])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[records_status]"<?if($form_r[records_status]=='1'){?> checked<?}elseif(!isset($form_r[records_status])){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[records_status]"<?if($form_r[records_status]=='2'){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>
                     
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="submit" value="Add Contact Record" style="float: left;" name="add"></div>
                </div>
                                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
