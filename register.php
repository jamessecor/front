<?php
// James Secor

include "frontHeader.php";
require("../includes/frontConfig.php");
require("../includes/frontConnect.php");

print "<div id='right_col'>";
print "<div class='headings'>Set New Password for Members</div>";

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
   
	if (count($errors) > 0) {
		print "<small class='errorText'>There are errors. Please make corrections and try again</small>";
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
			echo "INSERT error:" . mysqli_error($db);
	
	
		echo "<p class='center'>Thank you for registering. Please <a href=\"login.php?username=$username\">login.</a></p>";
		   
	}
    
	if (count($errors) == 0)
         $validInputs = true;	
  
}	

if (!$validInputs)  {      // DISPLAY THE REGISTRATION FORM if user inputs are not yet valid. 
?>
	<form class='center' action="" method="post">
		<table>
			<tr>
				<td>Username:</td>
				<td><select name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : '';  ?>">
					<option value=''>Choose Name</option>
					<?php
					
					// Get artists to populate username drop-down
					
					// TODO: Select members from correct db as below
					
					$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'username' FROM people ORDER BY firstname;";
					//$query = "SELECT username FROM users ORDER BY username;";
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
				<td colspan=2><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : "";?></small>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type='password' name='password1' required='required'></td>
			</tr>
			<tr>
				<td colspan=2><small class='errorText'><?php echo array_key_exists('password1',$errors) ? $errors['password1'] : "";?></small>
			</tr>
			
			<tr>
				<td>Password Confirmation:</td>
				<td><input type='password' name='password2' required='required'></td>
			</tr>
			<tr>
				<td colspan=2><small class='errorText'><?php echo array_key_exists('password2',$errors) ? $errors['password2'] : "";?></small>
			</tr>
			<tr>
				<td></td>
				<td><input type='submit' name='submit' value='Set Password' formnovalidate></td>
			</tr>
		</table>
	</form>
    <!-- THIS IS WHERE YOU WILL PUT ALL THE HTML FOR THE REGISTRATION FORM.  --> 
	<!-- You need inputs for username, email, password1 and password2 --> 
	
	<!-- Remember that each input should be followed by some HTML that will prints validation errors, if any -->
	
	
	
</div>
<?php 
} 
include "frontFooter.php";
?>

