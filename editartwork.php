<?php
// editartwork.php
// author jrs
// 2018
include "frontHeader.php";
?>

<div id="right_col">
<div class='headings'>Edit Artwork</div>
<div class='center'>

<?php 
if(isLoggedIn()) {
	$errors = array();
	// Get artistID
	$artist = $_SESSION['username'];
	$query = "SELECT personID FROM people WHERE CONCAT(firstname, ' ', lastname) = '$artist';";
	$personID = mysqli_query($db, $query);
	$selected = "";
	
	if($personID) {
		$id_array = mysqli_fetch_array($personID);
		$id = $id_array[0];
		
		// Get member's artwork info from database
		$query = "SELECT a.title, a.medium, a.yearMade, a.price, a.showNumber
				  FROM artwork a 
				  WHERE a.artistID = $id
				  ORDER BY a.title;";
		$artworkResult = mysqli_query($db, $query);
		
		if(!$artworkResult) {
			print "<h2>Database Error.</h2>";
		} else { // got member's artwork
			// Dropdown showing pieces to edit and edit button
			?>
			<form id="selectwork" method="post" action="">
				<table>
					<tr>
						<td>Select a work to edit</td>
						<td><select name="workSelected">
							<option value="">Select...</option>
						<?php
						if(isset($_POST['workSelected'])) {
							global $selected;
							$selected = $_POST['workSelected'];
						}
						while($work = mysqli_fetch_assoc($artworkResult)) {
							$n = $work['title'];
							if(isset($_POST['updatework']) && $n == $_POST['oldtitle']) {
								$n = $_POST['updatetitle'];
							}						
							if($n == $selected) {
								print "<option value='$n' selected>$n</option>";
							} else {
								print "<option value='$n'>$n</option>";
							}
						}
						?>
						</select>
						</td>
					</tr>
					<tr>
						<td></td><td><input type="submit" value="Edit Work" name="editwork"></td>
					</tr>
				</table>
			</form>
			<?php 
			if(isset($_POST['editwork']) || isset($_POST['updatework'])) {
				// TODO: validate entry
				// include showNumber in where clause to be sure we have the correct piece
				$editQuery = "SELECT a.artworkID, a.title, a.medium, a.yearMade, a.price, a.showNumber
						  FROM artwork a 
						  WHERE a.title = '$selected';";
				
				$editResult = mysqli_query($db, $editQuery);
				if(!$editResult) {
					die("Database error here.");
				} else {
					// This is the current info, to be edited
					$editWork = mysqli_fetch_assoc($editResult);
					
					// Get $workID, the artworkID to update
					$workID = $editWork['artworkID'];	
					
					if(isset($_POST['editwork'])) {
					?>
					<hr>					
					<form id="updateform" method="post" action="">
						<table>
							<tr>
								<th>Title</th>
								<td><input type="text" name="updatetitle" value="<?php echo "$editWork[title]";?>"></td>
								<td><small class='errorText'><?php echo array_key_exists('updatetitle',$errors) ? $errors['updatetitle'] : ''; ?></small></td>
							</tr>
							<tr>
								<th>Medium</th>
								<td><input type="text" name="updatemedium" value="<?php echo "$editWork[medium]";?>"></td>
							</tr>
							<tr>
								<th>Year</th>
								<td><input type="text" name="updateyear" value="<?php echo "$editWork[yearMade]";?>"></td>
							</tr>
							<tr>
								<th>Price</th>
								<td><input type="text" name="updateprice" value="<?php echo "$editWork[price]";?>"></td>
							</tr>
							<tr>
								<th>Show Number</th>
								<td><input type="text" name="updateshownumber" value="<?php echo "$editWork[showNumber]";?>"></td>
							</tr>
							<tr>
								<td></td><td><input type="submit" value="Submit Updates" name="updatework"></td>
							</tr>
							<tr>
								<td><input type="hidden" name="artworkid" value="<?php echo $workID; ?>" display="none">
								<input type="hidden" name="oldtitle" value="<?php echo $editWork[title]; ?>">
								</td>
								
							</tr>
						</table>
					</form>
					<?php		
					}	
					if(isset($_POST['updatework'])) {
						// TODO: validate entry
						if(!empty($_POST['updatetitle'])) {
							$newTitle = trim($_POST['updatetitle']);
							if(strlen($newTitle) == 0)
								$errors['updatetitle'] = "Enter a title";
						} else {
							$errors['updatetitle'] = "Enter a title";						
						}
						if(!empty($_POST['updatemedium'])) {
							$newMedium = $_POST['updatemedium'];
						}
						if(!empty($_POST['updateyear'])) {
							$newYear = $_POST['updateyear'];
						}
						if(!empty($_POST['updateprice'])) {
							$newPrice = $_POST['updateprice'];
						}
						if(!empty($_POST['updateshownumber'])) {
							$newShowNumber = $_POST['updateshownumber'];
						}
						$artworkID = $_POST['artworkid'];
						
						// Update Query
						$updateQuery = "UPDATE artwork SET title   = '$newTitle', 
										medium     = '$newMedium',
										yearMade   = '$newYear',
										price      = '$newPrice',
										showNumber = '$newShowNumber'
								WHERE artworkID = $artworkID;";
						$updateResult = mysqli_query($db, $updateQuery);
						if(!$updateResult) {
							die("Update Error. Unable to access the database.");
						} else {
							?>
							<hr>
							<table>
								<tr>
									<td>Artwork Updated Successfully!</td>
								</tr>
							</table>										
						<?php
							if(is_numeric($newPrice)) {
								$newPrice = "$$newPrice";
							}
							print "<p>$newTitle, $newYear</br>$newMedium</br>$newPrice</br>(Show: $newShowNumber)</p>";
						}
					}
				} 
			}
		 }
	} 
	?>

	<hr>
	<table>
		<tr>
			<td><a href='./artwork.php'>Back to Artwork</a></td>
		</tr>
	</table>
	<?php
} else {
	print "<h2><a href='./login.php?page=editartwork'>Log In to see your artwork info.</a></h2>";
}

?>
</div></div>

<?php
include "frontFooter.php";
?>