<?php
include "frontHeader.php";
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";
?>

<div id="right_col">
<div class='headings'>Contacts</div>


<?php 
print "<div class='center'>";
if(isLoggedIn()) {
	/*$query = "SELECT CONCAT(p.firstname, ' ', p.lastname) AS 'Name', p.phone, p.email 
				FROM people p JOIN artists a 
				ON p.personID = a.artistID 
				ORDER BY p.firstname;";
	TODO: use this query			
				*/
	$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'Name', phone, email, website FROM people WHERE member = 1 ORDER BY firstname;";
	$result = mysqli_query($db, $query);
	if(!$result)
		echo "<h2>Database Error. Please try again later.</h2>";
	else {
		
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
					print "<tr><td>$memberName</td><td>$phone</td><td>$email</td></tr>";
				else 
					print "<tr><td><a href='http://$website' target='_blank'>$memberName</a></td><td>$phone</td><td>$email</td></tr>";
			}
		}
		print "</table>";
		
	}
} else {
	
	print "<h2><a href='./login.php'>Log In to see your artwork info.</a></h2>";
}
?>

</div></div>
<?php
mysqli_close($db);
include "frontFooter.php";
?>