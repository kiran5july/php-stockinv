<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Warehouse/Product Qty List');
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
  	$sProdId = $_GET["prodId"];
  	$iLine = $_GET['ln'];
  	
	if(empty($sProdId)){
		echo "Please select a product before picking warehouse.";
	}else{
	  	$sId = "";
	  	$sWHCode = "";
		$sAddr = "";
	  	$sContactName = "";
 
		try{
 			$result = dbquery("select wh.ID, wh.WH_CODE, inv.QTY, CONCAT(a.ADDR1,', ',a.ADDR2, ', ',a.CITY, ', ',a.STATE, ', ',a.COUNTRY, ', ', a.ZIP_CODE) as ADDR, CONCAT(e.FIRST_NAME, ', ', e.LAST_NAME) as CONTACT
 								from T_INVT inv left outer join T_WAREHOUSE wh on wh.ID=inv.WH_ID
 									left outer join T_ADDR a on a.ID=wh.ADDR_ID 
 									left outer join T_EMP e on e.ID=wh.CONTACT_ID where inv.QTY>0 and inv.PROD_ID=".$sProdId);
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount = mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 
 					echo "<table class='rep' width='100%'><th class='rep'>ID</th>
  						<th class='rep'>WH CODE</th><th class='rep'>QTY AVAILABLE</th><th class='rep'>FULL ADDRESS</th><th class='rep'>CONTACT</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sId = $row[0];
 						$sWHCode = $row[1];
 						$iQty = $row[2];
 						$sAddr = $row[3];
 						$sContactName = $row[4];
 						echo "<tr class=\"rep\"><td class=\"rep\">".$sId."</td><td class=\"rep\"><a href=\"JavaScript:setWHId('".$sId."','".$sWHCode."','".$iLine."')\">".$sWHCode."</a></td><td class=\"rep\">".$iQty."</td><td class=\"rep\">".$sAddr."</td><td class=\"rep\">".$sContactName."</td></tr>";
    				}
    				echo "</table>";

 				}else 
 					echo "<p>Selected product is not available in any warehouse. please update filter criteria & try again.</p>";
 			}//end else
		}catch (Exception $e) {
     	    echo "<p>Record entry Exception. Please try again.</p>";
 		}


  	}//else $sProdId

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
