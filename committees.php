<?php
include "frontHeader.php";
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";
?>

<div id="right_col">
<div class='headings'>Committees</div>
<div class='center'>

<?php
if(!isLoggedIn()) {
	print "<h2><a href='./login.php'>Log In to see your committee info.</a></h2>";
} else {
	// Show committees
	$query = "SELECT CONCAT(p.firstname, ' ', p.lastname) AS 'member', c.committeeName, a.joinDate, a.endDate
			  FROM assignments a 
			  JOIN committees c ON a.committeeID = c.committeeID
			  JOIN people p ON a.personID = p.personID
			  ORDER BY c.committeeName;";
	
	$result = mysqli_query($db, $query);
	
	if(!$result) {
		die("Database Error: " . mysqli_error($db));
	} else {
		$numrows = mysqli_num_rows($result);
		print "<table><tr><th>Name</th><th>Committee Name</th><th>Join Date</th><th>End Date</th></tr>";
		for($i = 0; $i < $numrows; $i++) {
			$row = mysqli_fetch_assoc($result);
			if($row) {
				$name = $row['member'];
				$committeeName = $row['committeeName'];
				$join = $row['joinDate'];
				$end = $row['endDate'];
				if(!$end) 
					$end = 'n/a';
				print "<tr><td>$name</td><td>$committeeName</td><td>$join</td><td>$end</td></tr>";
			}
			
		}
		print "</table>";
	}
}


print "</div></div>";






include "frontFooter.php";
?>