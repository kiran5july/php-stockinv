<?php
// include function files for this application
require_once('includes/all_includes.php');
do_html_header('');
// start session which may be needed later
session_start();
// register session variable
$username = $_SESSION['valid_user'];

//try   {
	If(empty($username))
	{
		echo "Your session timed out. Please <a href=\"login.php\" target=\"main\">login</a> again.";
		die();
	}else{
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<title>Menu</title>
<style type="text/css">
<!--
body {
	background-color: #FFFFCC;
}
-->
</style></head>
<body background="images/yell.jpg">
	<?php 
		echo "<p>Logged User: ".$username.". | <a href=\"logout.php\" target=\"main\"> Logout</a>";
		echo "<br/><a href=\"update_profile.php\" target=\"items\"> Update Profile</a></p>"
	?>
<p><strong>Entry</strong></p>
<ul>
<li><a href="view_emp_dt.php" target="items">Employee Data View</a></li>
<li><a href="entry_inv.php" target="items">Inventory Entry</a></li>
<li><a href="entry_manufacturer.php" target="items">Manufacturer</a></li>
<li><a href="entry_product.php" target="items">Product</a></li>
<li><a href="entry_prod2emp.php" target="items">Products to Employee</a></li>
<li><a href="entry_prodfromemp.php" target="items">Products from Emp</a></li>
<li><a href="entry_prod_incntv.php" target="items">Product Incentive</a></li>
<li><a href="entry_empsales.php" target="items">Employee Sales Entry</a></li>
<li><a href="entry_stkmove.php" target="items">Stock Move</a></li>
</ul>
<p><strong>REPORTS</strong></p>
<ul>
<li><a href="rptf_emp_sales.php" target="items">Employee Sales Detail Report</a></li>
<li><a href="rptf_emp_salessum.php" target="items">Employee Sales Summary Report</a></li>
<li><a href="rptf_emp_salessum_mtrx.php" target="items">Employee Sales Summary Matrix</a></li>
<li><a href="rptf_stk_act.php" target="items">Stock activity</a></li>
<li><a href="rptf_stk_avl.php" target="items">Stock Availability</a></li>
<li><a href="rptf_stk_empheld.php" target="items">Stock with Employees</a></li>
<li><a href="rptf_emp_inctv.php" target="items">Emp Sales Incentive Report</a></li>
</ul>

<p><strong>Admin/setup</strong></p>
<ul>
<li><a href="entry_employee.php" target="items">Employee</a></li>
</ul>


</body>
</html>
	
	<?php 	
	}
	?>
	
	