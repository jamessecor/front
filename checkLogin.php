<?php

function isLoggedIn() {
	if(isset($_SESSION['username']))
		return TRUE;
	else
		return FALSE;
}

function adminIsUser() {
	$admin = 'James Secor';
	if(isLoggedIn() && $_SESSION['username'] == $admin)
		return TRUE;
	else
		return FALSE;
}

?>