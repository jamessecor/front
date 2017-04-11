<?php
include "frontHeader.php";
?>

<div id="right_col">
<div class='headings'>Artwork</div>
<div class='center'>

<?php 
// TODO: take away "!"
if(isLoggedIn()) {
	// Get member's artwork info from database
	/*
	$query = "SELECT a.title, a.price, CONCAT(p.firstname, ' ', p.lastname)
			  FROM Artwork a 
			  JOIN Buyers b ON a.buyerID = b.buyerID
			  JOIN People p ON b.buyerID = p.personID;";
	$result = mysqli_query($db, $query);
	
	if(!$result) {
		print "<h2>Database Error. Please try again later.</h2>";
		
	} else {
		// TODO: fill in with loop
	}
	*/
	
	$artwork = array(
		array('title',55.2, 'Peter'),
		array('othertitle',150,FALSE),
		array('yet', 250,'Glen')
	);
	$numrows = 3;
	// $numrows = mysqli_num_rows($result);
	
	print "<table id='memberart'>";

		print "<tr><th>Title</th><th>Price</th><th>Sold To</th></tr>";
		for($i = 0; $i < $numrows; $i++) {
			$title = $artwork[$i][0];
			$price = $artwork[$i][1];
			$sold = $artwork[$i][2];
			if(!$sold)
				$sold = 'n/a';
			print "<tr><td>$title</td><td>$price</td><td>$sold</td></tr>";
		}
	print "</table>";
	
	// TODO: get from db
	$totalSales = 50;
	if($totalSales > 0) {
		$lessCommission = .85 * $totalSales;
		print "<p><strong>Total Sales = \$$totalSales</strong>";
		print "<br>Your cut = \$$lessCommission</p>";
	}
	print "<button id='newart'>Submit New Artwork</button>";
	print "	<script language='JavaScript'>
				document.getElementById('newart').addEventListener('click', function() { window.location.href='./newartwork.php'; });
			</script> ";
} else {
	print "<h2><a href='./login.php'>Log In to see your artwork info.</a></h2>";
}

?>
</div></div>

<?php
include "frontFooter.php";
?>