<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Manufacturers List');
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
  	$sMnfName = $_GET["mnfName"];
  	$sMnfId = "";
  	//$sMnfContact = "";
  	//Check if QueryString has value
	  

		try{
 			$result = dbquery("select ID, NAME from T_MANUFACTURER ". 
 						(($sMnfName == "") ? "" : " where NAME like '".$sMnfName."%'"));
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 					?>

<?php 
 					echo "<table class='rep' width='100%'><th class='rep'>MNF ID</th>
  						<th class='rep'>MNF NAME</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sMnfId = $row[0];
 						$sMnfName = $row[1];
 						//$sPLDesc = $row[2];
 						echo "<tr class=\"rep\"><td class=\"rep\">".$sMnfId."</td><td class=\"rep\"><a href=\"JavaScript:setMnfId('".$sMnfId."','".$sMnfName."')\">".$sMnfName."</a></td></tr>";
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
