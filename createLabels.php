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

$errors = array();

// Check that admin is logged in
if(adminIsUser()) {
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
	} else {
?>
	<form method="post" action="" autocomplete='off'>
		<table>
			<tr>
				<td>Show Number</td>
				<td><input type='text' name='showNumber' value="<?php isset($_POST['showNumber']) ? $_POST['showNumber'] : '';?>"></td>
			</tr>
			<tr>
				<td><small class='errorText'><?php echo array_key_exists('showNumber',$errors) ? $errors['showNumber'] : ''; ?></small></td>
			</tr>
			<tr>
				<td></td><td><input type='submit' name='showForm' value="Create Labels"></td>
			</tr>
		</table>
	</form>
	
<?php
	}
} else {
	print "<div class='headings'><a href='./login.php'>Please Log In as Admin to proceed</a></div>";
}
print "</div>";
include "frontFooter.php";
?>