<?php
include "frontHeader.php";
include "currentShow.php";

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
		$path = "labels";
		$filename = "labels" . $showNumber . ".html";
		$filepath = $path . "/" . $filename;
		$fileptr = fopen($filepath, "w") or die("Unable to open file.");
		
		// Get number of rows for loop
		$numrows = mysqli_num_rows($works);
		
		// If no artwork
		if($numrows == 0) {
			print "<h1>No Artwork in show $showNumber. <a class='links' href='./createLabels.php'>Back to Create Labels</a></h1>";
		} else {
			// Loop through and print labels
			for($i = 0; $i < $numrows; $i++) {
				$piece = mysqli_fetch_assoc($works);
				if($piece) {
					$artistx = mb_convert_encoding($piece['artist'], "Windows-1252", "UTF-8");
					$titlex = mb_convert_encoding($piece['title'], "Windows-1252", "UTF-8");
					$label = "<strong>$titlex<br>$artistx</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$piece[yearMade]<br>$piece[medium]<br>";
					if(is_numeric($piece['price'])) {
						$label = $label . "$";
					}
					$label = $label . "$piece[price]<br><br><br>";
					fwrite($fileptr, $label);
				}
			}
			fclose($fileptr);
			?>
			<div class="row">
				<div class="col-12 center-it">
					Successfully created!
				</div>
				<div class="col-12 center-it">
					Labels created and saved as "<?php echo $filename; ?>" in the "<?php echo $path; ?>" folder.
				</div>
				<div class="col-12 center-it">
					<a class="links" href="./<?php echo $filepath; ?>" target="_blank">Preview Labels</a>
				</div>
			</div>
			<?php
			// print "<h1>Labels created and saved as \"$filename\". ";
			// print "<a href='./$filename' target='_blank'>Preview Labels</a></h1>";
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
		<div class="row">
			<div class="col-md center-it">Show Number</div>
				<div class="col-md center-it">
				<select name='showNumber'>
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
				</div>
			</div>
			<div class="row">
				<div class="col-md center-it"><small class='errorText'><?php echo array_key_exists('showNumber',$errors) ? $errors['showNumber'] : ''; ?></small></div>
			</div>
			<div class="spacer">&nbsp;</div>
			<div class="row">
				<div class="col-md center-it"><input type='submit' name='showForm' value="Create Labels"></div>
			</div>		
	</form>
	<div class="spacer">&nbsp;</div>
	<div class="row">
		<div class="col-md center-it"><a id="toggleMissingLabelInfo">Show Missing Artists</a></div>
	</div>
	<div class="spacer">&nbsp;</div>
	<table style="display:none" id='missingLabelInfo'>
		<div class="row"><th>Need Labels From...</th></tr>
		<?php
		global $currentShow;
		
		$query = "select distinct firstname, lastname from people 
					where member = 1 AND lastname not in (
						select distinct p.lastname FROM people p
						left OUTER JOIN artwork a ON p.personID = a.artistID
						where a.showNumber = ${currentShow}
						) ";
		$result = mysqli_query($db, $query);
		if(!$result)
			$errors['missingLabels'] = "Error in SQL statement." . mysqli_error($db);
		else {
			$numrows = mysqli_num_rows($result);
			for($i = 0; $i < $numrows; $i++) {
				$missingArtist = mysqli_fetch_assoc($result);
				if($missingArtist) {
					print "<div class=\"row\"><td>$missingArtist[firstname] $missingArtist[lastname]</td></tr>";
				}
			}
			print "<div class=\"row\"><td><em>Total missing:</em> $numrows</td></tr>";
		}
		?>
		
	</table>
	</div>
	<script>
	$(document).ready(function() {
		$("#toggleMissingLabelInfo").on("click", function() {
			$("#missingLabelInfo").toggle();
		});
	});
	</script>
<?php
	}
	?>
	<div class="row">
		<div class="col-12 center-it">
			<a class="links" href="./labels.php">Back to Labels</a></td>
		</div>
	</div>
	<?php
} else {
?>
<table>
	<div class="row">
		<td><a href="./login.php?page=createLabels">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
print "</div></div>";
include "frontFooter.php";
?>