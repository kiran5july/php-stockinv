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
					$r = -1;
					$sEmpName_old="";
 					while ($row=mysqli_fetch_row($result))
 					{
 						$sEmpName = $row[0];
 						$sPrdName = $row[1];
 						$sDate = $row[2];
 						$iQty = $row[3];
 							
 						if( ( $sEmpName != $sEmpName_old  ) )
 						{
 							$r += 1;
 							//Create new object to array
 							$aRptData[$r] = new cls_sale_emp_prd_dt_qty($sEmpName);
 						}
 						//Add data to the object
 						$aRptData[$r]->addSaleData($sPrdName, $sDate, $iQty);
 						//echo "<br>Data at ".$r."->".$sEmpName."/".$sPrdName."/".$sDate."/".$iQty;
 					
 						$sEmpName_old = $sEmpName;
 							
 					}
 					//$aPrdDtMtrx = Array();
 					foreach ($aRptData as $rec)
 					{
 						//echo "<br>Printing Sales data...";
 						//$rec->printSaleData();
 						//echo "<hr><br>Building Matrix..";
 						$rec->buildPrdDateMatrix();
 						//echo "<br>Printing matrix..";
 						$rec->printSaleMatrix();
						echo "<hr>";
 					}


 					//end output
 				}else 
 					echo "<p>No records found for your filter. please update filter criteria & try again.</p>";
 			}//end else
		}catch (Exception $e) {
     	    echo "<p>Record Query Exception. Please try again.</p>";
 		}

  	}//else session
   // end page
   do_html_footer();
  }catch (Exception $e) {
     do_html_header('Application Error:');
     echo $e->getMessage();
     do_html_footer();
     exit;
  }finally{
  	$aRptData = null;
  }
?>