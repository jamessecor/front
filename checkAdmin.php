<?php

function adminIsUser() {
	$admin = 'James Secor';
	if($_SESSION['username'] == $admin)
		return TRUE;
	else
		return FALSE;
}

?>