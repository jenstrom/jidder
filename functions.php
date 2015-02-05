<?php

require './config.php';

function sqlQuery($query)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE); 
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


function fetchMessages($userArray)
{
	if (isset($userArray[0]))
	{
		$query = "SELECT messages.message, messages.posted, users.nickname 
					FROM messages JOIN users ON messages.author = users.id 
					WHERE users.id = {$userArray[0]}";

		if (count($userArray) > 1)
		{
			for ($i = 1; $i < count($userArray); $i++)
			{
				$query = $query . " OR users.id = {$userArray[$i]}";
			}
		}

		$query = $query . " ORDER BY posted DESC";

		$result = sqlQuery($query);
	}


	else
	{
		$result = sqlQuery("SELECT messages.message, messages.posted, users.nickname 
							FROM messages JOIN users 
							ON messages.author = users.id
							ORDER BY posted DESC");
	}

	$messages = [];
    
    while($row = mysqli_fetch_assoc($result))
    {
        $messages[] = $row;
    }

    mysqli_free_result($result);

    printMessages($messages);
}


function printMessages($messageArray){
	
	for ($i = 0; $i < count($messageArray); $i++)
	{
		printf("<div class=\"message\">
					<div class=\"messagehead\">
						<a href=\".?profile=%s\">%s</a>
						%s
					</div>
					<p>
						%s
					</p>
				</div>",
				filter_var($messageArray[$i]['nickname'], FILTER_SANITIZE_SPECIAL_CHARS),
				filter_var($messageArray[$i]['nickname'], FILTER_SANITIZE_SPECIAL_CHARS),
				$messageArray[$i]['posted'],
				filter_var($messageArray[$i]['message'], FILTER_SANITIZE_SPECIAL_CHARS));
	}
}


function checkLogin($email, $pwd)
{
	$result = sqlQuery("SELECT * 
						FROM users 
						WHERE email = \"{$email}\" 
						AND pass = \"{$pwd}\"");

	$row = mysqli_fetch_assoc($result);

	if (isset($row["id"]))
	{
		return $row["id"];
	}

	return false;

}


function registerUser($email, $pwd1, $pwd2, $nickname)
{
	$validEmail 	= checkEmail($email);
	$validPassword 	= checkPassword($pwd1, $pwd2);
	$nicknameTaken 	= existingNickname($nickname);
	$emailTaken 	= existingEmail($email);

	if ((strlen($email) < 1) && (strlen($pwd1) < 1) && (strlen($nickname) < 1))
	{
		return "All fields are required";
	}

	if ($validEmail && $validPassword && !$nicknameTaken && !$emailTaken)
	{
		insertUser($email, $pwd1, $nickname);
		return "Success";
	}
	else
	{
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


function insertUser($email, $pwd, $nickname)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

	$email 		= mysqli_real_escape_string($connection, $email);
	$pwd 		= mysqli_real_escape_string($connection, $pwd);
	$nickname 	= mysqli_real_escape_string($connection, $nickname);

	sqlQuery("INSERT INTO users(email, pass, nickname)
				VALUES (\"{$email}\", \"{$pwd}\", \"{$nickname}\")");

}


function storeMessage($userId, $message, $recipient)
{
	$validMessage = checkMessage($message);

	if ($validMessage && $recipient === 0)
	{
		sqlQuery("INSERT INTO messages (posted, message, author) 
				VALUES (NOW(), \"{$message}\", {$userId})");

		return "Message posted";
	}
	if ($validMessage && $recipient !== 0)
	{
		sqlQuery("INSERT INTO messages (posted, message, author, reply_to) 
				VALUES (NOW(), \"{$message}\", {$userId}, {$recipient})");
		return "Reply posted";
	}
	if (!$validMessage)
	{
		return "Message length must be greater than 0 characters and less than 14 characters";
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






