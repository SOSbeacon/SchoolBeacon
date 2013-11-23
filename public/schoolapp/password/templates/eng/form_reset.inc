		        
<script>
        var RecaptchaOptions = { theme : 'white' };
  </script>

  <!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                       <div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="reset" method="POST" action="<?=$_CFG['rootWeb']?>password/">

                
                <? if(isset($error) || isset($reg_error))
                {
                	?>
              
                <div class="div1">
                <font color="red">Login Error</font>
                </div>
                 <?
                }
                ?>                       
                <div class="div1">
                <?if(isset($error[email])){?><div class="error">Required Field</div><?}?>
                <?if(isset($reg_error[email])){?><div class="error">Email Address Does Not Exist</div><?}?>
                        <div class="left">Email Address</div>
                        <div class="right">
                        <input type="text" class="txt" value="<?=$form_r['email']?>" id="user" name="form_r[email]">                        </div>
                </div>
                <div class="div1">
                 <?if(isset($reg_error[captcha])){?><div class="error">Type the Two Words Below</div><?}?>
                        <div class="left">&nbsp;</div>
                        <div class="right">
                        <? echo recaptcha_get_html($publickey);?>                       </div>
                </div>
    
                
                <div class="div1">
                
               
                        <div class="left">&nbsp;</div>
                        <div class="right"><input type="submit" value="Reset Password" style="float: left;" name="reset"></div>
                </div>
                
                                          
                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
