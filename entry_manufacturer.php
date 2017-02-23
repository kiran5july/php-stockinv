<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('New Manufacturer entry:');
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
  	$sName = "";
  	$sAddrId = "";
  	$sAddr1 = "";
  	$sAddr2 = "";
  	$sCity = "";
  	$sState = "";
  	$sCountry = "";
  	$sZip = "";
  	
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names
  	$sId = $_POST['sId'];
  	$sName = isset($_POST['sName'])?$_POST['sName']:"";
  	$sAddrId = isset($_POST['sAddrId'])?$_POST['sAddrId']:"";
  	$sAddr1 = isset($_POST['sAddr1'])?$_POST['sAddr1']:"";
  	$sAddr2 = isset($_POST['sAddr2'])?$_POST['sAddr2']:"";
  	$sCity = isset($_POST['sCity'])?$_POST['sCity']:"";
  	$sState = isset($_POST['sState'])?$_POST['sState']:"";
  	$sCountry = isset($_POST['sCountry'])?$_POST['sCountry']:"";
  	$sZip = isset($_POST['sZip'])?$_POST['sZip']:"";  	
  	// start it now because it must go before headers

    // check forms filled in
	if ((strlen($sName) < 1)) {
    	echo "<p class='inputerror'>Warning! Missing Required fields -Manufacturer Name. Try again.</p>";
    }elseif(strlen($sAddr1)>1){
    	if ((strlen($sCity) < 1)) {
    		$bInpValid = false;
    		echo "<p class='inputerror'>Warning! Missing Required fields -City. Try again.</p>";
    	}elseif ((strlen($sState) < 1)) {
    		$bInpValid = false;
    		echo "<p class='inputerror'>Warning! Missing Required fields -State. Try again.</p>";
    	}if ((strlen($sCountry) < 1)) {
			$bInpValid = false;
    		echo "<p class='inputerror'>Warning! Missing Required fields -Country. Try again.</p>";
    	}elseif ((strlen($sZip) < 1)) {
    		$bInpValid = false;
    		echo "<p class='inputerror'>Warning! Missing Required fields -Zip Code. Try again.</p>";
    	}
    }//end address validation
	else{
		try{
  		// if ok, put in db
			$db->autocommit(false);
			$sQuery = "insert into T_MANUFACTURER (NAME, ADDR_ID) values 
                         	('".$sName."','".$sAddrId."')";
			//echo "SQL= ".$sQuery;
  			$result = dbinsert($sQuery);
  		
  			if (!$result) {
					echo "<p class='excepmsg'>Exception during creating Manufacturer master record.<br>".$db->error."</p>";
					throw new Exception('KMDB Exception: Exception during creating Manufacturer master record.');
	  		}
  			$sId = $result;
  			if(!$sAddrId && $sAddr1 != "")
  			{
  				$result = $db->query("insert into T_ADDR (ADDR1, ADDR2, CITY, STATE, COUNTRY, ZIP_CODE) values
		                         ('".$sAddr1."', '".$sAddr2."', '".$sCity."','".$sState."','".$sCountry."','".$sZip."')");
  				if (!$result) {
  					echo "<p class='excepmsg'>Exception during inserting Address record.</p>";
  					throw new Exception('Exception during inserting Address record.');
  				}
  				$sAddrId = $db->insert_id;
  			
  			}//end address
  			$db->commit();
  			echo "<p class='successmsg'>Success!<br>*".$sName."</p>";

		}catch (Exception $e) {
			$db->rollback();
     	    echo "<p class='excepmsg'>Record entry Exception. Please try again.</p>";
 		}
	}

  	}//end of btnSubmit

 	?>
<p id='frmerror' class='inputerror'></p>
    <form method="post" name="frmParent" id="frmParent" action="entry_manufacturer.php">
    <table class="form">

    <tr>
    	<td>Manufacturer Name:</td>
    	<td><input type="text" name="sName" size="30" maxlength="100" value="<?=$sName?>"/>
    	<input type="text" name="sId" size="4" maxlength="4" value="<?=$sId?>" readonly/></td></tr>

    </table>
    
     <h2>Address (Optional)</h2>
     <table class="form">
         <tr>
    	<td>Address 1:</td>
    	<td><input type="text" name="sAddr1" id="sAddr1" size="16" maxlength="50" value="<?=$sAddr1?>"/>
    	<input type="text" name="sAddrId" size="5" maxlength="5" value="<?=$sAddrId?>" readonly/>
    <tr>
    	<td>Address 2:</td>
    	<td><input type="text" name="sAddr2" id="sAddr2" size="16" maxlength="50" value="<?=$sAddr2?>"/>
    <tr>
    	<td>City:</td>
    	<td><input type="text" name="sCity" id="sCity" size="16" maxlength="25" value="<?=$sCity?>"/>
    <tr>
    	<td>State:</td>
    	<td><input type="text" name="sState" id="sState" size="16" maxlength="25" value="<?=$sState?>"/>
     <tr>
    	<td>Country:</td>
    	<td><select name="sCountry" id="sCountry">
  				<option value="India" <?=($sCountry == "India") ? "selected" : "" ?>>India</option>
  				<option value="United States" <?=($sCountry == "United States") ? "selected" : "" ?>>United States</option>
			</select></td></tr> 
    <tr>
    	<td>Zip Code:</td>
    	<td><input type="text" name="sZip" id="sZip" size="16" maxlength="10" value="<?=$sZip?>"/>

    	
     </table>
     
     <table class="form">
     <tr><td colspan=2 align="center"><input type="submit" name="btnSubmit" value="Submit"/>
     	<input type="reset" value="Clear Fields"/></td></tr>
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
