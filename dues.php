<?php
include "frontHeader.php";

// Database Set-up
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";

?>

<div id='right_col'>
<div class='headings'>Dues</div>
<div class='center'>

<?php
if(adminIsUser()) {
	$errors = array();
	$validNewDues = false;
	$validDuesInput = false;
	$members = array();
	
	// ==========================================
	// New Dues
	// ==========================================
	if(isset($_POST['newdues'])) {
		// Begin Date
		if(!empty($_POST['begin'])) {
			$begin = $_POST['begin'];
		} else {
			$errors['begin'] = "Date Not Accepted";
		}
		
		// End Date
		if(!empty($_POST['end'])) {
			$end = $_POST['end'];
		} else {
			$errors['end'] = "Date Not Accepted";
		}
		
		// Amount
		if(!empty($_POST['amount'])) {
			$amount = mysqli_real_escape_string($db, trim($_POST['amount']));
			if($amount === '') $errors['amount'] = "Amount cannot be empty";
		} else {
			$errors['amount'] = "Amount cannot be empty";
		}
		
		// Valid if no errors
		if(count($errors)===0) {
			$validNewDues = true;
		}
	}
	
	// ==========================================
	// Payments
	// ==========================================
	if(isset($_POST['sendpayments'])) {
		// Members Checkboxes
		if(!empty($_POST['members'])) {			
			$members = $_POST['members'];
			if(!$members) {
				$errors['members'] = "Select 1 or more members";
			}
		} else {
			$errors['members'] = "Select 1 or more members";
		}
		
		// Payment Amount
		if(!empty($_POST['payamount'])) {			
			$payamount = $_POST['payamount'];
			if(!$payamount) {
				$errors['payamount'] = "Enter an payamount";
			}
		} else {
			$errors['payamount'] = "Enter an payamount";
		}
		
		// Dues Period
		if(!empty($_POST['duesperiod'])) {			
			$duesperiod = $_POST['duesperiod'];
			if(!$duesperiod) {
				$errors['duesperiod'] = "Select dues period";
			}
		} else {
			$errors['duesperiod'] = "Select dues period";
		}
		
		// Valid if no errors
		if(count($errors)===0) {
			$validDuesInput = true;
		}
		
	}
	
	// ==========================================
	// No Errors: Form 1
	// ==========================================
	if($validNewDues) {
		// First Query: Insert new dues period
		$query = "INSERT INTO dues (periodID, begin, end, amount) VALUES (NULL, '$begin', '$end', $amount);";
		$result = mysqli_query($db, $query);		
		
		// Second Query: Update Member Balances
		$query = "UPDATE people SET balance = (balance - $amount);";
		$result2 = mysqli_query($db, $query);
		
		// Print error on entry or submission success
		if(!$result || !$result2) {
			$errors['begin'] = "Database Error";
			die("<table><tr><td>Data Entry Error. <a href=''>Please try again.</a></td></tr>");
		} else {
			print "<table><tr><td>The dues period has been submitted.</td></tr>";
		}
		print "<tr><td><a href='./dues.php'>Back to Dues</a></td></tr></table>";
	}
	// ==========================================
	// No Errors: Form 2
	// ==========================================	
	else if($validDuesInput) {
	
		
	} else {
		
	// ==========================================
	// Forms
	// ==========================================
	?>
<form method='post' action=''>
	<!-- New Dues Form -->
	<table>
		<tr><th colspan=3>New Dues</th></tr>
		<tr>
			<td colspan=3><small class='errorText'><?php echo array_key_exists('begin',$errors) ? $errors['begin'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>Begin Date</td>
			<td><input type='date' name='begin'></td>
			<td>(ex: 1900-01-31)</td>
		</tr>
		<tr>
			<td colspan=3><small class='errorText'><?php echo array_key_exists('end',$errors) ? $errors['end'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>End Date</td>
			<td><input type='date' name='end'></td>
		</tr>
		<tr>
			<td colspan=3><small class='errorText'><?php echo array_key_exists('amount',$errors) ? $errors['amount'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>Amount</td>	
			<td><input type='text' name='amount'></td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='newdues' value='New Dues'></td>
		</tr>
	<table>
	<hr>
	<!-- Dues Payments Form -->
	<table>
		<tr><th colspan=4>Dues Payments</th></tr>
		<tr>
			<td></td><td></td><td><small class='errorText'><?php echo array_key_exists('duesperiod',$errors) ? $errors['duesperiod'] : ''; ?></small></td>
		</tr>
		<tr>
			<td></td><td>Period Begin Date:</td>
			<td>
			<select name='duesperiod' value="<?php echo isset($_POST['duesperiod']) ? $_POST['duesperiod'] : ''; ?>">
			<option value=''>Choose Period</option>
			<?php
			$query = "SELECT begin, end, amount FROM dues ORDER BY end DESC;";
			$result = mysqli_query($db, $query);
			if(!$result)
				$errors['duesperiod'] = "Error in SQL statement." . mysqli_error($db);
			else {
				$numrows = mysqli_num_rows($result);
				for($i = 0; $i < $numrows; $i++) {
					$dues = mysqli_fetch_array($result);
					if($dues) {
						$n = $dues[0];
						print "<option value='$n'>$n</option>";
					}
				}
			}
			?>
			</select>
			</td>
		<tr>
			<td></td><td></td><td><small class='errorText'><?php echo array_key_exists('payamount',$errors) ? $errors['payamount'] : ''; ?></small></td>
		</tr>
		<tr>
			<td></td><td>Payment Amount:</td>
			<td><input type='text' name='payamount'></td>
		</tr>
		<tr>
			<td></td><th>Members:</th><td><small class='errorText'><?php echo array_key_exists('members',$errors) ? $errors['members'] : ''; ?></small></td>
		</tr>
			<?php				
			// Print Member Checkboxes
			$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'username' FROM people ORDER BY firstname;";
			$result = mysqli_query($db, $query);
			if(!$result) {
				$errors['username'] = "Error in SQL statement." . mysqli_error($db);
			} else {
				$numrows = mysqli_num_rows($result);
				echo "<tr>";
				// To create columns
				$t = 1;
				for($i = 0; $i < $numrows; $i++) {
					$row = mysqli_fetch_assoc($result);
					if($row) {
						$username = $row['username'];
						echo "<td><input type='checkbox' name='members[]' value='$username'>$username</td>";
						// Creates 4 columns
						if($t % 4 == 0) echo "</tr><tr>";
						$t++;
					}
				}
				echo "</tr>";
			}
			?>
		<tr>
			<td><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : ''; ?></small></td>
		</tr>
		
		<tr>
			<td></td><td></td><td><input type='submit' name='sendpayments' value='Send Payments'></td>
		</tr>
	</table>	
</form>

<?php
	}
} else {
	print "<div class='headings'><a href='login.php'>Log In to Proceed</a></div>";
}
print "</div></div>";
mysqli_close($db);
include "frontFooter.php";
?>