<?php
include "frontHeader.php";

$username='';
$currentTitle='';
$errors=array();
$validation=false;

print "<div id='right_col'>";
print "<div class='headings'>Edit Artwork</div>";
print "<div class='center'>";

// Verify inputs
if(isset($_POST['changeartwork'])) {
	// Title
	if(!empty($_POST['currentTitle'])) {
		$currentTitle = addslashes($_POST['currentTitle']);	
		if(strlen(trim($currentTitle))==0)
			$errors['currentTitle']="Title cannot be blank.";
	} else {
		$errors['currentTitle']="This field is required.";
	}	
	
	// =============================================================	
	// NO ERRORS
	// =============================================================
	if(count($errors)==0) {
		
		
		if(count($errors)==0)
			$validation=true;
	}	
}




// Print message on successful entry / form if errors
if($validation==true) {
	// Successful Change
	print "<p>$_POST[currentTitle]: Your artwork info has been updated.</p>";
	print "<hr><p>Edit Another?</p>";
}
?>
<form method="post" action="">
	<table>
		<tr>
			<td>Current Title</td>
			<td><select name="currentTitle">
			<option value=''>Select Artwork</option>
			<?php
			$query = "SELECT a.title FROM artwork a 
						JOIN people p ON a.artistID = p.personID
						WHERE CONCAT(p.firstname, ' ', p.lastname) = '$_SESSION[username]';";
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("Could not find and artwork." . mysqli_error($db));
			} else {
				$numrows = mysqli_num_rows($result);
				for($i = 0; $i < $numrows; $i++) {
					$row = mysqli_fetch_array($result);
					if($row) {
						echo "<option value='$row[0]'>$row[0]</option>";
					}
				}
			}
			?>
			</select></td>
			<td>
				<small class='errorText'><?php echo array_key_exists('currentTitle',$errors) ? $errors['currentTitle'] : ''; ?></small>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="changeartwork" value="Change Artwork Info"></td>
		</tr>
	</table>
</form>

</div>

<?php
//}
include "frontFooter.php";
?>