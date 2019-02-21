<?php
// artwork.php
include "frontHeader.php";
?>

 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>

$(document).ready(function() {
	// select all checkboxes
	$("#allOrNone").on("change", function() {
		if(this.checked) {
			$(".membersCheckboxes").prop("checked", true);
		} else {
			$(".membersCheckboxes").prop("checked", false);
		}
	});
	
});

</script>

<div id="right_col">
<div class='headings'>Artwork</div>
<div class='center artwork'>
<?php 
function printArtwork($where, $order, $artistName, $oddInd) {
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
		$headerClass = $oddInd ? "artworkHeader" : "artworkHeaderEven";
		print "<h1 class='$headerClass'>$artistName</h1>";
		
		// Artist's works
		$tableClass = $oddInd ? "memberart" : "memberartEven";
		print "<table class='$tableClass artwork-table' >";

			print "<tr><th>Title</th><th>Medium</th><th>Year</th><th>Price</th><th>Show</th></tr>";
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
						$title = "<a class='links' href='../frontUploads/$filename' target='_blank'>$title</a>";
					}

					// Add "$" to price
					if(is_numeric($price))
						$price = "$$price";
						
					print "<tr><td>$title</td><td>$media</td><td>$y</td><td>$price</td><td>$show</td></tr>";
				}
			}
		print "</table>";
	}
}


if(isLoggedIn()) {
?>
	<div class="container-fluid" id="artworkLinks">	
		<div class="row">
			<div class="col-sm-12 center-it">
				<a class="links" href='./newartwork.php'>Submit New Artwork</a>
				<a class="links" href='./imageupload.php'>Upload Image(s)</a>
				<a class="links" href='./editartwork.php'>Edit Artwork</a>
			</div>
		</div>
	</div>
	<div class="spacer">&nbsp;</div>
	
	<?php
	// Process/Validate Filter
	$members = "";
	$validSelection = false;
	$all = "";
	if(isset($_POST['filterartwork'])) {
		if(!empty($_POST['allOrNone'])) {
			$all = "checked";
		}
		
		if(!empty($_POST['members'])) {
			$members = $_POST['members'];
			foreach($members as $member) {
				$member = addslashes($member);
			}
			$validSelection = true;
		}
	}
	?>
	
	<!-- Filter by member -->
	<form method='post' action='artwork.php'>
		<?php				
		// Print Member Checkboxes
		$query = "	SELECT DISTINCT CONCAT(firstname, ' ', lastname) AS 'username' FROM people p
					JOIN artwork a ON a.artistID = p.personID AND p.member <> 0
					ORDER BY member, firstname;";
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("Error in SQL statement." . mysqli_error($db));
		} else {
			$numrows = mysqli_num_rows($result);
			echo "<div class=\"row\">";
			// To create columns
			$t = 1;
			for($i = 0; $i < $numrows; $i++) {
				$row = mysqli_fetch_assoc($result);
				if($row) {
					$username = $row['username'];
					
					// Get selected members
					$checked = "";
					if($validSelection) {
						foreach($members as $member) {
							if($member === $username)
								$checked = "checked";
						}
					}
					
					echo "<div class=\"col-sm-3\"><input type='checkbox' class='membersCheckboxes' name='members[]' value='$username' $checked>$username</div>";
					// Creates 4 columns
					if($t % 4 == 0) echo "</div><div class=\"row\">";
					$t++;
				}
			}
			echo "</div>";
		}
		?>
		<div class="row">
			<div class="col-md-12 center-it">				
				<input type="checkbox" id="allOrNone" name="allOrNone" <?php echo "$all"; ?>>All or None
				<input type='submit' name='filterartwork' value='Filter Artwork'>
			</div>	
		</div>
	</form>
	
	<!-- End Filter by member -->
	<div id="accordion"><?php
	// Get artistID
	$artist = $_SESSION['username'];
	$query = "SELECT personID, firstname, lastname FROM people WHERE CONCAT(firstname, ' ', lastname) = '$artist';";
	$personResult = mysqli_query($db, $query);
	
	if($personResult) {
		$artistLoggedIn = mysqli_fetch_assoc($personResult);
		$artistName = "$artistLoggedIn[firstname] $artistLoggedIn[lastname]";
		$oddInd = true;
		if($validSelection) {
			foreach($members as $m) {
				if($artistName === $m) {
					printArtwork("WHERE a.artistID = $artistLoggedIn[personID]","ORDER BY a.showNumber DESC", "$artistName", $oddInd);
				}
			}
		} else {
			printArtwork("WHERE a.artistID = $artistLoggedIn[personID]","ORDER BY a.showNumber DESC", "$artistName", $oddInd);
		}		
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
			if($validSelection) {
				foreach($members as $m) {
					if($artistName === $m) {
						$oddInd = $oddInd ? false : true;			
						printArtwork("WHERE a.artistID = $artist[personID]","ORDER BY a.showNumber DESC", $artistName, $oddInd);
					}
				}
			} else {
				$oddInd = $oddInd ? false : true;			
				printArtwork("WHERE a.artistID = $artist[personID]","ORDER BY a.showNumber DESC", $artistName, $oddInd);
			}
		}
	}
	
	// Guest Artists
	$guestQuery = "SELECT personID, firstname, lastname FROM people WHERE CONCAT(firstname,' ',lastname) <> '$artist' AND member <> 1 AND member <> 0 ORDER BY firstname;";
	$artists = mysqli_query($db, $guestQuery);
	if(!$artists) {
		die("<table><tr><td>Data Entry Error. <a href=''>Please try again.</a></td></tr>");
	} else {
		// Get all their work
		while($artist = mysqli_fetch_assoc($artists)) {		
			$artistName = "$artist[firstname] $artist[lastname]";
			if($validSelection) {
				foreach($members as $m) {
					if($artistName === $m) {
						$oddInd = $oddInd ? false : true;
						printArtwork("WHERE a.artistID = $artist[personID]","ORDER BY a.showNumber DESC", $artistName, $oddInd);
					}
				}
			} else {
				$oddInd = $oddInd ? false : true;
				printArtwork("WHERE a.artistID = $artist[personID]","ORDER BY a.showNumber DESC", $artistName, $oddInd);
			}
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
</div>

<?php
include "frontFooter.php";
?>