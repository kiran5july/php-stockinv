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
  	$colId = "";
  	$idInvDtl = "";
  	$bOpSuccess = false;
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  		//print all POST variables
  		//print_r($_POST);
  		//km_disp_post_data($_POST);
  		
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
  	//$iQtyNegative = $iQty * -1;
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
			
			$stmt = $db->prepare("select ID from T_INVT where PROD_ID=? and WH_ID=?");
			$stmt->bind_param("ii", $sProdId, $sWHId);
			//echo "T_INVT query with PROD_ID=".$sProdId." and WH_ID=".$sWHId."<br>";
			
			if (!$stmt->execute()) {
				echo "<p class='excepmsg'>KMDB Exception: Inventory Query failed, please try again.</p>".$stmt->error;
			}else{
				
				$stmt->store_result();
				$rowcount = $stmt->num_rows;
				//echo "--T_INVT rows:".$rowcount."<br>";
				//if record exist, add the quantity to it
				if($rowcount >= 1)
				{
					$stmt->bind_result($colId);
					$stmt->fetch();
					$sInvId = $colId;
					//echo "-- T_INVT id: ".$sInvId."<br>";
					
					//insert inv dtl entry record
					$stmt = $db->prepare("insert into T_INVT_DTL (INVT_ID, OP_DT, QTY, OPERATION, OP_EMP_ID) values (?,?,?,?,?)");
					$stmt->bind_param("isisi", $sInvId, $dtGiven, $iQtyNegative=$iQty * -1, $pbr="OUT", $sEmpId);
					
					if (!$stmt->execute()){
						echo "<p class='excepmsg'>KMDB Exception: Inventory Detail record insert failed.</p>".$stmt->error;
						throw new Exception("KMDB Exception: Inventory Detail record insert failed.".$stmt->error);
					}
					$idInvDtl = $db->insert_id;
					//echo "T_INVT_DTL inserted id: ".$idInvDtl."<br>";
					
					//insert prod-emp entry
					$stmt = $db->prepare("insert into T_PROD_TO_EMP (EMP_ID, INVT_DTL_ID, GIVEN_DT, COMMENTS) values (?,?,?,?)");
		            $stmt->bind_param("iiss", $sEmpId, $idInvDtl, $dtGiven, $sComments);
					if (!$stmt->execute()){
						echo "<p class='excepmsg'>KMDB Exception: Emp Inventory out Detail record insert failed.</p>".$stmt->error;
						throw new Exception("KMDB Exception: Emp Inventory out Detail record insert failed.");
					}
					$idInvDtl = $db->insert_id;
					//echo "T_PROD_TO_EMP inserted id: ".$idInvDtl."<br>";
					
					//Update the inv master
					$stmt = $db->prepare("update T_INVT set QTY = QTY-? where ID=?");
					$stmt->bind_param("ii", $iQty, $sInvId);
					if (!$stmt->execute()){
						echo "<p class='excepmsg'>KMDB Exception: Emp Inventory out Detail record insert failed.</p>".$stmt->error;
						throw new Exception("KMDB Exception: Emp Inventory out Detail record insert failed.");
					}
					//echo "T_INVT updated."."<br>";
					
					//Update/insert Emp/Product master
					$stmt = $db->prepare("select ID from T_EMP_PRD_MST where EMP_ID=? and PRD_ID=?");
					$stmt->bind_param("ii", $sEmpId, $sProdId);
					if (!$stmt->execute()) {
						echo "<p class='excepmsg'>KMDB Exception: Emp Product master table Query failed, please close the window & try again.</p>".$stmt->error;
						throw new Exception("KMDB Exception: Emp-Product master table Detail record query failed.");
					}else{
						$stmt->bind_result($colId);
						
						if($stmt->fetch()){
							$iGenId = $colId;
							//update existing master
							$stmt = $db->prepare("update T_EMP_PRD_MST set QTY=QTY+? where ID=?");
							$stmt->bind_param("ii", $iQty, $colId);
							//echo "T_EMP_PRD_MST updating..<br>";
							
							if (!$stmt->execute()){
								echo "<p class='excepmsg'>KMDB Exception: Emp Product master record update failed.</p>".$stmt->error;
								throw new Exception("KMDB Exception: Emp Product master record update failed.");
							}
						}else{
							$stmt = $db->prepare("insert into T_EMP_PRD_MST (EMP_ID, PRD_ID, QTY) values (?,?,?)");
			                $stmt->bind_param("iii", $sEmpId, $sProdId, $iQty);
			                echo "T_EMP_PRD_MST inserting..";
			                
							if (!$stmt->execute()){
								echo "<p class='excepmsg'>KMDB Exception: Emp Product master record insert failed.</p>".$stmt->error;
								throw new Exception("KMDB Exception: Emp Product master record insert failed.");
							}
						}
					}//end else Emp/Prod master
					
					$bOpSuccess =true;
					
				}else 
					echo "<p>KMDB Exception: Inventory Detail record not found.</p>";
				}
			$db->commit();
			if($bOpSuccess)
			{
				$sId = $idInvDtl;
				echo "<p class='successmsg'>Success!<br>* ".$sEmpName."/".$sProdName."/".$sWHCode."</p>";
			}else 
				echo "<p class='excepmsg'>Failed! Data insert failed:<br>*".$sProdName."/".$sWHCode."/".$sEmpName."</p>";
  			
		}catch (Exception $e) {
     	    echo "<p class='excepmsg'>KMDB Exception: Record entry Exception. Please try again.</p>".$stmt->error;
     	    $db->rollback();
 		}
	}//end fields validations

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
