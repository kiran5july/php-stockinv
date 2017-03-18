<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('New Employee entry:');
  ?>
  <script src="includes/fns_form_validations.js"></script>
  <?php 
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
  	$sEmpCode = "";
  	$sFirstName = "";
  	$sLastName = "";
  	$sDeptId = "";
  	$sDeptName = "";

  	$sUserId = "";
  	$sUserName = "";
  	$sUsrTypeId = "";
  	$sUsrType = "";
  	$email="";
  	$passwd="";
  	$passwd2="";
  	$secretq="";
  	$secreta="";
  	
  	$sAddrId = "";
  	$sAddr1 = "";
  	$sAddr2 = "";
  	$sCity = "";
  	$sState = "";
  	$sCountry = "";
  	$sZip = "";
  	$bInpValid = true;
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names
  	$sId = $_POST['sId'];
  	$sEmpCode = $_POST['sEmpCode'];
  	$sFirstName = $_POST['sFirstName'];
  	$sLastName = $_POST['sLastName'];
  	$sDeptId = $_POST['sDeptId'];
  	$sDeptName = $_POST['sDeptName'];

  	$sUserId = $_POST['sUserId'];
  	$sUsrType = $_POST['sUsrType'];
  	$sUserName = $_POST['sUserName'];
  	$email=$_POST['email'];
  	$passwd=$_POST['passwd'];
  	$passwd2=$_POST['passwd2'];
  	$secretq=$_POST['secretq'];
  	$secreta=$_POST['secreta'];
  	
  	$sAddrId = $_POST['sAddrId'];
  	$sAddr1 = $_POST['sAddr1'];
  	$sAddr2 = $_POST['sAddr2'];
  	$sCity = $_POST['sCity'];
  	$sState = $_POST['sState'];
  	$sCountry = $_POST['sCountry'];
  	$sZip = $_POST['sZip'];
  	// start it now because it must go before headers

    // check forms filled in
	if ((strlen($sFirstName) < 1)) {
		$bInpValid = false;
    	echo "<p class='inputerror'>Warning! Missing Required fields -First Name. Try again.</p>";
    }elseif ((strlen($sLastName) < 1)) {
    	$bInpValid = false;
    	echo "<p class='inputerror'>Warning! Missing Required fields -Last Name. Try again.</p>";
    }
    if(strlen($sUserName)>1){
    	if (!valid_email($email)) {
    		$bInpValid = false;
    		//throw new Exception('That is not a valid email address.  Please go back and try again.');
    		echo "<p class='inputerror'>Warning! Invalid email address. Try again.</p>";
    	}
    	// passwords not the same
    	elseif ($passwd != $passwd2) {
    		$bInpValid = false;
    		//throw new Exception('The passwords you entered do not match - please go back and try again.');
    		echo "<p class='inputerror'>Warning! Passwords do not match. Try again.</p>";
    	}
    	// check password length is ok
    	elseif ((strlen($passwd) < 6) || (strlen($passwd) > 16)) {
    		$bInpValid = false;
    		//throw new Exception('Your password must be between 6 and 16 characters Please go back and try again.');
    		echo "<p class='inputerror'>Warning! Your password must be between 6 and 16 characters. Try again.</p>";
    	}
    	elseif ((strlen($secretq) < 1) || (strlen($secreta) < 1)) {
    		$bInpValid = false;
    		echo "<p class='inputerror'>Warning! Please select a secret Question and Answer. Try again.</p>";
    	}
    }
    if(strlen($sAddr1)>1){
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
    
    //If all validations pass
    if($bInpValid){
		try{
			$db->autocommit(false);
			//Create User Account
			//echo 'User: '.$sUserName." (".$suserId.") Input Data: '".$sUserName."', '".$passwd."', '".$email."','".$secretq."','".$secreta."'<br>";
			if(!$sUserId && $sUserName != "")
			{
				$stmt = $db->prepare("select * from T_USER where username=?");
				$stmt->bind_param("s", $sUserName);
					
				if (!$stmt->execute()) {
					throw new Exception('Could not execute query');
				}
				if ($stmt->get_result()->num_rows>0) {
					echo "<p class='excepmsg'>That username is taken - Please choose another one.</p>";
					throw new Exception("That username is taken - Please choose another one.");
				}else{
					// Get the User Type id
					//echo "select ID from T_USR_TYPE where USR_TYPE='".$sUsrType."'";
					//$result = dbquery("select ID from T_USR_TYPE where USR_TYPE='".$sUsrType."'");
					$stmt = $db->prepare("select ID from T_USR_TYPE where USR_TYPE=?");
					$stmt->bind_param("s", $sUsrType);
						
					if (!$stmt->execute()) {
					//if (!$result) {
						echo "<p class='excepmsg'>KMDB Exception: User Type Query failed, please try again.</p>";
						//echo "SQL:".$result;
						throw new Exception("User Type Query failed, please try again.");
					}else{
					
						$rowcount=$stmt->get_result()->num_rows; //mysqli_num_rows($result);
						//if record exist, add the quantity to it
						if($rowcount >= 1)
						{
							$row=mysqli_fetch_row($result);
							$sUsrTypeId = $row[0];
							
							//$result = $db->query("insert into T_USER (USERNAME, USR_TYPE_ID, PASSWD, EMAIL, SECRETQ, SECRETA) values
		                    //     ('".$sUserName."', $sUsrTypeId, '".sha1($passwd)."', '".$email."','".$secretq."','".$secreta."')");
							$stmt = $db->prepare("insert into T_USER (USERNAME, USR_TYPE_ID, PASSWD, EMAIL, SECRETQ, SECRETA) values (?,?,?,?,?,?)");
							$stmt->bind_param("sissss",$sUserName, $sUsrTypeId, sha1($passwd), $email, $secretq, $secreta);
							
							if (!$stmt->execute()) {
								echo "<p class='excepmsg'>KMDB Exception: User record creation failed, please try again.<br>".$stmt->error."</p>";
								//echo "SQL:".$result;
								throw new Exception("Exception during creating User account");
							}else{
								$sUserId = $db->insert_id;
							}
						}
						else{
							throw new Exception("user Type is not valid");
						}
						
					}
				}
			}//end User Account
			
			//echo "Address: '".$sAddr1."', '".$sAddr2."', '".$sCity."','".$sState."','".$sCountry."','".$sZip."'<br>";
			//Address
			if(!$sAddrId && $sAddr1 != "")
			{
				$result = $db->query("insert into T_ADDR (ADDR1, ADDR2, CITY, STATE, COUNTRY, ZIP_CODE) values
		                         ('".$sAddr1."', '".$sAddr2."', '".$sCity."','".$sState."','".$sCountry."','".$sZip."')");
				$stmt = $db->prepare("insert into T_ADDR (ADDR1, ADDR2, CITY, STATE, COUNTRY, ZIP_CODE) values (?,?,?,?,?,?)");
				$stmt->bind_param("ssssss", $sAddr1, $sAddr2, $sCity, $sState, $sCountry, $sZip);
					
				if (!$stmt->execute()) {
				//if (!$result) {
					echo "<p class='excepmsg'>Exception during inserting Address record.</p>";
					throw new Exception('Exception during inserting Address record.');
				}
				$sAddrId = $db->insert_id;

			}//end address
			
			
			//echo "Employee Input: '".$sEmpCode."','".$sFirstName."','".$sLastName."','".$sUserId."','".$sDeptId."','".$sAddrId."'<br>";
			//$result = $db->query("insert into T_EMP (EMP_CODE, FIRST_NAME, LAST_NAME, USER_ID, DEPT_ID, ADDR_ID) values 
            //             	('".$sEmpCode."','".$sFirstName."','".$sLastName."',".$sUserId.",'".$sDeptId."','".$sAddrId."')");
			$stmt = $db->prepare("insert into T_EMP (EMP_CODE, FIRST_NAME, LAST_NAME, USER_ID, DEPT_ID, ADDR_ID) values (?,?,?,?,?,?)");
			$stmt->bind_param("sssiii", $sEmpCode, $sFirstName, $sLastName, $sUserId, $sDeptId, $sAddrId);
			
			if (!$stmt->execute()) {
				echo "<p class='excepmsg'>Exception during inserting Employee record.".$stmt->error."</p>";
				//echo "SQL:".$result;
				throw new Exception('Exception during inserting Employee record.');
			}
			$sId = $db->insert_id;
			
	  		$db->commit();
	  		echo "<p class='successmsg'>Success! User account created.<br>* ".$sFirstName." ".$sLastName." / ".$sDeptName."</p>";
		}catch (Exception $e) {
			$db->rollback();
     	    echo "<p class='excepmsg'>KMDB Exception: Record entry Exception. Please try again.</p>";
 		}
	}//end $bInpValid=true

  	}//end of btnSubmit


 	//echo $username.$email.$secretq.$secreta;
 	?>
<p id='frmerror' class='inputerror'></p>

<form method="post" id="frmParent" action="entry_employee.php">

<table>
<tr><td>
<h2>Employee Details</h2>
    <table class="form">

    <tr>
    	<td>Employee Code:</td>
    	<td><input type="text" name="sEmpCode" size="16" maxlength="16" value="<?=$sEmpCode?>"/>
    	<input type="text" name="sId" size="5" maxlength="5" value="<?=$sId?>" readonly/>
    	</td></tr>
    <tr>
    	<td>First Name:</td>
    	<td><input type="text" name="sFirstName" id="sFirstName" size="30" maxlength="50" value="<?=$sFirstName?>"/></td></tr>
    <tr>  
    	<td>Last Name:</td>
    	<td><input type="text" name="sLastName" id="sLastName" size="16" maxlength="50" value="<?=$sLastName?>"/></td></tr>
    	    	
    <tr>
    	<td>Dept Name:</td>
    	<td><input type="text" name="sDeptName" id="sDeptName" size="16" maxlength="50" value="<?=$sDeptName?>"/>
    		<input type="text" name="sDeptId" id="sDeptId" size="5" value="<?=$sDeptId?>" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickDept(sDeptName.value)"></td></tr>

    </table>
<td>
  <h2>User Account (Optional)</h2>
  <table class="form">
  <tr>
  	<td>Preferred username <br />(max 16 chars):</td>
  		<td valign="top"><input type="text" name="sUserName" id="sUserName" size="16" maxlength="16" value="<?=$sUserName?>"/>
  		<input type="text" name="sUserId" size="5" maxlength="5" value="<?=$sUserId?>" readonly/></td></tr>
	<tr>
		<td>User Type:</td>
		<td><select name="sUsrType">
	 				<option value="USER" <?=($sUsrType == "USER") ? "selected" : "" ?>>USER</option>
	  				<option value="ADMIN" <?=($sUsrType == "ADMIN") ? "selected" : "" ?>>ADMIN</option>
				</select></td></tr>
  <tr>
  <td>Email address:</td>
  <td><input type="text" name="email" id="email" size="30" maxlength="40" value="<?=$email?>"/></td></tr>
  <tr>
  <tr>
  		<td>Password <br />(between 6 and 16 chars):</td>
  		<td valign="top"><input type="password" name="passwd" id="passwd" size="16" maxlength="16" value="<?=$passwd?>"/></td></tr>
  <tr>
  				<td>Confirm password:</td>
  				<td><input type="password" name="passwd2" id="passwd2" size="16" maxlength="16" value="<?=$passwd2?>"/></td></tr>

  <tr>
		<td>Select a secret question:</td>
		<td><select name="secretq" id="secretq">
  				<option value="What is your birth city?" <?=($secretq == "What is your birth city?") ? "selected" : "" ?>>What is your birth city?</option>
  				<option value="What is your first school?" <?=($secretq == "What is your first school?") ? "selected" : "" ?>>What is your first school?</option>
  				<option value="What is your favorite food?" <?=($secretq == "What is your favorite food?") ? "selected" : "" ?>>What is your favorite food?</option>
  				<option value="Who is your best friend?" <?=($secretq == "Who is your best friend?") ? "selected" : "" ?>>Who is your best friend?</option>
  				<option value="Use a secret code" <?=($secretq == "Use a secret code") ? "selected" : "" ?>>Use a secret code</option>
			</select></td></tr>
  <tr>
  				<td>Give secret answer:</td>
  				<td><input type="text" name="secreta" id="secreta" size="16" maxlength="20" value="<?=$secreta?>"/></td></tr>
  	 </table>
 </td></tr> </table>
  	     <hr>
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
    	<td><input type="text" name="sZip" id="sZip" size="16" maxlength="10" value="<?=$sZip?>"/></td></tr>

    	
     </table>
     <br>
     <table class="form">
     <tr><td colspan=2 align="center"><input type="submit" name="btnSubmit" id="btnSubmit" value="Create Employee"/>
     	<input type="reset" value="Clear Fields"/></td></tr>
     </table>
    </form>
     <hr>
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
