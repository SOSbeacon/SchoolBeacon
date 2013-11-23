<?//Fetch Users
$schools_type_array = array_schools_type($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_schools_type_size = sizeof($schools_type_array);
?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">
                
<div style="padding: 20px;">
<table width="100%" cellpadding="3">
<tr>
<td width="80%"><b>School Type</b></td>
<td width="20%"><b>Status</b></td>
</tr>
</table>
<table width="100%" cellpadding="3">
<?
for($i=0; $i<$array_schools_type_size; $i++)
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
<td width="80%"><a href="?edit=true&id=<?=$schools_type_array[$i]['id']?>"><?=$schools_type_array[$i]['schools_type_label']?></a></td>
<td width="20%"><? if($schools_type_array[$i]['schools_type_status']=='1'){?>Active<?}else{?>Inactive<?}?></td>
</tr>
<?
}if($array_schools_type_size==0)
{
?>
<tr>
<td width="100%" colspan="2">There are no school types in the database</td>
</tr>
<?	
}
?>
</table>               
</div>

<div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="add" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Add A New School Type</h3></div>
                

                
                
	
	
				<div class="div1">
                <?if(isset($error[schools_type_label])){?><div class="error">Required Field</div><?}?>
                <?if(isset($reg_error[schools_type_label])){?><div class="error">Duplicate School Name</div><?}?>
                <div class="left">School Type:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?=$form_r[schools_type_label]?>" id="user" name="form_r[schools_type_label]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($error[schools_type_status])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[schools_type_status]"<?if($form_r[schools_type_status]=='1'){?> checked<?}elseif(!isset($form_r[schools_type_status])){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[schools_type_status]"<?if($form_r[schools_type_status]=='2'){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>
                                               
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="submit" value="Add School Type" style="float: left;" name="add"></div>
                </div>
                
                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
