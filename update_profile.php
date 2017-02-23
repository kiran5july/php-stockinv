<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('Member Profile:');
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
  	$email = "";
  	$passwd = "";
  	$passwd2 = "";
  	
  	//Check if form submitted
  	If(isset($_POST["btnSubmit"])) {
  	  
  	//create short variable names
  	$email=$_POST['email'];
  	$passwd=$_POST['passwd'];
  	$passwd2=$_POST['passwd2'];
  	$secretq=$_POST['secretq'];
  	$secreta=$_POST['secreta'];

  	// start it now because it must go before headers

    // check forms filled in
    if (!filled_out($_POST)) {
    	echo "<p class='excepmsg'>Warning! All fields are not filled properly. Try again.</p>";
    }
    // email address not valid
    elseif (!valid_email($email)) {
      echo "<p class='excepmsg'>Warning! Invalid email address. Try again.</p>";
    }
    // passwords not the same
    elseif ($passwd != $passwd2) {
      echo "<p class='excepmsg'>Warning! Passwords do not match. Try again.</p>";
    }
    // check password length is ok
    elseif ((strlen($passwd) < 6) || (strlen($passwd) > 16)) {
    	echo "<p class='excepmsg'>Warning! Your password must be between 6 and 16 characters. Try again.</p>";
    }
    elseif ((strlen($secretq) < 1) || (strlen($secreta) < 1)) {
    	echo "<p class='excepmsg'>Warning! Please select a secret Question and Answer. Try again.</p>";
    }
	else{

  		// if ok, put in db
  		$result = dbquery("update T_USER set
                         PASSWD=sha1('".$passwd."'), EMAIL='".$email."', SECRETQ='".$secretq."',SECRETA='".$secreta."' 
  									where USERNAME='".$username."'");
  		if (!$result) {
    		echo 'User not found in the system - please log back in.';
    		die();
	  	}else
	  		echo '<p>Your profile is updated.!</p>';
	}

  	}//end of btnSubmit

 	If(empty($email))
 	{
 		// query for username
 	
 		$result = dbquery("select email, secretq, secreta from T_USER where username='".$username."'");
 		if (!$result) {
 			throw new Exception('Could not execute query');
 		}
 		$row=$result->fetch_row();
 		//$row = mysqli_fetch_row($result);
 		$email=$row[0];
 		$secretq=$row[1];
 		$secreta=$row[2];
 	}

 	//echo $username.$email.$secretq.$secreta;
 	?>
 	
    <form method="post" action="update_profile.php">
    <table bgcolor="#cccccc">
    <tr>
    	<td>Username (max 16 chars):</td>
    	<td valign="top"><input type="text" name="username" size="16" maxlength="16" value="<?=$username?>"/></td></tr>
    <tr>
    	<td>Email address:</td>
    	<td><input type="text" name="email" size="30" maxlength="100" value="<?=$email?>"/></td></tr>
    <tr>  
    	<td>Password (between 6 and 16 chars):</td>
    	<td valign="top"><input type="password" name="passwd" size="16" maxlength="16" value="<?=$passwd?>"/></td></tr>
    <tr>
    	<td>Confirm password:</td>
    	<td><input type="password" name="passwd2" size="16" maxlength="16" value="<?=$passwd2?>"/></td></tr>
    <tr>
    	<td>Select a secret question:</td>
    	<td><select name="secretq">
    			<option value="What is your birth city?" <?=($secretq == "What is your birth city?") ? "selected" : "" ?>>What is your birth city?</option>
    			<option value="What is your first school?" <?=($secretq == "What is your first school?") ? "selected" : "" ?>>What is your first school?</option>
    			<option value="What is your favorite food?" <?=($secretq == "What is your favorite food?") ? "selected" : "" ?>>What is your favorite food?</option>
    			<option value="Who is your best friend?" <?=($secretq == "Who is your best friend?") ? "selected" : "" ?>>Who is your best friend?</option>
    			<option value="Use a secret code" <?=($secretq == "Use a secret code") ? "selected" : "" ?>>Use a secret code</option>
    		</select></td></tr>
    <tr>
    	<td>Give secret answer:</td>
    	<td><input type="text" name="secreta" size="16" maxlength="16" value="<?=$secreta?>"/></td></tr>
    <tr>
    <tr>
    	<td colspan=2 align="center"><input type="submit" name="btnSubmit" value="Update Profile"></td></tr>
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
