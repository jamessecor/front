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
				$filepath = "./uploads/" . $row['filename'];
				print "<a href='$filepath' target='_blank'><img width='75%' src='$filepath' alt='No Image'></a>";
				print "<div id='label'><em>$row[title]</em>, $row[yearMade]. $row[medium]<br>$row[member]</div><br><br>";
				
			}
		}
		print "</div>";
		?>
		<div class="imagePage">
			<button id='toTop'>Back to Top</button>
			<button id="toImgSelection">Back to Image Selection</button>
			<script>
			document.getElementById("toImgSelection").addEventListener("click", function() {
				window.location.href = 'memberimages.php';
			});
			
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
	$validation = false;
	$member= '';
	$showNum= '';
	
	if(isset($_POST['go'])) {
		if(!empty($_POST['showNumber'])) {
			$showNum = $_POST['showNumber'];
		} else {
			$errors['showNumber'] = "Select a show number";
		}
		if(count($errors)===0) {
			$validation = true;
		}
	} else if(isset($_POST['gogogo'])) {
		$validation = true;
	} else if(isset($_POST['gomember'])) {
		if(!empty($_POST['member'])) {
			$member = $_POST['member'];
		} else {
			$errors['member'] = "Select a member";
		}
		if(count($errors)===0)
			$validation = true;
	}
	
	// Show number has been selected
	if($validation) {
		if(isset($_POST['gogogo'])) {
			$query = "SELECT CONCAT(m.firstname, ' ', m.lastname) AS 'member', a.title, a.yearMade, a.medium, a.filename 
				FROM artwork a 
				JOIN people m ON a.artistID = m.personID 
				WHERE a.filename IS NOT NULL
				ORDER BY m.firstname;";
		} else if(isset($_POST['go'])) {
			$query = "SELECT CONCAT(m.firstname, ' ', m.lastname) AS 'member', a.title, a.yearMade, a.medium, a.filename 
				FROM artwork a 
				JOIN people m ON a.artistID = m.personID 
				WHERE showNumber = '$showNum' AND a.filename IS NOT NULL
				ORDER BY showNumber;";
		} else if(isset($_POST['gomember'])) {
			$query = "SELECT CONCAT(m.firstname, ' ', m.lastname) AS 'member', a.title, a.yearMade, a.medium, a.filename 
				FROM artwork a 
				JOIN people m ON a.artistID = m.personID 
				WHERE CONCAT(m.firstname, ' ', m.lastname) = '$member' AND a.filename IS NOT NULL
				ORDER BY m.firstname;";
		}
		displayImages($query, 'xxx');  // 2nd parameter (member list) not used
	} else {
?>




<form method='post' action=''>
	<table>
		<tr>
			<th colspan=7>Select Images By:</th>
		</tr>
		<tr>
			<td colspan=3><small class='errorText'><?php echo array_key_exists('showNumber',$errors) ? $errors['showNumber'] : ''; ?></small></td>
			
			<td colspan=2><small class='errorText'><?php echo array_key_exists('member',$errors) ? $errors['member'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>Filters</td>
		</tr>
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
			<td><input type='submit' name='go' value='Go'></td>
		</tr>
		</tr>	
			<!-- Select member -->
			<td>
			<select name='member'>
			<option value=''>Member</option>
			<?php
			$query = "SELECT DISTINCT CONCAT(p.firstname, ' ', p.lastname)
					  FROM people p
					  JOIN artwork a ON p.personID = a.artistID
					  WHERE a.filename IS NOT NULL ORDER BY p.firstname;";
			$result = mysqli_query($db, $query);
			if(!$result)
				die("Error in SQL statement." . mysqli_error($db));
			else {
				$numrows = mysqli_num_rows($result);
				for($i = 0; $i < $numrows; $i++) {
					$artist = mysqli_fetch_array($result);
					if($artist) {
						$n = $artist[0];
						print "<option value='$n'>$n</option>";
					}
				}
			}
			?>
			</select>
			</td>
			<td><input type='submit' name='gomember' value='Go'></td>
		</tr>
		<tr>			
			<!-- Select all images -->
			<td>Show All Images</td>
			<td><input type='submit' name='gogogo' value='Go'></td>
		</tr>
	</table>
</form>

<!-- upload -->
<hr>
<table><tr><td>
<a href='imageupload.php'>Click to Upload New Image</a>
</td></tr></table>
</div></div>
<?php
	}
} else {
	print "<div class='headings'><a href='./login.php'>Please Log In</a></div>";
}
include "frontFooter.php";
?>