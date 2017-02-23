<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Employee Activity View:');
  try{
  // start session which may be needed later
  session_start();
  // register session variable
  $username = $_SESSION['valid_user'];
  
  if(empty($username))
  {
  	echo "Your session timed out. Please <a href=\"login.php\" target=\"main\">click here to login</a> again.";
  }else{
  	
?>
  	<script src="includes/jquery-2.2.3.js"></script>
  	


    <form method="post" name="frmParent" action="">
    <table class="form">

    <tr>
    	<td>Employee Name:</td>
    	<td><input type="text" name="sEmpName" id="sEmpName" size="16" maxlength="16" value=""/>
    		<input type="text" name="sEmpId" id="sEmpId" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickEmp(sEmpName.value)"></td></tr>
  	<tr>
 	 	<td>Start Date:</td>
  		<td><input type="text" id="dtStart" name="dtStart" size="16" maxlength="19" value="" readonly/>
 	 	<a href="javascript:NewCal('dtStart','YYYYMMDD',false,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>
  	<tr>
 	 	<td>End Date:</td>
  		<td><input type="text" id="dtEnd" name="dtEnd" size="16" maxlength="19" value="" readonly/>
  		<a href="javascript:NewCal('dtEnd','YYYYMMDD',false,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>
    <tr>
    	<td colspan=2 align="center"><input type="reset" value="Reset Filters"></td></tr>
  	</table>
  	</form>
    </table>

    </form>
<!-- table><tr class="menu" bgcolor="#f69546">
	<td class="stk-hld" width="1%"><a href="#">Stock-held</a></td>
	<td class="stk-out" width="1%"><a href="#">Stock-out</a></td>
	<td class="stk-in" width="5%"><a href="#">Stock-in</a></td>
	<td class="sle-act" width="5%"><a href="#">Sales Activity</a></td>
	</tr>
</table-->

<br><a class="stk-hld" href="#">Stock-held</a>
<a class="stk-rcd" href="#">Stock-Received</a>
<a class="stk-in" href="#">Stock-Returned</a>
<a class="sle-act"href="#">Sales Activity</a>

  <div class="result-container"></div>
  <p>

    <script type="text/javascript">
    $(".stk-hld").click(function() {
    	var iEmpId = document.getElementById("sEmpId").value;
    	var dtStart = document.getElementById("dtStart").value;
    	var dtEnd = document.getElementById("dtEnd").value;
         //Replace path/to/your/file.html and #id_of_element_to_fetch with appropriate values
         $('.result-container').load('v_stk_hld_emp.php?empId='+iEmpId);
         return false;
    });
    	
        $(".stk-rcd").click(function() {
        	var iEmpId = document.getElementById("sEmpId").value;
        	var dtStart = document.getElementById("dtStart").value;
        	var dtEnd = document.getElementById("dtEnd").value;
             //Replace path/to/your/file.html and #id_of_element_to_fetch with appropriate values
             $('.result-container').load('v_stk_out_emp.php?empId='+iEmpId+'&dtStart='+dtStart+'&dtEnd='+dtEnd);
             return false;
        });
 		$(".stk-in").click(function() {
        	var iEmpId = document.getElementById("sEmpId").value;
        	var dtStart = document.getElementById("dtStart").value;
        	var dtEnd = document.getElementById("dtEnd").value;
             //Replace path/to/your/file.html and #id_of_element_to_fetch with appropriate values
             $('.result-container').load('v_stk_in_emp.php?empId='+iEmpId+'&dtStart='+dtStart+'&dtEnd='+dtEnd);
             return false;
        });
 		$(".sle-act").click(function() {
        	var iEmpId = document.getElementById("sEmpId").value;
        	var dtStart = document.getElementById("dtStart").value;
        	var dtEnd = document.getElementById("dtEnd").value;
            //Replace path/to/your/file.html and #id_of_element_to_fetch with appropriate values
            $('.result-container').load('v_sl_act_emp.php?empId='+iEmpId+'&dtStart='+dtStart+'&dtEnd='+dtEnd);
            return false;
       });
    </script>
  </p>
 	<?php
  	}//end session else
   // end page
   do_html_footer();
  }catch (Exception $e) {
     do_html_header('Application Error:');
     echo $e->getMessage();
     do_html_footer();
     exit;
  }
?>
