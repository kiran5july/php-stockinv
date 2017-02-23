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
  	$sProdId = "";
  	$sUPCCode = "";
  	$sProdName = $_GET["prodName"];
  	$sCriteria = $_GET["cri"];
  	$iLine = (isset($_GET["ln"])?$_GET["ln"] :"") ;
  	$sPLName = "";
  	$sPLId = "";
  	$sPLDesc = "";
  	$sMnfName = "";
  	$iInvQty = "";
  	$dListPrice = "";
  	  

		try{
			switch($sCriteria){
				case "i":
 					$result = dbquery("select p.ID, p.NAME, p.UPC_CD, pl.NAME as ProdLine, mn.NAME as MNFR, sum(inv.QTY) as QTY, p.LIST_PRICE from T_PRODUCTS p
 								inner join T_INVT inv on inv.PROD_ID=p.ID
 								left outer join T_PROD_LINE pl on pl.ID=p.PROD_LINE_ID
 								left outer join T_MANUFACTURER mn on mn.ID=p.MNF_ID".
 				 								(($sProdName == "") ? " " : " where p.NAME like '".$sProdName."%' ").
 				 				"group by p.ID, p.NAME, p.UPC_CD, pl.NAME, mn.NAME, p.LIST_PRICE
 				 				having sum(inv.QTY)>0");
 					break;
					 					
 				case "a":
 				default:
 					$result = dbquery("select p.ID, p.NAME, p.UPC_CD, pl.NAME as ProdLine, mn.NAME as MNFR, sum(inv.QTY) as QTY, p.LIST_PRICE from T_PRODUCTS p
 								left outer join T_INVT inv on inv.PROD_ID=p.ID 							
 								left outer join T_PROD_LINE pl on pl.ID=p.PROD_LINE_ID
 								left outer join T_MANUFACTURER mn on mn.ID=p.MNF_ID".
 				 								(($sProdName == "") ? " " : " where p.NAME like '".$sProdName."%' ").
 				 				"group by p.ID, p.NAME, p.UPC_CD, pl.NAME, mn.NAME, p.LIST_PRICE");
 					break;
			}
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{

 					echo "<table class='rep' width='100%'><th class='rep'>ID</th><th class='rep'>PROD NAME</th><th class='rep'>UPC CODE</th>
  						<th class='rep'>PROD LINE NAME</th><th class='rep'>MANUFACTURER</th><th class='rep'>INVENTORY QTY</th><th class='rep'>LIST PRICE</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sProdId = $row[0];
 						$sProdName = $row[1];
 						$sUPCCode = $row[2];
 						$sPLName = $row[3];
 						$sMnfName = $row[4];
 						$iInvQty = $row[5];
 						$dListPrice = $row[6];
 						echo "<tr class=\"rep\"><td>".$sProdId."</td><td><a href=\"JavaScript:setProductId('".$sProdId."','".$sProdName."','".$iLine."')\">".$sProdName."</a>
  								</td><td>".$sUPCCode."</td><td>".$sPLName."</td><td>".$sMnfName."</td><td class=\"rep-num\">".$iInvQty."</td><td class=\"rep-num\">".$dListPrice."</td></tr>";
    				}
    				echo "</table>";

 				}else 
 					echo "<p>No records found for your filter. please update filter criteria & try again.</p>";
 			}//end else
		}catch (Exception $e) {
     	    echo "<p>Record entry Exception. Please try again.</p>";
 		}

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
