<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Employees List');
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
  	$sEmpId = "";
  	$sEmpCode = "";
  	$sEmpName = $_GET["empName"];
  	$sDeptName = "";
  	//Check if QueryString has value
  	//If(!empty($sDeptName)) {
 
		try{
 			$result = dbquery("select e.ID, e.EMP_CODE, CONCAT(e.FIRST_NAME,' ',e.LAST_NAME) as NAME, d.DEPT_NAME from T_EMP e left outer join T_DEPT d on d.ID=e.DEPT_ID ". 
 						(($sEmpName == "") ? "" : " where (e.FIRST_NAME like '".$sEmpName."%' OR e.LAST_NAME like '".$sEmpName."%')"));
 			if (!$result) {
 				echo "<p>Query failed, please close the window & try again.</p>";
 			}else{
 				$rowcount = mysqli_num_rows($result);
 				if($rowcount >= 1)
 				{
 					?>
<script>
function send2Parent(sId, sName)
{
	window.opener.document.frmParent.sTechId.value = sId;
	window.opener.document.frmParent.sTechName.value = sName;
	self.close();
}
</script>
<?php 
 					echo "<table class='rep' width='100%'><th class='rep'>ID</th>
  						<th class='rep'>EMP CODE</th><th class='rep'>EMP NAME</th><th class='rep'>DEPT NAME</th></tr>";
 					while ($row=mysqli_fetch_row($result))
    				{
 						$sEmpId = $row[0];
 						$sEmpCode = $row[1];
 						$sEmpName = $row[2];
 						$sDeptName = $row[3];
 						echo "<tr class=\"rep\"><td class=\"rep\">".$sEmpId."</td><td class=\"rep\"><a href=\"JavaScript:setEmpId('".$sEmpId."','".$sEmpName."')\">".$sEmpCode."</a></td><td class=\"rep\">".$sEmpName."</td><td class=\"rep\">".$sDeptName."</td></tr>";
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
