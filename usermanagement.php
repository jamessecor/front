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
<script>
$(document).ready(function() {
	$("#selectEditUser").change(function() {		
		var user = $("#selectEditUser").val();
		if(user !== "")
			$("#editLink").html("<a href=\"./createnewuser.php?userid=" + user + "\" >Edit User</a>");
		else
			$("#editLink").html("<a href=\"\" >Edit User</a>");
	});
});
</script>
<table align="center">
	<tr>
		<td><a href="./setmemberpassword.php">Set Member Password</a></td>
	
		<td><a href="./createnewuser.php">Create New User</a></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td><select id="selectEditUser" name="username">
			<option value=''>Choose Name</option>
			<?php
			
			// Use query to get artists to populate username drop-down		
			$query = "SELECT personID, CONCAT(firstname, ' ', lastname) AS 'username' FROM people ORDER BY username;";
			$result = mysqli_query($db, $query);
			if(!$result) {
				$errors['username'] = "Error in SQL statement." . mysqli_error($db);
			} else {
				$numrows = mysqli_num_rows($result);
				for($i = 0; $i < $numrows; $i++) {
					$row = mysqli_fetch_assoc($result);
					if($row) {
						$username = $row['username'];
						$userId = $row['personID'];
						echo "<option value='$userId'>$username</option>";
					}
				}
			}
			?>
		</select></td>
		<tr>
			<td id="editLink"><a href="">Edit User</a></td>
		</tr>
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