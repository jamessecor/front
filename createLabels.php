<?php
include "frontHeader.php";
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";

print "<div id='right_col'>";
print "<div class='headings'>Create Show Labels</div>";
print "<div class='center'>";

function createLabels($showNumber) {
	global $db;
	$query = "SELECT a.title, a.medium, a.yearMade, a.price, CONCAT(p.firstname, ' ', p.lastname) AS 'artist'
			  FROM artwork a 
			  JOIN people p ON a.artistID = p.personID
			  WHERE showNumber = '$showNumber';";
	$works = mysqli_query($db, $query);
	
	if(!$works)
		die("Data could not be reached.");
	else {
		// Create or open file
		$filename = "labels" . $showNumber . ".txt";
		$fileptr = fopen($filename, "w") or die("Unable to open file.");
		
		// Get number of rows for loop
		$numrows = mysqli_num_rows($works);
		
		// If no artwork
		if($numrows == 0) {
			print "<h1>No Artwork in show $showNumber. <a href='./createLabels.php'>Back to Create Labels</a></h1>";
		} else {
			// Loop through and print labels
			for($i = 0; $i < $numrows; $i++) {
				$piece = mysqli_fetch_assoc($works);
				if($piece) {
					$label = "$piece[title]\n$piece[artist]\t$piece[yearMade]\n$piece[medium]\n$$piece[price]\n\n";
					fwrite($fileptr, $label);
				}
			}
			fclose($fileptr);
			print "<h1>Labels created and saved as \"$filename\". ";
			print "<a href='./$filename' target='_blank'>Preview Labels</a></h1>";
		}
	}
}

$errors = array();

// Check that admin is logged in
if(labelCreatorIsUser()) {
	if(isset($_POST['showForm'])) {
		if(!empty($_POST['showNumber'])) {
			$show = $_POST['showNumber'];
			if(strlen($show) == 0)
					$errors['showNumber'] = "Please Enter a show number.";
		} else {
			$errors['showNumber'] = "Enter a valid show number.";
		}	
	
		if(count($errors) == 0) {
			createLabels($show);
		}
	
	// If no show clicked, error
	
	} else {
?>
	<div class='form'>
	<form method="post" action="" autocomplete='off'>
		<table>
			<tr>
				<td>Show Number</td>
				<td>
				<select name='showNumber'>
				<option value=''>Choose Show</option>
				<?php
				$query = "SELECT DISTINCT showNumber FROM artwork WHERE title IS NOT NULL ORDER BY showNumber DESC;";
				$result = mysqli_query($db, $query);
				if(!$result)
					$errors['showNumber'] = "Error in SQL statement." . mysqli_error($db);
				else {
					$numrows = mysqli_num_rows($result);
					for($i = 0; $i < $numrows; $i++) {
						$show = mysqli_fetch_array($result);
						if($show) {
							$n = $show[0];
							print "<option value='$n'>$n</option>";
						}
					}
				}
				?>
				</select>
				</td>
			</tr>
			<tr>
				<td><small class='errorText'><?php echo array_key_exists('showNumber',$errors) ? $errors['showNumber'] : ''; ?></small></td>
			</tr>
			<tr>
				<td></td><td><input type='submit' name='showForm' value="Create Labels"></td>
			</tr>
		</table>
	</form>
	</div>
<?php
	}
} else {
	print "<div class='headings'><a href='./login.php'>Please Log In as with Label Priveleges to proceed</a></div>";
}
print "</div></div>";
include "frontFooter.php";
?>