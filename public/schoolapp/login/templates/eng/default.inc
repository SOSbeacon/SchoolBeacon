   <!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                       <div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="login" method="POST" action="<?=$_CFG['rootWeb']?>authinicate/">
                <div class="login-title">&nbsp;</div>
                
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
                        <div class="left">Email Address</div>
                        <div class="right">
                        <input type="text" class="txt" value="" id="user" name="form_r[passports_name]">                        </div>
                </div>
                <div class="div1">
                        <div class="left">Password</div>
                        <div class="right">
                        <input type="password" class="txt" value="" id="password" name="form_r[password]">                        </div>
                </div>
    
                
                <div class="div1">
                        <div class="left">&nbsp;</div>
                        <div class="right"><input type="submit" value="" style="float: left;" name="btnLogin" class="button-login"></div>
                </div>
                
                <div class="div1">
                	                </div>   
                	                
                	                                     
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right">
                        <a href="<?=$_CFG['rootURL']?>password/">Forgot login?</a>
                        </div>
                </div>                           
                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
