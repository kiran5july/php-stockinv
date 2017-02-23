<?php

// include function files for this application
require_once('includes/all_includes.php');
session_start();

//create short variable names
$username = trim($_POST['username']);
$passwd = $_POST['passwd'];

if ($username && $passwd) {
// they have just tried logging in
  try  {
    login($username, $passwd);
    // if they are in the database register the user id
    $_SESSION['valid_user'] = $username;
    
    header('Location: main.html');
    die();
  }
  catch(Exception $e)  {
    // unsuccessful login
    do_html_header('Problem:');
    echo 'Login failed.
          You must be logged in access this page.';
    do_html_url('login.php', 'Login');
    do_html_footer();
    exit;
  }
}

/*
do_html_header('Home');
check_valid_user();
// get the bookmarks this user has saved
if ($url_array = get_user_urls($_SESSION['valid_user'])) {
  display_user_urls($url_array);
}

// give menu of options
display_user_menu();

do_html_footer();
*/
?>
