<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Product Incentive entry:');
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
  	$sIncType = "";
  	$sIncUnit = "";
  	$dtStart = "";
  	$dtEnd = "";
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names
  	$sId = $_POST['sId'];
  	$sProdId = $_POST['sProdId0'];
  	$sProdName = $_POST['sProdName0'];
  	$sIncType = $_POST['sIncType'];
  	$sIncUnit= $_POST['sIncUnit'];
  	$dtStart = $_POST['dtStart'];
  	$dtEnd = $_POST['dtEnd'];
  	$iRecInsert = "";
  	// start it now because it must go before headers

    // check forms filled in
	if ((strlen($sProdId) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Missing Required fields -Product. Try again.</p>";
    }elseif(!is_numeric($sIncUnit)){
    	echo "<p class=\"inputerror\"Warning! Invalid data -Incentive Unit. Try again.</p>";
    }elseif($sIncUnit<0){
    	echo "<p class=\"inputerror\"Warning! Invalid data -Incentive Unit must be greater than 0. Try again.</p>";
    }
	else{
		try{
	  		//if ok, put in db
  			$result = dbinsert("insert into T_PROD_INCNTV (PROD_ID, INCNTV_TYPE, INCNTV_UNIT, BGN_DT, END_DT) values 
                         	(".$sProdId.",'".$sIncType."',".$sIncUnit.",'".$dtStart."','".$dtEnd."')");
  			if (!$result) {
    			echo "<p class='excepmsg'>KMDB Exception: Record entry failed. Please try again.</p>";
    			throw new Exception('Exception during inserting Product Incentive record.');
    			//die();
	  		}
  			$sId = $result;
  			echo "<p class='successmsg'>Success!<br>* ".$sProdName." / ".$sIncUnit." / ".$sIncType." -> ".$dtStart." - ".$dtEnd."</p>";

		}catch (Exception $e) {
     	    echo "<p class='excepmsg'>KMDB Exception: Record entry Exception. Please try again.</p>";
 		}
	}

  	}//end of btnSubmit


 	?>

    <form method="post" name="frmParent" action="entry_prod_incntv.php">
    <table class="form">
    <tr>
    	<td>Id:</td>
    	<td><input type="text" name="sId" size="16" maxlength="16" value="<?=$sId?>" readonly/></td></tr>    
    <tr>
    	<td>Product:</td>
    	<td><input type="text" name="sProdName0" id="sProdName0" size="16" maxlength="25" value="<?=$sProdName?>"/>
    		<input type="text" name="sProdId0" id="sProdId0" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickProd(sProdName0.value,'a','0')"></td></tr>
    <tr>  
    	<td>Incentive Type:</td>
    	<td><select name="sIncType">
  				<option value="UNIT" <?=($sIncType == "UNIT") ? "selected" : "" ?>>UNIT</option>
  				<option value="PERCENTAGE" <?=($sIncType == "PERCENTAGE") ? "selected" : "" ?>>PERCENTAGE</option>
			</select></td></tr>   		
    <tr>  
    	<td>Incentive Unit:</td>
    	<td><input type="text" name="sIncUnit" size="16" maxlength="10" value="<?=$sIncUnit?>"/></td></tr>
    <tr>
    	<td>Start Date:</td>
    	<td><input type="text" id="dtStart" name="dtStart" size="16" maxlength="19" value="<?=$dtStart?>" readonly/>
    			<a href="javascript:NewCal('dtStart','YYYYMMDD',true,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>
    <tr>
    	<td>End Date:</td>
    	<td><input type="text" id="dtEnd" name="dtEnd" size="16" maxlength="19" value="<?=$dtEnd?>" readonly/>
    			<a href="javascript:NewCal('dtEnd','YYYYMMDD',true,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>

    <tr>
    	<td colspan=2 align="center"><input type="submit" name="btnSubmit" value="Insert"><input type="reset" value="Clear Fields"></td></tr>
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
     //exit;
  }
?>
