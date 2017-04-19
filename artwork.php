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
		$id_array = mysqli_fetch_array($person);
		$id = $id_array[0];
		
		// Get member's artwork info from database
		$query = "SELECT a.title, a.medium, a.yearMade, a.price, a.showNumber, CONCAT(p.firstname, ' ', p.lastname) AS 'buyer'
				  FROM artwork a 
				  LEFT OUTER JOIN people p ON a.buyerID = p.personID
				  WHERE a.artistID = $id;";
		$result = mysqli_query($db, $query);
		
		if(!$result) {
			print "<h2>Database Error. Please try again later.</h2>";
		} else {
			$numrows = mysqli_num_rows($result);
			
			print "<table id='memberart'>";

				print "<tr><th>Title</th><th>Medium</th><th>Year</th><th>Price</th><th>Show</th><th>Sold To</th></tr>";
				for($i = 0; $i < $numrows; $i++) {
					$row = mysqli_fetch_assoc($result);
					if($row) {
						$title = $row['title'];
						$media = $row['medium'];
						$y     = $row['yearMade'];
						$price = $row['price'];
						$show  = $row['showNumber'];
						$buyer = $row['buyer'];
						if(!$buyer)
							$buyer = 'n/a';
						print "<tr><td>$title</td><td>$media</td><td>$y</td><td>$$price</td><td>$show</td><td>$buyer</td></tr>";
					}
				}
			print "</table>";
		}
		
		// Select totals using $id
		$query = "SELECT sum(price) FROM artwork
				  WHERE buyerID IS NOT NULL
				  AND artistID = '$id'";
		$result = mysqli_query($db, $query);
		if($result) {
			$sum_array = mysqli_fetch_array($result);
			$sum = $sum_array[0];
			if(!$sum) 
				$sum = 0;
			$mcut = $sum * .85;
			print "<p><strong>Total Sales = \$$sum</strong>";
			print "<br>Your cut = \$$mcut</p>";
		}
	}
		
	// TODO: get from db
	
	$totalSales = 50;
	if($totalSales > 0) {
		$lessCommission = .85 * $totalSales;
		//print "<p><strong>Total Sales = \$$totalSales</strong>";
		//print "<br>Your cut = \$$lessCommission</p>";
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