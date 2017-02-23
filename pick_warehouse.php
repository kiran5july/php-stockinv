<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Warehouse List');
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
  	$sId = "";
  	$sWHCode = isset($_GET["sWHCode0"])?$_GET["sWHCode0"]:"";
  	$iLine = $_GET['ln'];
	$sAddr = "";
  	$sContactName = "";
  	//Check if QueryString has value
  	//If(!empty($sDeptName)) {
 
		try{
 			$result = dbquery("select wh.ID, wh.WH_CODE, CONCAT(a.ADDR1,', ',a.ADDR2, ', ',a.CITY, ', ',a.STATE, ', ',a.COUNTRY, ', ', a.ZIP_CODE) as ADDR, CONCAT(e.FIRST_NAME, ', ', e.LAST_NAME) as CONTACT
 								from T_WAREHOUSE wh left outer join T_ADDR a on a.ID=wh.ADDR_ID 
 									left outer join T_EMP e on e.ID=wh.CONTACT_ID ". 
 						(($sWHCode == "") ? "" : " where wh.WH_CODE like '".$sWHCode."%'"));
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount = mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 					?>

<?php 
 					echo "<table class='rep' width='100%'><th class='rep'>ID</th>
  						<th class='rep'>LOC CODE</th><th class='rep'>FULL ADDRESS</th><th class='rep'>CONTACT</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sId = $row[0];
 						$sWHCode = $row[1];
 						$sAddr = $row[2];
 						$sContactName = $row[3];
 						echo "<tr class=\"rep\"><td class=\"rep\">".$sId."</td><td class=\"rep\"><a href=\"JavaScript:setWHId('".$sId."','".$sWHCode."','".$iLine."')\">".$sWHCode."</a></td><td class=\"rep\">".$sAddr."</td><td class=\"rep\">".$sContactName."</td></tr>";
    				}
    				echo "</table>";

 				}else 
 					echo "<p>No records found for your filter. please update filter criteria & try again.</p>";
 			}//end else
		}catch (Exception $e) {
     	    echo "<p>Record entry Exception. Please try again.</p>";
 		}


  	//}//end of $sDeptName

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
