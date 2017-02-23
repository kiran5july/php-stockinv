<?php

require_once('db_fns.php');

function register($username, $email, $password, $secretq, $secreta) {
// register new person with db
// return true or error message

  // check if username is unique
  $result = dbquery("select * from T_USER where username='".$username."'");
  if (!$result) {
    throw new Exception('Could not execute query');
  }

  if ($result->num_rows>0) {
    throw new Exception('That username is taken - go back and choose another one.');
  }

  // if ok, put in db
  $result = dbquery("insert into T_USER (USERNAME, PASSWD, EMAIL, SECRETQ, SECRETA) values
                         ('".$username."', sha1('".$password."'), '".$email."','".$secretq."','".$secreta."')");
  if (!$result) {
    throw new Exception('Could not register you in database - please try again later.');
  }

  return true;
}

function login($username, $password) {
// check username and password with db
// if yes, return true
// else throw exception

  // check if username is unique
  $result = dbquery("select * from T_USER
                         where username='".$username."'
                         and passwd = sha1('".$password."')");
  if (!$result) {
     throw new Exception('Could not log you in.');
  }

  if ($result->num_rows > 0) {
     return true;
  } else {
     throw new Exception('Could not log you in.');
  }
}

function check_valid_user() {
// see if somebody is logged in and notify them if not
  if (isset($_SESSION['valid_user']))  {
      echo "Logged in as ".$_SESSION['valid_user'].".<br />";
  } else {
     // they are not logged in
     do_html_heading('Problem:');
     echo 'You are not logged in.<br />';
     do_html_url('login.php', 'Login');
     do_html_footer();
     exit;
  }
}

function change_password($username, $old_password, $new_password) {
// change password for username/old_password to new_password
// return true or false

  // if the old password is right
  // change their password to new_password and return true
  // else throw an exception
  login($username, $old_password);

  $result = dbquery("update T_USER
                          set passwd = sha1('".$new_password."')
                          where username = '".$username."'");
  if (!$result) {
    throw new Exception('Password could not be changed.');
  } else {
    return true;  // changed successfully
  }
}

function get_random_word() {
// grab a random word and return it

   // generate a random word
    $strTempString = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
    $strPassword = "";
    for ($i = 0; $i < 8; $i++) {
    	$intPos = rand(0, 58);
    	$strTempChar = substr($strTempString, $intPos, 1);
  		$strPassword = $strPassword.$strTempChar;
  	}
  //$strPassword2 = md5($strPassword);

  return $strPassword;
}

function reset_password($username, $secretq, $secreta) {
	
	//Check if user record exists for input
	$str = "select username from T_USER where username = '".$username."' and secretq = '".$secretq."' and secreta = '".$secreta."'";
	$result = dbquery($str);
	$row=$result->fetch_row();
	$uname=$row[0];

	if (empty($uname)) {
		return "1";
	}else{
		$result->close();
		// Generate a random string
  		$new_password = get_random_word();

  		if($new_password == false) {
    		return "3";
  		}

		// add a number  between 0 and 999 to it to make it a slightly better password
  		$rand_number = rand(0, 999);
  		$new_password .= $rand_number;
  		
		// set user's password to this in database or return false
  		$result = dbquery("update T_USER
                          set PASSWD = sha1('".$new_password."')
                          where username = '".$username."'");
  		if (!$result) {
    		return "2";
  		} else {
    		return $new_password;  // changed successfully
  		}
	}
	
}

function notify_password($username, $password) {
// notify the user that their password has been changed

    $result = dbquery("select email from T_USER where USERNAME='".$username."'");
    
    if (!$result) {
      throw new Exception('Could not find email address.');
    } else if ($result->num_rows == 0) {
      throw new Exception('Could not find email address.');
      // username not in db
    } else {
      $row = $result->fetch_object();
      $email = $row->email;
      $from = "From: support@phpbookmark \r\n";
      $mesg = "Your password has been changed to ".$password."\r\n"
              ."Please change it next time you log in.\r\n";

      if (mail($email, 'Application login information', $mesg, $from)) {
        return true;
      } else {
        throw new Exception('Could not send email.');
      }
    }
}

?>
