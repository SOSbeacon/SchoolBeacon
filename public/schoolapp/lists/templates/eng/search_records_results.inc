<?
//Fetch Users
$records_first_name = $form[records_first_name];
$records_last_name = $form[records_last_name];
$contacts_type = $form[contacts_type];
$contacts_status = $form[contacts_status];
$records_array = search_array_records($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $records_first_name, $records_last_name, $contacts_type, $contacts_status);
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
        <td width="95" align="right"><b>Status:</b></td>
        <td width="162">
				<select name="form[contacts_status]" style="width: 125px;" class="txt">
			    <?unset($options_codec_status);
				if(isset($form[contacts_status])){$codec_status_id_toggle = $form['contacts_status'];}
				options_codec_status($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $codec_status_id_toggle);
				print_r ($options_codec_status);
				?>
				</select>  

       
          <td width="95" align="right"><b>Contact Type:</b></td>
        <td width="162">
				<select name="form[contacts_type]" style="width: 125px;" class="txt">
			    <?unset($options_codec_primary);
				if(isset($form[contacts_type])){$codec_primary_id_toggle = $form['contacts_type'];}
				options_codec_primary($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $codec_primary_id_toggle);
				print_r ($options_codec_primary);
				?>
				</select>  
        </td>		
        
		</td>
        <td><input type="submit" value="Search" style="float: left;" name="records">
       </td>
		
        
        </tr>
       </table>
        
                                                                  
   </form>  
     
     
             
       <div style="padding-top: 10px; padding-right: 10px; text-align: right;"><a href="generate_csv_contacts.php?form[contacts_type]=<?=$form[contacts_type]?>&form[contacts_status]=<?=$form[contacts_status]?>" target="_blank">generate csv file</a></div>        
       
                           
   </div>
   </div> 



<div style="padding: 20px;">
<table width="100%" cellpadding="3">

</table>
<table width="100%" cellpadding="3">
<tr>
<td width=""><b>Contact Name</b></td>
<td width=""><b>Student Name</b></td>
<td width=""><b>Email Address</b></td>
<td width=""><b>Cell Phone</b></td>
<td width=""><b>Work Phone</b></td>
<td width=""><b>Home Phone</b></td>
<td width=""><b>Type</b></td>
<td width=""><b>Status</b></td>
</tr>
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
<td width=""><a href="<?=$_CFG['rootURL']?>records/?edit=true&id=<?=$records_array[$i]['id']?>"><?=$records_array[$i]['records_first_name']?> <?=$records_array[$i]['records_last_name']?></a></td>
<td width=""><?=$records_array[$i]['students_first_name']?> <?=$records_array[$i]['students_last_name']?></td>
<td width=""><?=$records_array[$i]['records_email']?></td>
<td width=""><?=$records_array[$i]['records_phone_mobile']?></td>
<td width=""><?=$records_array[$i]['records_phone_work']?></td>
<td width=""><?=$records_array[$i]['records_phone_home']?></td>
<td width=""><?if($records_array[$i]['contacts_type']=='P'){?>Primary<?}else{?>Secondary<?}?></td>
<td width=""><?if($records_array[$i]['records_status']=='1'){?>Active<?}else{?>Inactive<?}?></td>
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
     
