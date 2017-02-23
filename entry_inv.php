<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Inventory entry:');
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
  	$sProdId = "";
  	$sProdName = "";
  	$sWHId = "";
  	$sWHCode = "";
  	$iQty = "";
  	$dtAdded = "";
  	
  	$aInvtLines = Array();
 	
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names
  	$sWHCode = $_POST['sWHCode0'];
  	$sWHId = $_POST['sWHId0'];
  	$dtAdded = (isset($_POST['dtAdded'])?$_POST['dtAdded']:"");
  	$sProdId = "";
  	$sProdName = "";
  	$iQty = "";
  	$iLineCount = isset($_POST['iLineCount'])?$_POST['iLineCount']:0;
  	$iRecInsert = True;
	$sOpMsg = "";
  	// check forms filled in
  	if ((strlen($sWHId) < 1)) {
  		echo "<p class=\"inputerror\">Warning! Missing Required fields -Warehouse. Try again.</p>";
  	}else{
  	//Get all inventory entry data in array
  	for($r=0; $r < $iLineCount; $r++)
  	{
  			
  		$aInvtLines[$r] = Array();
  		$aInvtLines[$r][0] = isset($_POST['sProdId'.$r])?$_POST['sProdId'.$r]:"";
  		$aInvtLines[$r][1] = isset($_POST['sProdName'.$r])?$_POST['sProdName'.$r]:"";
  		$aInvtLines[$r][2] = isset($_POST['iQty'.$r])?$_POST['iQty'.$r]:"";
  		//echo "<p>Line# out of ".$iLineCount.":".$r."->".$aInvtLines[$r][0]."/".$aInvtLines[$r][1]."/".$aInvtLines[$r][2]."</p>";
  		
  		if(($aInvtLines[$r][0] == "" && $aInvtLines[$r][1] == "" && $aInvtLines[$r][2]==""))
  		{
  			//echo "<p>--> A blank row detected.</p>";
  			//break;
  		}else {
  			if(($aInvtLines[$r][0] == "" || $aInvtLines[$r][1] == "" || $aInvtLines[$r][2]=="") || !is_numeric($aInvtLines[$r][2]) || $aInvtLines[$r][2]<0)
  			{
  				echo "<p class='excepmsg'>Invalid entry detected.<br>Line# [".$r."]-> ".$aInvtLines[$r][0]." / ".$aInvtLines[$r][1]." / ".$aInvtLines[$r][2]."</p>";
  				$iRecInsert = false;
  			}
  		}//end else
  	}//end for
	if($iRecInsert)
	{

		try{
			$db->autocommit(false);				
			for($r=0; $r < $iLineCount; $r++)
			{
				$sProdId = $aInvtLines[$r][0];
				$sProdName = $aInvtLines[$r][1];
				$iQty = $aInvtLines[$r][2];
				if($sProdId == "" && $sProdName == "" && $iQty == "")
				{
					$sOpMsg .= "Line# [".$r."]-> is blank/deleted.... ignored<br>";
				}else {
					$result = dbquery("select ID from T_INVT where PROD_ID=".$sProdId." and WH_ID='".$sWHId."'");
					
					if (!$result) {
						echo "<p>Inventory Query failed, please close the window & try again.</p>";
					}else{
			
						$rowcount=mysqli_num_rows($result);
						//if record exist, add the quantity to it
						if($rowcount >= 1)
						{
							//Update existing inventory record
							$row=mysqli_fetch_row($result);
							$sInvId = $row[0];

							$result=$db->query("update T_INVT set QTY = QTY+".$iQty." where ID=".$sInvId);
							if(!$result )
							{
								echo "<p class='excepmsg'>Exception during updating Inventory detail record.<br>".$db->error."</p>";
								throw new Exception('KMDB Exception: Exception during updating Inventory detail record.');
							}

						}//end rowcount of T_INVENTORY
						else 
						{	//else create new Inventory master record for it
								$result=$db->query("insert into T_INVT (PROD_ID, WH_ID, QTY) values
			                         	('".$sProdId."','".$sWHId."',".$iQty.")");
								if(!$result )
								{
									echo "<p class='excepmsg'>Exception during updating Inventory master record.<br>".$db->error."</p>";
									throw new Exception('KMDB Exception: Exception during updating Inventory master record.');
								}
								$sInvId = $db->insert_id;
								//$iRecInsert = $db->insert_id;
						}//else Inv(Prod/WH)
						//Insert Inventory master record
						$result=$db->query("insert into T_INVT_DTL (INVT_ID, OP_DT, QTY, OPERATION) values
				                         	('".$sInvId."','".$dtAdded."',".$iQty.", 'IN-STK')");
						if(!$result )
						{
							echo "<p class='excepmsg'>Exception during inserting Inventory detail record.<br>".$db->error."</p>";
							throw new Exception('KMDB Exception: Exception during inserting Inventory detail record.');
						}
					}//end else query success
					$sOpMsg .= "Line# [".$r."]->".$aInvtLines[$r][0]." / ".$aInvtLines[$r][1]." / ".$aInvtLines[$r][2]." .... inserted.<br>";
				}//end validation		
			}//end for
			$db->commit();
			echo "<p class='successmsg'>Success!<br>".$sOpMsg."</p>";
		}catch(Exception $ex){
				$db->rollback();
				echo "<p class=\"inputerror\">Failed to update/insert record in inventory. Operation Log:<br>".$sOpMsg."</p>";
		}

	}//end of $iRecInsert true
	
	}//end if/else validations
  	}//end of btnSubmit
	else{
		//first time or not submit initialization
		$iLineCount = 1;
		for($r=0; $r < $iLineCount; $r++)
		{
			$aInvtLines[$r] = Array();
			$aInvtLines[$r][0]="";
			$aInvtLines[$r][1]="";
			$aInvtLines[$r][2]="";
		}
	}

 	?>

    <form method="post" name="frmParent" action="entry_inv.php">
    <table class="form">

    <tr>
    	<td>Warehouse Loc:</td>
    	<td><input type="text" name="sWHCode0" id="sWHCode0" size="16" maxlength="25" value="<?=$sWHCode?>"/>
    		<input type="text" name="sWHId0" id="sWHId0" size="2" readonly/>
    		<input type="button" value="..." onclick="JavaScript:pickWarehouse(sWHCode0.value,'0')"/></td></tr>
    <tr>
    	<td>Added Date:</td>
    	<td><input type="text" id="dtAdded" name="dtAdded" size="16" maxlength="19" value="<?=$dtAdded?>" readonly/>
    			<a href="javascript:NewCal('dtAdded','YYYYMMDD',true,24)"><img src="images/cal.gif" width="16" height="16" border="1" alt="Pick a datetime"></a></td></tr>

<table id='recTbl'>
	<tr><td>Product</td><td>Qty</td><td></td><tr>
<?php  
for($r=0; $r< $iLineCount; $r++)
{

?>
    <tr id="tr<?=$r?>">  
    	<td><input type="text" name="sProdName<?=$r?>" id="sProdName<?=$r?>" size="16" maxlength="25" value="<?=$aInvtLines[$r][1]?>"/>
    		<input type="text" name="sProdId<?=$r?>" id="sProdId<?=$r?>" size="2" value="<?=$aInvtLines[$r][0]?>" readonly/>
    		<input type="button" value="..." onclick="JavaScript:pickProd(sProdName<?=$r?>.value,'a','<?=$r?>')"/></td>
    	<td><input type="text" name="iQty<?=$r?>" size="16" maxlength="5" value="<?=$aInvtLines[$r][2]?>"/></td>
	<td><?= ($r>0)? "<img src='images/delete.png' onclick=\"remStkEntry('recTbl','tr".$r."')\"/>" : "" ?></td>
	</tr>
<?php 
} ?>
</table>


    <tr>
    	<td align="center"><input type="button" value="Add new" onclick="JavaScript:addStkEntry()">
    		<input type="text" name="iLineCount" id="iLineCount" size="2" value="<?=$iLineCount?>" readonly></td>
    		<td align="center"><input type="submit" name="btnSubmit" value="Insert"><input type="reset" value="Clear Fields"></td></tr>
    </table>
    </form>
 	<?php
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
