<?php

require '../pages/functions.php';

$regResult 		= "";
$loginResult	= "";
$feedback		= "";

$sessionTimeout = 600;

session_set_cookie_params($sessionTimeout);
session_start();

// IF WE WANT TO STORE FEEDBACK BETWEEN PAGE RELOADS

if (isset($_SESSION["feedback"]))
{
	$feedback = $_SESSION["feedback"];
	$_SESSION["feedback"] = "";
}

// USER IS LOGGING OUT

if (isset($_GET["logOut"]))
{
	session_destroy();
	setcookie(session_name(), "", 1);
	header("location: .");
	exit;
}

//USER IS ATTEMPTING TO LOG IN

if (isset($_POST["emaillogin"]))
{
	$email 	= $_POST["emaillogin"];
	$pwd 	= $_POST["pwdlogin"];

	$user = checkLogin($email, $pwd);

	if ($user)
	{
		$_SESSION["loggedIn"] 	= true;
		$_SESSION["userId"] 	= $user;

		header("location: .");
		exit;
	}
	$loginResult = "Invalid email/password";
}

// USER IS ATTEMPTING TO REGISTER

if (isset($_POST["emailreg"]))
{
	$email 		= $_POST["emailreg"];
	$pwd1 		= $_POST["pwdreg1"];
	$pwd2 		= $_POST["pwdreg2"];
	$nickname 	= $_POST["nickname"];

	$regResult = registerUser($email, $pwd1, $pwd2, $nickname);
}

// USER IS LOGGED IN AND UPLOADING A PROFILE PIC

if (isset($_SESSION["loggedIn"]) && isset($_FILES["upload"]))
{

	$profileNickname = $_GET["profile"];
	$user = getUserId($profileNickname);

	if ($_FILES["upload"]["error"] == 0){

		$tmp = $_FILES["upload"]["tmp_name"];
		$name = $_FILES["upload"]["name"];
		$size = $_FILES["upload"]["size"];
		$image = getimagesize($tmp);

		if ($size > 1048576){
			$_SESSION["feedback"] = "TOO BIG FILE";
		}
		//print_r($image);
		else if (getimagesize($tmp) == false)
		{
			$_SESSION["feedback"] = "you are misstaken sire";
		}
		else{
			move_uploaded_file($tmp, "./pics/{$name}");
			setUserProfilePic($user, $name);
			$_SESSION["feedback"] = "IT IS DONE";
		}

	}
	else{
		$_SESSION["feedback"] = "ERROR";
	}
	header("location: .?profile={$profileNickname}");
	exit;
}

// USER IS LOGGED IN AND ATTEMPTING TO VIEW A PROFILE PAGE

if (isset($_GET["profile"]) && isset($_SESSION["loggedIn"]))
{
	setcookie(session_name(), session_id(), time() + $sessionTimeout);

	print " "; // Prevents header(location) on included pages from redirecting

	require '../pages/header.php';
	require '../pages/top-menu.php';
	require "../pages/profile.php";
	exit;
}

// USER IS LOGGED IN AND ATTEMPTING TO POST A MESSAGE

if (isset($_SESSION["loggedIn"]) && isset($_POST['message']))
{
	$message = $_POST['message'];
	$userId = $_SESSION['userId'];

	if (isset($_POST['reply']))
	{
		$recipient = $_POST['reply'];

		storeMessage($userId, $message, $recipient);
	}

	else
	{
		storeMessage($userId, $message, 0);
	}

	header("location: .");
	exit;
}

// USER IS LOGGED IN

if (isset($_SESSION["loggedIn"]))
{
	setcookie(session_name(), session_id(), time() + $sessionTimeout);
	print " "; // Prevents header(location) on included pages from redirecting

	require '../pages/header.php';
	require '../pages/top-menu.php';
	require '../pages/loggedin.php';
	exit;
}

// USER IS NOT LOGGED IN

//print " "; // Prevents header(location) on included pages from redirecting
require '../pages/header.php';
require '../pages/start.php';

