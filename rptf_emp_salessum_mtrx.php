<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Report: Employee Sales Matrix:');
  try{
  // start session which may be needed later
  session_start();
  // register session variable
  $username = $_SESSION['valid_user'];
  
  if(empty($username))
  {
  	echo "Your session timed out. Please <a href=\"login.php\" target=\"main\">click here to login</a> again.";
  }else{

  	//initialize variables
  	$sId = "";
  	$sEmpId = "";
  	$sEmpName = "";
  	$sProdId = "";
  	$sProdName = "";
	$dtGiven = "";
  	$iQty = "";
  	$dtStart = "";
  	$dtEnd = "";
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names

  	$sEmpId = $_POST['sEmpId'];
  	$sEmpName = $_POST['sEmpName'];
  	$sProdId = $_POST['sProdId0'];
  	$sProdName = $_POST['sProdName0'];
  	$dtStart = $_POST['dtStart'];
  	$dtEnd = $_POST['dtEnd'];

  	// start it now because it must go before headers

    // check forms filled in

  	}//end of btnSubmit


 	//echo $username.$email.$secretq.$secreta;
 	?>

    <form method="post" name="frmParent" action="rpt_emp_salessum_mtrx.php" target="_blank">
    <table class="form">

    <tr>
    	<td>Employee Name:</td>
    	<td><input type="text" name="sEmpName" id="sEmpName" size="16" maxlength="16"/>
    		<input type="text" name="sEmpId" id="sEmpId" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickEmp(sEmpName.value)"></td></tr>
    <tr>
    	<td>Start Date:</td>
    	<td><input type="text" id="dtStart" name="dtStart" size="16" maxlength="19" readonly/>
    			<a href="javascript:NewCal('dtStart','YYYYMMDD',false,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>
    <tr>
    	<td>End Date:</td>
    	<td><input type="text" id="dtEnd" name="dtEnd" size="16" maxlength="19" readonly/>
    			<a href="javascript:NewCal('dtEnd','YYYYMMDD',false,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>

    <tr>
    	<td colspan=2 align="center"><input type="submit" name="btnSubmit" value="Get My Report"><input type="reset" value="Reset Filters"></td></tr>
    </table>
    </form>
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
