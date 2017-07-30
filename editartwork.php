<?php
include "frontHeader.php";

$username='';
$currentTitle='';
$errors=array();
$newErrors=array();
$validation=false;
$newValidation = false;

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
		$validation=true;
	}	
} else if(isset($_POST['submitChanges'])) {	
	// Title
	if(!empty($_POST['newTitle'])) {
		$title = addslashes(trim($_POST['newTitle']));
		if(strlen($title) == 0)
			$newErrors['newTitle'] = "Please Enter a Title.";
	} else {
		$newErrors['newTitle'] = "Please Enter a Title.";
	}
	
	// Date complete (Year)	
	if(!empty($_POST['newYear'])) {
		$year = trim($_POST['newYear']);
		if(strlen($year) == 0)
			$newErrors['newYear'] = "Please Enter a Year";		
	} else {
		$newErrors['newYear'] = "Please Enter a Year";
	}
	
	// Medium/Media
	if(!empty($_POST['newMedia'])) {
		//$media = trim($_POST['media']);
		$media = addslashes(trim($_POST['newMedia']));
		if(strlen($media) == 0) 
			$newErrors['newMedia'] = "Please Enter a Medium or Media";
	} else {
		$newErrors['newMedia'] = "Please Enter a Medium or Media";
	}

	// Price	
	if(!empty($_POST['newPrice'])) {
		$price = trim($_POST['newPrice']);
		if(strlen($price) == 0) {
			$newErrors['newPrice'] = "Please Enter a Dollar Amount";
		} 
	} else {
		$newErrors['newPrice'] = "Please Enter a Dollar Amount";
	}
	
	// =============================================================	
	// NO ERRORS
	// =============================================================
	if(count($newErrors)==0) {		
		$newValidation=true;
	}	
	
	if($newValidation) {
		
		print "<hr><p>Edit Another?</p>";
	}
}

// New form to change artwork
if($validation==true) {
	// Successful Change
	// Get Current Artwork Info
	$query = "SELECT * FROM artwork WHERE title = '$_POST[currentTitle]';";
	// TODO: Do I worry about duplicate titles ("Untitled")
	$result = mysqli_query($db, $query);
	if(!$result)
		$result = array();
	else {
		$piece = mysqli_fetch_assoc($result);		
	}
	?>
	<form method="post" action="">
	<table>
		<tr>
			<th></th><th>New</th><th>Current</th>
		</tr>
		<tr>
			<td><label for="artist">Artist</label></td>
			<td><?php echo $_SESSION['username']; ?></td>			
		<tr>
			<td><label for="title">Title</label></td>
			<td><input type="text" name="newtitle" placeholder="New Title"></td>
			<td><?php echo $piece['title'] ? $piece['title'] : ''; ?></td>
		</tr>
		<tr>
			<td><small class='errorText'><?php echo array_key_exists('newTitle',$errors) ? $errors['newTitle'] : ''; ?></small></td>
		</tr>
		<tr>
			<td><label for="media">Medium</label></td>
			<td><input type="text" name="newMedia" placeholder="New Media"></td>
			<td><?php echo $piece['medium'] ? $piece['medium'] : ''; ?></td>
		</tr>
		<tr>
			<td><small class='errorText'><?php echo array_key_exists('newMedia',$errors) ? $errors['newMedia'] : ''; ?></small></td>
		</tr>
		<tr>
			<td><label for="year">Year Made</label></td>
			<td><input type="text" name="newYear" placeholder="New Year"></td>
			<td><?php echo $piece['yearMade'] ? $piece['yearMade'] : ''; ?></td>
		</tr>
		<tr>
			<td><small class='errorText'><?php echo array_key_exists('newYear',$errors) ? $errors['newYear'] : ''; ?></small></td>
		</tr>
		<tr>
			<td><label for="price">Price</label></td>
			<td><input type="text" name="newPrice" placeholder="New Price"></td>
			<td><?php echo $piece['price'] ? $piece['price'] : ''; ?></td>
		</tr>
		<tr>
			<td><small class='errorText'><?php echo array_key_exists('newPrice',$errors) ? $errors['newPrice'] : ''; ?></small></td>
		</tr>
		<tr>		
			<td></td><td>
				<input type="submit" name="submitChanges" value="Submit Changes">
			</td>
		</tr>
	</table>
	</form>
	<?php
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
			<td><input type="submit" name="changeartwork" value="Edit Artwork Info"></td>
		</tr>
	</table>
</form>

</div>

<?php
//}
include "frontFooter.php";
?>