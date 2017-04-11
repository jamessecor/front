<?php

include "frontHeader.php";

print "<div id='right_col'>";

if(isset($_SESSION['username'])) {
	unset($_SESSION['username']);
	header('Location: logout.php');
}

print "<div class='headings'>Log Out Successful</div>";
print "<div class='center'>";
print "<p>You are currently logged out. 
		<br>Please use this link to <a href='login.php'>Login</a></p>";
print "</div></div>";

include "frontFooter.php";

?>