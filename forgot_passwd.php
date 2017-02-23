<?php
  require_once("includes/all_includes.php");
  do_html_header("Resetting password");

  // creating short variable name
  $username = $_POST['username'];
  $secretq = $_POST['secretq'];
  $secreta = $_POST['secreta'];
  //echo $username.$secretq.$secreta.empty($username).empty($secretq).empty($secreta);
  
  try {
  	if(empty($username) || empty($secretq) || empty($secreta))
  		echo "Please fill in all required fields before submit. <a href=\"forgot_form.php\">Resubmit</a>";
  	else{
    	$password = reset_password($username, $secretq, $secreta);
    	
    	switch($password)
    	{
    		case "1":
    				echo "No user record for the input. Please verify input & resubmit. <a href=\"forgot_form.php\">Resubmit</a>";
    				break;
    		case "2":
    				echo "Password update failed. Please try again.";
    				break;
    		case "3":
    				echo "Password generation failed. Please try again.";
    				break;
    		default:
    			//notify_password($username, $password);
    			echo "Your new password is: <i>".$password."<i>.<br/>";
    			//echo "It is also emailed to the email account you provided. ";
    			echo "You will be able to change your password after you log back in. <br/>";
    			//echo "<a href=\"login.php\">Login</a>";
    			break;
    	}//end switch

  	}//end else
  }catch (Exception $e) {
    echo 'Your password could not be reset - please try again later.';
  }
  do_html_url('login.php', 'Login');
  do_html_footer();
?>
