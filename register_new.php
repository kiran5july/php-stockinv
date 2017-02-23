<?php
  // include function files for this application
  require_once('includes/all_includes.php');
  do_html_header('New User Registration:');
  try   {
  	$username = "";
  	$email = "";
  	$passwd = "";
  	$passwd2 = "";
  	$secretq = "";
  	$secreta = "";
  //Check if form submitted
  If(isset($_POST["btnSubmit"])) {
  	
  
  //create short variable names
  $username=$_POST['username'];
  $email=$_POST['email'];
  $passwd=$_POST['passwd'];
  $passwd2=$_POST['passwd2'];
  $secretq=$_POST['secretq'];
  $secreta=$_POST['secreta'];

 
    // check forms filled in
    if (!filled_out($_POST)) {
      //throw new Exception('You have not filled the form out correctly - please go back and try again.');
      echo '<p>Warning! All fields are not filled properly. Try again.</p>';
    }
    // email address not valid
    elseif (!valid_email($email)) {
      //throw new Exception('That is not a valid email address.  Please go back and try again.');
    	echo '<p>Warning! Invalid email address. Try again.</p>';
    }
    // passwords not the same
    elseif ($passwd != $passwd2) {
      //throw new Exception('The passwords you entered do not match - please go back and try again.');
      echo '<p>Warning! Passwords do not match. Try again.</p>';
    }
    // check password length is ok
    elseif ((strlen($passwd) < 6) || (strlen($passwd) > 16)) {
      //throw new Exception('Your password must be between 6 and 16 characters Please go back and try again.');
    	echo '<p>Warning! Your password must be between 6 and 16 characters. Try again.</p>';
    }
    elseif ((strlen($secretq) < 1) || (strlen($secreta) < 1)) {
    	echo '<p>Warning! Please select a secret Question and Answer. Try again.</p>';
    }
    // attempt to register
    // this function can also throw an exception
    else{
    	register($username, $email, $passwd, $secretq, $secreta);
    	// start session which may be needed later
    	// start it now because it must go before headers
    	session_start();
    	
    	// register session variable
    	$_SESSION['valid_user'] = $username;

    	// provide link to members page
    	do_html_header('Registration successful');
    	echo 'Your registration was successful.  Please login to start using the application.!';
    	do_html_url('login.php', 'Login');
    }
  }//end if button submit
  
  if(empty($_SESSION['valid_user'])) {
  ?>
  
  <form method="post" action="register_new.php">
  <table bgcolor="#cccccc">
  <td>Preferred username <br />(max 16 chars):</td>
  <td valign="top"><input type="text" name="username" size="16" maxlength="16" value="<?=$username?>"/></td></tr>
  <tr>
  <td>Email address:</td>
  <td><input type="text" name="email" size="30" maxlength="25" value="<?=$email?>"/></td></tr>
  <tr>
  <tr>
  		<td>Password <br />(between 6 and 16 chars):</td>
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
		<td colspan=2 align="center">
		<input type="submit" name="btnSubmit" value="Register"></td></tr>
	</table></form>
	
 <?php
  }//end empty(session)
   // end page
   do_html_footer();
  }catch (Exception $e) {
     do_html_header('Problem:');
     echo $e->getMessage();
     do_html_footer();
     exit;
  }
?>
