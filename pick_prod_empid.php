<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Products List');
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

  	//initialize variables
  	$sEmpId = $_GET["empId"]; 
  	$sProdId = "";
  	$sUPCCode = "";
  	$sProdName = "";
  	$sPLName = "";
  	$sPLId = "";
  	$sPLDesc = "";
  	$sMnfName = "";
  	$iInvQty = "";
  	$dListPrice = "";
  	//Check if QueryString has value
  	If(empty($sEmpId)) {
  		echo "<p class=\"inputerror\">Warning! You must select an employee to see his products. Try again.</p>";
  	}else{
		try{
 			$result = dbquery("select ID, NAME, UPC_CD, PRODLINE, MNFR, sum(QTY) as QTY
					from
					(
					select p.ID, p.NAME, p.UPC_CD, pl.NAME as PRODLINE, mn.NAME as MNFR, invdt.QTY as QTY from T_INVT_DTL invdt
					 								inner join T_INVT invt on invt.ID=invdt.INVT_ID
					 								inner join T_PRODUCTS p on p.ID=invt.PROD_ID
					 								left outer join T_PROD_LINE pl on pl.ID=p.PROD_LINE_ID
					 								left outer join T_MANUFACTURER mn on mn.ID=p.MNF_ID 
												where invdt.OP_EMP_ID is not null
					 									and invdt.OP_EMP_ID =".$sEmpId."                               
					union all
					select p.ID, p.NAME, p.UPC_CD, pl.NAME as ProdLine, mn.NAME as MNFR, ol.QTY as QTY from T_EMP_SALES empsl
								inner join T_ORDER_LINES ol on empsl.ORD_LN_ID=ol.ID
								inner join T_ORDERS ord on ord.ID=ol.ORD_ID
 								inner join T_PRODUCTS p on p.ID=ol.PROD_ID
 								inner join T_EMP e on e.ID=ord.EMP_ID
					 								left outer join T_PROD_LINE pl on pl.ID=p.PROD_LINE_ID
					 								left outer join T_MANUFACTURER mn on mn.ID=p.MNF_ID 
												where 1=1 and empsl.EMP_ID =".$sEmpId."                                
					) prl
					group by ID, NAME, UPC_CD, PRODLINE, MNFR
					having sum(QTY)<0");
 			
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 
 					echo "<table class='rep' width='100%'><th class='rep'>ID</th><th class='rep'>PROD NAME</th><th class='rep'>UPC CODE</th>
  						<th class='rep'>PROD LINE NAME</th><th class='rep'>MANUFACTURER</th><th class='rep'>QTY</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sProdId = $row[0];
 						$sProdName = $row[1];
 						$sUPCCode = $row[2];
 						$sPLName = $row[3];
 						$sMnfName = $row[4];
 						$iInvQty = $row[5];
 						//$dListPrice = $row[5];
 						echo "<tr class=\"rep\"><td>".$sProdId."</td><td><a href=\"JavaScript:setProductId('".$sProdId."','".$sProdName."','0')\">".$sProdName."</a>
  								</td><td>".$sUPCCode."</td><td>".$sPLName."</td><td>".$sMnfName."</td><td class=\"rep-num\">".$iInvQty."</td></tr>";
    				}
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
