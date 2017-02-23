<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Depts List');
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
  	$sDeptName = $_GET["deptname"];
  	$sDeptId = "";
  	
  	//Check if QueryString has value

		try{
 			$result = dbquery("select ID, dept_name from T_DEPT ". 
 						(($sDeptName == "") ? "" : " where DEPT_NAME like '".$sDeptName."%'"));
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount=mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 					?>

<?php 
 					echo "<table class='rep' width='100%'><th class='rep'>DEPT ID</th>
  						<th class='rep'>DEPT NAME</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sDeptId = $row[0];
 						$sDeptName = $row[1];
 						echo "<tr class=\"rep\"><td class=\"rep\">".$sDeptId."</td><td class=\"rep\"><a href=\"JavaScript:setDeptId('".$sDeptId."','".$sDeptName."')\">".$sDeptName."</a></td></tr>";
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
