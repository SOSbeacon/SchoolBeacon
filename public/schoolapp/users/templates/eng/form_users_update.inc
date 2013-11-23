<?
$_REQUEST[id] = urldecode($_REQUEST[id]);
$user_id_sql = trim(addslashes(urldecode($_REQUEST[id])));

//Fetch Users
$user_array = array_users($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_users_size = sizeof($user_array);

user_data($db, $user_id_sql);

?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                
<div style="padding: 20px;">
<table width="100%" cellpadding="3">
<tr>
<td width="20%"><b>User Name</b></td>
<td width="20%"><b>School Name</b></td>
<td width="20%"><b>Screen Name</b></td>
<td width="20%"><b>Email Address</b></td>
<td width="20%"><b>User Title</b></td>
</tr>
</table>
<table width="100%" cellpadding="3">
<?
for($i=0; $i<$array_users_size; $i++)
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
<td width="20%"><a href="?edit=true&id=<?=$user_array[$i]['id']?>"><?=$user_array[$i]['user_f_name']?> <?=$user_array[$i]['user_l_name']?></a></td>
<td width="20%"><?=$user_array[$i]['schools_label']?></td>
<td width="20%"><?=$user_array[$i]['passports_name']?></td>
<td width="20%"><?=$user_array[$i]['user_email']?></td>
<td width="20%"><?=$user_array[$i]['user_title']?></td>
</tr>
<?
}if($array_users_size==0)
{
?>
<tr>
<td width="100%" colspan="5">There are no users in the database</td>
</tr>
<?	
}
?>
</table>               
</div>

<div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="update" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Edit System User</h3></div>
                 <?if(isset($reg_update['user'])){?><div class="information">Record Updated</div><?}?>
                 <?if(isset($reg_insert['user'])){?><div class="information">Record Added</div><?}?>
                 
                <div class="div1">
                <?if(isset($error[user_school_id])){?><div class="error">Required Field</div><?}?>
                <div class="left">School:<span style="color:#ff0000;">*</span></div>
                <div class="right">
               <select name="form_r[user_school_id]" class="txt">
			    <?
				if(isset($form_r[user_school_id])){$school_id_toggle = $form_r['user_school_id'];}else{$school_id_toggle = $user_data[0]['user_school_id'];}
				options_schools($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $school_id_toggle);
				print_r ($options_schools);
				?>
				</select>
                </div>
                </div>

                               
                <div class="div1">
                <?if(isset($error[user_f_name])){?><div class="error">Required Field</div><?}?>
                <div class="left">First Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['user_f_name'])){print $form_r['user_f_name'];}else{print $user_data[0]['user_f_name'];}?>" id="user" name="form_r[user_f_name]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($error[user_l_name])){?><div class="error">Required Field</div><?}?>
                <div class="left">Last Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['user_l_name'])){print $form_r['user_l_name'];}else{print $user_data[0]['user_l_name'];}?>" id="user" name="form_r[user_l_name]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($error[user_title])){?><div class="error">Required Field</div><?}?>
                <div class="left">Title:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['user_title'])){print $form_r['user_title'];}else{print $user_data[0]['user_title'];}?>" id="user" name="form_r[user_title]">
                </div>
                </div>
                
                <div class="div1">
                <div class="left">Phone Number:</div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['user_phone_primary'])){print $form_r['user_phone_primary'];}else{print $user_data[0]['user_phone_primary'];}?>" id="user" name="form[user_phone_primary]">
                </div>
                </div>
                
                <div class="div1">
                <div class="left">Mobile Number:</div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['user_fax'])){print $form_r['user_fax'];}else{print $user_data[0]['user_fax'];}?>" id="user" name="form[user_fax]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($error[user_email])){?><div class="error">Required Field</div><?}?>
                <?if(isset($reg_error[duplicate_user_email])){?><div class="error">Duplicate Email Address</div><?}?>
                <?if(isset($reg_error[user_email]) && !isset($error[user_email])){?><div class="error">Invalid Email Address</div><?}?>
                <div class="left">Email Address:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['user_email'])){print $form_r['user_email'];}else{print $user_data[0]['user_email'];}?>" id="user" name="form_r[user_email]">
                </div>
                </div>
                
                <div class="div1">
                <div class="left">Address</div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form['user_address'])){print $form['user_address'];}else{print $user_data[0]['user_address'];}?>" id="user" name="form[user_address]">
                </div>
                </div>
                
                <div class="div1">
                <div class="left">City</div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form['user_city'])){print $form['user_city'];}else{print $user_data[0]['user_city'];}?>" id="user" name="form[user_city]">
                </div>
                </div>
                
                <div class="div1">
                <div class="left">State</div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form['user_state'])){print $form['user_state'];}else{print $user_data[0]['user_state'];}?>" id="user" name="form[user_state]">
                </div>
                </div>
                
                <div class="div1">
                <div class="left">Zip</div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form['user_zip'])){print $form['user_zip'];}else{print $user_data[0]['user_zip'];}?>" id="user" name="form[user_zip]">
                </div>
                </div>
    
                <div class="div1">
                <div class="left">Comments</div>
                <div class="right">
                <textarea id="user" name="form[user_comments]" style="width: 225px; font-size: 13px !important;"><?if(isset($form[user_comments])){print str_replace("<br />","",nl2br($form[user_comments]));}else{ print str_replace("<br />","",nl2br($user_data[0][user_comments]));}?></textarea>
                </div>
                </div>

                <div class="div1">
                <?if(isset($error[user_active])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[user_active]"<?if($form_r[user_active]=='1'){?> checked<?}elseif(!isset($form_r[user_active]) && $user_data[0]['user_active']=='1'){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[user_active]"<?if($form_r[user_active]=='2'){?> checked<?}elseif(!isset($form_r[user_active]) && $user_data[0]['user_active']=='2'){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>
                                               
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="hidden" name="id" value="<?=$user_data[0]['id']?>"><input type="submit" value="Edit User" style="float: left;" name="update"></div>
                </div>
                
                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
