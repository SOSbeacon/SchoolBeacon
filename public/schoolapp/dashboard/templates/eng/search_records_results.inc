<?//Fetch Users
$records_first_name = $form[records_first_name];
$records_last_name = $form[records_last_name];
$records_array = search_array_records($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $records_first_name, $records_last_name);
$array_records_size = sizeof($records_array);
?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">

     <div class="login-home">
      
        
        <div class="login-panel">
		         
   <form id="frmLogin" name="records" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Search Contacts</h3></div>
                

                
               
        <table width="100%" cellpadding="3" cellspacing="3">
        <tr>
        <td width="15%" align="right">First Name:</td>
        <td width="15%"><input type="text" style="width: 100px;" class="txt" value="<?=$form[records_first_name]?>" id="" name="form[records_first_name]"></td>
        <td width="15%" align="right">Last Name:</td>
        <td width="15%"><input type="text" style="width: 100px;" class="txt" value="<?=$form[records_last_name]?>" id="" name="form[records_last_name]"></td>
        <td width="15%"><input type="submit" value="Search" style="float: left;" name="records"></td>
        <td width="15%"></td>
        <td width="15%"></td>
        </tr>
        </table>
        
                                                                  
        </form>  
                         
        </div>
</div> 



<div style="padding: 20px;">
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
<td width="20%"><a href="<?=$_CFG['rootURL']?>records/?edit=true&id=<?=$records_array[$i]['id']?>"><?=$records_array[$i]['records_first_name']?> <?=$records_array[$i]['records_last_name']?></a></td>
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

               </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
