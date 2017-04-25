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
	?>
<form method='post' action=''>
	<table>
		<tr><th colspan=2>Dues Payments</th></tr>
		<tr>
			<td>Member:</td>
			<td>Amount:</td>
		</tr>
		<tr>
			<?php				
			$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'username' FROM people ORDER BY firstname;";
			$result = mysqli_query($db, $query);
			if(!$result) {
				$errors['username'] = "Error in SQL statement." . mysqli_error($db);
			} else {
				$numrows = mysqli_num_rows($result);
				for($i = 0; $i < $numrows; $i++) {
					$row = mysqli_fetch_assoc($result);
					if($row) {
						$username = $row['username'];
						echo "<tr><td>$username</td><td><input type='text' name='dues'></td></tr>";
					}
				}
			}
			?>
			<td><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : ''; ?></small></td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='sendpayments' value='Send Payments'></td>
		</tr>
	</table>
	<table>
		<tr><th colspan=3>New Dues</th></tr>
		<tr>
			<td>Begin Date</td>
			<td><input type='date' name='begin'></td>
			<td>(ex: 1900-01-31)</td>
		</tr>
		<tr>
			<td>End Date</td>
			<td><input type='date' name='end'></td>
		</tr>
		<tr>
			<td>Amount</td>	
			<td><input type='text' name='amount'></td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='newdues' value='New Dues'></td>
		</tr>
	<table>
</form>

<?php
} else {
	print "<div class='headings'><a href='login.php'>Log In to Proceed</a></div>";
}
print "</div></div>";
mysqli_close($db);
include "frontFooter.php";
?>