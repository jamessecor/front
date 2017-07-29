<?php
include "frontHeader.php";
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
	$personID = mysqli_query($db, $query);
	
	if($personID) {
		$id_array = mysqli_fetch_array($personID);
		$id = $id_array[0];
		
		// Get member's artwork info from database
		$query = "SELECT a.title, a.medium, a.yearMade, a.price, a.showNumber, a.filename, CONCAT(p.firstname, ' ', p.lastname) AS 'buyer'
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

				print "<tr><th>View Image</th><th>Title</th><th>Medium</th><th>Year</th><th>Price</th><th>Show</th><th>Sold To</th></tr>";
				for($i = 0; $i < $numrows; $i++) {
					$row = mysqli_fetch_assoc($result);
					if($row) {
						$title = $row['title'];
						$media = $row['medium'];
						$y     = $row['yearMade'];
						$price = $row['price'];
						$show  = $row['showNumber'];
						$buyer = $row['buyer'];
						$filename = $row['filename'];
						
						if(!$buyer)
							$buyer = 'n/a';
						//else
							//$total += $price;
						// TODO: add link to upload on title
						if($filename)
							$link = "<a href='./uploads/$filename' target='_blank'>open</a>";
						else
							$link = "n/a";
						print "<tr><td>$link</td><td>$title</td><td>$media</td><td>$y</td><td>$$price</td><td>$show</td><td>$buyer</td></tr>";
					}
				}
			print "</table>";
		}
	}
	?>
	<hr><table>
		<tr>
			<td><a href='./newartwork.php'>Submit New Artwork</a></td>
			<td><a href='./imageupload.php'>Upload Image(s)</a></td>
			<td><a href='./editartwork.php'>Edit Artwork Info</a></td>
		</tr>
	</table>
	<?php
} else {
	print "<h2><a href='./login.php'>Log In to see your artwork info.</a></h2>";
}

?>
</div></div>

<?php
include "frontFooter.php";
?>