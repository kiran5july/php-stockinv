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
  	$sEmpId = isset($_POST['sEmpId'])?$_POST['sEmpId']:"";
  	$sEmpName = isset($_POST['sEmpName'])?$_POST['sEmpName']:"";
  	$sPrdName = "";
  	$iQty = "";
  	$dtStart = isset($_POST['dtStart'])?$_POST['dtStart']:"";
  	$dtEnd = isset($_POST['dtEnd'])?$_POST['dtEnd']:"";

  	$sDate = "";
  	//$dAmount = "";

	//$nGrp1_total = 0;
	$bFirst=TRUE;
	$aRptData = Array();
	$r = 0;

	//echo "Input: sEmpId=".$sEmpId.";sProdId=".$sProdId.";sGrpBy1=".$sGrpBy1.";sGrpBy2=".$sGrpBy2.";sGrpPrd=".$sGrpPrd;
  	//Add filter section
	echo "<h3>Filter Criteria</h3>";
	echo "<table><tr><th>Employee Name:</th><td>".(($sEmpName)?$sEmpName:"--ALL--</td></tr>").
		"<tr><th>Date Range:</th><td>From:".(($dtStart)?$dtStart:"--System Launch--")."<br>To:".(($dtEnd)?$dtEnd:date("Y-m-d H:i:s"))."</td></tr>".
		"</table><hr>";

		try{
			$sQuery = "select CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) as ENAME, CONCAT(p.NAME,'  (', p.UPC_CD,')') as PNAME, DATE(ord.ORD_DT) as ORD_DT, sum(ol.QTY) as QTY
 							from T_EMP_SALES empsl
								inner join T_ORDER_LINES ol on empsl.ORD_LN_ID=ol.ID
								inner join T_ORDERS ord on ord.ID=ol.ORD_ID
 								inner join T_PRODUCTS p on p.ID=ol.PROD_ID
 								inner join T_EMP e on e.ID=ord.EMP_ID
 								where 1=1 ".
 									(($sEmpId) ? " and empsl.EMP_ID=".$sEmpId : "").
 									((strlen($dtStart)>1) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') >= STR_TO_DATE('".$dtStart."','%Y-%m-%d')" : "").
 									((strlen($dtEnd)>1) ? " and STR_TO_DATE(ord.ORD_DT,'%Y-%m-%d') <= STR_TO_DATE('".$dtEnd."','%Y-%m-%d')" : "").
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
 					$r=0;
 					while ($row=mysqli_fetch_row($result))
 					{
 						
 						$aRptData[$r] = Array();
 						$aRptData[$r][0] = $row[0];
 						$aRptData[$r][1] = $row[1];
 						$aRptData[$r][2] = $row[2];
 						$aRptData[$r][3] = $row[3];
 						//echo "<br>Query looping at ".$r."->".$aRptData[$r][0]."/".$aRptData[$r][1]."/".$aRptData[$r][2]."/".$aRptData[$r][3];

 						$r += 1;

 					}

 					//Start printing output 
 					$sEmpName_old="";
 					$sPrdName_old="";
 					$sDate_old="";
 					$aPrdSold = Array();
 					$aPrdSold[0]="*";
 					$sPrdDtList = Array();
 					//$iPrdRow = 0;
 					$iPrdCol = 0;
 					$iDtRow = 0;
 					//$iDtCol = 0;
 					$bFirst = true;

 					for($r=0; $r < $rowcount; $r++)
 					{

 						$sEmpName = $aRptData[$r][0]; //Emp name
 						$sPrdName = $aRptData[$r][1]; //Prod name
 						$sDate = $aRptData[$r][2]; //Sale dt
 						$iQty = $aRptData[$r][3]; //Qty

 						if( ( $sEmpName != $sEmpName_old  ) )
 						{
 							if(!$bFirst)
 							{
 								$iPrdCol = count($aPrdSold);
								$iDtRow = count($sPrdDtList);
								
								$aPrdSold[$iPrdCol]="Total";
								$sPrdDtList[$iDtRow][0]="Total";
								
 								for($a=0; $a<$iDtRow; $a++)
 								{
 									for($b=0; $b<$iPrdCol; $b++)
 										if($b!=0){
 											$sPrdDtList[$a][$iPrdCol] += $sPrdDtList[$a][$b]; //row total
 											$sPrdDtList[$iDtRow][$b] += $sPrdDtList[$a][$b]; //col total
 											$sPrdDtList[$iDtRow][$iPrdCol] += $sPrdDtList[$a][$b]; //Grand total
 										}
 								}
 								$iPrdCol+=1;
 								$iDtRow+=1;
 								
 								echo "<h2>".$sEmpName_old."</h2><br><table class='repnw' border=1>";
								//print Column header/Products list
 								echo "<tr class='rptgrp1h'>";
 								for($a=0; $a<$iPrdCol; $a++)
 								{
 									echo "<th width=5>". $aPrdSold[$a];
 								}
 								echo "</tr>";
 								//Start printing sale data
 								
 								for($a=0; $a<$iDtRow; $a++)
 								{
 									echo "<tr>";
 									for($b=0; $b<$iPrdCol; $b++)
 										if($b==0)
 											echo "<th width=5>". $sPrdDtList[$a][$b];
 										else
 											echo "<td>". $sPrdDtList[$a][$b];
 									echo "</tr>";
 								}
 								echo "</table><hr>";

 								$sPrdDtList=null;
 								$aPrdSold =null;
 								$aPrdSold[0]="*";
 							}
 							$iPrdCol=0; $iDtRow=-1; //Start from 1,1 for data
 							$sPrdName_old = "";
 							$sDate_old = "";
 						}
	
 						if(array_search($sPrdName, $aPrdSold)){
 							$iPrdCol = array_search($sPrdName, $aPrdSold);
 						}else{
 							$iPrdCol += 1;
 							$aPrdSold[$iPrdCol]=$sPrdName; //Col header
 							//array_push($aPrdSold, $sPrdName);
 							//sort($aPrdSold);
 						}
 						if($sDate != $sDate_old )//&& ( $sEmpName == $sEmpName_old  ))
 						{
 							$iDtRow +=1;
 							$sPrdDtList[$iDtRow][0]=$sDate; //Row Header
 						}			
 						//echo "<br>Line#=".$r."->".$sPrdName."/".$sDate."/".$iQty.";---- iDtRow=".$iDtRow."; iPrdCol=".$iPrdCol;

 						$sPrdDtList[$iDtRow][$iPrdCol]=$iQty; //Cell data
 						
 						$sEmpName_old = $sEmpName;
 						$sPrdName_old = $sPrdName;
 						$sDate_old = $sDate;

 						$bFirst = false;
 					}
 					//Print last employee data
 								$iPrdCol = count($aPrdSold);
								$iDtRow = count($sPrdDtList);
								
								$aPrdSold[$iPrdCol]="Total";
								$sPrdDtList[$iDtRow][0]="Total";
								
 								for($a=0; $a<$iDtRow; $a++)
 								{
 									for($b=0; $b<$iPrdCol; $b++)
 										if($b!=0){
 											$sPrdDtList[$a][$iPrdCol] += $sPrdDtList[$a][$b]; //row total
 											$sPrdDtList[$iDtRow][$b] += $sPrdDtList[$a][$b]; //col total
 											$sPrdDtList[$iDtRow][$iPrdCol] += $sPrdDtList[$a][$b]; //Grand total
 										}
 								}
 								$iPrdCol+=1;
 								$iDtRow+=1;
 								
 								echo "<h2>".$sEmpName_old."</h2><br><table class='repnw' border=1>";
								//print Column header/Products list
 								echo "<tr class='rptgrp1h'>";
 								for($a=0; $a<$iPrdCol; $a++)
 								{
 									echo "<th width=5>". $aPrdSold[$a];
 								}
 								echo "</tr>";
 								//Start printing sale data
 								
 								for($a=0; $a<$iDtRow; $a++)
 								{
 									echo "<tr>";
 									for($b=0; $b<$iPrdCol; $b++)
 										if($b==0)
 											echo "<th width=5>". $sPrdDtList[$a][$b];
 										else
 											echo "<td>". $sPrdDtList[$a][$b];
 									echo "</tr>";
 								}
 								echo "</table><hr>";

 					//end output
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
