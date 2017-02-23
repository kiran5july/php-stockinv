<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Addresses List');
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
  	$sAddr1 = $_GET["addr1"];
  	$sAddrId = "";
  	$sAddr2 = "";
  	$sCity = "";
  	$sState = "";
  	$sCountry = "";
  	$sZip = "";
  	//Check if QueryString has value
  	//If(!empty($sDeptName)) {
  	  

		try{
 			$result = dbquery("select ID, ADDR1, ADDR2, CITY, STATE, COUNTRY, ZIP_CODE from T_ADDR ". 
 						(($sAddr1 == "") ? "" : " where ADDR1 like '".$sAddr1."%'"));
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 					?>

<?php 
 					echo "<table class='rep' width='100%'><th class='rep'>ADDR ID</th><th class='rep'>ADDR1</th><th class='rep'>ADDR2</th>
  							<th class='rep'>CITY</th><th class='rep'>STATE</th><th class='rep'>COUNTRY</th><th class='rep'>ZIP</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sAddrId = $row[0];
 						$sAddr1 = $row[1];
 						$sAddr2 = $row[2];
 						$sCity = $row[3];
 						$sState = $row[4];
 						$sCountry = $row[5];
 						$sZip = $row[6];
 						echo "<tr class=\"rep\"><td class=\"rep\">".$sAddrId."</td>
  									<td class=\"rep\"><a href=\"JavaScript:setAddressId('".$sAddrId."','".$sAddr1."')\">".$sAddr1."</a></td>
  									<td class=\"rep\">".$sAddr2."</td><td class=\"rep\">".$sCity."</td><td class=\"rep\">".$sState."</td>
 									<td class=\"rep\">".$sCountry."</td><td class=\"rep\">".$sZip."</td></tr>";
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
