<?php
// usermanagement.php
// author James Secor
// January 2018
include "frontHeader.php";
?>

<div id="right_col">
<div class='headings'>User Management</div>
<div class="center">
<?php
if(adminIsUser()) {
?>
<table>
	<tr>
		<td><a href="./setmemberpassword.php">Set Member Password</td>
		<td><a href="./createnewuser.php">Create New User</td>
	</tr>
</table>
<?php
} else {
?>
<table>
	<tr>
		<td><a href="./login.php?page=usermanagement">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
?>
</div></div>
<?php

include "frontFooter.php";
?>