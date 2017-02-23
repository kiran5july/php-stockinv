<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_rpt_header('Stock activity Report');
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
  	$sWHCode = isset($_POST['sWHCode0'])?$_POST['sWHCode0']:"";
  	$sWHId = isset($_POST['sWHId0'])?$_POST['sWHId0']:"";
  	$dtStart = isset($_POST['dtStart'])?$_POST['dtStart']:"";
  	$dtEnd = isset($_POST['dtEnd'])?$_POST['dtEnd']:"";
  	$sGrpBy = $_POST['sGrpBy'];
  	$sGrpBy2 = $_POST['sGrpBy2'];
  	$sOp = $_POST['sOp'];
  	$sEName = "";
  	//$sPLName = "";
  	$sDate = "";
  	//$sPLDesc = "";
  	$sOperation = "";
  	$iQty = "";
	$sColSelect = "";
	$sGrpByCols = "";
	$sCol1 = "";
	$sCol2 = "";
	$sCol1_old = "";
	$sCol2_old = "";
	$nGrp1Qty_ttl = 0;
	$nGrp2Qty_ttl = 0;
	$nRptQty_ttl = 0;
	$nTotalCols = 6;
	$bFirst=TRUE;
	
  	//Add filter section
    echo "<h3>Filter Criteria</h3>";
    echo "<table><tr><th>Employee Name:</th><td>".(($sEmpName)?$sEmpName:"--ALL--</td></tr>").
				"<tr><th>Product Name:</th><td>".(($sProdName)?$sProdName:"--ALL--</td></tr>").
				"<tr><th>Warehouse Code:</th><td>".(($sWHCode)?$sWHCode:"--ALL--</td></tr>").
				"<tr><th>Activity:</th><td>".$sOp."</td></tr>".
				"<tr><th>Groups:</th><td>".$sGrpBy.(($sGrpBy2)?"/".$sGrpBy2:"")."</td></tr>".
      			"<tr><th>Date Range:</th><td>From:".(($dtStart)?$dtStart:"--System Launch--")."<br>To:".(($dtEnd)?$dtEnd:date("Y-m-d H:i:s"))."</td></tr>".
				"</table><hr>";



    	switch($sGrpBy){
    		case "Product":
    			switch($sGrpBy2){
    				case "Employee":
    					$sColSelect = "CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME, COALESCE(CONCAT(e.FIRST_NAME,' ',e.LAST_NAME), '-STOCK IN-') as ENAME";
    					$sGrpByCols = "PNAME, ENAME";
    					break;
	    			default:
	    				$sColSelect = "CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME, '' as ENAME";
	    				$sGrpByCols = "PNAME, ENAME";
	    				break;
	    			}//end switch $sGrpBy2
    				break;
    		case "Employee":
    		default:
    			switch($sGrpBy2){
    				case "Product":
		    			$sColSelect = "COALESCE(CONCAT(e.FIRST_NAME,' ',e.LAST_NAME), '-STOCK IN-') as ENAME, CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME";
    					$sGrpByCols = "ENAME, PNAME";
    					break;
    				default:
    					$sColSelect = "COALESCE(CONCAT(e.FIRST_NAME,' ',e.LAST_NAME), '-STOCK IN-') as ENAME, '' as PNAME";
    					$sGrpByCols = "ENAME, PNAME";
    			}//end switch $sGrpBy2
    			break;
    	}//end switch $sGrpBy1

    	//Set Inventry filter based on operation
    	$sOpFilter = " ";
    	switch($sOp){
    		case "ToEmp":	$sOpFilter = "and invdt.QTY<0 and invdt.OP_EMP_ID is not null ";  break;
    		case "FromEmp": $sOpFilter = "and invdt.QTY>0 and invdt.OP_EMP_ID is not null ";  break;
    		case "All":
    		default:
    			break;
    				 
    	}
    	
		try{
			$sQuery = "select ".$sColSelect.", invdt.OP_DT, CONCAT(invdt.OPERATION, COALESCE(CONCAT(' (',e.FIRST_NAME,' ',e.LAST_NAME,')'), '')) as OPERATION, wh.WH_CODE, invdt.QTY
 							from T_INVT_DTL invdt
								left outer join T_INVT invt on invt.ID=invdt.INVT_ID
 								inner join T_PRODUCTS p on p.ID=invt.PROD_ID
 								left outer join T_EMP e on e.ID=invdt.OP_EMP_ID
								left outer join T_WAREHOUSE wh on wh.ID=invt.WH_ID
 								where 1=1 ".$sOpFilter.
			 								(($sEmpId) ? " and invdt.OP_EMP_ID=".$sEmpId : "").
			 								((strlen($sProdId)>1) ? " and invt.PROD_ID=".$sProdId : "").
			 								((strlen($sWHId)>1) ? " and invt.WH_ID=".$sWHId : "").
			 								((strlen($dtStart)>1) ? " and STR_TO_DATE(invdt.OP_DT,'%Y-%m-%d') >= STR_TO_DATE('".$dtStart."','%Y-%m-%d')" : "").
			 								((strlen($dtEnd)>1) ? " and STR_TO_DATE(invdt.OP_DT,'%Y-%m-%d') <= STR_TO_DATE('".$dtEnd."','%Y-%m-%d')" : "").
			 								" order by ". $sGrpByCols.", invdt.OP_DT";
			//echo "My Query: ".$sQuery;
 			$result = dbquery($sQuery);
 								
 									//invdt.OP_EMP_ID is not null and CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) is not null
 									//pl.NAME as ProdLine, mn.NAME as MNFR,
 									//left outer join T_PROD_LINE pl on pl.ID=p.PROD_LINE_ID
 									//left outer join T_MANUFACTURER mn on mn.ID=p.MNF_ID 	
 									//"group by invdt.OP_EMP_ID,p.ID, p.NAME, p.UPC_CD, pl.NAME, mn.NAME
 									//having sum(invdt.QTY)<0");
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 					echo "<table width='80%'>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sCol1 = $row[0];
 						$sCol2 = $row[1];
 						$sDate = $row[2];
 						$sOperation = $row[3];
 						$sWHCode = $row[4];
 						$iQty = $row[5];

 						if( ( $sCol1 != $sCol1_old || $sCol2 != $sCol2_old ) )
 						{
 							if(!$bFirst) {
 	//							echo "</table>";
 									echo "<tr class='rptgrp2h'><td class=\"white\" colspan=2></td><th class='rptsmry' colspan=".($nTotalCols-3).">--TOTAL</th><th class='rptsmry-num'>".$nGrp2Qty_ttl."</th></tr>";
 									$nGrp2Qty_ttl = 0;
 							}
 								
 						}
						if($sCol1 != $sCol1_old) //Grp1 header
						{
							//flush previous group total
							if(!$bFirst)  //Grp1 total
							{
								if($sGrpBy2!="")
									echo "<tr class='rptgrp1h'><td class=\"white\" width=\"5%\"></td><td colspan=".($nTotalCols-2)." class='rptgrp1'>--TOTAL</td><td class='rptgrp1-num'>".$nGrp1Qty_ttl."</td></tr>";
								echo "<tr><th colspan=".($nTotalCols)." class='rpthdr'> </th></tr>"; //blank row with underline
								$nGrp1Qty_ttl = 0;
								$sCol2_old = "";
							}
							echo "<tr class='rptgrp1h'><td class='rptgrp1' colspan=".($nTotalCols).">".$sCol1."</td></tr>";
						
						}
						if($sCol2 != $sCol2_old || $sCol1 != $sCol1_old)//Grp2 header
						{
							echo "<tr class='rptgrp2h'><td class=\"white\" width=\"5%\"></td><td colspan=".($nTotalCols-1)." class='rptgrp2'>".$sCol2."</td></tr>";
							echo "<tr><th class=\"white\" colspan=2 width=\"10%\"></th><th class='rpthdr'>DATE</th><th class='rpthdr'>ACTIVITY</th><th class='rpthdr'>Warehouse</th><th class=\"rpthdr-num\">QTY</th></tr>";
						}
						
 						//echo "<tr class=\"rptdata\"><td class=\"white\"></td><td>".$sDate."</td><td>".$sOperation."</td><td>".$sWHCode."</td><td class=\"rep-num\">".$iInvQty."</td></tr>";
 						echo "<tr class=\"rptdata\"><td class=\"white\" colspan=2 ></td><td>".$sDate."</td><td>".$sOperation."</td><td>".$sWHCode."</td><td class=\"rep-num\">".$iQty."</td></tr>";
 						$nGrp1Qty_ttl += $iQty;
 						$nGrp2Qty_ttl += $iQty;
 						$nRptQty_ttl += $iQty;
 						$bFirst = FALSE; //first round complete
 						$sCol1_old = $sCol1;
 						$sCol2_old = $sCol2;
 						
    				}//end while
					//last groups totals
    				//echo "<table class='rptgrp1'><tr><td class=\"white\" width=\"5%\"></td><td width=\"80%\" class='rptgrp1'>--TOTAL</td><td class='rptgrp1-num'>".$nGrp1_total."</td></tr></table><br>";
    				echo "<tr class='rptgrp2h'><td class=\"white\" colspan=2></td><th class='rptsmry' colspan=".($nTotalCols-3).">--TOTAL</th><th class='rptsmry-num'>".$nGrp2Qty_ttl."</th></tr>";
    				if($sGrpBy2!="")
	    				echo "<tr class='rptgrp1h'><td class=\"white\" width=\"5%\"></td><td colspan=".($nTotalCols-2)." class='rptgrp1'>--TOTAL</td><td class='rptgrp1-num'>".$nGrp1Qty_ttl."</td></tr>";
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
