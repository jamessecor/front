<?php
// artwork.php
include "frontHeader.php";
?>

<div id="right_col">
<div class='headings'>Artwork</div>
<div class='center'>

<?php 
function printArtwork($where, $order) {
	global $db;
	// Get member's artwork info from database
	$query = "SELECT a.title, a.medium, a.yearMade, a.price, a.showNumber, a.filename, CONCAT(p.firstname, ' ', p.lastname) AS 'buyer'
			  FROM artwork a 
			  LEFT OUTER JOIN people p ON a.buyerID = p.personID
			  $where
			  $order
			  ;";
	$result = mysqli_query($db, $query);
	
	if(!$result) {
		print "<h2>Database Error. Please try again later.</h2>";
	} else {
		$numrows = mysqli_num_rows($result);
		//$total = 0;
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
					$filename = $row['filename'];
					
					if(!$buyer)
						$buyer = 'n/a';
					//else
						//$total += $price;
					// TODO: add link to upload on title
					if($filename) {
						$title = "<a href='../frontUploads/$filename' target='_blank'>$title</a>";
					}

					// Add "$" to price
					if(is_numeric($price))
						$price = "$$price";
						
					print "<tr><td>$title</td><td>$media</td><td>$y</td><td>$price</td><td>$show</td><td>$buyer</td></tr>";
				}
			}
		print "</table>";
	}
}



// TODO: take away "!"
if(isLoggedIn()) {
	// Get artistID
	$artist = $_SESSION['username'];
	$query = "SELECT personID FROM people WHERE CONCAT(firstname, ' ', lastname) = '$artist';";
	$personID = mysqli_query($db, $query);
	
	if($personID) {
		$id_array = mysqli_fetch_array($personID);
		$id = $id_array[0];
		
		printArtwork("WHERE a.artistID = $id","ORDER BY a.showNumber DESC");
		/*
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
						$filename = $row['filename'];
						
						if(!$buyer)
							$buyer = 'n/a';
						//else
							//$total += $price;
						// TODO: add link to upload on title
						if($filename) {
							$title = "<a href='../frontUploads/$filename' target='_blank'>$title</a>";
						}

						// Add "$" to price
						if(is_numeric($price))
							$price = "$$price";
							
						print "<tr><td>$title</td><td>$media</td><td>$y</td><td>$price</td><td>$show</td><td>$buyer</td></tr>";
					}
				}
			print "</table>";
		}*/
	}
	?>
	<hr><table>
		<tr>
			<td><a href='./newartwork.php'>Submit New Artwork</a></td>
			<td><a href='./imageupload.php'>Upload Image(s)</a></td>
			<td><a href='./editartwork.php'>Edit Artwork</a></td>
		</tr>
	</table>
	<!-- Form for viewing other members' work -->
	<form id="memberswork" method="post" action="artwork.php#memberswork" autocomplete="off">
		<table>
			<tr>
				<th colspan='10'>See Other Members' Work</th>
			</tr>
			<tr>
				<td>Sort by</td>
				<td>
					<select name="order">
						<option value="a.artistID">Artist</option>
						<option value="a.showNumber DESC">Show Number</option>
					</select>
				</td>
			</tr>
			<tr>
				<td></td><td><input type="submit" value="See Work" name="seework"></td>
			</tr>
		</table>
	</form>
	<?php
	if(isset($_POST['seework'])) {
		if(!empty($_POST['order'])) {
			$order = trim(addslashes($_POST['order']));
		}
		?>
		<hr>
		<?php
		$orderBy = "ORDER BY $order";
		printArtwork("", $orderBy);
	}	
} else {
?>
<table>
	<tr>
		<td><a href="./login.php?page=artwork">Log In to Continue</a></td>
	</tr>
</table>
<?php
}

?>
</div></div>

<?php
include "frontFooter.php";
?>