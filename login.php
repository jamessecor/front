<?php
include "frontHeader.php";

$username='';
$passwd='';
$errors=array();

print "<div id='right_col'>";
print "<div class='headings'>Log In</div>";


if(isset($_POST['login'])) {
	// Username
	if(!empty($_POST['username'])) {
		$username=addslashes($_POST['username']);
	} else {
		$errors['username']="This field is required.";
	}
	
	// Password
	if(!empty($_POST['passwd'])) {
		$passwd=addslashes($_POST['passwd']);	
		if(strlen(trim($passwd))==0)
			$errors['passwd']="Password cannot be blank";
	} else {
		$errors['passwd']="This field is required.";
	}	
	
	// NO ERRORS
	if(count($errors)==0) {		
		// Query Database
		$query = "SELECT CONCAT(firstname,' ',lastname) AS 'username', passwdHash FROM people WHERE CONCAT(firstname, ' ',lastname) = '$username';";		
		$result = mysqli_query($db, $query);
		
		if(!$result)
			$errors['username'] = "Error in SQL statement." . mysqli_error($db);
		else {
			$row = mysqli_fetch_assoc($result);
			if($row) {
				if(password_verify($passwd, $row['passwdHash'])) {
					header('Location: login.php');
					$_SESSION['username'] = $username;					
				} else {
					$errors['passwd'] = "Your login credentials could not be verified. Please check username and re-enter password";
				}
			} else {
				$errors['passwd'] = "Username does not match. Please create an account.";
			}
		}
	}
}

if(isLoggedIn()) {
	// Successful Login
	header('Location: artwork.php');
	print "<div class='center'>";
	print "<p>You are currently logged in as $_SESSION[username].";
	print "<br><a href='./logout.php'>Click here to log out.</a></p>";
	print "</div>";
} else {

?>

<form class="center" id="login" method="post" action="">
	<table>
		<tr>
			<td>Name</td>
			<td><select name="username">
				<option value=''>Choose Name</option>
				<?php
				
				// Get artists to populate username drop-down
				
				// TODO: Select members from correct db as below
				//$query = "SELECT CONCAT(firstname, ' ', lastname) FROM artists ORDER BY firstname;";
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
							if(isset($_POST['username']) && $_POST['username']==$username)
								echo "<option value='$username' selected ='selected'>$username</option>";
							else
								echo "<option value='$username'>$username</option>";
						}
					}
				}
				?>
			</select></td>
		</tr>
		<tr>
			<td colspan=2><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="passwd"></td>
		</tr>
		<tr>
			<td colspan=2><small class='errorText'><?php echo array_key_exists('passwd',$errors) ? $errors['passwd'] : ''; ?></small></td>
		</tr>
		<tr>
			<td></td><td><input type="submit" name="login" value="Log In"></td>
		</tr>
	</table>
</form>

</div>

<?php
}
include "frontFooter.php";
?>