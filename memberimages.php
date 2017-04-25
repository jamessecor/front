<?php
include "frontHeader.php";

// Database Set-up
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";

// Displays member images given a show number and a member array of >= 1
// TODO: implement
function displayImages($query, $memberArray) 
{
	global $db;
	global $query;
	
	$data = mysqli_query($db, $query);
	if(!$data) {
		//print "Images could not be retrieved.";
	} else {
		// Number of images
		$numrows = mysqli_num_rows($data);
		print "<table>";
		for($i = 0; $i < $numrows; $i++) {
			$row = mysqli_fetch_assoc($data);
			if($row) {
				$filepath = "./uploads/" . $row['filename'];
				print "<tr><td><em>$row[title]</em>, $row[yearMade]. $row[medium]<br>$row[member]</td></tr>";
				print "<tr><td><img width='60%' src='$filepath' alt='No Image'></td></tr>";
				//print "<div id='label'><em>$row[title]</em>, $row[yearMade]. $row[medium]<br>$row[member]</div><br>";
				//print "<img width='60%' src='$filepath' alt='No Image'><br><br>";
			}
		}
		print "</table>";
		?>
		<div style="text-align:center;">
			<button id='toTop'>Back to Top</button>
			<button id="toImgSelection">Back to Image Selection</button>
			<script>
			document.getElementById("toImgSelection").addEventListener("click", function() {
				window.location.href = 'memberImages.php';
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
	}
	
	// Show number has been selected
	if($validation) {
		if(isset($_POST['gogogo'])) {
			$query = "SELECT CONCAT(m.firstname, ' ', m.lastname) AS 'member', a.title, a.yearMade, a.medium, a.filename 
				FROM artwork a 
				JOIN people m ON a.artistID = m.personID 
				WHERE a.filename IS NOT NULL
				ORDER BY m.firstname;";
		} else {
			$query = "SELECT CONCAT(m.firstname, ' ', m.lastname) AS 'member', a.title, a.yearMade, a.medium, a.filename 
				FROM artwork a 
				JOIN people m ON a.artistID = m.personID 
				WHERE showNumber = '$showNum' AND a.filename IS NOT NULL
				ORDER BY m.firstname;";
		}
		displayImages($query, 'xxx');  // 2nd parameter (member list) not used
	} else {
?>




<form method='post' action=''>
	<table>
		<tr>
			<td></td><td colspan=2><small class='errorText'><?php echo array_key_exists('showNumber',$errors) ? $errors['showNumber'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>Show Number:</td>
			<td>
			<select name='showNumber' value="<?php echo isset($_POST['showNumber']) ? $_POST['showNumber'] : ''; ?>">
			<option value=''>Choose Show</option>
			<?php
			$query = "SELECT showNumber FROM artwork WHERE filename IS NOT NULL ORDER BY showNumber DESC;";
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
			<td>or</td>
			<td><input type='submit' name='gogogo' value='Show All Images'></td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='go' value='Go'></td>
		</tr>
	</table>
</form>
</div></div>
<?php
	}
} else {
	print "<div class='headings'><a href='./login.php'>Please Log In</a></div>";
}
mysqli_close($db);
include "frontFooter.php";
?>