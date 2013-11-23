<?//Fetch Students
$form_r = $_REQUEST[form_r];
$form = $_REQUEST[form];

$students_gender = $form[students_gender];
$students_status = $form[students_status];


$students_array = print_array_students($db, $_SESSION['culture'], $_SESSION['u_id'], $_SESSION['u_type'], $students_gender, $students_status);
$array_students_size = sizeof($students_array);
?>

<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
          <div id="Content">
                <div class="main-content">

<div class="login-home">
      
        
        <div class="login-panel">
                        
                 <form id="frmLogin" name="students" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Search Students</h3></div>
                

   <table width="100%" cellpadding="3" cellspacing="3">
        <tr>
        <td width="95" align="right"><b>Gender:</b></td>
        <td width="162">
				<select name="form[students_gender]" style="width: 125px;" class="txt">
			    <?
				if(isset($form[students_gender])){$codec_gender_id_toggle = $form['students_gender'];}
				options_codec_gender($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $codec_gender_id_toggle);
				print_r ($options_codec_gender);
				?>
				</select> 
        </td>
        <td width="90" align="right"><b>Status:</b></td>
        <td width="162">
				<select name="form[students_status]" style="width: 125px;" class="txt">
			    <?
				if(isset($form[students_status])){$codec_status_id_toggle = $form['students_status'];}
				options_codec_status($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $codec_status_id_toggle);
				print_r ($options_codec_status);
				?>
				</select>  
        </td>
        <td><input type="submit" value="Search" style="float: left;" name="students">
      </td>
        </tr>
        </table>    
               
			
            </form>                                                      
         
        
       <div style="padding-top: 10px; padding-right: 10px; text-align: right;"><a href="generate_csv_students.php?form[students_gender]=<?=$form[students_gender]?>&form[students_status]=<?=$form[students_status]?>" target="_blank">generate csv file</a></div>        
                 
  </div>
  </div>
        
        
<div style="padding: 20px;">

<table width="100%" cellpadding="3">
<tr>
<td width=""><b>Student Name</b></td>
<td width=""><b>School Name</b></td>
<td width=""><b>DOB</b></td>
<td width=""><b>Gender</b></td>
<td width=""><b>Status</b></td>
</tr>
<?
for($i=0; $i<$array_students_size; $i++)
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
<td width=""><a href="<?=$_CFG['rootURL']?>students/?edit=true&id=<?=$students_array[$i]['id']?>"><?=$students_array[$i]['students_first_name']?> <?=$students_array[$i]['students_last_name']?></a></td>
<td width=""><?=$students_array[$i]['schools_label']?></td>
<td width=""><?=$students_array[$i]['students_dob']?></td>
<td width=""><?=$students_array[$i]['students_gender']?></td>
<td width=""><?if($students_array[$i]['students_status']=='1'){?>Active<?}else{?>Inactive<?}?></td>
</tr>
<?
}if($array_students_size==0)
{
?>
<tr>
<td width="100%" colspan="5">There are no students in the database</td>
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
     
