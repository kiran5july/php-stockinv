<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('New Product to Employee entry:');
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
  	$sWHCode = "";
  	$sComments = "";
  	$iGenId = "";
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names
  	$sId = $_POST['sId'];
  	$sEmpId = $_POST['sEmpId'];
  	$sEmpName = $_POST['sEmpName'];
  	$sProdId = $_POST['sProdId0'];
  	$sProdName = $_POST['sProdName0'];
  	$sWHCode = $_POST['sWHCode0'];
  	$sWHId = $_POST['sWHId0'];
  	$dtGiven = $_POST['dtGiven'];
  	$iQty = $_POST['iQty'];
  	$sComments = $_POST['sComments'];
  	// start it now because it must go before headers

    // check forms filled in
	if ((strlen($sEmpId) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Invalid fields -Employee. Try again.</p>";
    }elseif ((strlen($sProdId) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Invalid fields -Product Name. Try again.</p>";
    }elseif ((strlen($sWHId) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Invalid fields -Warehouse Loc. Try again.</p>";
    }elseif(!is_numeric($iQty)){
    	echo "<p class=\"inputerror\"Warning! Invalid data -Inventory Qty. Try again.</p>";
    }
	else{
		try{
			//global $db;
	
			$db->autocommit(false);
			
			$result = $db->query("select ID from T_INVT where PROD_ID=".$sProdId." and WH_ID='".$sWHId."'");
				
			if (!$result) {
				echo "<p class='excepmsg'>KMDB Exception: Inventory Query failed, please close the window & try again.</p>";
			}else{
				$rowcount = mysqli_num_rows($result);
				//if record exist, add the quantity to it
				if($rowcount >= 1)
				{
					$row=mysqli_fetch_row($result);
					$sInvId = $row[0];

					//insert inv dtl entry record
					$result = $db->query("insert into T_INVT_DTL (INVT_ID, OP_DT, QTY, OPERATION, OP_EMP_ID) values
					                         	('".$sInvId."','".$dtGiven."',-".$iQty.", 'OUT', ".$sEmpId.")");
					if (!$result){
						echo "<p class='excepmsg'>KMDB Exception: Inventory Detail record insert failed.</p>";
						throw new Exception("KMDB Exception: Inventory Detail record insert failed.");
					}
					$idInvDtl = $db->insert_id;
					//insert prod-emp entry
					$result = $db->query("insert into T_PROD_TO_EMP (EMP_ID, INVT_DTL_ID, GIVEN_DT, COMMENTS) values
		                         	(".$sEmpId.",".$idInvDtl.",'".$dtGiven."','".$sComments."')");
					if (!$result){
						echo "<p class='excepmsg'>KMDB Exception: Emp Inventory out Detail record insert failed.</p>";
						throw new Exception("KMDB Exception: Emp Inventory out Detail record insert failed.");
					}
					$idInvDtl = $db->insert_id;
					//Update the inv master
					$result = $db->query("update T_INVT set QTY = QTY-".$iQty." where ID=".$sInvId);
					if (!$result){
						echo "<p class='excepmsg'>KMDB Exception: Emp Inventory out Detail record insert failed.</p>";
						throw new Exception("KMDB Exception: Emp Inventory out Detail record insert failed.");
					}
					
					//Update/insert Emp/Product master
					$result = $db->query("select ID from T_EMP_PRD_MST where EMP_ID='".$sEmpId."' and PRD_ID='".$sProdId."'");
					
					if (!$result) {
						echo "<p class='excepmsg'>KMDB Exception: Emp Product master table Query failed, please close the window & try again.</p>";
						throw new Exception("KMDB Exception: Emp Product master table Detail record query failed.");
					}else{
						$row=mysqli_fetch_row($result);
						$iGenId = $row[0];
						if($iGenId){
							//update existing master
							$result = $db->query("update T_EMP_PRD_MST set QTY=QTY+".$iQty." where ID=".$iGenId);

							if (!$result){
								echo "<p class='excepmsg'>KMDB Exception: Emp Product master record update failed.</p>";
								throw new Exception("KMDB Exception: Emp Product master record update failed.");
							}
						}else{
							$result = $db->query("insert into T_EMP_PRD_MST (EMP_ID, PRD_ID, QTY) values
			                         	(".$sEmpId.",".$sProdId.",'".$iQty."')");
							if (!$result){
								echo "<p class='excepmsg'>KMDB Exception: Emp Product master record insert failed.</p>";
								throw new Exception("KMDB Exception: Emp Product master record insert failed.");
							}
						}
					}//end else Emp/Prod master

				}else 
					echo "<p>KMDB Exception: Inventory Detail record not found.</p>";
				}
			$db->commit();
			
  			echo "<p class='successmsg'>Success!<br>* ".$sEmpName."/".$sProdName."/".$sWHCode."</p>";
  			$sId = $idInvDtl;
		}catch (Exception $e) {
     	    echo "<p class='excepmsg'>KMDB Exception: Record entry Exception. Please try again.</p>";
     	    $db->rollback();
 		}
	}

  	}//end of btnSubmit


 	//echo $username.$email.$secretq.$secreta;
 	?>


    <form method="post" name="frmParent" action="entry_prod2emp.php">
    <table class="form">
    <tr>
    	<td>Id:</td>
    	<td><input type="text" name="sId" size="16" maxlength="16" value="<?=$sId?>" readonly/></td></tr>    
    <tr>
    	<td>Employee Name:</td>
    	<td><input type="text" name="sEmpName" id="sEmpName" size="16" maxlength="16" value="<?=$sEmpName?>"/>
    		<input type="text" name="sEmpId" id="sEmpId" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickEmp(sEmpName.value)"></td></tr>
    <tr>
    	<td>Product:</td>
    	<td><input type="text" name="sProdName0" id="sProdName0" size="16" maxlength="25" value="<?=$sProdName?>"/>
    		<input type="text" name="sProdId0" id="sProdId0" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickProd(sProdName0.value,'i','0')"></td></tr>
    <tr>
    	<td>Warehouse Loc:</td>
    	<td><input type="text" name="sWHCode0" id="sWHCode0" size="16" maxlength="25" value="<?=$sWHCode?>"/>
    		<input type="text" name="sWHId0" id="sWHId0" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickWH_by_prodId(sProdId0.value, '0')"></td></tr>
    <tr>
    	<td>Given Date:</td>
    	<td><input type="text" id="dtGiven" name="dtGiven" size="16" maxlength="19" value="<?=$dtGiven?>" readonly/>
    			<a href="javascript:NewCal('dtGiven','YYYYMMDD',true,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>
    <tr>  
    	<td>Qty:</td>
    	<td><input type="text" name="iQty" size="16" maxlength="10" value="<?=$iQty?>"/></td></tr>
    <tr>
    	<td>Comments:</td>
    	<td><input type="text" name="sComments" size="16" maxlength="16" value="<?=$sComments?>"/></td></tr>  

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
     exit;
  }
?>
