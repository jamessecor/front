<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>The Front</title>
	<link href="frontStyle.css" rel="stylesheet">
</head>
<body>
<div id="wrapper">

<header id="top">The Front</header>

<?php
session_start();
include "checkLogin.php";
if(isLoggedIn()) {
	print "<h1 id='membername'>[ Logged in as $_SESSION[username] ]</h1>";
}
?>

<img id='logo' src="./logo.gif" alt="">
<div id='nav'>
	<ul>
		<?php 
		if(isLoggedIn()) { 
		?>
		
		<li>
			<a <?php if(strpos($_SERVER['REQUEST_URI'], '/memberinfo.php')) echo "class='active'";?> href="./memberinfo.php">Member Info</a>
		</li>
		<li>
			<a <?php if(strpos($_SERVER['REQUEST_URI'], '/artwork.php')) echo "class='active'";?> href="./artwork.php">Artwork</a>
		</li>
		<!--  REMOVE COMMITTEES ???
		<li>
			<a <?php // if(strpos($_SERVER['REQUEST_URI'], '/committees.php')) echo "class='active'";?> href="./committees.php">Committees</a>
		</li>
		-->
		<li>
			<a <?php if(strpos($_SERVER['REQUEST_URI'], '/contacts.php')) echo "class='active'";?> href="./contacts.php">Member Contacts</a>
		</li>
		<?php // Only admin sees this link
			if(adminIsUser()) {
				?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/createLabels.php')) echo "class='active'";?> href="./createLabels.php">Labels</a>
				</li>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/register.php')) echo "class='active'";?> href="./register.php">Set Member Password</a>
				</li>
				<?php
			} ?>	
		<li>
			<a <?php if(strpos($_SERVER['REQUEST_URI'], '/newpassword.php')) echo "class='active'";?> href="./newpassword.php">Change Password</a>
		</li>
		<li>
			<a <?php if(strpos($_SERVER['REQUEST_URI'], '/logout.php')) echo "class='active'";?> href="./logout.php">Log Out</a>
		</li>
		
		<?php
		} else { 
		?>
		
		<li>
			<a <?php if(strpos($_SERVER['REQUEST_URI'], '/login.php')) echo "class='active'";?> href="./login.php">Log In</a>
		</li>
		
		<?php } ?>
		
	</ul>
</div>