<?//Fetch Users
$group_type = $form[group_type];
$group_name = $form[group_name];
$group_teacher = $form[group_teacher];
$groups_array = search_array_groups($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $group_type, $group_name, $group_teacher);
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
        <td width="15%" align="right">Group Name:</td>
        <td width="15%"><select name="form[group_name]" style="width: 125px;" class="txt">
			    <?
				if(isset($form[group_name])){$groups_id_toggle = $form['group_name'];}
				options_groups($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $groups_id_toggle);
				print_r ($options_groups);
				?>
				</select></td>
        <td width="15%" align="right">Teacher Name:</td>
        <td width="15%"> <input type="text" style="width: 100px;" class="txt" value="<?=$form[group_teacher]?>" id="" name="form[group_teacher]"></td>
        <td width="15%" align="right">Group Type:</td>
        <td width="15%"> <select name="form[group_type]" style="width: 125px;" class="txt">
			    <?
				if(isset($form[group_type])){$group_type_id_toggle = $form['group_type'];}
				options_groups_type_codec($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $group_type_id_toggle);
				print_r ($options_groups_type_codec);
				?>
				</select></td>
        <td width="15%"> <input type="submit" value="Search" style="float: left;" name="groups"></td>
        </tr>
        </table>
        
                                                                  
        </form>  
        	         
                 
        </div>
</div> 




                
<div style="padding: 20px;">
<table width="100%" cellpadding="3">
<tr>
<td width="20%"><b>Group Name</b></td>
<td width="20%"><b>Student Name</b></td>
<td width="20%"><b>School Name</b></td>
<td width="10%"><b>Type</b></td>
<td width="20%"><b>Teacher</b></td>
<td width="10%"><b>Status</b></td>
</tr>
</table>
<table width="100%" cellpadding="3">
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
<td width="20%"><a href="<?=$_CFG['rootURL']?>groups/?edit=true&id=<?=$groups_array[$i]['id']?>"><?=$groups_array[$i]['group_name']?></a></td>
<td width="20%"><?=$groups_array[$i]['students_first_name']?>&nbsp;<?=$groups_array[$i]['students_last_name']?></td>
<td width="20%"><?=$groups_array[$i]['schools_label']?></td>
<td width="10%"><? if($groups_array[$i]['group_type']=='1'){?>School<?}elseif($groups_array[$i]['group_type']=='2'){?>Class<?}elseif($groups_array[$i]['group_type']=='3'){?>Sports<?}elseif($groups_array[$i]['group_type']=='4'){?>Activity<?}elseif($groups_array[$i]['group_type']=='5'){?>Other<?}?></td>
<td width="20%"><?=$groups_array[$i]['group_teacher']?></td>
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
     
