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
	$selectedArray = "";
	$selectedTitle = ""; 
	$selectedShow = "";
	$disabled="";
	$validWork=false;
	
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
			die("<h2>Database Error.</h2>");
		} else { // got member's artwork
			// Dropdown showing pieces to edit and edit button
			?>
			<form id="selectwork" method="post" action="">
				<table>
					<tr>
						<td>Select a work to edit</td>
					</tr>
					<tr>
						<td><select name="workSelected">
							<option value="">Select...</option>
						<?php
						// Populate drop-down
						if(isset($_POST['editwork']) || isset($_POST['deletework'])) {
							if(!empty($_POST['workSelected'])) {
								global $selectedTitle;
								global $selectedShow;	
								$s = $_POST['workSelected'];
								$selected = addslashes(trim($_POST['workSelected']));
								$selectedArray = explode(" ___ ", $selected);
								$selectedTitle = $selectedArray[0];
								$selectedShow = $selectedArray[1];
							} else {
								$errors['validwork'] = "Choose a work to edit or delete.";
							}
							
						}
						
						// Work is selected 
						if(count($errors) == 0)
							$validWork = true;
						
						while($work = mysqli_fetch_assoc($artworkResult)) {
							$n = $work['title'];
							$show = $work['showNumber'];														
							// Correct Updated Artwork for dropdown and display
							if(isset($_POST['updatework']) && ($n == $_POST['oldtitle'] )) { //|| $show == $_POST['oldshownumber']) {
								$n = $_POST['updatetitle'];
								$show = $_POST['updateshownumber'];
							}						
							// Only display on Dropdown menu if not deleted
							if(!(isset($_POST['submitdeletion']) && $n == $_POST['oldtitle'])) {
								if("${n} ___ ${show}" == $s) {
									print "<option value=\"${n} ___ ${show}\" selected>$n (Show $show)</option>";
								} else {
									print "<option value=\"${n} ___ ${show}\">$n (Show $show)</option>";
								}
							}
						}
						?>
						</select>
						</td>
					</tr>
					<tr>
						<td><small class='errorText'><?php echo array_key_exists('validwork',$errors) ? $errors['validwork'] : ''; ?></small></td>
					</tr>
					<tr>
						<td><input type="submit" value="Edit Work" name="editwork">
							<input type="submit" value="Delete Work" name="deletework"></td>
					</tr>
				</table>
			</form>
			<?php 					
			if($validWork && (isset($_POST['editwork']) || isset($_POST['updatework']) || isset($_POST['deletework']) || isset($_POST['submitdeletion']))) {
				// TODO: validate entry
				// include showNumber in where clause to be sure we have the correct piece
				$editQuery = "SELECT a.artworkID, a.title, a.medium, a.yearMade, a.price, a.showNumber
						  FROM artwork a 
						  WHERE a.title = '$selectedTitle'
						  AND a.showNumber = '$selectedShow';";
				
				$editResult = mysqli_query($db, $editQuery);
				if(!$editResult) {
					die("Database error here.");
				} else {
					// This is the current info, to be edited
					$editWork = mysqli_fetch_assoc($editResult);
					
					// Get $workID, the artworkID to update
					$workID = $editWork['artworkID'];	
					
					if(isset($_POST['editwork']) || isset($_POST['deletework'])) {
						if(isset($_POST['deletework']))
							$disabled="disabled";
					?>
					<hr>					
					<form id="updateform" method="post" action="">
						<table>
							<tr>
								<th>Title</th>
								<td><input type="text" name="updatetitle" value="<?php echo "$editWork[title]";?>" <?php echo $disabled; ?>></td>
								<td><small class='errorText'><?php echo array_key_exists('updatetitle',$errors) ? $errors['updatetitle'] : ''; ?></small></td>
							</tr>
							<tr>
								<th>Medium</th>
								<td><input type="text" name="updatemedium" value="<?php echo "$editWork[medium]";?>" <?php echo $disabled; ?>></td>
							</tr>
							<tr>
								<th>Year</th>
								<td><input type="text" name="updateyear" value="<?php echo "$editWork[yearMade]";?>" <?php echo $disabled; ?>></td>
							</tr>
							<tr>
								<th>Price</th>
								<td><input type="text" name="updateprice" value="<?php echo "$editWork[price]";?>" <?php echo $disabled; ?>></td>
							</tr>
							<tr>
								<th>Show Number</th>
								<td><input type="text" name="updateshownumber" value="<?php echo "$editWork[showNumber]";?>" <?php echo $disabled; ?>></td>
							</tr>
							<?php 
							$ok = "	<tr>
										<td colspan='10'><small class='errorText'>Selecting \"Delete\" will remove this piece from the database.</small></td>
									</tr>";
							if(isset($_POST['deletework'])) {
								$value = "Delete";
								$name = "submitdeletion";
								echo $ok;
							} else {
								$value = "Update";
								$name = "updatework";
							}
							?>
							<tr>
								<td></td><td><input type="submit" value="<?php echo "$value"; ?>" name="<?php echo "$name"; ?>"></td>
							</tr>
							<tr>
								<td><input type="hidden" name="artworkid" value="<?php echo $workID; ?>" display="none">
								<input type="hidden" name="oldtitle" value="<?php echo $editWork['title']; ?>">
								</td>
								
							</tr>
						</table>
					</form>
					<?php		
					} elseif(isset($_POST['updatework'])) {
						// Validate Title
						if(!empty($_POST['updatetitle'])) {
							$newTitle = addslashes(trim($_POST['updatetitle']));
							if(strlen($newTitle) == 0)
								$errors['updatetitle'] = "Enter a title";
						} else {
							$errors['updatetitle'] = "Enter a title";						
						}
						// Validate Medium
						if(!empty($_POST['updatemedium'])) {
							$newMedium = addslashes(trim($_POST['updatemedium']));
							if(strlen($newMedium) == 0)
								$errors['updatemedium'] = "Enter a medium";
						} else {
							$errors['updatemedium'] = "Enter a medium";						
						}
						
						// Validate Year
						if(!empty($_POST['updateyear'])) {
							$newYear = $_POST['updateyear'];
							if(strlen($newYear) == 0)
								$errors['updateyear'] = "Enter a year";
						} else {
							$errors['updateyear'] = "Enter a year";						
						}
						
						// Validate Price
						if(!empty($_POST['updateprice'])) {
							$newPrice = $_POST['updateprice'];
							if(strlen($newPrice) == 0)
								$errors['updateprice'] = "Enter a price";
						} else {
							$errors['updateprice'] = "Enter a price";						
						}
						
						// Validate Show Number
						if(!empty($_POST['updateshownumber'])) {
							$newShowNumber = $_POST['updateshownumber'];
							if(strlen($newShowNumber) == 0)
								$errors['updateshownumber'] = "Enter a shownumber";
						} else {
							$errors['updateshownumber'] = "Enter a shownumber";						
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
								$newTitle = str_replace("\'", "'", $newTitle);
								$newMedium = str_replace("\'", "'", $newMedium);
							}
							print "<p>$newTitle, $newYear</br>$newMedium</br>$newPrice</br>(Show: $newShowNumber)</p>";
						}
					} elseif(isset($_POST['submitdeletion'])) {
						$artworkID = $_POST['artworkid'];
						$title = $_POST['oldtitle'];
						
						// Query for deletion
						$deletionQuery = "DELETE FROM artwork WHERE artworkID = '$artworkID';";
						
						// Delete Work
						$deleteWork = mysqli_query($db, $deletionQuery);
						
						// Tell user what happened
						if(!$deleteWork)
							die("Deletion Failed. Try again or contact label people");
						else {
						?>
							<hr>
							<table>
								<tr>
									<td>Deleted<?php echo " <em>$title</em>"; ?> Successfully!</td>
								</tr>
							</table>	
						<?php
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