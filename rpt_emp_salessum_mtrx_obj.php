<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  require_once('includes/cls_sale_emp_prd_dt_qty.php');
  do_rpt_header('Employee Sales Matrix Report');
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
  	$sEmpName = isset($_POST['sEmpName'])?$_POST['sEmpName']:"";
  	$sEmpId = ($sEmpName) ? (isset($_POST['sEmpId'])?$_POST['sEmpId']:"") : "";
  	$sPrdName = "";
  	$iQty = "";
  	$dtStart = isset($_POST['dtStart'])?$_POST['dtStart']:"";
  	$dtEnd = isset($_POST['dtEnd'])?$_POST['dtEnd']:"";

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
	$aRptData = Array();
	$sEmpName_old = "";
	$r = 0;

	//echo "Input: sEmpId=".$sEmpId.";sProdId=".$sProdId.";sGrpBy1=".$sGrpBy1.";sGrpBy2=".$sGrpBy2.";sGrpPrd=".$sGrpPrd;
  	//Add filter section
	echo "<h3>Filter Criteria</h3>";
	echo "<table><tr><th>Employee Name:</th><td>".(($sEmpName && $sEmpId)?$sEmpName:"--ALL--</td></tr>").
		"<tr><th>Date Range:</th><td>From:".(($dtStart)?$dtStart:"--System Launch--")."<br>To:".(($dtEnd)?$dtEnd:date("Y-m-d H:i:s"))."</td></tr>".
		"</table><hr>";

    	//Build grouping columns for DB query
		if($sEmpName==""){
			$sColSelect = "";
			$sGrpByCols = "ENAME ";
		}else{
    			$sColSelect = "COALESCE(CONCAT(e.FIRST_NAME,' ',e.LAST_NAME), '-STOCK IN-') as ENAME ";
    			$sGrpByCols = " ";
    			switch($sGrpBy2){
    				case "Product":
    					$sColSelect .= "";
    					$sGrpByCols .= ", PNAME";
    					break;
    				default:
    					$sColSelect .= ", '' as PNAME";
    					$sGrpByCols .= ", PNAME";
    			}//end switch $sGrpBy2
    			break;
    	}//end switch $sGrpBy1
    	
    	
		try{
			$sQuery = "select CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) as ENAME, CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME, DATE(ord.ORD_DT) as ORD_DT, sum(ol.QTY) as QTY
 							from T_EMP_SALES empsl
								inner join T_ORDER_LINES ol on empsl.ORD_LN_ID=ol.ID
								inner join T_ORDERS ord on ord.ID=ol.ORD_ID
 								inner join T_PRODUCTS p on p.ID=ol.PROD_ID
 								inner join T_EMP e on e.ID=ord.EMP_ID
 								where 1=1 ".
 									(($sEmpId && $sEmpName) ? " and empsl.EMP_ID=".$sEmpId : "").
 									((strlen($dtStart)>9) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') >= STR_TO_DATE('".$dtStart."','%Y-%m-%d')" : "").
 									((strlen($dtEnd)>9) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') <= STR_TO_DATE('".$dtEnd."','%Y-%m-%d')" : "").
 								" group by ENAME, PNAME, DATE(ord.ORD_DT)".
 								" order by ENAME, DATE(ord.ORD_DT) asc";
			//echo "My SQL: ".$sQuery;
 			$result = dbquery($sQuery);
 								
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 					$r=-1;
 					while ($row=mysqli_fetch_row($result))
 					{
 						$sEmpName = $row[0];
 						$sPrdName = $row[1];
 						$sDate = $row[2];
 						$iQty = $row[3];
 						
 						if( ( $sEmpName != $sEmpName_old  ) )
 						{
 							$r += 1;
 							$aRptData[$r] = new cls_sale_emp_prd_dt_qty($sEmpName);
 						}
 						$aRptData[$r]->addSaleData($sPrdName, $sDate, $iQty);
 						echo "<br>Data at ".$r."->".$sEmpName."/".$sPrdName."/".$sDate."/".$iQty;
						
 						$sEmpName_old = $sEmpName;
 						
 					}

 					//echo "<br>recordcount:".$rowcount."<br>";
 					$table = Array();
 					//$empNames = array();
 					//$prodNames = array();
 					//$total = array();
 					//$rec2 = "";
 					foreach ($aRptData as $rec)
 					{
 						echo "<hr>Emp:".$rec->sEmpName."<br><table border=1>";
 						$iDtRow = $rec->buildPrdDateMatrix();
 						
 					for($a=0; $a<$iPrdCol; $a++)
 					{
 						echo "<tr>";
 						for($b=0; $b<$iDtRow; $b++)
 							if($b==0 || $a==0)
 								echo "<th width=5>". $iDtRow[$a][$b];
 								else
 									echo "<td>". $iDtRow[$a][$b];
 									echo "</tr>";
 					}
 					echo "</table>";
 						
 					}
 					


 					
/* 					
 					$sEmpName_old="";
 					$sPrdName_old="";
 					$sDate_old="";
 					$sPrdDtList = Array();
 					$iPrdRow = 0;
 					$iPrdCol = 0;
 					$iDtRow = 0;
 					$iDtCol = 0;
 					$bFirst = true;
 					//echo "<table width='80%' border=1>";
 					for($r=0; $r < $rowcount; $r++)
 					{

 						$sEmpName = $aRptData[$r][0]; //Emp name
 						$sPrdName = $aRptData[$r][1]; //Prod name
 						$sDate = $aRptData[$r][2]; //Sale dt
 						$iQty = $aRptData[$r][3]; //Qty
 					

 						if( ( $sEmpName != $sEmpName_old  ) )
 						{
 							echo "<hr>";
 							if(!$bFirst)
 							{
 								echo "Emp:".$sEmpName_old."<br><table border=1>";
 								asort($sPrdDtList);
 								for($a=0; $a<$iPrdCol; $a++)
 								{
 									echo "<tr>";
 									for($b=0; $b<$iDtRow; $b++)
 										if($b==0 || $a==0)
 											echo "<th width=5>". $sPrdDtList[$a][$b];
 										else
 											echo "<td>". $sPrdDtList[$a][$b];
 									echo "</tr>";
 								}
 								echo "</table>";
 								reset($sPrdDtList);
 							}
 							$iPrdCol=1; $iDtRow=1; //Start from 1,1 for data
 							$sPrdDtList = Array();
 							$sPrdDtList[0] = Array();
 							array_push($sPrdDtList[0],"Date\Product");
 							$sPrdName_old = "";
 							$sDate_old = "";
 						}
 						if($sDate != $sDate_old )//&& ( $sEmpName == $sEmpName_old  ))
 						{
							echo "--New row.";
 							$sPrdDtList[$iDtRow] = Array();
 						}else $iDtRow -=1;
 						
 						echo "<br>Line#=".$r."->".$sPrdName."/".$sDate."/".$iQty."; iDtRow=".$iDtRow."; iPrdCol=".$iPrdCol;
 						$sPrdDtList[0][$iPrdCol]=$sPrdName; //Col header
 						//array_push($sPrdDtList[0],$sPrdName); //Col header
 						$sPrdDtList[$iDtRow][0]=$sDate; //Row Header
 						$sPrdDtList[$iDtRow][$iPrdCol]=$iQty; //Cell data
 						
 						$iDtRow += 1;
 						$iPrdCol+=1;

 						$sEmpName_old = $sEmpName;
 						$sPrdName_old = $sPrdName;
 						$sDate_old = $sDate;

 						$bFirst = false;
 					}
 					echo "<hr>";
 					echo "Emp:".$sEmpName_old."<br><table border=1>";
 					asort($sPrdDtList);
 					for($a=0; $a<$iPrdCol; $a++)
 					{
 						echo "<tr>";
 						for($b=0; $b<$iDtRow; $b++)
 							if($b==0 || $a==0)
 								echo "<th width=5>". $sPrdDtList[$a][$b];
 							else
 								echo "<td>". $sPrdDtList[$a][$b];
 							echo "</tr>";
 					}
 					echo "</table>";
*/
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
