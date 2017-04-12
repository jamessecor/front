<?php
include "frontHeader.php";

// Database Set-up
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";

$username='';
$currentpasswd='';
$newpasswd1='';
$newpasswd2='';
$errors=array();
$validation=false;

print "<div id='right_col'>";
print "<div class='headings'>Create New Password</div>";

// Verify inputs
if(isset($_POST['changepasswd'])) {
	// Username
	if(!empty($_POST['username'])) {
		$username=addslashes($_POST['username']);
	} else {
		$errors['username']="This field is required.";
	}
	
	// Current Password
	if(!empty($_POST['currentpasswd'])) {
		$currentpasswd=addslashes($_POST['currentpasswd']);	
		if(strlen(trim($currentpasswd))==0)
			$errors['currentpasswd']="Password cannot be blank";
	} else {
		$errors['currentpasswd']="This field is required.";
	}	
	
	// New Password
	if(!empty($_POST['newpasswd1'])) {
		$newpasswd1=addslashes($_POST['newpasswd1']);	
		if(strlen(trim($newpasswd1))==0)
			$errors['newpasswd1']="Password cannot be blank";
		$pattern = "/^[a-zA-Z0-9*&^%$#@!]{4,25}$/";
		if(!preg_match($pattern, $newpasswd1))
			$errors['newpasswd1'] = "Passwords must be 4-25 characters.";
	} else {
		$errors['newpasswd1']="This field is required.";
	}	
	
	// Re-enter New Password
	if(!empty($_POST['newpasswd2'])) {
		$newpasswd2=addslashes($_POST['newpasswd2']);	
		if(strlen(trim($newpasswd2))==0)
			$errors['newpasswd2']="Password cannot be blank";
		
		// Check if two new passwords match
		if(strcmp($newpasswd1, $newpasswd2)) {
				$errors['newpasswd2'] = "Passwords do not match.";
		}
	} else {
		$errors['newpasswd2']="This field is required.";
	}


	// =============================================================	
	// NO ERRORS
	// =============================================================
	if(count($errors)==0) {
		// Database setup
		require("../includes/frontConfig.php");
		require("../includes/frontConnect.php");
		
		// TODO: Change query to work with correct database
		
		// Check current password before proceding
		// Select password hash from db, check, then change password in db
		$query1="SELECT password FROM users WHERE username = '$username';";
		$result1 = mysqli_query($db, $query1);
		if(!$result1) {
			$errors['passwd1']="Error in SQL Statement";
		} else {
			// Check current password
			$row = mysqli_fetch_assoc($result1);
			if($row) {
				if(!password_verify($currentpasswd, $row['password'])) {
					$errors['currentpasswd'] = "Your Current Password Does Not Match Our Records.";
				} else {
					$newpasswd_hashed=password_hash($newpasswd2, PASSWORD_DEFAULT);
					$query2 = "UPDATE people SET passwordHash = '$newpasswd_hashed' WHERE CONCAT(firstname,' ',lastname) = '$username';";
					$result2 = mysqli_query($db, $query2);
					
					if(!$result2) {
						$errors['username'] = "Error in setting new password." . mysqli_error($db);
					}
				}
			}
			
		}
		if(count($errors)==0)
			$validation=true;
	}
}

if($validation) {
		// Successful Password Set-up
		print "<div class='center'>";
		print "<p>Your password has been changed.<br>You may now use that password to log in as $username.</p>";
		print "<p><a href='./login.php'>Click here to log in.</a></p>";
		print "</div>";

} else {

?>

<form class="center" method="post" action="">
	<table>
		<tr>
			<td>Username</td>
			<td><select name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : '';  ?>">
				<option value=''>Choose Name</option>
				<?php
				
				// Get artists to populate username drop-down
				
				// TODO: Select members from correct db as below
				$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'username' FROM people ORDER BY firstname;";
				//$query = "SELECT  FROM users ORDER BY username;";
				$result = mysqli_query($db, $query);
				if(!$result) {
					$errors['username'] = "Error in SQL statement." . mysqli_error($db);
				} else {
					$numrows = mysqli_num_rows($result);
					for($i = 0; $i < $numrows; $i++) {
						$row = mysqli_fetch_assoc($result);
						if($row) {
							$username = $row['username'];
							if($_POST['name']==$username)
								echo "<option value='$username' selected ='selected'>$username</option>";
							else
								echo "<option value='$username'>$username</option>";
						}
					}
				}
				?>
			</select></td>
			<td><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>Current Password</td>
			<td><input type="password" name="currentpasswd"></td>
			<td><small class='errorText'><?php echo array_key_exists('currentpasswd',$errors) ? $errors['currentpasswd'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>New Password</td>
			<td><input type="password" name="newpasswd1"></td>
			<td><small class='errorText'><?php echo array_key_exists('newpasswd1',$errors) ? $errors['newpasswd1'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>Re-enter New Password</td>
			<td><input type="password" name="newpasswd2"></td>
			<td><small class='errorText'><?php echo array_key_exists('newpasswd2',$errors) ? $errors['newpasswd2'] : ''; ?></small></td>
		</tr>
		<tr>
			<td></td><td><input type="submit" name="changepasswd" value="Change Password"></td>
		</tr>
	</table>
</form>

</div>

<?php
}
mysqli_close($db);
include "frontFooter.php";
?>