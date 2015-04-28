<?php

require 'config.php';

function sqlQuery($query)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    mysqli_set_charset($connection, 'utf8');
    $result     = mysqli_query($connection, $query); 
    
    if ($result === false)
    {
  	  	printf("Error occured: %s\n", mysqli_error($connection));
        mysqli_close($connection);
        exit;
    }

    mysqli_close($connection);
    
    return $result;
}

function sqlResultToArray($result)
{
	$resultArray = [];

	while ($row = mysqli_fetch_assoc($result))
	{
		$resultArray[] = $row;
	}

	mysqli_free_result($result);

	return $resultArray;
}






//LOGIN

function checkLogin($email, $inputPass)
{
	$result = sqlQuery("SELECT * 
						FROM users 
						WHERE email = \"{$email}\"");

	$user = sqlResultToArray($result);

	

	if (isset($user[0]['id']))
	{
		$storedPass = $user[0]['pass'];
		
		$correctPassword = unhashPassword($inputPass, $storedPass);

		if ($correctPassword)
		{
			return $user[0]["id"];
		}
	}

	return false;

}

function unhashPassword($inputPass, $storedPass)
{
	if ( $storedPass == crypt($inputPass, $storedPass) ) {
		return true;
	}
	return false;
}

function registerUser($email, $pwd1, $pwd2, $nickname)
{
	$validNickname	= checkNickname($nickname);
	$validEmail 	= checkEmail($email);
	$validPassword 	= checkPassword($pwd1, $pwd2);
	$nicknameTaken 	= existingNickname($nickname);
	$emailTaken 	= existingEmail($email);

	if ((strlen($email) < 1) && (strlen($pwd1) < 1) && (strlen($nickname) < 1))
	{
		return "All fields are required";
	}

	if ($validNickname && $validEmail && $validPassword && !$nicknameTaken && !$emailTaken)
	{
		$password = hashPassword($pwd1);
		//printf( $password);
		insertUser($email, $password, $nickname);
		//die;
		return "Success";
	}
	else
	{
		if (!$validNickname)
		{
			return "Nickname must be longer than 3 characters and may only contain a-z 0-9 .-_";
		}
		if (!$validEmail)
		{
			return "Invalid Email";
		}
		if (!$validPassword)
		{
			return "Passwords do not match";
		}
		if ($nicknameTaken)
		{
			return "Existing nickname";
		}
		if ($emailTaken)
		{
			return "Email already in use";
		}
	}
}

function checkNickname($nickname)
{
	if (preg_match('/[^A-Za-z0-9-._]/', $nickname) || strlen($nickname) < 4)
	{
		return false;
	}
	return true;
}

function checkEmail($email)
{
	if (filter_var($email, FILTER_VALIDATE_EMAIL) === $email)
	{
		return true;
	}

	return false;
}


function checkPassword($pwd1, $pwd2)
{
	if ($pwd1 === $pwd2)
	{
		return true;
	}

	return false;
}


function existingEmail($email)
{
	$result = sqlQuery("SELECT email 
						FROM users 
						WHERE email = \"{$email}\"");

	$row = mysqli_fetch_assoc($result);

	if (isset($row["email"]))
	{
		return true;
	}

	return false;
}


function existingNickname($nickname)
{
	$result = sqlQuery("SELECT nickname 
						FROM users 
						WHERE nickname = \"{$nickname}\"");

	$row = mysqli_fetch_assoc($result);

	if (isset($row["nickname"]))
	{
		return true;
	}

	return false;
}

function hashPassword($password)
{
	$cost = 10;

	$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');

	$salt = sprintf("$2a$%02d$", $cost) . $salt;

	$hash = crypt($password, $salt);

	return $hash;
}

function insertUser($email, $pwd, $nickname)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);


	$email 		= mysqli_real_escape_string($connection, $email);
	$pwd 		= mysqli_real_escape_string($connection, $pwd);
	$nickname 	= mysqli_real_escape_string($connection, $nickname);

	sqlQuery("INSERT INTO users(email, pass, nickname)
				VALUES (\"{$email}\", \"{$pwd}\", \"{$nickname}\")");

}


//MESSAGES

function getNumberOfMessages($userArray = 0, $includeReplies = false)
{
	if (isset($userArray[0]))
	{
		$query = "SELECT COUNT(*)
					FROM messages
					WHERE author = {$userArray[0]}";

		if (count($userArray) > 1)
		{
			for ($i = 1; $i < count($userArray); $i++)
			{
				$query = $query . " OR author = {$userArray[$i]}";
			}
		}

		if (!$includeReplies)
		{
			$query = $query . "  AND messages.reply_to = 0";
		}

		$result = sqlQuery($query);
	}


	else
	{
		$query = "SELECT COUNT(*)
					FROM messages";

		if (!$includeReplies)
		{
			$query = $query . " WHERE messages.reply_to = 0";
		}

		$result = sqlQuery($query);
	}

	$messageCount = mysqli_fetch_assoc($result);

    mysqli_free_result($result);

    return $messageCount["COUNT(*)"];
}

function pagination($baseURL, $userArray = 0, $includeReplies = false)
{
	$totalMessages = getNumberOfMessages($userArray, $includeReplies);
	$pages = ceil($totalMessages/10);

	print "<ul class='list-inline text-center'>";

	for ($i = 1; $i <= $pages; $i++)
	{
		print "<li><a href=\"{$baseURL}&page={$i}\">[{$i}]</a></li>";
	}

	print "</ul>";
}


function fetchMessages($pageNumber, $userArray = 0, $includeReplies = false)
{

	$start = ($pageNumber-1) * 10;
	if ($start < 0)
	{
		$start = 0;
	}


	if (isset($userArray[0]))
	{
		$query = "SELECT messages.message, messages.posted, messages.id, messages.reply_to, users.nickname
					FROM messages JOIN users 
					ON messages.author = users.id
					WHERE users.id = {$userArray[0]}";

		if (count($userArray) > 1)
		{
			for ($i = 1; $i < count($userArray); $i++)
			{
				$query = $query . " OR users.id = {$userArray[$i]}";
			}
		}

		if (!$includeReplies)
		{
			$query = $query . " AND reply_to = 0";
		}

		$query = $query . "  ORDER BY posted DESC LIMIT {$start}, 10";

		$result = sqlQuery($query);
	}


	else
	{
		$query = "SELECT messages.message, messages.posted, messages.id, messages.reply_to, users.nickname
							FROM messages JOIN users 
							ON messages.author = users.id";


		if (!$includeReplies)
		{
			$query = $query . " WHERE reply_to = 0";
		}

		$query = $query . " ORDER BY posted DESC LIMIT {$start}, 10";

		$result = sqlQuery($query);
	}

	$messages = sqlResultToArray($result);

    return $messages;
}


function fetchReplies($messageId){

	$query = "SELECT messages.message, messages.posted, users.nickname, messages.id
					FROM messages JOIN users ON messages.author = users.id 
					WHERE messages.reply_to = {$messageId}";

	$result = sqlQuery($query);

	$replies = sqlResultToArray($result);

    if (count($replies) > 0)
    {
        return $replies;
    }
    return [];
}


function printMessages($pageNumber, $userArray = 0, $includeReplies = false){

	$messageArray 	= fetchMessages($pageNumber, $userArray, $includeReplies);
	
	for ($i = 0; $i < count($messageArray); $i++)
	{
		

		$messageId		= $messageArray[$i]['id'];
		$replyHtml		= "";

		include "single-message.php";	
		
	}
}

function storeMessage($userId, $message, $recipient)
{
	$validMessage = checkMessage($message);

	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

	$escapedMessage = mysqli_real_escape_string($connection, $message);

	if ($validMessage)
	{
		sqlQuery("INSERT INTO messages (posted, message, author, reply_to) 
				VALUES (NOW(), \"{$escapedMessage}\", {$userId}, {$recipient})");

		return "Message posted";
	}
	if (!$validMessage)
	{
		return "Message length must be greater than 0 characters and less than 140 characters";
	}
}


function checkMessage($message)
{
	if (mb_strlen($message, 'UTF-8') > 0 && mb_strlen($message, 'UTF-8') <= 140)
	{
		return true;
	}

	return false;
}


//PROFILE


function getNickname($userId)
{
	$result = sqlQuery("SELECT nickname 
							FROM users 
							WHERE users.id = {$userId}");

	$row = mysqli_fetch_assoc($result);

    return $row['nickname'];
}

function getUserId($nickname)
{
	$result = sqlQuery("SELECT id 
							FROM users 
							WHERE users.nickname = \"{$nickname}\"");

	$row = mysqli_fetch_assoc($result);

    return $row['id'];
}

function setUserProfilePic($user, $name)
{
	$query = "UPDATE users 
				SET picture = \"{$name}\"
				WHERE users.id = {$user}";

	sqlQuery($query);

}

function getUserProfilePic($user)
{
	$query = "SELECT picture 
				FROM users
				WHERE users.id = {$user}";

	$result = sqlQuery($query);

    $picName = mysqli_fetch_assoc($result);

    if (isset($picName['picture']))
    {
    	return $picName['picture'];
    }

    return "default.jpg";
}


