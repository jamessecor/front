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
<img id='logo' src="./logo.gif" alt="">
	<ul>
		<?php 
		session_start();
		include "checkLogin.php";
		if(isLoggedIn()) { 
		?>
		
		<li>
			<a <?php if(strpos($_SERVER['PHP_SELF'], '/memberinfo.php')) echo "class='active'";?> href="./memberinfo.php">Member Info</a>
		</li>
		<li>
			<a <?php if(strpos($_SERVER['PHP_SELF'], '/artwork.php')) echo "class='active'";?> href="./artwork.php">Artwork</a>
		</li>
		<li>
			<a <?php if(strpos($_SERVER['PHP_SELF'], '/committees.php')) echo "class='active'";?> href="./committees.php">Committees</a>
		</li>
		<li>
			<a <?php if(strpos($_SERVER['PHP_SELF'], '/contacts.php')) echo "class='active'";?> href="./contacts.php">Contacts</a>
		</li>
		<?php // Only admin sees this link
			if(adminIsUser()) {
				?>
				<li>
					<a <?php if(strpos($_SERVER['PHP_SELF'], '/register.php')) echo "class='active'";?> href="./register.php">Set Member Password</a>
				</li>
				<?php
			} ?>		
		<li>
			<a <?php if(strpos($_SERVER['PHP_SELF'], '/logout.php')) echo "class='active'";?> href="./logout.php">Log Out</a>
		</li>
		
		<?php
		} else { 
		?>
		
		<li>
			<a <?php if(strpos($_SERVER['PHP_SELF'], '/login.php')) echo "class='active'";?> href="./login.php">Log In</a>
		</li>
		
		<?php } ?>
		
	</ul>