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
				  WHERE a.artistID = $id
				  ORDER BY a.showNumber DESC;";
		$result = mysqli_query($db, $query);
		
		if(!$result) {
			print "<h2>Database Error. Please try again later.</h2>";
		} else {
			$numrows = mysqli_num_rows($result);
			//$total = 0;
			print "<table id='memberart'>";

				print "<tr><th>View Image</th><th>Title (Click to Upload Image)</th><th>Medium</th><th>Year</th><th>Price</th><th>Show</th><th>Sold To</th></tr>";
				for($i = 0; $i < $numrows; $i++) {
					$row = mysqli_fetch_assoc($result);
					if($row) {
						$title = $row['title'];
						$media = $row['medium'];
						$y     = $row['yearMade'];
						$price = $row['price'];
						$show  = $row['showNumber'];
						$buyer = $row['buyer'];
						
						// TODO: fix these lines (get from db)
						//$filename = $row['filename'];
						$filename = 'Mobile2016.jpg';
						
						if(!$buyer)
							$buyer = 'n/a';
						//else
							//$total += $price;
						// TODO: add link to upload on title
						print "<tr><td><a href='../img/$filename' target='_blank'>open</a></td><td>$title</td><td>$media</td><td>$y</td><td>$$price</td><td>$show</td><td>$buyer</td></tr>";
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
	print  "<table>
				<tr>
					<td><a href='./newartwork.php'>Submit New Artwork</a></td>
					<td><a href='./imageupload.php'>Upload Image(s)</a></td>
				</tr>
			</table>";
	/*	
	print "<button id='newart'>Submit New Artwork</button>";
	print "	<script language='JavaScript'>
				document.getElementById('newart').addEventListener('click', function() { window.location.href='./newartwork.php'; });
			</script> ";
			*/
} else {
	print "<h2><a href='./login.php'>Log In to see your artwork info.</a></h2>";
}

?>
</div></div>

<?php
include "frontFooter.php";
?>