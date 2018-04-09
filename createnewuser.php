<?php
// createnewuser.php
// author James Secor
// January 2018

include "frontHeader.php";
include "currentShow.php";
$username = $_SESSION['username'];
?>

<div id="right_col">
<div class='headings'><?php echo isset($_GET['userid']) ? "Update User" : "Create New User"; ?></div>
<div class="center">
<?php
if(adminIsUser()) {
	$errors = array();
	$errors[0] = "Missing info: ";
	$errors[1] = false;
	$validInputs = false;
	$firstname  = "";
	$lastname   = "";
	$address    = "";
	$city       = "";
	$state      = "";
	$zip        = "";
	$phone      = "";
	$usertype   = "";
	$website    = "";
	$email      = "";
	$buttonName = "submitNewUser";
	$buttonValue= "Submit New Info";
	$successfulQueryText = "New User Added Successfully!";
	if(isset($_GET['userid'])) {
		$userId = $_GET['userid'];
		$buttonName = "submitUserUpdate";
		$buttonValue= "Update User Info";
		$successfulQueryText = "User Updated Successfully!";
		
		// Select user's info
		$userInfoQuery = "SELECT * FROM people WHERE personID = '$userId';";
		$userInfoResults = mysqli_query($db, $userInfoQuery);
		if(!$userInfoResults || mysqli_num_rows($userInfoResults) > 1) {
			die("Unable to find user" . mysqli_error());
		} else {
			$userInfo  = mysqli_fetch_assoc($userInfoResults);
			$firstname = $userInfo['firstname'];
			$lastname  = $userInfo['lastname'];
			$address   = $userInfo['address'];
			$city      = $userInfo['city'];
			$state     = $userInfo['state'];
			$zip       = $userInfo['zip'];
			$phone     = $userInfo['phone'];
			$email     = $userInfo['email'];
			$usertype  = $userInfo['member'];
			$website   = $userInfo['website'];
		}
				
		// Set query (INSERT or UPDATE)
		
	}
	
	if(isset($_POST['submitNewUser']) || isset($_POST['submitUserUpdate'])) {
		// Set parallel arrays for column names and values
		$columnName = array();
		$columnValue = array();

		// firstname
		if(!empty($_POST['firstname'])) {
			$firstname = addslashes(trim($_POST['firstname']));
			$columnName[] = "firstname";
			$columnValue[] = $firstname;
		} else {
			$errors[1] = "first name";
		}
		
		// lastname
		if(!empty($_POST['lastname'])) {
			$lastname = addslashes(trim($_POST['lastname']));
			$columnName[] = "lastname";
			$columnValue[] = $lastname;
		} else {
			$errors[1] = $errors[1] ? "$errors[1], last name" : "last name";
		}
		
		// address
		if(!empty($_POST['address'])) {
			$address = addslashes(trim($_POST['address']));
			$columnName[] = "address";
			$columnValue[] = $address;
		}
		
		// city
		if(!empty($_POST['city'])) {
			$city = addslashes(trim($_POST['city']));
			$columnName[] = "city";
			$columnValue[] = $city;
		}
		
		// state
		if(!empty($_POST['state'])) {
			$state = addslashes(trim($_POST['state']));
			$columnName[] = "state";
			$columnValue[] = $state;
		}
		
		// zip
		if(!empty($_POST['zip'])) {
			$zip = addslashes(trim($_POST['zip']));
			$columnName[] = "zip";
			$columnValue[] = $zip;
		}
		
		// email
		if(!empty($_POST['email'])) {
			$email = addslashes(trim($_POST['email']));
			$columnName[] = "email";
			$columnValue[] = $email;
		}

		// phone
		if(!empty($_POST['phone'])) {
			$phone = addslashes(trim($_POST['phone']));
			$columnName[] = "phone";
			$columnValue[] = $phone;
		}
		
		// website
		if(!empty($_POST['website'])) {
			$website = addslashes(trim($_POST['website']));
			$columnName[] = "website";
			$columnValue[] = $website;
		}
		
		// usertype
		if(!empty($_POST['usertype']) || $_POST['usertype'] == 0) {
			$usertype= addslashes(trim($_POST['usertype']));
			$columnName[] = "member";
			$columnValue[] = $usertype;		
			if(strlen($usertype) == 0) {
				$errors[1] = $errors[1] ? "$errors[1], user type" : "user type";
			}
		} else {
			
			$errors[1] = $errors[1] ? "$errors[1], user type" : "user type";
		}		
		
		// If all good, proceed
		if(!$errors[1]) {
			$validInputs = true;
		}
	}
	if($validInputs) {
		if(isset($_POST['submitUserUpdate'])) {
			$userid = $_GET['userid'];
			$q1 = "UPDATE people SET ";
			$q2 = "";
			$q3 = "WHERE personID = $userid;";
			for($i = 0; $i < count($columnName); $i++) {
				if($q2 != "") {
					$q2 .= ", ";
				}
				// Use quotes or not
				if($columnName[$i] == 'zip' || $columnName[$i] == 'member')	
					$q2 .= $columnName[$i] . " = ${columnValue[$i]} ";
				else 
					$q2 .= $columnName[$i] . " = '${columnValue[$i]}' ";
								
			}
			$userQuery = $q1 . $q2 . $q3;
		} elseif(isset($_POST['submitNewUser'])) {
			$joinDate = date("Ymd");	
			$q1 = "INSERT INTO people (";
			$q2 = "";
			$q3 = ") VALUES (";
			$q4 = "";
			$q5 = ");";
			
			// Put specific columns from user input
			for($i = 0; $i < count($columnName); $i++) {
				if($q2 != "" && $q4 != "") {
					$q2 .= ", ";
					$q4 .= ", ";
				}
				
				// Use quotes or not
				if($columnName[$i] == 'zip' || $columnName[$i] == 'member')
					$q4 .= " ${columnValue[$i]} ";
				else 
					$q4 .= " '${columnValue[$i]}' ";
				$q2 .= " $columnName[$i] ";
								
			}
			$userQuery = $q1 . $q2 . $q3 . $q4 . $q5;
		} else {
			die("Something went wrong, and there's no query" . mysqli_error());
		}			
		
		// Query db 
		$queryResponse = mysqli_query($db, $userQuery);
		
		// Did it work?
		if($queryResponse) {
			?>
			<table>
				<tr>
					<td colspan="<?php echo count($columnName);?>"> <?php echo $successfulQueryText; ?></td>
				</tr>
				<tr>
				<?php 
				foreach($columnName as $cname) {
					echo "<th>${cname}</th>";
				}				
				?>				
				</tr>
				<tr>
				<?php 
				foreach($columnValue as $cValue) {
					echo "<td>${cValue}</td>";
				}				
				?>				
				</tr>
				<tr>
					<td colspan="<?php echo count($columnName);?>"><a href="./createnewuser.php">Add Another</a></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td colspan="<?php echo count($columnName);?>"><a href="./usermanagement.php">Back to User Management</a></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td colspan="<?php echo count($columnName);?>"><a href="./createnewuser.php?userid=<?php echo $userid; ?>">Edit This User</a></td>
				</tr>
			</table>
			<?php
		} else {
			// Nope, it didn't
		?>
			<table>
				<tr>
					<td>Unable to add user.</td>
				</tr>
				<tr>
					<td><a href="./usermanagement.php">Try Again</a></td>
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
			<td><input type="text" value="<?php echo $firstname ? $firstname : '';?>" name="firstname"></td>
		</tr>
		<tr>
			<td>Last Name</td>
			<td><input type="text" value="<?php echo $lastname ? $lastname : '';?>" name="lastname"></td>
		</tr>
		<tr>
			<td>Address</td>
			<td><input type="text" value="<?php echo $address ? $address : '';?>" name="address"></td>
		</tr>		
		<tr>
			<td>City</td>
			<td><input type="text" value="<?php echo $city ? $city : '';?>" name="city"></td>
		</tr>
		<tr>
			<td>State</td>
			<td><input type="text" value="<?php echo $state ? $state : '';?>" name="state"></td>
		</tr>
		<tr>
			<td>Zip Code</td>
			<td><input type="text" value="<?php echo $zip ? $zip : '';?>" name="zip"></td>
		</tr>						
		<tr>
			<td>Phone Number</td>
			<td><input type="text" value="<?php echo $phone ? $phone : '';?>" name="phone"></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input type="email" value="<?php echo $email ? $email : '';?>" name="email"></td>
		</tr>
		<tr>
			<td>Website</td>
			<td><input type="text" value="<?php echo $website ? $website : '';?>" name="website"></td>
		</tr>
		<tr>
			<td>User Type</td>
			<td>
			<select name="usertype">
				<option value="">Select...</option>
				<option value="1" <?php echo ($usertype == 1) ? "selected" : ""; ?>>member (1)</option>
				<option value="<?php echo $currentShow; ?>" 
								<?php echo ($usertype != 1 && $usertype != 0) ? "selected" : ""; ?>>Guest Artist (<?php echo $currentShow; ?>)</option>
				<option value="0" <?php echo (isset($_GET['userid']) && $usertype == 0) ? "selected" : ""; ?>>non-member (0)</option>
			</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="<?php echo $buttonName; ?>" value="<?php echo $buttonValue; ?>"> </td>
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