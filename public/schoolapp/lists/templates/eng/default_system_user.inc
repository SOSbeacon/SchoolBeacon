<!-- Container -->
    <div id="Container">
         <div id="ContainerInside">
           <div id="Content">
                <div class="main-content">

                
<div class="login-home">
      
        
        <div class="login-panel">
		         

        <form id="frmLogin" name="students" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Print Student Records</h3></div>
                
   <table width="100%" cellpadding="3" cellspacing="3" border="0">
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

        
        

        
    <br />  
          <form id="frmLogin" name="groups" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Print Group Records</h3></div>
                

                
               
		 <table width="100%" cellpadding="3" cellspacing="3">
        <tr>
        <td width="95" align="right"><b>Group Name:</b></td>
        <td width="162"><select name="form[group_name]" style="width: 125px;" class="txt">
			    <?
				if(isset($form[group_name])){$groups_id_toggle = $form['group_name'];}
				options_groups($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $groups_id_toggle);
				print_r ($options_groups);
				?>
				</select></td>
				
				
        <td width="90" align="right"><b>Contact Type:</b></td>
        <td width="162">
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
			    <?unset($options_codec_status);
				if(isset($form[contacts_status])){$codec_status_id_toggle = $form['contacts_status'];}
				options_codec_status($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $codec_status_id_toggle);
				print_r ($options_codec_status);
				?>
				</select>    
        </td>     
                
                
<?/*				
        <td width="10%" align="right">Teacher Name:</td>
        <td width="15%"> <input type="text" style="width: 100px;" class="txt" value="<?=$form[group_teacher]?>" id="" name="form[group_teacher]"></td>
        <td width="10%" align="right">Group Type:</td>
        <td width="15%"> <select name="form[group_type]" style="width: 125px;" class="txt">
			    <?
				if(isset($form[group_type])){$school_type_id_toggle = $form['group_type'];}
				options_groups_type_codec($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $school_type_id_toggle);
				print_r ($options_groups_type_codec);
				?>
				</select></td>
				
				*/?>


        <td width="90"> <input type="submit" value="Search" style="float: left;" name="groups">
        				</td>
        </tr>
        </table>
        
                                                                  
        </form>  
        
        

        
        

        
        
            <br />
          <form id="frmLogin" name="records" method="POST" action="<?=$PHP_SELF?>">
                <div><h3>Print Contact Records</h3></div>
                

                
               
               
        <table width="100%" cellpadding="3" cellspacing="3" border="0">
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

       
          <td width="90" align="right"><b>Contact Type:</b></td>
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
        
        
        </div>
</div>  
                
                
                </div>               
           </div> 
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
     
