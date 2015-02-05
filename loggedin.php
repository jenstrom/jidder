<?php
print "logged in";
print_r($_SESSION);
?>
<form action="index.php" method="post">
	<input type="submit" name="logOut">
</form>