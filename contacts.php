<?php
include "frontHeader.php";
?>

<div id="right_col">
<div class='headings'>Contacts</div>


<?php 
print "<div class='center'>";
if(isLoggedIn()) {
	if(isset($_POST['updateinfo'])) {
	} else {
		$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'Name', phone, email, website FROM people WHERE member = 1 ORDER BY firstname;";
		$result = mysqli_query($db, $query);
		if(!$result)
			echo "<h2>Database Error. Please try again later.</h2>";
		else {
			// Print member table
			print "<table id='membercontacts'>";
			print "<tr><th>Name (link to website)</th><th>Phone</th><th>Email</th></tr>";
			$numrows = mysqli_num_rows($result);
			for($i = 0; $i < $numrows; $i++) {
				$row = mysqli_fetch_assoc($result);
				if($row) {				
					$memberName = $row['Name'];
					$phone = $row['phone'];
					$email = $row['email'];
					$website = $row['website'];
					if($phone == NULL)
						$phone = 'unknown';
					if($email == NULL)
						$email = 'unknown';
					
					// Print row with or without link to website
					if($website == NULL)
						print "<tr><td>$memberName</td><td>$phone</td><td>$email</td>";
					else { 
						print "<tr><td><a href='http://$website' target='_blank'>$memberName</a></td><td>$phone</td><td>$email</td>";
					}				
					print "</tr><tr><td colspan='3'><hr></td></tr>";
				}
			}
			print "</table>";
			?>
			<hr>
			<table>
				<tr>
					<td><a href='changecontact.php'>Click to Change Contact Info</a></td>
				</tr>
			</table>
			<?php
		}
	}
} else {
?>
<table>
	<tr>
		<td><a href="./login.php">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
?>

</div></div>
<?php
include "frontFooter.php";
?>