<?php
// newpassword.php
// author James Secor
include "frontHeader.php";

$username='';
$currentpasswd='';
$newpasswd1='';
$newpasswd2='';
$errors=array();
$validation=false;

print "<div id='right_col'>";
print "<div class='headings'>Create New Password</div>";
print "<div class='center'>";
if(isLoggedIn()) {
	// Verify inputs
	if(isset($_POST['changepasswd'])) {
		// Username
		if(adminIsUser()) {		
			if(!empty($_POST['username'])) {
				$username=addslashes($_POST['username']);
			} else {
				$errors['username']="This field is required.";
			}
		} else {
			$username = $_SESSION['username'];
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
			if(strcmp($newpasswd1, $newpasswd2) != 0) {
					$errors['newpasswd2'] = "Passwords do not match.";
			}
		} else {
			$errors['newpasswd2']="This field is required.";
		}
		
		// =============================================================	
		// NO ERRORS
		// =============================================================
		if(count($errors)==0) {		
			// TODO: Change query to work with correct database
			
			// Check current password before proceding
			// Select password hash from db, check, then change password in db
			$query1="SELECT passwdHash FROM people WHERE CONCAT(firstname, ' ', lastname) = '$username';";
			$result1 = mysqli_query($db, $query1);
			if(!$result1) {
				$errors['passwd1']="Error in SQL Statement";
			} else {
				// Check current password
				$row = mysqli_fetch_assoc($result1);
				if($row) {
					if(!password_verify($currentpasswd, $row['passwdHash'])) {
						$errors['currentpasswd'] = "Your Current Password Does Not Match Our Records.";
					} else {
						$newpasswd_hashed=password_hash($newpasswd2, PASSWORD_DEFAULT);
						$query2 = "UPDATE people SET passwdHash = '$newpasswd_hashed' WHERE CONCAT(firstname,' ',lastname) = '$username';";
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
	
	// Print message on successful entry / form if errors
	if($validation==true) {
			// Successful Password Set-up
			print "<p>Your password has been changed.<br>You must now use that password to log in as $username.</p>";	
	} else {
	
	?>
	
	<form method="post" action="">
		<table>
			<tr>
				<td>Name</td>
				<?php 
				if(adminIsUser()) { ?>
					<td><select name="username">
					<option value=''>Choose Name</option>
					<?php
					$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'username' FROM people ORDER BY username;";
					$result = mysqli_query($db, $query);
					if(!$result) {
						$errors['username'] = "Error in SQL statement." . mysqli_error($db);
					} else {
						$numrows = mysqli_num_rows($result);
						for($i = 0; $i < $numrows; $i++) {
							$row = mysqli_fetch_assoc($result);
							if($row) {
								$username = $row['username'];
								if($_POST['username']==$username)
									echo "<option value='$username' selected='selected'>$username</option>";
								else
									echo "<option value='$username'>$username</option>";
							}
						}
					}
					?>
				</select></td>
				<td><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : ''; ?></small></td>
				<?php
				} else {
					print "<td>$_SESSION[username]</td>";
				}?>
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
} else {
?>
<table>
	<tr>
		<td><a href="./login.php?page=newpassword">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
include "frontFooter.php";
?>