<?php
include "frontHeader.php";

print "<div id='right_col'>";
print "<div class='headings'>Image Upload</div>";
print "<div class='center'>";

if(isLoggedIn()) {
	$errors = array();
	$validation = false;
	
	if(isset($_POST['upload'])) {
		if(!empty($_POST['title'])) {
			$title = trim(addslashes($_POST['title']));
		} else {
			$errors['title'] = 'Title not recognized.';
		}
		if(empty($_FILES['filename'])) {
			$errors['filename'] = "Please Select a File.";
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
		
		$target_dir = "../frontUploads/";
		$target_file = $target_dir . basename($_FILES["filename"]["name"]);
		$uploadOk = 1;
		
		$path_parts = pathinfo($target_file);  // new line added by JDS
		//$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);   original code. 
		$imageFileType = $path_parts['extension'];
        // line above was suggested in PHP manual pages online. pathinfo returns an array!		
		
		// Check if image file is a actual image or fake image		
		$tmp_file = $_FILES["filename"]["tmp_name"];
		$check = getimagesize($_FILES["filename"]["tmp_name"]);
		//print "Checking image size of $tmp_file. Size is $check[0]<br>";
		
		if($check !== false) {
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
		
		// Check if file already exists
		if (file_exists($target_file)) {
			echo "File already exists.";
			$uploadOk = 0;
		}
		
		// Check file size
		if ($_FILES["filename"]["size"] > 10000000) {           // I changed this to 10 MB to accomodate hi res images. 
			echo "Your file is too large.";              // I also had to change the setting in PHP.ini for max upload size
			$uploadOk = 0;
		}

		// Allow certain file formats    // JDS: I had to change these to uppercase. 
		$imageFileType = strtoupper($imageFileType);
		if($imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG"
		&& $imageFileType != "GIF" ) {
			echo "Image File type is $imageFileType. Only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}
		
		// Additional Error Checking Added by Joan: 
		if ($_FILES['filename']['error'] > 0) {
			$uploadOk = 0;
			switch ($_FILES['filename']['error']) {
				case 1:
					echo "File exceeded upload_max_filesize";   break;  
				case 2:
					echo "File exceeded max_file_size.";     break;
				case 3: 
					echo "File ony partially uploaded.";  break;
				case 4:
					echo "No file uploaded.";  break;     
				case 6: 
					echo "No temp directory specified.";  break;
				case 7:
					echo "Upload failed. Cannot write to disk";    break;          
				case 8:
					echo "A PHP extension blocked the file upload.";   break;
			}
		}

				
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "Your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["filename"]["tmp_name"], $target_file)) {
				$filename = basename($_FILES["filename"]["name"]);
				$imgLocation = "../frontUploads/" . $filename;
				
				// Send to the database
				$query = "UPDATE artwork SET filename = '$filename' WHERE title = '$title';";
				$result = mysqli_query($db, $query);
				if(!$result) {
					die("Connection error" . mysqli_error($db));
				} else {
					// Print success message
					echo "<table><tr><td>The file $filename has been uploaded.</td></tr></table>";
					echo "<table><tr>
						<td><a href='$imgLocation' target='_blank'>Preview Image</a></td>
						<td><a href='./imageupload.php'>Upload Another</a></td>
						</tr></table>";
				}
			} else {
				echo "There was an error uploading your file.";
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
			<td><select name="title">
				<option value=''>select work</option>
				<?php
				$query =   "SELECT a.title 
							FROM artwork a 
							JOIN people p ON a.artistID = p.personID
							WHERE CONCAT(p.firstname, ' ', p.lastname) = '$_SESSION[username]'
							ORDER BY a.title;";
				$result = mysqli_query($db, $query);
				if(!$result) {
					$errors['artwork'] = "Error in SQL statement." . mysqli_error($db);
				} else {
					$numrows = mysqli_num_rows($result);
					for($i = 0; $i < $numrows; $i++) {
						$row = mysqli_fetch_assoc($result);
						if($row) {
							$title = $row['title'];
							if(isset($_POST['title']) && $_POST['title']==$title) { ?>
								<option value="<?php echo $title; ?>" selected ='selected'><?php echo $title; ?></option>
								<?php
							} else { ?>
								<option value="<?php echo $title; ?>"><?php echo $title; ?></option>
								<?php
							}
						}
					}
				}
				?>
			</select></td>
			<td><small class='errorText'><?php echo array_key_exists('title',$errors) ? $errors['title'] : ''; ?></small></td>
		</tr>
		<tr>
			<td>Find File:</td>
			<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
			<td><input type='file' name='filename'></td>
			<td><small class='errorText'><?php echo array_key_exists('filename',$errors) ? $errors['filename'] : ''; ?></small></td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='upload' value='Upload'></td>
		</tr>
	</table>
</form>

<?php
	}
	echo "<table><tr><td></td><td colspan=2><a href='./artwork.php'>Back to Artwork</a></td></tr></table>";
} else {
?>
<table>
	<tr>
		<td><a href="./login.php?page=imageupload">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
print "</div></div>";
include "frontFooter.php";
?>