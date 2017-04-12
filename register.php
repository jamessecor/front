<?php
// James Secor

include "frontHeader.php";
require("../includes/frontConfig.php");
require("../includes/frontConnect.php");

print "<div id='right_col'>";
print "<div class='headings'>Set New Password for Members</div>";

$username='';

$validInputs = false; 

// You can use the same type of array for errors as lab 4
$errors = array();

if (isset ($_POST['submit']))
{
	// THIS IS WHERE YOU SHOULD VALIDATE VALUES FROM POST ARRAY SIMILAR TO LAB 4
	
	// 1. Validate username. Make sure you use !empty() to make sure $_POST contains a value. 
	// Then assign a shortname ($username). 
	// Remember to also use Trim() and strlen() functions to make sure username is not whitespace. 
	// You DO NOT need to use regex to check for valid characters. 
	if (!empty($_POST['username'])) {
		$username=trim($_POST['username']);
		if(strlen($username) == 0) {
			$errors['username']="Name cannot be blank.";
		}
	} else {
		$errors['username']="This field is required.";
	}
	
    
	// 2. Validate email. Make sure you use !empty() to make sure $_POST contains a value. 
	// Then assign a shortname. Use filter_var() to check the $email just like lab 4. 
    if (!empty ($_POST['email'])) {
		$email=$_POST['email'];
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors['email']="Invalid email address.";
		}
	} else {
		$errors['email']="This field is required.";
	}
	
		
	// if password does not match the pattern in the regular expression, save an error message in $errors['password1']
	// else : password1 field is empty. This also triggers an error message. It can be saved as $errors['password1']
    if (!empty($_POST['password1'])) {
		$password1=$_POST['password1'];
		
		// Make sure password1 includes 1 each of A-Z, a-z, 0-9, special char.
		if(!preg_match("/^[a-zA-Z0-9-*&^%$#@!{4,25}$/", $password1)) {
			$errors['password1']="Your password must contain 1 or more: uppercase letter, lowercase letter, digit, special character. It must contain 8 or more characters.";
		}
	} else {
		$errors['password1']="Password cannot be empty.";
	}		
   
   
    // AFTER ALL FIEDS ARE VALIDATED... check the error count. 	
	$errorCount = count($errors);	
	if ($errorCount > 0) {             // No errors on form, we can just echo output
		print "<small class='errorText'>There are errors. Please make corrections and try again</small>";
		$validInputs = false;
	}
	else {  // There are no errors with inputs, now we must check to see if the username is available. 	

		$query = "SELECT * FROM users WHERE username = '$username';";	// INSERT THE SQL QUERY HERE AS A STRING. See slide 12 of Lab_7_Essential_Skills.pdf. 
		
		$result = mysqli_query($db, $query); // CALL THE MYSQL FUNCTION THAT WILL SEND THE QUERY TO THE DATABASE. See slide 13 of Lab7_Essential_Skills.pdf
		if(!$result)
			echo mysqli_error($db);// CALL THE MYSQL FUNCTION THAT WILL RETURN AN ERROR MESSAGE. Also slide 13. 
		else
			$numRows = mysqli_num_rows($result);	// CALL THE MYSQL FUNCTION THAT WILL RETURN THE NUMBER OF ROWS IN THE RESULT SET. 
		
		if($numRows > 0)
		{
			$errors['username'] = "username already exists.";
		}
		else { // User name is available and we can enter this new user into the database. 
			
			// HASH the password
		   $hashed_password = password_hash($password1, PASSWORD_DEFAULT);
		   
		   // Create Query
		   $query= "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$hashed_password');";
		   
		   // Send query to the database
		   $result = mysqli_query($db, $query);
		   
		   // Check to see if the query was sent
		   if (!$result)
				echo "INSERT error:" . mysqli_error($db);
		
		
		   echo "<p>Thank you for registering. Please <a href=\"login.php?username=$username\">login.</a></p>";
		}		   
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

