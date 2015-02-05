<?php

require 'functions.php';

$regResult 		= "";
$loginResult	= "";
$sessionTimeout = 10;

session_set_cookie_params($sessionTimeout);
session_start();

if (isset($_POST["logOut"]))
{
	session_destroy();
	setcookie(session_name(), "", 1);
	header("location: .");
	exit;
}


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


if (isset($_POST["emailreg"]))
{
	$email 		= $_POST["emailreg"];
	$pwd1 		= $_POST["pwdreg1"];
	$pwd2 		= $_POST["pwdreg2"];
	$nickname 	= $_POST["nickname"];

	$regResult = registerUser($email, $pwd1, $pwd2, $nickname);
}

if (isset($_GET["profile"]))
{
	setcookie(session_name(), session_id(), time() + $sessionTimeout);
	require "profile.php";
	exit;
}


/*if (isset($_GET["profile"]) && isset($_SESSION["loggedIn"]))
{
	setcookie(session_name(), session_id(), time() + $sessionTimeout);
	require "profile.php?profile={$_GET["profile"]}";
	exit;
}*/


if (isset($_SESSION["loggedIn"]))
{
	setcookie(session_name(), session_id(), time() + $sessionTimeout);
	require 'loggedin.php';
	exit;
}

require 'start.php';







/*$array = [];

print_r(fetchMessages($array));

//storeMessage("hej alla", 3, 0);

$data = [];
$result = sqlQuery("select * frm messages");
    
    while($row = mysqli_fetch_array($result))
    {
        $data[] = $row;
    }
	mysqli_free_result($result);


print_r($data);*/
/*
if (isset($_POST["loggedin"]))
{
}

?>

<form action='.' method="POST">
	<input type="text" name="textbox">
	<input type="submit">
</form>*/