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
        <td width="15%" align="right">Student ID:</td>
        <td width="15%"><input type="text" style="width: 100px;" class="txt" value="<?=$form[students_label_id]?>" id="" name="form[students_label_id]"></td>
        <td width="15%" align="right">First Name:</td>
        <td width="15%"> <input type="text" style="width: 100px;" class="txt" value="<?=$form[students_first_name]?>" id="" name="form[students_first_name]"></td>
        <td width="15%" align="right">Last Name:</td>
        <td width="15%"><input type="text" style="width: 100px;" class="txt" value="<?=$form[students_last_name]?>" id="" name="form[students_last_name]"></td>
        <td width="15%"><input type="submit" value="Search" style="float: left;" name="students"></td>
        </tr>
        </table>        
               
			
                                                                  
        </form>   

        
        

        
    <br />  
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
				if(isset($form[group_type])){$school_type_id_toggle = $form['group_type'];}
				options_groups_type_codec($db, $_SESSION[culture], $_SESSION[u_id], $_SESSION[u_type], $_SESSION[permissions], '1', $school_type_id_toggle);
				print_r ($options_groups_type_codec);
				?>
				</select></td>
        <td width="15%"> <input type="submit" value="Search" style="float: left;" name="groups"></td>
        </tr>
        </table>
        
                                                                  
        </form>  
        
        

        
        

        
        
            <br />
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
                
                
                </div>               
           </div> 
          <? include($_CFG['fragmentsDir'].$_SESSION[culture].'/fragment_footer.inc');?>
         </div>   
     </div>
     <!-- /Container -->
     
     
