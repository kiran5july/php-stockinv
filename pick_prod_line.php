<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Product Lines List');
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
  	$sPLName = $_GET["prodLineName"];
  	$sPLId = "";
  	$sPLDesc = "";
  	//Check if QueryString has value
  	//If(!empty($sDeptName)) {
  	  

		try{
 			$result = dbquery("select ID, NAME, DESCRIP from T_PROD_LINE ". 
 						(($sPLName == "") ? "" : " where NAME like '".$sPLName."%'"));
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 					?>

<?php 
 					echo "<table class='rep' width='100%'><th class='rep'>PROD LINE ID</th>
  						<th class='rep'>PROD LINE NAME</th><th class='rep'>DESCRIPTION</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sPLId = $row[0];
 						$sPLName = $row[1];
 						$sPLDesc = $row[2];
 						echo "<tr class=\"rep\"><td class=\"rep\">".$sPLId."</td><td class=\"rep\"><a href=\"JavaScript:setPLId('".$sPLId."','".$sPLName."')\">".$sPLName."</a></td><td class=\"rep\">".$sPLDesc."</td></tr>";
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
