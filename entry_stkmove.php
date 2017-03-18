<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Stock/Inventory transfer:');
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
	$dtAdded = "";
  	$iQty = "";
  	$sWHCode = "";
  	$sWHId1 = "";
  	$sWHCode1 = "";
  	$sComments = "";
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names
  	$sId = $_POST['sId'];
  	//$sEmpId = $_POST['sEmpId'];
  	//$sEmpName = $_POST['sEmpName'];
  	$sProdId = $_POST['sProdId0'];
  	$sProdName = $_POST['sProdName0'];
  	$sWHId = $_POST['sWHId0'];
  	$sWHCode = $_POST['sWHCode0'];
  	$sWHId1 = $_POST['sWHId1'];
  	$sWHCode1 = $_POST['sWHCode1'];
  	$dtAdded = $_POST['dtAdded'];
  	$iQty = $_POST['iQty'];
  	//$sComments = $_POST['sComments'];
  	// start it now because it must go before headers
	$iWH0Qty = 0;
	$idTargetInvt = "";
    // check forms filled in
	if ((strlen($sProdId) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Invalid fields -Product Name. Try again.</p>";
    }elseif ((strlen($sWHId) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Invalid fields -Source Warehouse Loc. Try again.</p>";
    }elseif ((strlen($sWHId1) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Invalid fields -Target Warehouse Loc. Try again.</p>";
    }elseif(!is_numeric($iQty)){
    	echo "<p class=\"inputerror\"Warning! Invalid data -Inventory Qty. Try again.</p>";
    }
	else{
		try{

			//Check source WH Inventory qty
			$result = dbquery("select ID, QTY from T_INVT where PROD_ID=".$sProdId." and WH_ID='".$sWHId."'");
			
			if (!$result) {
				echo "<p class='excepmsg'>Inventory Query failed, please close the window & try again.</p>";
			}else{
				$rowcount = mysqli_num_rows($result);
				//if record exist, add the quantity to it
				if($rowcount >= 1)
				{
					$row=mysqli_fetch_row($result);
					$sInvId = $row[0];
					$iWH0Qty = $row[1];
					
					if($iWH0Qty>=$iQty){
						
						//Check for Target WH/Inventory
						$result = dbquery("select ID from T_INVT where PROD_ID=".$sProdId." and WH_ID='".$sWHId1."'");
							
						if (!$result) {
							echo "<p class='excepmsg'>Inventory Query failed, please close the window & try again.</p>";
						}else{
							$rowcount = mysqli_num_rows($result);
							
							try{
								$db->autocommit(false);
								//if record exist, add the quantity to it
								//
								if($rowcount >= 1)
								{
									$row=mysqli_fetch_row($result);
									$idTargetInvt = $row[0];
				
									$result = $db->query("update T_INVT set QTY = QTY-".$iQty." where ID=".$sInvId);
									if (!$result) {
										echo "<p class='excepmsg'>Source Inventory update failed, please try again.<br>".$db->error."</p>";
										throw new Exception("Exception during updating Source Inventory record.");
									}
									$result = $db->query("insert into T_INVT_DTL (INVT_ID, OP_DT, QTY, OPERATION) values
					                         	('".$sInvId."','".$dtAdded."',-".$iQty.", 'OUT-STK-MV')");
									if (!$result) {
										echo "<p class='excepmsg'>Target Inventory Detail creation failed, please try again.<br>".$db->error."</p>";
										throw new Exception("Exception during inserting Source Inventory record.");
									}
									//$sQuery= "";
									//Update Target WH/Inventory
									$result = $db->query("update T_INVT set QTY = QTY+".$iQty." where ID=".$idTargetInvt);
									if (!$result) {
										echo "<p class='excepmsg'>Target Inventory update failed, please try again. ".$sQuery."<br>".$db->error."</p>";
										throw new Exception("Exception during updating Target Inventory record.");
									}
									$result = $db->query("insert into T_INVT_DTL (INVT_ID, OP_DT, QTY, OPERATION) values
					                         	('".$idTargetInvt."','".$dtAdded."',".$iQty.", 'IN-STK-MV')");
									if (!$result) {
										echo "<p class='excepmsg'>Target Inventory Detail creation failed, please try again.<br>".$db->error."</p>";
										throw new Exception("Exception during inserting Target Inventory record.");
									}
				
								}//end rowcount of T_INVENTORY
								else //if no record, create new record for it
								{
									$result = $db->query("update T_INVT set QTY = QTY-".$iQty." where ID=".$sInvId);
									if (!$result) {
										echo "<p class='excepmsg'>Source Inventory update failed, please try again.<br>".$db->error."</p>";
										throw new Exception("Exception during updating Source Inventory record.");
									}
									$result = $db->query("insert into T_INVT_DTL (INVT_ID, OP_DT, QTY, OPERATION) values
					                         	('".$sInvId."','".$dtAdded."',-".$iQty.", 'OUT-STK-MV')");
									if (!$result) {
										echo "<p class='excepmsg'>Target Inventory Detail creation failed, please try again.<br>".$db->error."</p>";
										throw new Exception("Exception during inserting Source Inventory record.");
									}
									//Insert new Target WH/Inventory record
									$result = $db->query("insert into T_INVT (PROD_ID, WH_ID, QTY) values
					                         	('".$sProdId."','".$sWHId1."',".$iQty.")");
									if (!$result) {
										echo "<p class='excepmsg'>Target Inventory record insert failed, please try again.<br>".$db->error."</p>";
										throw new Exception("Exception during inserting Target Inventory record.");
									}
									$idTargetInvt = $db->insert_id;
									$result = $db->query("insert into T_INVT_DTL (INVT_ID, OP_DT, QTY, OPERATION) values
						                         	('".$idTargetInvt."','".$dtAdded."',".$iQty.", 'IN-STK-MV')");
									if (!$result) {
										echo "<p class='excepmsg'>Target Inventory detail insert failed, please try again.<br>".$db->error."</p>";
										throw new Exception("Exception during updating Target Inventory record.");
									}
										//$iRecInsert = $db->insert_id;
								}//else Inv(Prod/WH)
								$db->commit();
								echo "<p class='successmsg'>Success!<br>*".$sProdName." / ".$sWHCode." -> ".$sWHCode1."</p>";
								$sId = $idTargetInvt;
							}catch(Exception $e){
								echo "<p class='excepmsg'>Stock Move Record entry Exception. Please try again.<br></p>";
								$db->rollback();
							}
						}//end target WH query
					}//end WHQty>Input Qty
					else 
						echo "<p class='excepmsg'>Insufficient Quantity in Source WH/Inventory. Please check & try again.<br></p>";
				}//end source WH record found
			}//end SOurce WH query success

		}catch (Exception $e) {
     	    echo "<p class='excepmsg'>KMDB Exception: Record entry Exception. Please try again.</p>";
 		}
	}

  	}//end of btnSubmit


 	//echo $username.$email.$secretq.$secreta;
 	?>


    <form method="post" name="frmParent" action="entry_stkmove.php">
    <table class="form">
    <tr>
    	<td>Id:</td>
    	<td><input type="text" name="sId" size="16" maxlength="16" value="<?=$sId?>" readonly/></td></tr>    
    <tr>
    	<td>Product:</td>
    	<td><input type="text" name="sProdName0" id="sProdName0" size="16" maxlength="25" value="<?=$sProdName?>"/>
    		<input type="text" name="sProdId0" id="sProdId0" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickProd(sProdName0.value,'i','0')"></td></tr>
    <tr>
    	<td>Current Warehouse Loc:</td>
    	<td><input type="text" name="sWHCode0" id="sWHCode0" size="16" maxlength="25" value="<?=$sWHCode?>"/>
    		<input type="text" name="sWHId0" id="sWHId0" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickWH_by_prodId(sProdId0.value,'0')"></td></tr>
	<tr>
    	<td>Target Warehouse Loc:</td>
    	<td><input type="text" name="sWHCode1" id="sWHCode1" size="16" maxlength="25" value="<?=$sWHCode1?>"/>
    		<input type="text" name="sWHId1" id="sWHId1" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickWarehouse(sProdId0.value,'1')"></td></tr>

    <tr>
    	<td>Move Date:</td>
    	<td><input type="text" id="dtAdded" name="dtAdded" size="16" maxlength="19" value="<?=$dtAdded?>" readonly/>
    			<a href="javascript:NewCal('dtAdded','YYYYMMDD',true,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>
    <tr>  
    	<td>Qty:</td>
    	<td><input type="text" name="iQty" size="16" maxlength="10" value="<?=$iQty?>"/></td></tr>

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
