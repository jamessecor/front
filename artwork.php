<?php
// artwork.php
include "frontHeader.php";
?>

 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(document).ready(function() {
	$( function() {
		$( "#accordion" ).accordion({
			fade: "slow",
			collapsible: true,
			heightStyle: "content",
			animate: 0
		});
	} );
});
</script>

<div id="right_col">
<div class='headings'>Artwork</div>
<div class='center'>
<?php 
function printArtwork($where, $order, $artistName) {
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
		// Artist Name header
		print "<h1 class='artworkHeader'>$artistName</h1>";
		
		// Artist's works
		print "<table class='memberart' rules='rows'>";

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


if(isLoggedIn()) {
?>
	<table id="artworkLinks">	
		<tr>
			<td><a href='./newartwork.php'>Submit New Artwork</a></td>
			<td><a href='./imageupload.php'>Upload Image(s)</a></td>
			<td><a href='./editartwork.php'>Edit Artwork</a></td>
		</tr>
	</table>
<?php 
// TODO: take away "!"

	?><div id="accordion"><?php
	// Get artistID
	$artist = $_SESSION['username'];
	$query = "SELECT personID FROM people WHERE CONCAT(firstname, ' ', lastname) = '$artist';";
	$personID = mysqli_query($db, $query);
	
	if($personID) {
		$id_array = mysqli_fetch_array($personID);
		$id = $id_array[0];
		
		printArtwork("WHERE a.artistID = $id","ORDER BY a.showNumber DESC", "$artist");
		
	}
	
	// Get the rest of the artists'
	$allQuery = "SELECT personID, firstname, lastname FROM people WHERE CONCAT(firstname,' ',lastname) <> '$artist' AND member = 1 ORDER BY firstname;";
	$artists = mysqli_query($db, $allQuery);
	if(!$artists) {
		die("<table><tr><td>Data Entry Error. <a href=''>Please try again.</a></td></tr>");
	} else {
		// Get all their work
		while($artist = mysqli_fetch_assoc($artists)) {			
			$artistName = "$artist[firstname] $artist[lastname]";
			printArtwork("WHERE a.artistID = $artist[personID]","ORDER BY a.showNumber DESC", $artistName);
		}
	}
	?>
	</div>
	<?php
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