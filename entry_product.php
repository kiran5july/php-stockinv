<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('New Product entry:');
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
  	$sProdId = "";
  	$sUPCCode = "";
  	$sName = "";
  	$sPLName = "";
  	$sMnfId = "";
  	$sMnfName = "";
  	//$iInvQty = "";
  	$dListPrice = "";
  	
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names
  	$sUPCCode = $_POST['sUPCCode'];
  	$sName = $_POST['sName'];
  	$sPLId = $_POST['sPLId'];
  	$sPLName = $_POST['sPLName'];
  	$sMnfId = $_POST['sMnfId'];
  	$sMnfName = $_POST['sMnfName'];
  	//$iInvQty = $_POST['iInvQty'];
  	$dListPrice = $_POST['dListPrice'];
  	
  	// start it now because it must go before headers

    // check forms filled in
	if ((strlen($sUPCCode) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Missing Required fields -UPC Code. Try again.</p>";
    }elseif ((strlen($sName) < 1)) {
    	echo "<p class=\"inputerror\">Warning! Missing Required fields -Product Name. Try again.</p>";
    }elseif(!is_numeric( $dListPrice )){
    	echo "<p class=\"inputerror\"Warning! Invalid data -List Price. Try again.</p>";
    }
	else{
		try{
  		// if ok, put in db
  		$sQuery = "insert into `T_PRODUCTS` (UPC_CD, NAME, PROD_LINE_ID, MNF_ID, LIST_PRICE) values 
                         	('".$sUPCCode."','".$sName."','".$sPLId."','".$sMnfId."','".$dListPrice."')";
  		//echo "SQL:".$sQuery;
  		$result = dbinsert($sQuery);
  		
  			if (!$result) {
    			echo "<p class='excepmsg'>KMDB Exception: Record entry failed. Please try again.</p>";
    			//die();
	  		}else{
	  			$sProdId = $result;
	  			echo "<p class='successmsg'>SUCCESS! Product inserted.<br>-".$sName." (".$sUPCCode.") - ".$sPLName." / ".$dListPrice."</p>";
	  		}
		}catch (Exception $e) {
     	    echo "<p class='excepmsg'>KMDB Exception: Record entry Exception. Please try again.</p>";
 		}
	}

  	}//end of btnSubmit


 	//echo $username.$email.$secretq.$secreta;
 	?>

    <form method="post" name="frmParent" action="entry_product.php">
    <table class="form">
    <tr>
    	<td>Id:</td>
    	<td><input type="text" name="sProdId" size="16" maxlength="16" value="<?=$sProdId?>" readonly/></td></tr>    
    <tr>
    	<td>UPC Code:</td>
    	<td><input type="text" name="sUPCCode" size="16" maxlength="16" value="<?=$sUPCCode?>"/></td></tr>
    <tr>
    	<td>Name:</td>
    	<td><input type="text" name="sName" size="30" maxlength="100" value="<?=$sName?>"/></td></tr>
    <tr>
    	<td>Product Line:</td>
    	<td><input type="text" name="sPLName"  id="sPLName" size="16" maxlength="16" value="<?=$sPLName?>"/>
    		<input type="text" name="sPLId" id="sPLId" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickProdLine(sPLName.value)"></td></tr>
    <tr>
    	<td>Manufacturer:</td>
    	<td><input type="text" name="sMnfName" id="sMnfName" size="16" maxlength="16" value="<?=$sMnfName?>"/>
    		<input type="text" name="sMnfId" id="sMnfId" size="5" readonly>
    		<input type="button" value="..." onclick="JavaScript:pickManufacturer(sMnfName.value)"></td></tr>
    <tr>
    	<td>List price:</td>
    	<td><input type="text" name="dListPrice" size="16" maxlength="12" value="<?=$dListPrice?>"/>
    <tr>
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
