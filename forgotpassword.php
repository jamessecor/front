<?php
include "frontHeader.php";

$username='';
$passwd='';
$errors=array();

print "<div id='right_col'>";
print "<div class='headings'>Password Reset</div>";
print "<div class='center'>";

if(isset($_POST['forgot'])) {
	// Username
	if(!empty($_POST['username'])) {
		$username=addslashes($_POST['username']);
	} else {
		$errors['username']="Select your name.";
	}
}

if(isset($_POST['forgot']) && count($errors) === 0) {
	// send admin email
	// TODO: replace email with admin email from database
	$msg = "I forgot my password."; // $message;
	$msg = wordwrap($msg,70);
	$subject = 'Password Reset for ' . $username . ".";
	if(mail('james.secor@gmail.com', $subject , $msg)) {
		// messgae user
		?>
		<table><tr>
				<td>A message has been sent to admin.</td>
			<tr>
				<td>They will send you a temporary password.</td>
			</tr>
		</tr></table>
		<?php
	}
} else {

?>

<form id="login" method="post" action="">
	<table>
		<tr>
			<td>Name</td>
			<td><select name="username">
				<option value=''>Choose Name</option>
				<?php
				
				// Use query to get artists to populate username drop-down		
				$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'username' FROM people WHERE member = 1 ORDER BY username;";
				$result = mysqli_query($db, $query);
				if(!$result) {
					$errors['username'] = "Error in SQL statement." . mysqli_error($db);
				} else {
					$numrows = mysqli_num_rows($result);
					for($i = 0; $i < $numrows; $i++) {
						$row = mysqli_fetch_assoc($result);
						if($row) {
							$username = $row['username'];
							echo "<option value='$username'>$username</option>";
						}
					}
				}
				?>
			</select></td>
		</tr>
		<tr>
			<td></td><td colspan=2><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : ''; ?></small></td>
		</tr>
		<!-- Maybe add
		<tr>
			<td>Message</td>
			<td><input type="text" name="message" value="I forgot my password."></td>
		</tr>
		-->
		<tr>
			<td></td><td><input type="submit" name="forgot" value="Request Password Reset"></td>
		</tr>
	</table>
</form>

</div>
</div>
<?php
}
include "frontFooter.php";
?>