<?
$label_display = display_2($_DISPLAY[2]);
?>


    <!-- Header -->
    <div id="Header">

    

	
	
<div id="loginRegion" class="login-region">
      
		    <div class="logout">&nbsp;
		                <? if($_SESSION['u_type']=='0'){?>
						<a href="<?=$_CFG['rootURL']?>login">Login</a>
						<?}elseif($_SESSION['u_type']!='0'){?>
						<a href="<?=$_CFG['rootURL']?>logout/">Logout</a>
						<?}?>
			</div>
	
			<div class="account">
						<?if($_SESSION['u_type']!='0'){?>Welcome: <strong><?=$_SESSION['screen_name'];?></strong> | <?}?>
			</div>
   
</div>
         
         
         
         <div id="LogoContainer">
            <div class="logo">
                    <a class="logo-img" href="<?=$_CFG['rootURL']?>"></a>

            </div>
         </div>
         <div id="SFish-Menu">
    <div class="nav_top_left">&nbsp;</div>
    <div class="nav_top_center">&nbsp;</div>
    <div class="nav_top_right">&nbsp;</div>
    <div class="nav_mid">
    <div class="menu">
    <!-- Drop down menu -->    
    <ul class="sf-menu">
<?if($_SESSION['u_type']=='2'){?>
        
        
         <li class="current first">
            <a class="" href="<?=$_CFG['rootURL']?>dashboard/">Dashboard</a>
        </li>
        
        <li class="">
            <a class="" href="<?=$_CFG['rootURL']?>students/">Students</a>
        </li>
        <li>
            <a class="" href="<?=$_CFG['rootURL']?>records/">Contact Records</a>
        </li>
        <li>
            <a  href="<?=$_CFG['rootURL']?>groups/">Groups</a>				
        </li>

        <li>
            <a href="<?=$_CFG['rootURL']?>lists/">Print</a>
        </li>	
        <li class="last">
            <a href="<?=$_CFG['rootURL']?>account/">My Account</a>
        </li>	
       
        <?}elseif($_SESSION['u_type']=='1'){?>
        <li class="current first">
            <a class="" href="<?=$_CFG['rootURL']?>dashboard/">Dashboard</a>
        </li>

        <li class="">
            <a class="" href="<?=$_CFG['rootURL']?>users/">User Accounts</a>
        </li>
        <li>
            <a class="" href="<?=$_CFG['rootURL']?>schools/">Schools</a>
        </li>
        <li class="last">
            <a  href="<?=$_CFG['rootURL']?>types/">School Types</a>				
        </li>

   
       
        <?}
      	elseif($label_display=='Reset Password')
        {
        ?>
        <li class="last">
       <a class="" href="<?=$_CFG['rootURL']?>password/">Reset Password</a>
        </li>
      <?}elseif($label_display=='User Login')
      {?>
       <li class="last">
       <a class="" href="<?=$_CFG['rootURL']?>login/">User Login</a>
        </li>
      <?}?>
     </ul>

    </div>
    </div>
</div>  
    </div>
   <!-- /Header -->