<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Stock Availability filter:');
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
  	$sProdId = "";
  	$sProdName = "";
  	$sWHCode = "";
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names


  	$sProdId = $_POST['sProdId0'];
  	$sProdName = $_POST['sProdName0'];
  	$sWHCode = $_POST['sWHCode0'];
  	$sWHId = $_POST['sWHId0'];
  	//$dtStart = $_POST['dtStart'];
  	//$dtEnd = $_POST['dtEnd'];

  	}//end of btnSubmit


 	//echo $username.$email.$secretq.$secreta;
 	?>


    <form method="post" name="frmParent" action="rpt_stk_avl.php" target="_blank">
    <table class="form">
    <tr>
    	<td>Product:</td>
    	<td><input type="text" name="sProdName0" id="sProdName0" size="16" maxlength="25" value=""/>
    		<input type="text" name="sProdId0" id="sProdId0" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickProd(sProdName0.value,'i','0')"></td></tr>
    <tr>
    	<td>Warehouse Loc:</td>
    	<td><input type="text" name="sWHCode0" id="sWHCode0" size="16" maxlength="25" value=""/>
    		<input type="text" name="sWHId0" id="sWHId0" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickWarehouse(sWHCode0.value,'0')"></td></tr>
	<tr>    			
    	<td>Group 1 Criteria:</td>
    	<td><select name="sGrpBy">
  				<option value="Product">Product</option>
  				<option value="Warehouse" selected>Warehouse</option>
			</select></td></tr>  

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
