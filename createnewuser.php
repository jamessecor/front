<?php
// createnewuser.php
// author James Secor
// January 2018

include "frontHeader.php";
include "currentShow.php";
$username = $_SESSION['username'];
?>

<div id="right_col">
<div class='headings'>Create New User</div>
<div class="center">
<?php
if(adminIsUser()) {
	$errors = array();
	$errors[0] = "Missing info: ";
	$errors[1] = false;
	$validInputs = false;
	if(isset($_POST['submitNewUser'])) {
		// firstname
		if(!empty($_POST['firstname'])) {
			$firstname = addslashes(trim($_POST['firstname']));
		} else {
			$errors[1] = "first name";
		}
		
		// lastname
		if(!empty($_POST['lastname'])) {
			$lastname = addslashes(trim($_POST['lastname']));
		} else {
			$errors[1] = $errors[1] ? "$errors[1], last name" : "last name";
		}
		
		// address
		if(!empty($_POST['address'])) {
			$address = addslashes(trim($_POST['address']));
		} else {
			$address = "NULL";
		}
		
		// city
		if(!empty($_POST['city'])) {
			$city = addslashes(trim($_POST['city']));
		} else {
			$city = "NULL";
		}
		
		// state
		if(!empty($_POST['state'])) {
			$state = addslashes(trim($_POST['state']));
		} else {
			$state = "NULL";
		}
		
		// zip
		if(!empty($_POST['zip'])) {
			$zip = addslashes(trim($_POST['zip']));
		} else {
			$zip = "NULL";
		}	
		
		// email
		if(!empty($_POST['email'])) {
			$email = addslashes(trim($_POST['email']));
		} else {
			$email = "NULL";
		}

		// phone
		if(!empty($_POST['phone'])) {
			$phone = addslashes(trim($_POST['phone']));
		} else {
			$phone = "NULL";
		}
		
		// usertype
		if(!empty($_POST['usertype'])) {
			$usertype= addslashes(trim($_POST['usertype']));
			if(strlen($usertype) == 0) {
				$errors[1] = $errors[1] ? "$errors[1], user type" : "user type";
			}
		} else {
			$errors[1] = $errors[1] ? "$errors[1], user type" : "user type";
		}	
		
		// website
		if(!empty($_POST['website'])) {
			$website = addslashes(trim($_POST['website']));
		} else {
			$website = "NULL";
		}
		
		// If all good, proceed
		if(!$errors[1]) {
			$validInputs = true;
		}
	}
	if($validInputs) {
		$joinDate = date("Ymd");
		$addUserQuery = "INSERT INTO people (
						firstname, 
						lastname, 
						phone, 
						email, 
						address, 
						city,
						state,
						zip,
						joinDate,
						website,
						member
					)
				VALUES (
						'$firstname',
						'$lastname',
						'$phone',
						'$email', 
						'$address', 
						'$city',
						'$state',
						$zip,
						'$joinDate',
						'$website',
						$usertype
					);";
		$queryResponse = mysqli_query($db, $addUserQuery);
		if($queryResponse) {
			?>
			<table>
				<tr>
					<td colspan='2'>New User Added Successfully!</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td><a href="./createnewuser.php">Add Another</a></td>
					<td><a href="./usermanagement.php">Back to User Management</a></td>
				</tr>
			</table>
			<?php
		} else {
		?>
			<table>
				<tr>
					<td>Unable to add user.</td>
				</tr>
				<tr>
					<td><a href="./createnewuser.php">Try Again</a></td>
				</tr>				
			</table>
		<?php
		}
	} else {
?>
<form id="updateinfo" method="post" action="" autocomplete='off'>
	<table>
		<tr>
			<td colspan=2><small class="errorText"><?php if($errors[1]) echo "$errors[0]$errors[1]"; ?> </small></td>
		<tr>
			<td>First Name</td>
			<td><input type="text" name="firstname"></td>
		</tr>
		<tr>
			<td>Last Name</td>
			<td><input type="text" name="lastname"></td>
		</tr>
		<tr>
			<td>Address</td>
			<td><input type="text" name="address"></td>
		</tr>		
		<tr>
			<td>City</td>
			<td><input type="text" name="city"></td>
		</tr>
		<tr>
			<td>State</td>
			<td><input type="text" name="state"></td>
		</tr>
		<tr>
			<td>Zip Code</td>
			<td><input type="text" name="zip"></td>
		</tr>						
		<tr>
			<td>Phone Number</td>
			<td><input type="text" name="phone"></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input type="email" name="email"></td>
		</tr>
		<tr>
			<td>Website</td>
			<td><input type="text" name="website"></td>
		</tr>
		<tr>
			<td>User Type</td>
			<td>
			<select name="usertype">
				<option value="">Select...</option>
				<option value="1">member - 1</option>
				<option value="<?php echo $currentShow; ?>">Guest Artist - <?php echo $currentShow; ?></option>
				<option value="0">buyer - 0</option>
			</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="submitNewUser" value="Submit New Info"> </td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td></td><td><a href="./usermanagement.php">Back to User Management</a></td>
		</tr>	
	</table>
</form>
<?php
	}
} else {
?>
<table>
	<tr>
		<td><a href="./login.php?page=createnewuser">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
?>
</div></div>
<?php

include "frontFooter.php";
?>