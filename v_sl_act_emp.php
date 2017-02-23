<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  //do_html_header('Products List');
  
  try{
  // start session which may be needed later
  session_start();
  // register session variable
  $username = $_SESSION['valid_user'];
  
  if(empty($username))
  {
  	echo "Your session timed out. Please <a href=\"login.php\" target=\"main\">click here to login</a> again.";
  }else{
  	
  	//Get input data 
  	$sEmpId = isset($_GET["empId"])?$_GET["empId"]:"";
  	$dtStart = isset($_GET['dtStart'])?$_GET['dtStart']:"";
  	$dtEnd = isset($_GET['dtEnd'])?$_GET['dtEnd']:"";
  	

  	//initialize variables

  	$sProdId = "";
  	$dAct = "";
  	$sProdName = "";
  	$sOrdNum = "";
  	//$sPLId = "";
  	//$sPLDesc = "";
  	$sMnfName = "";
  	$iQty = "";
	$iQtyTotal = 0;
  	//Check if QueryString has value
  	If(empty($sEmpId)) {
  		echo "<p class=\"inputerror\">Warning! You must select an employee to see his products. Try again.</p>";
  	}else{
		try{
			$sQuery = "select ord.ORD_DT, CONCAT(p.NAME,'(', p.UPC_CD,')') as PNAME, ord.ORD_NO, ol.QTY, ol.AMOUNT
 							from T_EMP_SALES empsl
								inner join T_ORDER_LINES ol on empsl.ORD_LN_ID=ol.ID
								inner join T_ORDERS ord on ord.ID=ol.ORD_ID
								inner join T_PRODUCTS p on p.ID=ol.PROD_ID
 								inner join T_EMP e on e.ID=ord.EMP_ID
 								where 1=1 and CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) is not null ".
			 								(($sEmpId) ? " and empsl.EMP_ID=".$sEmpId : "").
			 								((strlen($dtStart)>1) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') >= STR_TO_DATE('".$dtStart."','%Y-%m-%d')" : "").
			 								((strlen($dtEnd)>1) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') <= STR_TO_DATE('".$dtEnd."','%Y-%m-%d')" : "").
			 								" order by ord.ORD_DT";
 			$result = dbquery($sQuery);

 			
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 
 					echo "<table width='95%'><th class='rep'>SALE DATE</th><th class='rep'>PRODUCT</th><th class='rep'>ORDER/SALES RECEIPT #</th>
  						<th class='rep-num'>QTY</th><th class='rep-num'>AMOUNT</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$dAct = $row[0];
 						$sProdName = $row[1];
 						$sOrdNum = $row[2];
 						$iQty = $row[3];
 						$dAmt = $row[4];
 						$iQtyTotal += $iQty;
 						$iAmtTotal = $dAmt;
 						echo "<tr class=\"rep\"><td>".$dAct."</td><td>".$sProdName."
  								</td><td>".$sOrdNum."</td><td class=\"rep-num\">".$iQty."</td><td class=\"rep-num\">".$dAmt."</td></tr>";
    				}
    				echo "<tr><th class='rep' colspan='3'>--TOTAL</th><th class='rep-num'>".$iQtyTotal."</th><th class='rep-num'>".$iAmtTotal."</th></tr>";
    				echo "</table>";

 				}else 
 					echo "<p>No records found for your filter. please update filter criteria & try again.</p>";
 			}//end else
		}catch (Exception $e) {
     	    echo "<p>Record Query Exception. Please try again.</p>";
 		}


  	}//else $sEmpId

  	}//else session
   // end page
   do_html_footer();
  }catch (Exception $e) {
     do_html_header('Application Error:');
     echo $e->getMessage();
     do_html_footer();
     exit;
  }
?>
