<?php
include "frontHeader.php";

// Open right_col
print "<div id='right_col'>";

if(isLoggedIn()) {
	
// Get member info from database
	// Total sold
	// Total in sales
	// TODO: Get values from database

	$username = $_SESSION['username'];
	print "<div class='headings'>Membership Info for $username</div>";
	
	// Center Table
	print "<div class='center'>";
	/*	$query = "	SELECT CONCAT(m.firstname, ' ', m.lastname), m.joinDate, sum(a.price), sum(a.toArtist)
					From artists m
					JOIN artwork a ON m.artistID = a.artistID
					WHERE a.sold = TRUE AND m.username = '$username'; ";
		$result = mysqli_query($db, $query);
		if(!$result)
			

	*/

	$sales = 350;
	$joinDate = '2017-05-15';
	$lessCommission =  .85 * $sales;
	$memberinfo = array('Name'=>$username,'Join Date'=>$joinDate, 'Total Sales'=>'$' . $sales, 'Your Cut'=>'$' . $lessCommission);

?>
	<table id="memberinfo">
	<?php
		print "<tr><th>Description</th><th>Info</th></tr>";
		foreach($memberinfo as $key => $value) {
			print "<tr><td>$key</td><td>$value</td></tr>";
		}
	?>
	</table>
<?php
} else {
	print "<div class='headings'>Membership Info</div>";
	print "<div class='center'>";
	print "<h2><a href='./login.php'>Log In to see your membership info.</a></h2>";
}
// Close .center and #right_col
print "</div></div>";

include "frontFooter.php";
?>