<?//Fetch Users
$user_array = array_users($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_users_size = sizeof($user_array);

$schools_array = array_schools($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_schools_size = sizeof($schools_array);

$schools_type_array = array_schools_type($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $_SESSION['permissions'], '1');
$array_schools_type_size = sizeof($schools_type_array);
?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
           <div id="Content">
                <div class="main-content">

                

<div style="padding: 20px;">
<h3 style="margin-left: 0px; margin-bottom: 5px;">System Users</h3>
<table width="100%" cellpadding="3">
<tr>
<td width="20%"><b>User Name</b></td>
<td width="20%"><b>School Name</b></td>
<td width="20%"><b>Screen Name</b></td>
<td width="20%"><b>Email Address</b></td>
<td width="20%"><b>User Title</b></td>
</tr>
</table>
<table width="100%" cellpadding="3">
<?
for($i=0; $i<$array_users_size; $i++)
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
<td width="20%"><a href="<?=$_CFG['rootURL']?>users/?edit=true&id=<?=$user_array[$i]['id']?>"><?=$user_array[$i]['user_f_name']?> <?=$user_array[$i]['user_l_name']?></a></td>
<td width="20%"><?=$user_array[$i]['schools_label']?></td>
<td width="20%"><?=$user_array[$i]['passports_name']?></td>
<td width="20%"><?=$user_array[$i]['user_email']?></td>
<td width="20%"><?=$user_array[$i]['user_title']?></td>
</tr>
<?
}if($array_users_size==0)
{
?>
<tr>
<td width="100%" colspan="5">There are no users in the database</td>
</tr>
<?	
}
?>
</table>               
</div>
                
                
                
                
              
<div style="padding: 20px;">
<h3 style="margin-left: 0px; margin-bottom: 5px;">Schools</h3>  
<table width="100%" cellpadding="3">
<tr>
<td width="20%"><b>School Name</b></td>
<td width="80%"><b>School Type</b></td>
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
<td width="20%"><a href="<?=$_CFG['rootURL']?>schools/?edit=true&id=<?=$schools_array[$i]['id']?>"><?=$schools_array[$i]['schools_label']?></a></td>
<td width="80%"><?=$schools_array[$i]['schools_type_label']?></td>
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
                
               





     
<div style="padding: 20px;">
<h3 style="margin-left: 0px; margin-bottom: 5px;">School Types</h3>
<table width="100%" cellpadding="3">
<tr>
<td width="100%"><b>School Type</b></td>
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
<td width="100%"><a href="<?=$_CFG['rootURL']?>types/?edit=true&id=<?=$schools_type_array[$i]['id']?>"><?=$schools_type_array[$i]['schools_type_label']?></a></td>
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


                
                
                </div>               
           </div> 
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
     
