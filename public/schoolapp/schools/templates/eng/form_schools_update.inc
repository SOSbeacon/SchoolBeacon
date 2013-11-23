<?
$_REQUEST[id] = urldecode($_REQUEST[id]);
$schools_id_sql = trim(addslashes(urldecode($_REQUEST[id])));

//Fetch Users
$schools_array = array_schools($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_schools_size = sizeof($schools_array);

school_data($db, $schools_id_sql);



?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">

<div style="padding: 20px;">
<table width="100%" cellpadding="3">
<tr>
<td width="60%"><b>School Name</b></td>
<td width="20%"><b>School Type</b></td>
<td width="20%"><b>Status</b></td>
</tr>
</table>
<table width="100%" cellpadding="3">
<?
for($i=0; $i<$array_schools_size; $i++)
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
<td width="60%"><a href="?edit=true&id=<?=$schools_array[$i]['id']?>"><?=$schools_array[$i]['schools_label']?></a></td>
<td width="20%"><?=$schools_array[$i]['schools_type_label']?></td>
<td width="20%"><? if($schools_array[$i]['schools_status']=='1'){?>Active<?}else{?>Inactive<?}?></td>
</tr>
<?
}if($array_schools_size==0)
{
?>
<tr>
<td width="100%" colspan="2">There are no schools in the database</td>
</tr>
<?	
}
?>
</table>               
</div>

<div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="update" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Edit School</h3></div>
                 <?if(isset($reg_update['schools'])){?><div class="information">Record Updated</div><?}?>
                 <?if(isset($reg_insert['schools'])){?><div class="information">Record Added</div><?}?>
                 
              <div class="div1">
                <?if(isset($error[schools_type])){?><div class="error">Required Field</div><?}?>
                <div class="left">School:<span style="color:#ff0000;">*</span></div>
                <div class="right">
               <select name="form_r[schools_type]" class="txt">
			    <?
				if(isset($form_r[schools_type])){$school_type_id_toggle = $form_r['schools_type'];}else{$school_type_id_toggle = $school_data[0]['schools_type'];}
				options_schools_type($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $school_type_id_toggle);
				print_r ($options_schools_type);
				?>
				</select>
                </div>
                </div>
	
	
				<div class="div1">
                <?if(isset($error[schools_label])){?><div class="error">Required Field</div><?}?>
                <?if(isset($reg_error[schools_label])){?><div class="error">Duplicate School Name</div><?}?>
                <div class="left">School Name:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="text" class="txt" value="<?if(isset($form_r['schools_label'])){print $form_r['schools_label'];}else{print $school_data[0]['schools_label'];}?>" id="user" name="form_r[schools_label]">
                </div>
                </div>
                
                <div class="div1">
                <?if(isset($error[schools_status])){?><div class="error">Required Field</div><?}?>
                <div class="left">Status:<span style="color:#ff0000;">*</span></div>
                <div class="right">
                <input type="radio" value="1" id="active" name="form_r[schools_status]"<?if($form_r[schools_status]=='1'){?> checked<?}elseif(!isset($form_r[schools_status]) && $school_data[0]['schools_status']=='1'){?> checked<?}?>>&nbsp;Active
                <input type="radio" value="2" id="active" name="form_r[schools_status]"<?if($form_r[schools_status]=='2'){?> checked<?}elseif(!isset($form_r[schools_status]) && $school_data[0]['schools_status']=='2'){?> checked<?}?>>&nbsp;Inactive
                </div>
                </div>
                                               
                <div class="div1">
                <div class="left">&nbsp;</div>
                <div class="right"><input type="hidden" name="id" value="<?=$school_data[0]['id']?>"><input type="submit" value="Edit School" style="float: left;" name="update"></div>
                </div>
                
                                                  
        </form>                      
        </div>
</div>                </div>               
           </div>
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
