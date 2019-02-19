<!doctype html>
<!-- frontHeader.php -->
<!-- author James Secor -->
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>The Front</title>
	<link href="frontStyle.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<?php
session_start();
include "checkLogin.php";
require "../../includes/frontConfig.php";
require "../../includes/frontConnect.php";
?>
<nav class="navbar navbar-default">
	<div class="container-fluid">
			<div class="navbar-header">
				<button type = "button" id="tbut" class = "navbar-toggle" 
			         data-toggle = "collapse" data-target = "#nav-front">
			         <span class = "sr-only">Toggle navigation</span>
			         <span class = "icon-bar"></span>
			         <span class = "icon-bar"></span>
			         <span class = "icon-bar"></span>
			      </button>
			  <a class="navbar-brand" href="./artwork.php">The Front</a>
			</div>

		<div id="nav-front" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">		
		<?php 
		if(isLoggedIn()) { 
		?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/artwork.php')) echo "class='active'";?> href="./artwork.php">Artwork</a>
				</li>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/memberimages.php')) echo "class='active'";?> href="./memberimages.php">Images</a>
				</li>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/contacts.php')) echo "class='active'";?> href="./contacts.php">Member Contacts</a>
				</li>
		<?php // Only admin sees this link
			if(adminIsUser()) {
				?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/usermanagement.php') || 
							strpos($_SERVER['REQUEST_URI'], '/setmemberpassword.php') ||
							strpos($_SERVER['REQUEST_URI'], '/createnewuser.php')) echo "class='active'";?> href="./usermanagement.php">User Management</a>
				</li>
			<?php } 
			// Only Label Creators see this link
			if(labelCreatorIsUser()) { ?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/labels.php')) echo "class='active'";?> href="./labels.php">Labels</a>
				</li>
			<?php } 
			// Only bookkeeper see this link
			if(bookkeeperIsUser()) { ?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/dues.php')) echo "class='active'";?> href="./dues.php">Dues</a>
				</li>
			<?php } 
			// Only non-admin users see this link
			if(!adminIsUser()) {?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/newpassword.php')) echo "class='active'";?> href="./newpassword.php">Change Password</a>
				</li>
			<?php
			}
			if(adminIsUser()) {
				// These are not part of beta
				?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/memberinfo.php')) echo "class='active'";?> href="./memberinfo.php">Info</a>
				</li>			
			<?php
			} ?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/logout.php')) echo "class='active'";?> href="./logout.php">Log Out</a>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="navbar-text"><?php echo "user: $_SESSION[username]";?></li>
			</ul>
		<?php
		} else { 
		?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/login.php')) echo "class='active'";?> href="./login.php">Log In</a>
				</li>
			</ul>
		<?php } ?>
		</div>
	</div>
</nav>