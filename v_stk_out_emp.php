<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  //do_html_header('Products List');
  //echo "";
  
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
  	$sWHCode = "";
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
 			$result = dbquery("select invdt.OP_DT, CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME,  wh.WH_CODE, invdt.QTY
 							from T_INVT_DTL invdt
								left outer join T_INVT invt on invt.ID=invdt.INVT_ID
 								inner join T_PRODUCTS p on p.ID=invt.PROD_ID
 								left outer join T_EMP e on e.ID=invdt.OP_EMP_ID
								left outer join T_WAREHOUSE wh on wh.ID=invt.WH_ID
 								where 1=1 and invdt.QTY<0 and invdt.OP_EMP_ID is not null ".
			 								(($sEmpId) ? " and invdt.OP_EMP_ID=".$sEmpId : "").
			 								((strlen($dtStart)>1) ? " and STR_TO_DATE(invdt.OP_DT,'%Y-%m-%d') >= STR_TO_DATE('".$dtStart."','%Y-%m-%d')" : "").
			 								((strlen($dtEnd)>1) ? " and STR_TO_DATE(invdt.OP_DT,'%Y-%m-%d') <= STR_TO_DATE('".$dtEnd."','%Y-%m-%d')" : "").
			 								" order by invdt.OP_DT");

 			
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 
 					echo "<table width='95%'><th class='rep'>DATE</th><th class='rep'>PROD NAME</th>
  						<th class='rep'>FROM WAREHOUSE</th><th class='rep'>QTY</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
    					$dAct = $row[0];
 						$sProdName = $row[1];
 						$sWHCode = $row[2];
 						$iQty = $row[3];
 						$iQtyTotal += $iQty;
 						//$dListPrice = $row[5];
 						echo "<tr class=\"rep\"><td>".$dAct."</td><td>".$sProdName."</td><td>".$sWHCode."</td><td class=\"rep-num\">".$iQty."</td></tr>";
    				}
    				echo "<tr><th class='rep' colspan='3'>--TOTAL</th><th class='rep-num'>".$iQtyTotal."</th></tr>";
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
