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
    $message = false;
    if(isset($_GET['artworkUpdated'])) {
        $message = "$_GET[artworkUpdated] successfully updated";
    } elseif(isset($_GET['artworkDeleted'])) {
        $message = "$_GET[artworkDeleted] successfully deleted.";
    }
    if($message) {
    ?>
        <div class="flash-message"><?php echo $message; ?></div>
    <?php
    }

	$errors = array();
	// Get artistID
	$artist = $_SESSION['username'];
	$query = "SELECT personID FROM people WHERE CONCAT(firstname, ' ', lastname) = '$artist';";
	$personID = mysqli_query($db, $query);
	$selectedArray = "";
	$selectedTitle = ""; 
	$selectedShow = "";
	$artworkID = "";
	$selectedArtworkID = "";
	$disabled="";
	$validWork=false;
	
	if($personID) {
		$id_array = mysqli_fetch_array($personID);
		$id = $id_array[0];
		
		// Get member's artwork info from database
		$query = "SELECT a.artworkID, a.title, a.medium, a.yearMade, a.price, s.id, s.showName
				  FROM artwork a 
				  INNER JOIN shows s on a.showNumber = s.id
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
								global $selectedArtworkID;
								$selectedArtworkID = $_POST['workSelected'];
							} else {
								$errors['validwork'] = "Choose a work to edit or delete.";
							}
						}
						
						// Work is selected 
						if(count($errors) == 0)
							$validWork = true;
						
						while($work = mysqli_fetch_assoc($artworkResult)) {
							$n = $work['title'];
							$show = $work['showName'];
							$artworkID = $work['artworkID'];
							// Correct Updated Artwork for dropdown and display
							if(isset($_POST['updatework']) && ($n == $_POST['oldtitle'] )) { //|| $show == $_POST['oldshownumber']) {
								$n = $_POST['updatetitle'];
								$show = $_POST['updateshownumber'];
							}						
							// Only display on Dropdown menu if not deleted
							if(!(isset($_POST['submitdeletion']) && $n == $_POST['oldtitle'])) {
								if($artworkID == $selectedArtworkID) {
									print "<option value=\"${artworkID}\" selected>$n (Show $show)</option>";
								} else {
									print "<option value=\"${artworkID}\">$n (Show $show)</option>";
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
						<td>
                            <input type="submit" value="Edit Work" name="editwork">
							<input type="submit" value="Delete Work" name="deletework">
                        </td>
					</tr>
				</table>
			</form>
           
            <?php 
            // Do the processing
            if($validWork && (isset($_POST['editwork']) || isset($_POST['deletework']))) {
                include "editartwork_form.php";
            } elseif(isset($_POST['updatework'])) {
                include "updateartwork.php";
            } elseif(isset($_POST['submitdeletion'])) {
                include "deleteartwork.php";
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
<script>
    $(document).ready(function() {
        $(".flash-message").fadeOut(3000, function() {
            window.location.href = window.location.href.split("?")[0];
        });
    });
</script>