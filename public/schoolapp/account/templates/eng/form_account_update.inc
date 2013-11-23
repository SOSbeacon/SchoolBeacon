<?
user_data($db, $_SESSION[u_id]);

?>

<!-- Container -->
<div id="Container">
<div id="ContainerInside">
<div id="Content">
<div class="main-content">
                
<div class="login-home">
<div class="login-panel">
		         

        		<form id="frmLogin" name="update" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Edit Account Informationr</h3></div>
                 <?if(isset($reg_update['user'])){?><div class="information">Record Updated</div><?}?>
                 <?if(isset($reg_insert['user'])){?><div class="information">Record Added</div><?}?>
                 
             

                               
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
                <div class="left">&nbsp;</div>
                <div class="right"><input type="hidden" name="id" value="<?=$user_data[0]['id']?>"><input type="submit" value="Edit Account Information" style="float: left;" name="update"></div>
                </div>
                
                                                  
        </form> 

        <div style="clear: both;"></div>
        
        
        		<form id="frmLogin" name="password" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Change Password</h3></div>
                 <?if(isset($reg_update['password'])){?><div class="information">Password Updated</div><?}?>
                 
             

                               
                <div class="div1">
                <?if(isset($error[password])){?><div class="error">Required Field</div><?}?>
                <?if(isset($reg_error[password_length_max])){?><div class="error">Passwords Must Be Less Than 16 Characters</div><?}?>
                <?if(isset($reg_error[password_length_min])){?><div class="error">Passwords Must Be More Than 7 Characters</div><?}?>
                <div class="left">New Password:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="password" class="txt" value="" id="" name="form_r[password]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($reg_error[password])){?><div class="error">Passwords Do Not Match</div><?}?>
                <div class="left">Confirm Password:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="password" class="txt" value="" id="" name="form_r[password_confirm]">
                </div>
                </div>
                
               <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="hidden" name="id" value="<?=$user_data[0]['id']?>"><input type="submit" value="Change Password" style="float: left;" name="password"></div>
                </div>
                
                </form>
                       
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
