<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_rpt_header('Employee Sales Detail Report');
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
  	$sEmpId = isset($_POST['sEmpId'])?$_POST['sEmpId']:"";
  	$sEmpName = isset($_POST['sEmpName'])?$_POST['sEmpName']:"";
  	$sProdId = isset($_POST['sProdId0'])?$_POST['sProdId0']:"";
  	$sProdName = isset($_POST['sProdName0'])?$_POST['sProdName0']:"";
  	$sOrdNum = isset($_POST['sOrdNum'])?$_POST['sOrdNum']:"";
  	$iQty = "";
  	//$sWHId = isset($_POST['sWHId0'])?$_POST['sWHId0']:"";
  	//$sWHCode = isset($_POST['sWHCode0'])?$_POST['sWHCode0']:"";
  	$dtStart = isset($_POST['dtStart'])?$_POST['dtStart']:"";
  	$dtEnd = isset($_POST['dtEnd'])?$_POST['dtEnd']:"";
  	$sGrpBy = $_POST['sGrpBy'];
  	//$sPLName = "";
  	$sDate = "";
  	//$sPLDesc = "";
  	$sOrdNo = "";
  	$dAmount = "";
	$sColSelect = "";
	$sGrpByCols = "";
	$sCol1 = "";
	$sCol2 = "";
	$sCol1_old = "";
	$sCol2_old = "";
	$nGrp1_total = 0;
	$nGrp2_total = 0;
	$nGrp1Qty_ttl = 0;
	$nGrp2Qty_ttl = 0;
	$nRpt_total = 0;
	$nRptQty_ttl = 0;
	$nTotalCols = 6;
	$bFirst=TRUE;

  	//Add filter section
	echo "<h3>Filter Criteria</h3>";
	echo "<table><tr><th>Employee Name:</th><td>".(($sEmpName)?$sEmpName:"--ALL--</td></tr>").
			"<tr><th>Product Name:</th><td>".(($sProdName)?$sProdName:"--ALL--</td></tr>").
			"<tr><th>Date Range:</th><td>From:".(($dtStart)?$dtStart:"--System Launch--")."<br>To:".(($dtEnd)?$dtEnd:date("Y-m-d H:i:s"))."</td></tr>".
			"<tr><th>Groups:</th><td>".$sGrpBy."</td></tr>".
			"</table><hr>";

	
    	switch($sGrpBy){
    		case "Product":
    			$sColSelect = "CONCAT(p.NAME,'(', p.UPC_CD,')') as PNAME, CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) as ENAME";
    			$sGrpByCols = "PNAME, ENAME";
    			break;
    		case "Employee":
    		default:
    			$sColSelect = "CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) as ENAME, CONCAT(p.NAME,'(', p.UPC_CD,')') as PNAME";
    			$sGrpByCols = "ENAME, PNAME";
    			break;
    	}
		try{
			$sQuery = "select ".$sColSelect.", ord.ORD_DT, ord.ORD_NO, ol.QTY, ol.AMOUNT
 							from T_EMP_SALES empsl
								inner join T_ORDER_LINES ol on empsl.ORD_LN_ID=ol.ID
								inner join T_ORDERS ord on ord.ID=ol.ORD_ID
								inner join T_PRODUCTS p on p.ID=ol.PROD_ID
 								inner join T_EMP e on e.ID=ord.EMP_ID
 								where 1=1 and CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) is not null ".
 									(($sEmpId) ? " and empsl.EMP_ID=".$sEmpId : "").
 									((strlen($sProdId)>1) ? " and ol.PROD_ID=".$sProdId : "").
 									(($sOrdNum) ? " and ord.ORD_NO='".$sOrdNum."'" : "").
 									((strlen($dtStart)>1) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') >= STR_TO_DATE('".$dtStart."','%Y-%m-%d')" : "").
 									((strlen($dtEnd)>1) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') <= STR_TO_DATE('".$dtEnd."','%Y-%m-%d')" : "").
 								" order by ". $sGrpByCols.", ord.ORD_DT";
			//echo "My Query: ".$sQuery;
 			$result = dbquery($sQuery);
 								
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
					echo "<table width='80%'>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sCol1 = $row[0]; //Prod/Emp name
 						$sCol2 = $row[1]; //Prod/Emp name
 						$sDate = $row[2];
 						$sOrdNo = $row[3];
 						$iQty = $row[4];
 						$dAmount = $row[5];

 						if( ( $sCol1 != $sCol1_old || $sCol2 != $sCol2_old ) )
 						{
 							if(!$bFirst) {
 								echo "<tr class='rptgrp2h'><td class=\"white\" colspan=2></td><th class='rptsmry' colspan=".($nTotalCols-4).">--TOTAL</th><th class='rptsmry-num'>".$nGrp2Qty_ttl."</th><th class='rptsmry-num'>".number_format($nGrp2_total,2)."</th></tr>";

 								 $nGrp2Qty_ttl = 0; $nGrp2_total = 0;
 							}
 								
 						}
						if($sCol1 != $sCol1_old) //Grp1 header
						{
							//flush previous group total
							if(!$bFirst)  //Grp1 total
							{
									echo "<tr class='rptgrp1h'><td class=\"white\" width=\"5%\"></td><td colspan=".($nTotalCols-3)." class='rptgrp1'>--TOTAL</td><td class='rptgrp1-num'>".$nGrp1Qty_ttl."</td><td class='rptgrp1-num'>".number_format($nGrp1_total,2)."</td></tr>";
									echo "<tr><th colspan=".($nTotalCols)." class='rpthdr'> </th></tr>"; //blank row with underline
									$nGrp1Qty_ttl = 0; $nGrp1_total = 0;
									$sCol2_old = "";
							}
							echo "<tr class='rptgrp1h'><td class='rptgrp1' colspan=".($nTotalCols).">".$sCol1."</td></tr>";
						
						}
						if($sCol2 != $sCol2_old || $sCol1 != $sCol1_old)//Grp2 header
						{
							echo "<tr class='rptgrp2h'><td class=\"white\" width=\"5%\"></td><td colspan=".($nTotalCols-1)." class='rptgrp2'>".$sCol2."</td></tr>";
							echo "<tr><th class=\"white\" colspan=2 width=\"10%\"></th><th class='rpthdr'>DATE</th><th class='rpthdr'>INVC/BILL NUM</th><th class='rpthdr-num'>QTY</th><th class=\"rpthdr-num\">AMOUNT</th></tr>";
						}
						
 						echo "<tr class=\"rptdata\"><td class=\"white\" colspan=2 ></td><td>".$sDate."</td><td>".$sOrdNo."</td><td class=\"rep-num\">".$iQty."</td><td class=\"rep-num\">".$dAmount."</td></tr>";
 						$nGrp1Qty_ttl += $iQty; $nGrp1_total += $dAmount;
 						$nGrp2Qty_ttl += $iQty; $nGrp2_total += $dAmount;
 						$nRptQty_ttl += $iQty; $nRpt_total += $dAmount;
 						$bFirst = FALSE; //first round complete
 						$sCol1_old = $sCol1;
 						$sCol2_old = $sCol2;
    				}//end while
					//last groups totals
    				echo "<tr class='rptgrp2h'><td class=\"white\" colspan=2></td><th class='rptsmry' colspan=".($nTotalCols-4).">--TOTAL</th><th class='rptsmry-num'>".$nGrp2Qty_ttl."</th><th class='rptsmry-num'>".number_format($nGrp2_total,2)."</th></tr>";
    				echo "<tr class='rptgrp1h'><td class=\"white\" width=\"5%\"></td><td colspan=".($nTotalCols-3)." class='rptgrp1'>--TOTAL</td><td class='rptgrp1-num'>".$nGrp1Qty_ttl."</td><td class='rptgrp1-num'>".number_format($nGrp1_total,2)."</td></tr>";
    				echo "<tr><th colspan=".($nTotalCols)." class='rpthdr'> </th></tr>";
    				echo "<tr class='rptgrp1h'><td colspan=".($nTotalCols-2)." class='rptgrp1'>REPORT TOTAL</td><td class='rptgrp1-num'>".$nRptQty_ttl."</td><td class='rptgrp1-num'>".number_format($nRpt_total,2)."</td></tr></table><hr>";

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
