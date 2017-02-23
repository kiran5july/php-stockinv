<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('New Employee Sales entry:');
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
	$dtSale = "";
  	$iQty = "";
  	$sOrdNo = "";
  	$sComments = "";
  	$dTotal = "";
  	$sOrdId = "";
  	$sOrdLnId = "";
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names
  	$sId = $_POST['sId'];
  	$sEmpId = $_POST['sEmpId'];
  	$sEmpName = $_POST['sEmpName'];
  	$sProdId = $_POST['sProdId0'];
  	$sProdName = $_POST['sProdName0'];
  	$sOrdNo = isset($_POST['sOrdNo'])?$_POST['sOrdNo']:"";
  	$dtSale = isset($_POST['dtSale'])?$_POST['dtSale']:"";
  	$iQty = $_POST['iQty'];
  	$dTotal = $_POST['dTotal'];
  	// start it now because it must go before headers

    // check forms filled in
	if ((strlen($sEmpId) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Invalid fields -Employee. Try again.</p>";
    }elseif ((strlen($sProdId) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Invalid fields -Product Name. Try again.</p>";
    }elseif(!is_numeric($iQty)){
    	echo "<p class=\"inputerror\"Warning! Invalid data -Inventory Qty. Try again.</p>";
    }
	else{
		try{
			//
			if($sOrdNo)
			{
			//Query Invoice by $sOrdNo
				$result = dbquery("select ID from T_ORDERS where EMP_ID=".$sEmpId." AND ORD_NO=".$sOrdNo);
				
				if (!$result) {
					echo "<p class='excepmsg'>Exception during Querying Order record.</p>";
					throw new Exception('Exception during querying Order record.');
				}else{
						
					$rowcount=mysqli_num_rows($result);
					//if record exist, get id
					if($rowcount >= 1)
					{
						$row=mysqli_fetch_row($result);
						$sOrdId = $row[0];
				
					}//end rowcount of T_INVOICE
				}
			}//end $sOrdNo
			try{	
				$db->autocommit(false);
				if($sOrdNo){
					if(!$sOrdId){
						$result = $db->query("insert into T_ORDERS (ORD_NO, ORD_DT, EMP_ID, ORD_TTL) values
				                         	('".$sOrdNo."','".$dtSale."','".$sEmpId."','".$dTotal."')");
						if (!$result) {
							echo "<p class='excepmsg'>Exception during inserting Order record.</p>";
							throw new Exception('Exception during inserting Order record.');
						}
						$sOrdId = $db->insert_id;
					}
					$result = $db->query("insert into T_ORDER_LINES (ORD_ID,PROD_ID,QTY,AMOUNT) values ('".$sOrdId."','".$sProdId."','".$iQty."','".$dTotal."')");
					if (!$result) {
						echo "<p class='excepmsg'>Exception during inserting Order Line record. <br>".$db->error."</p>";
						throw new Exception('Exception during inserting Order Line record.');
					}
					$sOrdLnId = $db->insert_id;
				}//end $sOrdNo
				
				$sQuery = "insert into T_EMP_SALES (EMP_ID, ORD_LN_ID, SALE_DT) values
	                         	(".$sEmpId.",'".$sOrdLnId."','".$dtSale."')";

				//echo "inserting in T_EMP_SALES: ".$sQuery;
				$result = $db->query($sQuery);
				if(!$result )
				{
					echo "<p class='excepmsg'>Exception during inserting Employee Sale record.<br>".$db->error."</p>";
					throw new Exception('Exception during inserting Employee Sale record.');
				}
				//Subtract this from Emp/Prod Master table
				$result = $db->query("update T_EMP_PRD_MST set QTY=QTY-".$iQty." where EMP_ID=".$sEmpId." and PRD_ID=".$sProdId);
				if(!$result )
				{
					echo "<p class='excepmsg'>Exception during updating Employee Product master record.<br>".$db->error."</p>";
					throw new Exception('KMDB Exception: Exception during updating Employee Product master record.');
				}
				$db->commit();				
				$sId = $db->insert_id;
					
	  			echo "<p class='successmsg'>Success!<br>*".$sEmpName." / ".$sProdName." / ".$iQty."</p>";
			
			}catch (Exception $e) {
     	    	echo "<p class='excepmsg'>KMDB Exception: Employee sales entry record entry Exception. Please try again.<br></p>";
     	    	$db->rollback();
			}
 		}catch (Exception $e) {
 			echo "<p class='excepmsg'>Order Query Exception. Please try again.</p>";
 		}
	}

  	}//end of btnSubmit


 	//echo $username.$email.$secretq.$secreta;
 	?>


    <form method="post" name="frmParent" action="entry_empsales.php">
    <table class="form">
    <tr>
    	<td>Id:</td>
    	<td><input type="text" name="sId" size="16" maxlength="16" value="<?=$sId?>" readonly/></td></tr>    
    <tr>
    	<td>Employee Name:</td>
    	<td><input type="text" name="sEmpName" id="sEmpName" size="16" maxlength="16" value="<?=$sEmpName?>"/>
    		<input type="text" name="sEmpId" id="sEmpId" size="5" value="<?=$sEmpId?>" readonly/>
    		<input type="button" value="..." onclick="JavaScript:pickEmp(sEmpName.value)"/></td></tr>
    <tr>
    	<td>Product:</td>
    	<td><input type="text" name="sProdName0" id="sProdName0" size="16" maxlength="25" value="<?=$sProdName?>"/>
    		<input type="text" name="sProdId0" id="sProdId0" size="5" readonly/>
    		<input type="button" value="..." onclick="JavaScript:pickProd_by_empId(sEmpId.value)"/></td></tr>
    <tr>
    	<td>Order#/Bill Receipt #:</td>
    	<td><input type="text" name="sOrdNo" size="16" maxlength="16" value="<?=$sOrdNo?>"/></td></tr>  
    <tr>
    	<td>Sale Date:</td>
    	<td><input type="text" id="dtSale" name="dtSale" size="16" maxlength="19" value="<?=$dtSale?>" readonly/>
    			<a href="javascript:NewCal('dtSale','YYYYMMDD',true,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>
    <tr>  
    	<td>Qty:</td>
    	<td><input type="text" name="iQty" size="16" value="1" maxlength="10" value="<?=$iQty?>"/></td></tr>
    <tr>
    	<td>Total:</td>
    	<td><input type="text" name="dTotal" size="16" maxlength="12" value="<?=$dTotal?>"/>
    <tr>
    	<td colspan=2 align="center"><input type="submit" name="btnSubmit" value="Submit"></td></tr>
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
