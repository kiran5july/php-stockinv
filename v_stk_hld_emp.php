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
			$sQuery = "select CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME, emppr.QTY
 							from T_EMP_PRD_MST emppr 
 								inner join T_PRODUCTS p on p.ID=emppr.PRD_ID
								inner join T_EMP e on e.ID=emppr.EMP_ID
 								where 1=1 and emppr.QTY >0".
			 								(($sEmpId) ? " and emppr.EMP_ID=".$sEmpId : "").
			 								" order by PNAME";
 			$result = dbquery($sQuery);

 			
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 
 					echo "<table width='95%'><th class='rep'>PRODUCT</th><th class='rep-num'>QTY</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sProdName = $row[0];
 						$iQty = $row[1];
 						$iQtyTotal += $iQty;
 						echo "<tr class=\"rep\"><td>".$sProdName."</td><td class=\"rep-num\">".$iQty."</td></tr>";
    				}
    				echo "<tr><th class='rep'>--TOTAL</th><th class='rep-num'>".$iQtyTotal."</th></tr>";
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
