<?php
session_start();

//
// for debug only 
//
//  echo 'Username: '.$_POST['form-username'];
//  echo 'Password: '.$_POST['form-password'];
//

if (empty($_POST['form-username']) || empty($_POST['form-password'])) 
{
	$error = "Wrong";
}
else
{
	$username = $_POST['form-username'];
	$password = $_POST['form-password'];
		
	if ((strcmp($username, 'dima20!6')==0) && (strcmp($password, 'Legend20!6')==0))
	{
		$error = "Success";
		$_SESSION['auth_login']='Ok';
	}
	else
	if ((strcmp($username, 'Dmitry.Romanoff')==0) && (strcmp($password, 'Legend20!6')==0))
	{
		$error = "Success";
		$_SESSION['auth_login']='Ok';
	}
	else
	{
		$error = "Wrong";
		unset($_SESSION['auth_login']);
	}
}

if (strcmp($error, 'Success')==0)
{
	// success
	header("Location: dashboard.html"); // Redirecting To The Welcome Dashboard Page
}
else
{
	unset($_SESSION['auth_login']);
	// wrong
	if(session_destroy()) // Destroying All Sessions
	{
		header("Location: wrong_login.php"); // Redirecting To The Wrong Login Page
	}
}

?>
