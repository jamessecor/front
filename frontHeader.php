<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>The Front</title>
	<link href="frontStyle.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
</head>
<body>
<div id="wrapper">

<header id="top">The Front</header>

<?php
session_start();
include "checkLogin.php";
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";

if(isLoggedIn()) {
	print "<h1 id='membername'>[ Logged in as $_SESSION[username] ]</h1>";
} 
?>
<script>
// This scrolls the navbar to top and removes 'The Front" heading
$(window).scroll(
{
	previousTop:0
},
function() {
	var currentTop = $(window).scrollTop();
	if(currentTop <= this.previousTop) {
		$("ul").css("padding", "3.4em 0 0 0");
		$("#top").show();
	} else {
		$("ul").css("padding", "0 0 0 0");
		$("#top").hide();
	}
	// this.previousTop = currentTop;
});

$(".more").on("click", function() {
	$("").show("slow");
});
</script>
<nav class="navbar">
	<ul>
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
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/setmemberpassword.php')) echo "class='active'";?> href="./setmemberpassword.php">Set Member Password</a>
				</li>
			<?php } 
			// Only Label Creators see this link
			if(labelCreatorIsUser()) { ?>
				<li>
					<a <?php if(strpos($_SERVER['REQUEST_URI'], '/createLabels.php')) echo "class='active'";?> href="./createLabels.php">Labels</a>
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
			<li>
				<a <?php if(strpos($_SERVER['REQUEST_URI'], '/committees.php')) echo "class='active'";?> href="./committees.php">Committees</a>
			</li>
			
			
			<?php
			} ?>
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
</nav>
</div>