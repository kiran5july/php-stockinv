/**
 * 
 */

//Functions to validate User registration fields
function vd_fn_usr(vType)
{
	//var bValidStatus = true;
	var sValidMessage = "";

	switch(vType)
	{
	case "ADDR":
	    sValidMessage = vd_fn_address();
		break;
	case "ALL":
	default:
		sValidMessage = vd_fn_user_basic();
		if(sValidMessage == "" && document.getElementById('sUserName').value !="")
	    	sValidMessage = vd_fn_user_prof();
		if(sValidMessage == "" && document.getElementById('sAddr1').value != "")
			sValidMessage = vd_fn_address();
		break;	
	}

    if(sValidMessage!="")
    {
    	sValidMessage = "Invalid Input. Please check:"+sValidMessage;
    	document.getElementById('frmerror').innerHTML = sValidMessage;
    	//return false;
    }else{
    //alert('Validation Response:'+sValidMessage);
    	document.getElementById('frmParent').submit();
    	//document.forms['frmParent'].submit();
    }
}

function vd_fn_user_basic()
{
	//alert('vd_fn_user_basic');
	var sValidMessage = "";
	var sFirstName = document.getElementById('sFirstName').value;
	var sLastName = document.getElementById('sLastName').value;
	if ( sFirstName=="" ) {
		//bValidStatus = false;
		sValidMessage += "<br>-First Name";
		document.getElementById('sFirstName').focus();
    }
	if( sLastName=="") {
    	//bValidStatus = false;
    	sValidMessage += "<br>-Last Name";
    	document.getElementById('sLastName').focus();
    }
	return(sValidMessage);
}
function vd_fn_user_prof()
{

	var sValidMessage = "";

   	var sEmail = document.getElementById('email').value;
   	var sPwd = document.getElementById('passwd').value;
   	var sPwd2 = document.getElementById('passwd2').value;
   	var sSecretq = document.getElementById('secretq').value;
   	var sSecreta = document.getElementById('secreta').value;
   	if (sEmail.length < 1) {
   		//bValidStatus = false;
  		sValidMessage += "<br>-Email Address";
   		document.getElementById('email').focus();
   	}
   	if (!checkEmail(sEmail)) {
   		//bValidStatus = false;
   		sValidMessage += "<br>-Email Address format";
   		document.getElementById('email').focus();
   	}
   	// passwords not the same
   	if (sPwd != sPwd2) {
   		//bValidStatus = false;
   		sValidMessage += "<br>-Passwords do not match.";
   		document.getElementById('passwd').focus();
   	}
   	// check password length is ok
   	if ((sPwd.length < 6) || (sPwd2.length > 16)) {
   		sValidMessage += "<br>-Password should be between 6 to 16 characters";
   		document.getElementById('passwd').focus();
   	}
   	if ((sSecretq.length < 1) || (sSecreta.length < 1)) {
   		sValidMessage += "<br>-Secret Q & A";
   		document.getElementById('secreta').focus();
   	}

	return(sValidMessage);
}

function vd_fn_address()
{
	var sValidMessage = "";
    var sAddr1 = document.getElementById('sAddr1').value;
    if(sAddr1.length > 1)
    {
    	var sCity = document.getElementById('sCity').value;
    	var sState = document.getElementById('sState').value;
    	var sCountry = document.getElementById('sCountry').value;
    	var sZip = document.getElementById('sZip').value;
    	
    	if (sCity.length < 1) {
    		//bValidStatus = false;
    		sValidMessage += "<br>-City";
    		//document.getElementById('sCity').focus();
    	}
    	if ((sState.length < 1)) {
    		//bValidStatus = false;
    		sValidMessage += "<br>-State";
    		//document.getElementById('sState').focus();
    	}
    	if ((sCountry.length < 1)) {
    		//bValidStatus = false;
    		sValidMessage += "<br>-Country";
    		//document.getElementById('sCountry').focus();
    	}
    	if (sZip.length < 1) {
    		//bValidStatus = false;
    		sValidMessage += "<br>-Zip Code";
    		//document.getElementById('sZip').focus();
    	}
    }//end address validation
    return(sValidMessage);
}
function checkEmail(email) 
{
	//var emailReg = /^([w-.]+@([w-]+.)+[w-]{2,4})?$/;
	//if(!(email).match(emailReg)) {//invalid email}
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))
	{
		return (true)
	}
	return (false)
}	
