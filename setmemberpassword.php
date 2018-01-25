<?php
// James Secor
// setmemberpassword.php

include "frontHeader.php";
?>
<div id='right_col'>
	<div class='headings'>Set Member Password</div>
		<div class='center'>
<?php
if(adminIsUser()) {
	
	$username='';

	$validInputs = false; 
	$errors = array();

	if (isset ($_POST['submit']))
	{
		// Name
		if (!empty($_POST['username'])) {
			$username=trim($_POST['username']);
			if(strlen($username) == 0) {
				$errors['username']="Name cannot be blank.";
			}
		} else {
			$errors['username']="This field is required.";
		}
		
		// Password 1
		if (!empty($_POST['password1'])) {
			$password1=$_POST['password1'];
			
			if(!preg_match("/^[a-zA-Z0-9-*&^%$#@!]{4,25}$/", $password1)) {
				$errors['password1']="Passwords are length 4-25";
			}
		} else {
			$errors['password1']="Password cannot be empty.";
		}		
		
		// Password 2
		if (!empty($_POST['password2'])) {
			$password2 = $_POST['password2'];
			
			if($password2 !== $password1) {
				$errors['password2']="Passwords do not match";
			}
		} else {
			$errors['password2']="Password cannot be empty.";
		}		
	   
		if (count($errors) > 0) {
			$validInputs = false;
		} else { 
			// HASH the password
			$newpasswd_hashed = password_hash($password1, PASSWORD_DEFAULT);
			
			// Create Query
			$query = "UPDATE people SET passwdHash = '$newpasswd_hashed' WHERE CONCAT(firstname,' ',lastname) = '$username';";
			
			// Send query to db
			$result = mysqli_query($db, $query); 

			// Check to see if the query was sent
			if (!$result)
				die("INSERT error:" . mysqli_error($db));
		
		
			echo "<p>Password set for $username.</p>";
			   
		}
		
		if (count($errors) == 0)
			 $validInputs = true;	
	  
	}	

	if (!$validInputs)  {      // DISPLAY THE REGISTRATION FORM if user inputs are not yet valid. 
	?>
		<form action="" method="post">
			<table>
				<tr>
					<td>Username:</td>
					<td>
					<select name="username">
						<option value=''>Choose Name</option>
						<?php						
						$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'username' FROM people WHERE member = '1' ORDER BY firstname;";
						$result = mysqli_query($db, $query);
						if(!$result) {
							die("Error in SQL statement." . mysqli_error($db));
						} else {
							$numrows = mysqli_num_rows($result);
							for($i = 0; $i < $numrows; $i++) {
								$row = mysqli_fetch_assoc($result);
								if($row) {
									$username = $row['username'];
									if(isset($_POST['username']) && $_POST['username']==$username)
										echo "<option value='$username' selected='selected'>$username</option>";
									else
										echo "<option value='$username'>$username</option>";
								}
							}
						}
						?>
					</select>
					</td>
					<td><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : ''; ?></small></td>
				</tr>
				<tr>
					<td colspan=2><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : "";?></small></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td><input type='password' name='password1' required='required'></td>
				</tr>
				<tr>
					<td colspan=2><small class='errorText'><?php echo array_key_exists('password1',$errors) ? $errors['password1'] : "";?></small></td>
				</tr>
				
				<tr>
					<td>Password Confirmation:</td>
					<td><input type='password' name='password2' required='required'></td>
				</tr>
				<tr>
					<td colspan=2><small class='errorText'><?php echo array_key_exists('password2',$errors) ? $errors['password2'] : "";?></small></td>
				</tr>
				<tr>
					<td></td>
					<td><input type='submit' name='submit' value='Set Password' formnovalidate></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
			</table>
		</form>
<?php 
	}
	?>
	<table>
		<tr>
			<td></td><td><a href="./usermanagement.php">Back to User Management</a></td>
		</tr>
	</table>
	<?php
} else {
?>
<table>
	<tr>
		<td><a href="./login.php?page=setmemberpassword">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
echo "</div>";
include "frontFooter.php";
?>

