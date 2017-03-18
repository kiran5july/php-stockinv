<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_rpt_header('Employee Sales Summary Report');
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

  	//km_disp_post_data($_POST);
  	
  	//initialize variables
  	//$sId = $_POST['sId'];
  	$sEmpName = isset($_POST['sEmpName'])?$_POST['sEmpName']:"";
  	$sEmpId = ($sEmpName) ? (isset($_POST['sEmpId'])?$_POST['sEmpId']:"") : "";
  	$sProdName = isset($_POST['sProdName0'])?$_POST['sProdName0']:"";
  	$sProdId = ($sProdName) ? (isset($_POST['sProdId0'])?$_POST['sProdId0']:"") : "";
  	$sOrdNum = isset($_POST['sOrdNum'])?$_POST['sOrdNum']:"";
  	$iQty = "";
  	$dtStart = isset($_POST['dtStart'])?$_POST['dtStart']:"";
  	$dtEnd = isset($_POST['dtEnd'])?$_POST['dtEnd']:"";
  	$sGrpBy1 = $_POST['sGrpBy1'];
  	$sGrpBy2 = $_POST['sGrpBy2'];
  	$sGrpPrd = $_POST['sGrpPrd'];
  	$sDate = "";
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
	$nTotalCols = 5;
	$bFirst=TRUE;

	//echo "Input: sEmpId=".$sEmpId.";sProdId=".$sProdId.";sGrpBy1=".$sGrpBy1.";sGrpBy2=".$sGrpBy2.";sGrpPrd=".$sGrpPrd;
  	//Add filter section
	echo "<h3>Filter Criteria</h3>";
	echo "<table><tr><th>Employee Name:</th><td>".(($sEmpName && $sEmpId)?$sEmpName:"--ALL--</td></tr>").
		"<tr><th>Product Name:</th><td>".(($sProdName && $sProdId)?$sProdName:"--ALL--</td></tr>").
		"<tr><th>Period:</th><td>".$sGrpPrd."</td></tr>".
		"<tr><th>Groups:</th><td>".$sGrpBy1.(($sGrpBy2)?"/".$sGrpBy2:"")."</td></tr>".
		"<tr><th>Date Range:</th><td>From:".(($dtStart)?$dtStart:"--System Launch--")."<br>To:".(($dtEnd)?$dtEnd:date("Y-m-d H:i:s"))."</td></tr>".
		"</table><hr>";
	

    	//Build grouping columns for DB query
    	switch($sGrpBy1){
    		case "Product":
    			$sColSelect = "CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME ";
    			$sGrpByCols = "PNAME ";
    			switch($sGrpBy2){
    				case "Employee":
    					$sColSelect .= ", CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) as ENAME";
    					$sGrpByCols .= ", ENAME";
    					
    					break;
    				default:
    					$sColSelect .= ", '' as ENAME";
    					$sGrpByCols .= ", ENAME";
    					break;
    			}//end switch $sGrpBy2
    			break;
    		case "Employee":
    		default:
    			$sColSelect = "CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) as ENAME ";
    			$sGrpByCols = "ENAME ";
    			switch($sGrpBy2){
    				case "Product":
    					$sColSelect .= ", CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME";
    					$sGrpByCols .= ", PNAME";
    					break;
    				default:
    					$sColSelect .= ", '' as PNAME";
    					$sGrpByCols .= ", PNAME";
    			}//end switch $sGrpBy2
    			break;
    	}//end switch $sGrpBy1
    	
    	switch($sGrpPrd){
    		case "Periodic":
    			$sColSelect .= ",''  as ORD_DT_BLNK ";
    			$sGrpByCols .= ", ORD_DT_BLNK ";
    			break;
    		case "Daily":
    		//default:
    			$sColSelect .= ", DATE(ord.ORD_DT) as ORD_DT ";
    			$sGrpByCols .= ", DATE(ord.ORD_DT)";
    			break;
    	}
    	
		try{
			$sQuery = "select ".$sColSelect.", sum(ol.QTY) as QTY, sum(ol.AMOUNT) as AMOUNT
 							from T_EMP_SALES empsl
								inner join T_ORDER_LINES ol on empsl.ORD_LN_ID=ol.ID
								inner join T_ORDERS ord on ord.ID=ol.ORD_ID
 								inner join T_PRODUCTS p on p.ID=ol.PROD_ID
 								inner join T_EMP e on e.ID=ord.EMP_ID
 								where 1=1 ".
 									(($sEmpId && $sEmpName) ? " and empsl.EMP_ID=".$sEmpId : "").
 									(($sProdId && $sProdName) ? " and ol.PROD_ID=".$sProdId : "").
 									(($sOrdNum) ? " and ord.ORD_NO='".$sOrdNum."'" : "").
 									((strlen($dtStart)>9) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') >= STR_TO_DATE('".$dtStart."','%Y-%m-%d')" : "").
 									((strlen($dtEnd)>9) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') <= STR_TO_DATE('".$dtEnd."','%Y-%m-%d')" : "").
 								" group by ".$sGrpByCols.
 								" order by ". $sGrpByCols."";
			//echo "My Query: ".$sQuery;
 			$result = dbquery($sQuery);
 								
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
					echo "<table width='80%' border=0>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sCol1 = $row[0]; //Prod/Emp name
 						$sCol2 = $row[1]; //Prod/Emp name
 						$sDate = $row[2];
 						$iQty = $row[3];
 						$dAmount = $row[4];

 						if( ( $sCol1 != $sCol1_old || $sCol2 != $sCol2_old ) )
 						{
 							if(!$bFirst) {
 								if($sDate !="")
	 								echo "<tr class='rptgrp2h'><td class=\"white\" colspan=2></td><th class='rptsmry'>--TOTAL</th><th class='rptsmry-num'>".$nGrp2Qty_ttl."</th><th class='rptsmry-num'>".number_format($nGrp2_total,2)."</th></tr>";

 								 $nGrp2Qty_ttl = 0; $nGrp2_total = 0;
 							}
 								
 						}
						if($sCol1 != $sCol1_old) //Grp1 header
						{
							//flush previous group total
							if(!$bFirst)  //Grp1 total
							{
								if($sGrpBy2!="")
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
							if($sDate !="")
							echo "<tr><th class=\"white\" colspan=2 width=\"10%\"></th><th class='rpthdr'>DATE</th><th class='rpthdr-num'>QTY</th><th class=\"rpthdr-num\">AMOUNT</th></tr>";
						}
						
 						echo "<tr class=\"rptdata\"><td class=\"white\" colspan=2 ></td><td>".$sDate."</td><td class=\"rep-num\">".$iQty."</td><td class=\"rep-num\">".$dAmount."</td></tr>";
 						$nGrp1Qty_ttl += $iQty; $nGrp1_total += $dAmount;
 						$nGrp2Qty_ttl += $iQty; $nGrp2_total += $dAmount;
 						$nRptQty_ttl += $iQty; $nRpt_total += $dAmount;
 						$bFirst = FALSE; //first round complete
 						$sCol1_old = $sCol1;
 						$sCol2_old = $sCol2;
    				}//end while
					//last groups totals
    				if($sDate !="")
	    				echo "<tr class='rptgrp2h'><td class=\"white\" colspan=2></td><th class='rptsmry'>--TOTAL</th><th class='rptsmry-num'>".$nGrp2Qty_ttl."</th><th class='rptsmry-num'>".number_format($nGrp2_total,2)."</th></tr>";
    				if($sGrpBy2!="")
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
