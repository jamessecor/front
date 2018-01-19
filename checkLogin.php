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


function bookkeeperIsUser() {
	$bk = 'James Secor';
	if(isLoggedIn() && $_SESSION['username'] == $bk)
		return TRUE;
	else
		return FALSE;
}


function labelCreatorIsUser() {
	$labelCreators = ['James Secor', 'Deluxe Unlimited', 'Glen Coburn Hutcheson'];
	$labelCreator = FALSE;
	if(isLoggedIn()) {
		foreach($labelCreators as $lc) {
			if($_SESSION['username'] == $lc)
				return TRUE;
		}
	}
	return $labelCreator;
}

?>