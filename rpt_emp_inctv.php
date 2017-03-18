<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_rpt_header('Employee Sales Incentive Report');
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
  	$sEmpId = isset($_POST['sEmpId'])?$_POST['sEmpId']:"";
  	$sEmpName = isset($_POST['sEmpName'])?$_POST['sEmpName']:"";
  	$sPrdName = "";

  	$dtStart = isset($_POST['dtStart'])?$_POST['dtStart']:"";
  	$dtEnd = isset($_POST['dtEnd'])?$_POST['dtEnd']:"";
  	
  	$iQty = "";
	$sColSelect = "";
	$sGrpByCols = "";
	$sCol1 = "";
	$sInctvType = "";
	$sInctvAmt = 0;
	$sCol1_old = "";
	$nGrp1Qty_ttl = 0;
	$nGrp1Amt_ttl = 0;
	$nRptQty_ttl = 0;
	$nRptAmt_ttl = 0;
	$nTotalCols = 5;
	$bFirst=TRUE;
	//echo "Input: Prod=".$sProdId.";WH=".$sWHId;
  	//Add filter section
	echo "<h3>Filter Criteria</h3>";
	echo "<table><tr><th>Employee Name:</th><td>".(($sEmpName)?$sEmpName:"--ALL--</td></tr>").
		"<tr><th>Date Range:</th><td>From:".(($dtStart)?$dtStart:"--System Launch--")."<br>To:".(($dtEnd)?$dtEnd:date("Y-m-d H:i:s"))."</td></tr>".
		"</table><hr>";
    
		try{
			$sQuery = "select CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) as ENAME, CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME, CONCAT(INCNTV_UNIT,' / ',INCNTV_TYPE) INCTV, 
						sum(ol.QTY) QTY, ROUND((case when INCNTV_TYPE='PERCENTAGE' then ((sum(QTY) * INCNTV_UNIT*LIST_PRICE)/100) else (sum(QTY) * INCNTV_UNIT) end),2) INCTVAMOUNT
						from T_EMP_SALES empsl
								inner join T_ORDER_LINES ol on empsl.ORD_LN_ID=ol.ID
								inner join T_ORDERS ord on ord.ID=ol.ORD_ID
 								inner join T_PRODUCTS p on p.ID=ol.PROD_ID
								inner join T_PROD_INCNTV inctv on inctv.PROD_ID=p.ID
 								inner join T_EMP e on e.ID=ord.EMP_ID
 								where 1=1 ".
 									(($sEmpId) ? " and empsl.EMP_ID=".$sEmpId : "").
 									((strlen($dtStart)>8) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') >= STR_TO_DATE('".$dtStart."','%Y-%m-%d')" : "").
 									((strlen($dtEnd)>8) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') <= STR_TO_DATE('".$dtEnd."','%Y-%m-%d')" : "").
 								" group by ENAME, PNAME, INCTV".
 								" order by ENAME, PNAME, ORD_DT";
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
 						$sCol1 = $row[0]; //Emp Name
 						$sPrdName = $row[1];
 						$sInctvType = $row[2];
 						$iQty = $row[3];
 						$sInctvAmt = $row[4];

						if($sCol1 != $sCol1_old) //Grp1 header
						{
							//flush previous group total
							if(!$bFirst)  //Grp1 total
							{
								echo "<tr class='rptgrp1h'><td class=\"white\" width=\"5%\"></td><th colspan=".($nTotalCols-3)." class='rptsmry'>--TOTAL</td><th class='rptsmry-num'>".$nGrp1Qty_ttl."</td><th class='rptsmry-num'>".number_format($nGrp1Amt_ttl,2)."</th></tr>";
								echo "<tr><th colspan=".($nTotalCols)." class='rpthdr'> </th></tr>"; //blank row with underline
								$nGrp1Qty_ttl = 0;
								$nGrp1Amt_ttl = 0;
								$sCol2_old = "";
							}
							echo "<tr class='rptgrp1h'><td class='rptgrp1' colspan=".($nTotalCols).">".$sCol1."</td></tr>";

							//echo "<tr class='rptgrp2h'><td class=\"white\" width=\"5%\"></td><td colspan=".($nTotalCols-1)." class='rptgrp2'>".$sCol2."</td></tr>";
							//echo "<tr><th class=\"white\" colspan=2 width=\"10%\"></th><th class='rpthdr'>DATE</th><th class='rpthdr'>ACTIVITY</th><th class='rpthdr'>Warehouse</th><th class=\"rpthdr-num\">QTY</th></tr>";
						}
						
 						//echo "<tr class=\"rptdata\"><td class=\"white\"></td><td>".$sDate."</td><td>".$sOperation."</td><td>".$sWHCode."</td><td class=\"rep-num\">".$iInvQty."</td></tr>";
 						echo "<tr class=\"rptdata\"><td class=\"white\" ></td><td>".$sPrdName."</td><td>".$sInctvType."</td><td class=\"rep-num\">".$iQty."</td><td class=\"rep-num\">".number_format($sInctvAmt,2)."</td></tr>";
 						$nGrp1Qty_ttl += $iQty;
 						$nGrp1Amt_ttl += $sInctvAmt;
 						$nRptQty_ttl += $iQty;
 						$nRptAmt_ttl += $sInctvAmt;
 						$bFirst = FALSE; //first round complete
 						$sCol1_old = $sCol1;
 						//$sCol2_old = $sCol2;
 						
    				}//end while
					//last groups totals
    				echo "<tr class='rptgrp1h'><td class=\"white\" width=\"5%\"></td><th colspan=".($nTotalCols-3)." class='rptsmry'>--TOTAL</td><th class='rptsmry-num'>".$nGrp1Qty_ttl."</th><th class='rptsmry-num'>".number_format($nGrp1Amt_ttl,2)."</th></tr>";
    				echo "<tr><th colspan=".($nTotalCols)." class='rpthdr'> </th></tr>";
    				//Report Total
    				echo "<tr class='rptgrp1h'><td colspan=".($nTotalCols-2)." class='rptgrp1'>REPORT TOTAL</td><td class='rptgrp1-num'>".$nRptQty_ttl."</td><td class='rptgrp1-num'>".number_format($nRptAmt_ttl,2)."</td></tr></table><br>";
    				
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
