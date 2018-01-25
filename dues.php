<?php
include "frontHeader.php";
?>

<div id='right_col'>
<div class='headings'>Dues</div>
<div class='center'>

<?php
if(bookkeeperIsUser()) {
	$errors = array();
	$validNewDues = false;
	$validDuesInput = false;
	$members = array();
	
	// ==========================================
	// Submit New Dues Period
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
	// Submit Payments
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
			if(!is_numeric($payamount)) {
				$errors['payamount'] = "Enter a dollar amount";
			}
		} else {
			$errors['payamount'] = "Enter an payment amount";
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
	// No Errors: New Dues Period
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
	// No Errors: Submit Dues
	// ==========================================	
	else if($validDuesInput) {
		foreach($members as $m) {
			// ===========================
			// Create payment in memberdues
			// ===========================
			// select personID
			$query = "SELECT personID FROM people WHERE CONCAT(firstname, ' ', lastname) = '$m';";
			$id = mysqli_query($db, $query);
			
			// select periodID
			$query = "SELECT periodID FROM dues WHERE begin = '$duesperiod';";
			$duesID = mysqli_query($db, $query);
			
			// Check for errors
			if(!($duesID) || !($id)) {
				die("Database Error: " . mysqli_error($db));
			} else {
				// Set personID
				$id_array = mysqli_fetch_array($id);
				if($id_array)
					$id = $id_array[0];
				// Set periodID
				$duesID_array = mysqli_fetch_array($duesID);
				if($duesID_array)
					$dID = $duesID_array[0];
				
				// Insert into memberdues
				$today=date('Y-m-d');
				$query = "INSERT INTO memberdues (personID, periodID, paymentDate) VALUES ('$id', '$dID', '$today');";
				$result = mysqli_query($db, $query);
				if(!$result) {
					die("Database Error: " . mysqli_error($db));
				} else {
					print "Success: ";
				}
			}
			
			// =====================
			// Update member balance
			// =====================
			$query = "UPDATE people SET balance = (balance + $payamount) WHERE CONCAT(firstname, ' ', lastname) = '$m';";
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("Database Error: " . mysqli_error($db));
			} else 
				print "Payment processed for $m<br>";
		}
		
	} 
	// ==========================================
	// Member Dues Status Table
	// ==========================================	
	else if(isset($_POST['status'])) {
		// Show most recent payment date, begin and end date of period, and how much is owed.
		
		// Get person, id, and balance
		$allMembersQuery = "SELECT personID, CONCAT(firstname, ' ', lastname) AS 'Name', balance FROM people WHERE member = 1 ORDER BY firstname;";
		$allMembers = mysqli_query($db, $allMembersQuery);
		if(!$allMembers) {
			die("Database Error: " . mysqli_error($db));
		} else {
			echo "<table><tr><th>Name</th><th>Balance</th><th>Last Payment</th><th>Period Begin</th><th>Period End</th></tr>";
			$n = mysqli_num_rows($allMembers);
			for($i = 0; $i < $n; $i++) {
				// Get Members, one at a time
				$m = mysqli_fetch_assoc($allMembers);
				$id = $m['personID'];
				
				// Get most recent payment
				$paymentQuery = "SELECT m.paymentDate, d.begin, d.end, d.amount 
								 FROM dues d 
								 JOIN memberdues m ON d.periodID = m.periodID 
								 WHERE m.personID = $id AND m.paymentDate = 
									(SELECT max(paymentDate) FROM memberdues WHERE personID = $id);";
				$paymentInfo = mysqli_query($db, $paymentQuery);
				if(!$paymentInfo) {
					die("Database Error: " . mysqli_error($db));
				} else {
					// Print table row
					$p = mysqli_fetch_assoc($paymentInfo);
					echo "<tr><td>$m[Name]</td><td>$m[balance]</td><td>$p[paymentDate]</td><td>$p[begin]</td><td>$p[end]</td></tr>";//</td></tr>";
				}
				
			}
		}
		
		
		$query = "SELECT * FROM memberdues WHERE ";
	// ==========================================
	// Member Dues Status Table
	// ==========================================	
	} else if(isset($_POST['missingDues'])) {	
		print "here";
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
	</table>
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
			<select name='duesperiod'>
			<option value=''>Choose Period</option>
			<?php
			$query = "SELECT begin, end, amount FROM dues ORDER BY end DESC;";
			$result = mysqli_query($db, $query);
			if(!$result)
				die("Error in SQL statement." . mysqli_error($db));
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
				die("Error in SQL statement." . mysqli_error($db));
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
	<hr>
	<!-- See Member Dues Status -->
	
	<table>
		<tr><th>Check Dues Status</th></tr>
		<tr><td><input type="submit" name="status" value="Member Status"></td></tr>
	</table>
	<!-- Check missing dues by date -->
	<table>
		<tr><th>Check Missing Dues</th></tr>
		<tr>
			<td>
			<select name='duesStartDate'>
				<option value=''>Choose Dues Period</option>
				<?php
				$query = "SELECT DISTINCT begin FROM dues ORDER BY begin DESC;";
				$result = mysqli_query($db, $query);
				if(!$result)
					$errors['missingDuesError'] = "Error in SQL statement." . mysqli_error($db);
				else {
					$numrows = mysqli_num_rows($result);
					for($i = 0; $i < $numrows; $i++) {
						$duesPeriod = mysqli_fetch_array($result);
						if($duesPeriod) {
							$n = $duesPeriod[0];
							print "<option value='$n'>$n</option>";
						}
					}
				}
				?>
			</select>
			</td>
			<td><input type="submit" name="missingDues" value="Find Missing Dues"></td>
			<td><small class='errorText'><?php echo array_key_exists('showNumber',$errors) ? $errors['showNumber'] : ''; ?></small></td>
		</tr>
	</table>
</form>

<?php
	}
} else {
	print "<div class='headings'><a href='login.php?page=dues'>Log In to Proceed</a></div>";
}
print "</div></div>";
include "frontFooter.php";
?>