<?php
include "frontHeader.php";
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";
?>

<div id="right_col">
<div class='headings'>Artwork</div>
<div class='center'>

<?php 
// TODO: take away "!"
if(isLoggedIn()) {
	// Get artistID
	$artist = $_SESSION['username'];
	$query = "SELECT personID FROM people WHERE CONCAT(firstname, ' ', lastname) = '$artist';";
	$person = mysqli_query($db, $query);
	
	if($person) {
		$id = mysqli_fetch_array($person);
		
		// Get member's artwork info from database
		$query = "SELECT a.title, a.price, CONCAT(p.firstname, ' ', p.lastname) AS 'buyer'
				  FROM artwork a 
				  LEFT OUTER JOIN people p ON a.buyerID = p.personID
				  WHERE a.artistID = ${id[0]};";
		$result = mysqli_query($db, $query);
		
		if(!$result) {
			print "<h2>Database Error. Please try again later.</h2>";
		} else {
			$numrows = mysqli_num_rows($result);
			
			print "<table id='memberart'>";

				print "<tr><th>Title</th><th>Price</th><th>Sold To</th></tr>";
				for($i = 0; $i < $numrows; $i++) {
					$row = mysqli_fetch_assoc($result);
					if($row) {
						$title = $row['title'];
						$price = $row['price'];
						$buyer = $row['buyer'];
						if(!$buyer)
							$buyer = 'n/a';
						print "<tr><td>$title</td><td>$$price</td><td>$buyer</td></tr>";
					}
				}
			print "</table>";
		}
	}
	
	
	
	
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