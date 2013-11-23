<?
//Fetch Users
$group_type = $form[group_type];
$group_name = $form[group_name];
$group_teacher = $form[group_teacher];
$contacts_type = $form[contacts_type];
$contacts_status = $form[contacts_status];
$groups_array = search_array_groups($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $group_type, $group_name, $group_teacher, $contacts_type, $contacts_status);
$array_groups_size = sizeof($groups_array);
?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                
                
                <div class="login-home">
      
        
        <div class="login-panel">
	 <form id="frmLogin" name="groups" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Search Groups</h3></div>
                

                
               
		<table width="100%" cellpadding="3" cellspacing="3">
        <tr>
        <td width="" align="right"><b>Group Name:</b></td>
        <td width=""><select name="form[group_name]" style="width: 125px;" class="txt">
			    <?
				if(isset($form[group_name])){$groups_id_toggle = $form['group_name'];}
				options_groups($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $groups_id_toggle);
				print_r ($options_groups);
				?>
				</select>
		</td>
				
				
        <td width="" align="right"><b>Contact Type:</b></td>
        <td width="">
				<select name="form[contacts_type]" style="width: 125px;" class="txt">
			    <?unset($options_codec_primary);
				if(isset($form[contacts_type])){$codec_primary_id_toggle = $form['contacts_type'];}
				options_codec_primary($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $codec_primary_id_toggle);
				print_r ($options_codec_primary);
				?>
				</select>  
        </td>		
				
				

        <td width="" align="right"><b>Status:</b></td>
        <td width="">
				<select name="form[contacts_status]" style="width: 125px;" class="txt">
			    <?
				if(isset($form[contacts_status])){$codec_status_id_toggle = $form['contacts_status'];}
				options_codec_status($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $codec_status_id_toggle);
				print_r ($options_codec_status);
				?>
				</select>    
        </td>     



        <td width="">
        <input type="submit" value="Search" style="float: left;" name="groups">
        </td>
        
        </tr>
        </table>
        
                                                                  
        </form>  
        	         
         <div style="padding-top: 10px; padding-right: 10px; text-align: right;"><a href="generate_csv_groups.php?form[group_name]=<?=$form[group_name]?>&form[group_teacher]=<?=$form[group_teacher]?>&form[group_type]=<?=$form[group_type]?>" target="_blank">generate csv file</a></div>        
        </div>
</div> 




                
<div style="padding: 20px;">

<table width="100%" cellpadding="3">
<tr>
<td width=""><b>Group Name</b></td>
<td width=""><b>Student Name</b></td>
<td width=""><b>Contact Name</b></td>
<td width=""><b>Contact Email</b></td>
<td width=""><b>Type</b></td>
<td width=""><b>Status</b></td>
</tr>
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
<td width="10%"><a href="<?=$_CFG['rootURL']?>groups/?edit=true&id=<?=$groups_array[$i]['id']?>"><?=$groups_array[$i]['group_name']?></a></td>
<td width="20%"><?=$groups_array[$i]['students_first_name']?>&nbsp;<?=$groups_array[$i]['students_last_name']?></td>
<td width="20%"><?=$groups_array[$i]['records_first_name']?>&nbsp;<?=$groups_array[$i]['records_last_name']?></td>
<td width="10%"><?=$groups_array[$i]['records_email']?></td>
<td width="20%"><?if($groups_array[$i]['contacts_type']=='P'){?>Primary<?}else{?>Secondary<?}?></td>
<td width="10%"><?if($groups_array[$i]['group_status']=='1'){?>Active<?}else{?>Inactive<?}?></td>
</tr>
<?
}if($array_groups_size==0)
{
?>
<tr>
<td width="100%" colspan="5">There are no groups in your search</td>
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
     
