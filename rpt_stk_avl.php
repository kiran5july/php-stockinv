<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_rpt_header('Stock Availability Report');
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
  	//$sId = $_POST['sId'];
  	$sProdId = isset($_POST['sProdId0'])?$_POST['sProdId0']:"";
  	$sProdName = isset($_POST['sProdName0'])?$_POST['sProdName0']:"";
  	$sWHCode = isset($_POST['sWHCode0'])?$_POST['sWHCode0']:"";
  	$sWHId = isset($_POST['sWHId0'])?$_POST['sWHId0']:"";
  	//$dtStart = isset($_POST['dtStart'])?$_POST['dtStart']:"";
  	//$dtEnd = isset($_POST['dtEnd'])?$_POST['dtEnd']:"";
  	$sGrpBy = $_POST['sGrpBy'];
  	$iQty = "";
	$sColSelect = "";
	$sGrpByCols = "";
	$sCol1 = "";
	$sCol2 = "";
	$sCol1_old = "";
	$sCol2_old = "";
	$nGrp1Qty_ttl = 0;
	//$nGrp2Qty_ttl = 0;
	$nRptQty_ttl = 0;
	$nTotalCols = 3;
	$bFirst=TRUE;
	//echo "Input: Prod=".$sProdId.";WH=".$sWHId;
  	//Add filter section
    echo "<h3>Filter Criteria</h3>";
    echo "<table><tr><th>Product Name:</th><td>".(($sProdName)?$sProdName:"--ALL--</td></tr>").
				"<tr><th>Warehouse Code:</th><td>".(($sWHCode)?$sWHCode:"--ALL--</td></tr>").
				"<tr><th>Groups:</th><td>".$sGrpBy."</td></tr>".
				"</table><hr>";



    	switch($sGrpBy){
    		case "Product":
   					$sColSelect = "CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME, wh.WH_CODE";
   					$sGrpByCols = "PNAME, WH_CODE";
    				break;
    		case "Warehouse":
    		default:
	    			$sColSelect = "wh.WH_CODE, CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME";
   					$sGrpByCols = "WH_CODE, PNAME";
    			break;
    	}//end switch $sGrpBy1

		try{
			$sQuery = "select ".$sColSelect.", invt.QTY
 							from T_INVT invt 
 								inner join T_PRODUCTS p on p.ID=invt.PROD_ID
								left outer join T_WAREHOUSE wh on wh.ID=invt.WH_ID
 								where 1=1 ".
			 								((strlen($sProdId)>1) ? " and invt.PROD_ID=".$sProdId : "").
			 								(($sWHId) ? " and invt.WH_ID=".$sWHId : "").
			 								" and invt.QTY>0".
			 								" order by ". $sGrpByCols."";
			//echo "My Query: ".$sQuery;
 			$result = dbquery($sQuery);

 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 					echo "<table width='50%'>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sCol1 = $row[0];
 						$sCol2 = $row[1];
 						$iQty = $row[2];

						if($sCol1 != $sCol1_old) //Grp1 header
						{
							//flush previous group total
							if(!$bFirst)  //Grp1 total
							{
								echo "<tr class='rptgrp1h'><td class=\"white\" width=\"5%\"></td><th colspan=".($nTotalCols-2)." class='rptsmry'>--TOTAL</td><th class='rptsmry-num'>".$nGrp1Qty_ttl."</td></tr>";
								echo "<tr><th colspan=".($nTotalCols)." class='rpthdr'> </th></tr>"; //blank row with underline
								$nGrp1Qty_ttl = 0;
								$sCol2_old = "";
							}
							echo "<tr class='rptgrp1h'><td class='rptgrp1' colspan=".($nTotalCols).">".$sCol1."</td></tr>";

							//echo "<tr class='rptgrp2h'><td class=\"white\" width=\"5%\"></td><td colspan=".($nTotalCols-1)." class='rptgrp2'>".$sCol2."</td></tr>";
							//echo "<tr><th class=\"white\" colspan=2 width=\"10%\"></th><th class='rpthdr'>DATE</th><th class='rpthdr'>ACTIVITY</th><th class='rpthdr'>Warehouse</th><th class=\"rpthdr-num\">QTY</th></tr>";
						}
						
 						//echo "<tr class=\"rptdata\"><td class=\"white\"></td><td>".$sDate."</td><td>".$sOperation."</td><td>".$sWHCode."</td><td class=\"rep-num\">".$iInvQty."</td></tr>";
 						echo "<tr class=\"rptdata\"><td class=\"white\" ></td><td>".$sCol2."</td><td class=\"rep-num\">".$iQty."</td></tr>";
 						$nGrp1Qty_ttl += $iQty;
 						//$nGrp2Qty_ttl += $iQty;
 						$nRptQty_ttl += $iQty;
 						$bFirst = FALSE; //first round complete
 						$sCol1_old = $sCol1;
 						//$sCol2_old = $sCol2;
 						
    				}//end while
					//last groups totals
    				echo "<tr class='rptgrp1h'><td class=\"white\" width=\"5%\"></td><th colspan=".($nTotalCols-2)." class='rptsmry'>--TOTAL</td><th class='rptsmry-num'>".$nGrp1Qty_ttl."</td></tr>";
    				echo "<tr><th colspan=".($nTotalCols)." class='rpthdr'> </th></tr>";
    				//Report Total
    				echo "<tr class='rptgrp1h'><td colspan=".($nTotalCols-1)." class='rptgrp1'>REPORT TOTAL</td><td class='rptgrp1-num'>".$nRptQty_ttl."</td></tr></table><br>";
    				
 				}else 
 					echo "<p>No records found for your filter. please update filter criteria & try again.</p>";
 			}//end else
		}catch (Exception $e) {
     	    echo "<p>Record Query Exception. Please try again.</p>";
 		}


  	//}//else $sEmpId/$sProdid input

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
