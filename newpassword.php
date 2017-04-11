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
			$errors['passwd1']="Your password doesn't match our current records. Contact administrator.";
		} else {
			$newpasswd_hashed=password_hash($passwd2, PASSWORD_DEFAULT);
			$query2 = "ALTER TABLE users SET password = '$newpasswd_hashed';";
			$result2 = mysqli_query($db, $query2);
			
			if(!$result2)
				$errors['username'] = "Error in setting new password." . mysqli_error($db);
			else {
				if($row) {
					if(password_verify($passwd, $row['password'])) {
						//header('Location: login.php');
						$_SESSION['username'] = $username;					
					} else {
						$errors['passwd'] = "Your login credentials could not be verified. Please check username and re-enter password";
					}
				} else {
					$errors['passwd'] = "Username does not match. Please create an account.";
				}
			}
		}
		$validation=true;
	}
}

if($validation) {
		// Successful Password Set-up
		header('Location: login.php');
		print "<div class='center'>";
		print "<p>Your password has been changed. You may now use that password to log in as $_SESSION[username].";
		print "<br><a href='./logout.php'>Click here to log out.</a></p>";
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
				//$query = "SELECT CONCAT(firstname, ' ', lastname) FROM artists ORDER BY firstname;";
				$query = "SELECT username FROM users ORDER BY username;";
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