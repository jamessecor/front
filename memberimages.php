<?php
// memberimages.php
// author James Secor
// TODO: convert to using only one submit button
// drop-downs as filters
include "frontHeader.php";

// Displays member images given a show number and a member array of >= 1
// TODO: implement
function displayImages($query, $memberArray) 
{
	global $db;
	global $query;
	
	$data = mysqli_query($db, $query);
	if(!$data) {
		die("Error in image selection: " . mysqli_error($db));
		//print "Images could not be retrieved.";
	} else {
		// Number of images
		$numrows = mysqli_num_rows($data);
		print "<div class='imagePage'>";
		for($i = 0; $i < $numrows; $i++) {
			$row = mysqli_fetch_assoc($data);
			if($row) {
				$filepath = "../frontUploads/" . $row['filename'];
				print "<a href='$filepath' target='_blank'><img width='50%' src='$filepath' alt='No Image'></a>";
				print "<table><tr><td><em>$row[title]</em>, $row[yearMade]. $row[member]. $row[medium], Show $row[showNumber]</td></tr></table><br><br>";
				
			}
		}
		print "</div>";
		?>
		<div class="imagePage">
			<button id='toTop'>Back to Top</button>
			<script>			
			document.getElementById("toTop").addEventListener("click", function() {
				window.location.href = '#';
			});
			</script>
		</div>
		<br><br>
		<?php
	}
}

?>

<div id='right_col'>
<div class='headings'>Images</div>
<div class='center'>
<?php
if(isLoggedIn()) {
	$errors = array();
	$showNum= '';
	$members = "";
	$validSelection = false;
	
	if(isset($_POST['viewimages'])) {
		if(!empty($_POST['showNumber'])) {
			$showNum = addslashes(trim($_POST['showNumber']));
		}
		if(!empty($_POST['members'])) {			
			$members = $_POST['members'];
			foreach($members as $m) {
				$m = addslashes($m);
			}
			$validSelection = true;
		}
	}
?>
<table><tr><td>
<a href='imageupload.php'>Upload New Image</a>
</td></tr></table>
<hr>
<form method='post' action=''>
	<table>
		<?php				
		// Print Member Checkboxes
		$query = "	SELECT DISTINCT CONCAT(firstname, ' ', lastname) AS 'username' FROM people p
					JOIN artwork a ON a.artistID = p.personID AND a.filename IS NOT NULL
					ORDER BY firstname;";
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("Error in SQL statement." . mysqli_error($db));
		} else {
			$numrows = mysqli_num_rows($result);
			echo "<tr>";
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

					echo "<td><input type='checkbox' class='membersCheckboxes' name='members[]' value='$username' $checked>$username</td>";
					// Creates 4 columns
					if($t % 4 == 0) echo "</tr><tr>";
					$t++;
				}
			}
			echo "</tr>";
		}
		?>
	</table>
	<table>
		<tr>
			<!-- Select show number -->
			<td>
			<select name='showNumber'>
			<option value=''>Show Number</option>
			<?php
			$query = "SELECT DISTINCT showNumber FROM artwork WHERE filename IS NOT NULL ORDER BY showNumber DESC;";
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
		<tr><td>&nbsp;</td></tr>
		<tr>			
			<td colspan='4'><input type='submit' name='viewimages' value='Filter Images'></td>
		</tr>
	</table>
</form>
<?php
// Show images
$where = "WHERE a.filename IS NOT NULL";
if($showNum !== "") {
	$where = $where . " AND showNumber = '$showNum'";
}
if($members !== "") {
	$membersIn = "";
	foreach($members as $m) {
		if($membersIn === "") {
			$membersIn = "'$m'";
		} else {
			$membersIn .= ", '$m'";
		}			
	}
	$where = $where . " AND CONCAT(m.firstname, ' ', m.lastname) IN ($membersIn) AND a.filename IS NOT NULL";
}
$query = "SELECT CONCAT(m.firstname, ' ', m.lastname) AS 'member', a.title, a.yearMade, a.medium, a.filename, a.showNumber 
		FROM artwork a 
		JOIN people m ON a.artistID = m.personID 
		$where
		ORDER BY m.firstname;";
displayImages($query, 'xxx');  // 2nd parameter (member list) not used

?>

<!-- upload -->
<hr>
<table><tr><td>
<a href='imageupload.php'>Upload New Image</a>
</td></tr></table>
</div></div>
<?php
	
} else {
?>
<table>
	<tr>
		<td><a href="./login.php?page=memberimages">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
include "frontFooter.php";
?>