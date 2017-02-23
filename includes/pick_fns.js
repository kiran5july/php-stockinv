/**
 * 
 */

//Sets the parent windows elements values
function setParentId(ctrlId, valId, ctrlName, valName)
{
	window.opener.document.getElementById(ctrlId).value = valId;
	window.opener.document.getElementById(ctrlName).value = valName;
	//window.opener.document.frmParent.sDeptName.value=name;
	self.close();
}

//Address
function setAddressId(sId, sName)
{
	setParentId('sAddrId', sId, 'sAddr1', sName);
}
function pickAddress(addr1)
{
	var features = 'height=400,width=750,resizable=1,scrollbars=yes,top=100,left=100,status=no,help=no,titlebar=no,menubar=no,toolbar=no'; 
	window.open("pick_address.php?addr1="+addr1, "winaddr", features, false);
}

//Dept
function setDeptId(sId, sName)
{
	setParentId('sDeptId', sId, 'sDeptName', sName);
}
function pickDept(sDeptName)
{
	var features = 'height=300,width=400,resizable=1,scrollbars=yes,top=100,left=100,status=no,help=no,titlebar=no,menubar=no,toolbar=no'; 
	window.open("pick_dept.php?deptname="+sDeptName, "windept", features, false);
}

//Employee
function setEmpId(sId, sName)
{
	setParentId('sEmpId', sId, 'sEmpName', sName);
}
function pickEmp(sEmpName)
{
	var features = 'height=400,width=400,resizable=1,scrollbars=yes,top=100,left=100,status=no,help=no,titlebar=no,menubar=no,toolbar=no'; 
	window.open("pick_employee.php?empName="+sEmpName, "winemp", features, false);
}


//Manufacturer
function setMnfId(sId, sName)
{
	setParentId('sMnfId', sId,'sMnfName', sName);
}
function pickManufacturer(sMnfName)
{
	var features = 'height=400,width=400,resizable=1,scrollbars=yes,top=100,left=100,status=no,help=no,titlebar=no,menubar=no,toolbar=no'; 
	window.open("pick_manufacturer.php?mnfName="+sMnfName, "winmnf", features, false);
}






//Products
function setProductId(sId, sName, iLine)
{
	setParentId('sProdId'+iLine, sId, 'sProdName'+iLine, sName);
}
function pickProd(sProdName, sCriteria, iLine)
{
	//sCriteria: a=all products(default); 
	//			i=Inventory available products
	//iLine: the line of the product selected during Stock Entry
	var features = 'height=400,width=900,resizable=1,scrollbars=yes,top=100,left=100,status=no,help=no,titlebar=no,menubar=no,toolbar=no'; 
	window.open("pick_product.php?prodName="+sProdName+"&cri="+sCriteria+"&ln="+iLine, "winproduct", features, false);
}
function pickProd_by_empId(sEmpId)
{
	var features = 'height=400,width=700,resizable=1,scrollbars=yes,top=100,left=100,status=no,help=no,titlebar=no,menubar=no,toolbar=no'; 
	window.open("pick_prod_empid.php?empId="+sEmpId, "win1", features, false);	
}


//Product Lines
function setPLId(sId, sName)
{
	setParentId('sPLId', sId, 'sPLName', sName);
}
function pickProdLine(sProdLineName)
{
	var features = 'height=300,width=400,resizable=1,scrollbars=yes,top=100,left=100,status=no,help=no,titlebar=no,menubar=no,toolbar=no'; 
	window.open("pick_prod_line.php?prodLineName="+sProdLineName, "winprodline", features, false);
}


//Warehouse
function setWHId(sId, sName, iLine)
{
	setParentId('sWHId'+iLine, sId, 'sWHCode'+iLine, sName);
}

function pickWarehouse(sWHCode, iLine)
{
	var features = 'height=400,width=400,resizable=1,scrollbars=yes,top=100,left=100,status=no,help=no,titlebar=no,menubar=no,toolbar=no'; 
	window.open("pick_warehouse.php?sWHCode="+sWHCode+"&ln="+iLine, "winstkloc", features, false);
}
function pickWH_by_prodId(sProdId, iLine)
{
	var features = 'height=300,width=700,resizable=1,scrollbars=yes,top=100,left=100,status=no,help=no,titlebar=no,menubar=no,toolbar=no'; 
	window.open("pick_warehouse_prodid.php?prodId="+sProdId+"&ln="+iLine, "win1", features, false);	
}