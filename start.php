<?php
print "start page";
storeMessage(1, "här skriver vi på svenska", 0);
fetchMessages($data = [1,3]);

?>
<div id="loginform">
	<form action="index.php" method="post" name="login">
		<input type="email" 	name="emaillogin">
		<input type="password" 	name="pwdlogin">
		<input type="submit" 	value="Log in"> 
	</form>
	<p><?=$loginResult?></p>
</div>

<div id="regform">
	<form action "index.php" method="post" name="register">
		<input type="email" 	name="emailreg">
		<input type="password" 	name="pwdreg1">
		<input type="password" 	name="pwdreg2">
		<input type="text" 		name="nickname">
		<input type="submit" 	value="register">
	</form>
	<p><?=$regResult?></p>
<div>
