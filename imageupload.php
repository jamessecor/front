<?php
include "frontHeader.php";
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";

print "<div id='right_col'>";
print "<div class='headings'>Image Upload</div>";
print "<div class='center'>";

if(isLoggedIn()) {
	$errors = array();
	$validation = false;
	
	if(isset($_POST['upload'])) {
		if(!empty($_POST['title'])) {
			$title = $_POST['title'];
		} else {
			$errors['title'] = 'Title not recognized.';
		}
		if(count($errors) == 0) {
			$validation = true;
		}
	} 
	
	if($validation) {
		// ======================
		// Begin Image Processing
		// Code from https://www.w3schools.com/php/php_file_upload.asp
		// ======================
		
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES["filename"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image		
		$check = getimagesize($_FILES["filename"]["tmp_name"]);
		if($check !== false) {
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
		
		// Check if file already exists
		if (file_exists($target_file)) {
			echo "Sorry, file already exists.";
			$uploadOk = 0;
		}
		
		// Check file size
		if ($_FILES["filename"]["size"] > 1000000) {
			echo "Sorry, your file is too large.";
			$uploadOk = 0;
		}

		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}
		
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["filename"]["tmp_name"], $target_file)) {
				$filename = basename($_FILES["filename"]["name"]);
				$imgLocation = "uploads/" . $filename;
				
				// Send to the database
				$query = "UPDATE artwork SET filename = '$filename' WHERE title = '$title';";
				$result = mysqli_query($db, $query);
				if(!$result) {
					die("Connection error" . mysqli_error($db));
				} else {
					// Print success message
					echo "<table><tr><td>The file $filename has been uploaded.</td></tr>";
					echo "<tr><td><a href='$imgLocation' target='_blank'>Preview Image</a> | <a href='./artwork.php'>Back to Artwork</a></td></tr></table>";
				}
			} else {
				echo "Sorry, there was an error uploading your file.";
			}
		}
		
		// ====================
		// End image processing
		// ====================
	} else {

?>

<form method='post' enctype='multipart/form-data'>
	<table>
		<tr>
			<td>Member:</td>
			<td><?php print "$_SESSION[username]";?></td>
		<tr>
			<td>Select Work:</td>
			<td><select name="title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : '';  ?>">
				<option value=''>select work</option>
				<?php
				$query =   "SELECT a.title 
							FROM artwork a 
							JOIN people p ON a.artistID = p.personID
							WHERE CONCAT(p.firstname, ' ', p.lastname) = '$_SESSION[username]';";
				$result = mysqli_query($db, $query);
				if(!$result) {
					$errors['artwork'] = "Error in SQL statement." . mysqli_error($db);
				} else {
					$numrows = mysqli_num_rows($result);
					for($i = 0; $i < $numrows; $i++) {
						$row = mysqli_fetch_assoc($result);
						if($row) {
							$title = $row['title'];
							if($_POST['title']==$title)
								echo "<option value='$title' selected ='selected'>$title</option>";
							else
								echo "<option value='$title'>$title</option>";
						}
					}
				}
				?>
			</select></td>
			<td><small class='errorText'><?php echo array_key_exists('title',$errors) ? $errors['title'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>Find File:</td>
			<td colspan=2><input type='file' name='filename'></td>
			<td><small class='errorText'><?php echo array_key_exists('filename',$errors) ? $errors['filename'] : ''; ?></small></td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='upload' value='Upload'></td>
		</tr>
		<tr></tr><tr>
			<td></td><td colspan=2>| <a href='./artwork.php'>Back to Artwork</a> |</td>
		</tr>
	</table>
</form>

<?php
	}
} else {
	print "<div class='headings'><a href='./login.php'>Please Log In to proceed</a></div>";
}
print "</div>";
include "frontFooter.php";
?>