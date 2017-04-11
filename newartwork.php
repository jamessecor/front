<?php
include "frontHeader.php";
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";
?>

<div id="right_col">
<div class='headings'>Submit New Artwork</div>
<div class='center'>
<?php
if(isLoggedIn()) {

	$errors = array();
	$validInputs = false;

	$name = '';
	$title = '';
	$year = 0;
	$media = '';
	$price = 0;

	if(isset($_POST['newart'])) {
		// TODO: finish processing
		
		// Name	
		if(!empty($_POST['name'])) {
			$name = trim($_POST['name']);
			if(strlen($name) == 0)
				$errors['name'] = "Please Enter a Name";		
		} else {
			$errors['name'] = "Please Enter a Name";
		}

		
		// Title
		if(!empty($_POST['title'])) {
			$title = trim($_POST['title']);
			if(strlen($title) == 0)
				$errors['title'] = "Please Enter a Title.";
		} else {
			$errors['title'] = "Please Enter a Title.";
		}
		
		
		// Date complete (Year)	
		if(!empty($_POST['year'])) {
			$year = trim($_POST['year']);
			if(strlen($year) == 0)
				$errors['year'] = "Please Enter a Year";		
		} else {
			$errors['year'] = "Please Enter a Year";
		}


		
		// Medium/Media
		if(!empty($_POST['media'])) {
			$media = trim($_POST['media']);
			if(strlen($media) == 0) 
				$errors['media'] = "Please Enter a Medium or Media";
		} else {
			$errors['media'] = "Please Enter a Medium or Media";
		}

		// Price	
		if(!empty($_POST['price'])) {
			$price = trim($_POST['price']);
			if(strlen($price) == 0) {
				$errors['price'] = "Please Enter a Dollar Amount";
			} 
		} else {
			$errors['price'] = "Please Enter a Dollar Amount";
		}
		
		// Check for errors on form
		if(count($errors) == 0) {
			$validInputs = true;
		}
	} 

	if($validInputs) {
		// TODO: subquery is something like: SELECT artistID FROM artists WHERE CONCAT(firstname,' ',lastname) = '$name'));";
		$query = "INSERT INTO artwork (artworkID, title, yearMade, medium, price, artistID) VALUES (NULL, '$title', '$year', '$media', $price, (SELECT ID FROM users WHERE username = '$name'));";
		$result = mysqli_query($db, $query);
		if(!$result)
			echo "<div>Data Entry Error. <a href=''>Please try again.</a></div>";
		else
			print "<div>Your artwork has been submitted.</div>";
	} else {
	?>

	<form id="login" method="post" action="" autocomplete='off'>
		<p class='small'>Please Double Check Before Submitting</p>
		<table>
			<tr>
				<td>Name</td>
				<td><select name='name' value="<?php echo isset($_POST['name']) ? $_POST['name'] : '';  ?>">
					<?php
					// Get artists to populate username drop-down
					print "<option value=''>Choose Name</option>";
					// TODO: Select members from correct db as below
					//$query = "SELECT CONCAT(firstname, ' ', lastname) FROM Artists ORDER BY firstname;";
					$query = "SELECT username FROM users ORDER BY username;";
					$result = mysqli_query($db, $query);
					if(!$result) {
						$errors['username'] = "Error in SQL statement." . mysqli_error($db);
					} else {
						$numrows = mysqli_num_rows($result);
						for($i = 0; $i < $numrows; $i++) {
							$row = mysqli_fetch_assoc($result);
							if($row) {
								$username = $row['username'];
								if($_POST['name']==$username)
									echo "<option value='$username' selected ='selected'>$username</option>";
								else
									echo "<option value='$username'>$username</option>";
							}
						}
					}
					?>
				</select></td>
				<td><small class='errorText'><?php echo array_key_exists('name',$errors) ? $errors['name'] : ''; ?></small></td>
			</tr>
			<tr>
				<td>Title</td>
				<td><input type='text' name='title' value="<?php echo isset($_POST['title']) ? $_POST['title'] : '';  ?>"></td>
				<td><small class='errorText'><?php echo array_key_exists('title',$errors) ? $errors['title'] : ''; ?></small></td>
			</tr>
			<tr>
				<td>Date</td>
				<td><select name='year'>
						<option value=''>Year Completed</option>
						<?php
						// Drop-down with 15 years back
						for($i = 0, $y = date('Y'); $i < 15; $i++, $y--) {
							if($_POST['year']==$y)
								echo "<option value='$y' selected ='selected'>$y</option>";
							else
								echo "<option value='$y'>$y</option>";
						}
						?>
						
					</select>
				</td>
			
				<td>
					<small class='errorText'>
						<?php echo array_key_exists('year',$errors) ? $errors['year'] : ''; ?>
					</small>
				</td>
			</tr>
			<tr>
				<td>Medium/Media</td>
				<td><input type='text' name='media' value="<?php echo isset($_POST['media']) ? $_POST['media'] : '';  ?>"></td>
				<td><small class='errorText'><?php echo array_key_exists('media',$errors) ? $errors['media'] : ''; ?></small></td>
			</tr>
			<tr>
				<td>Price</td>
				<td><input type='number' name='price' value="<?php echo isset($_POST['price']) ? $_POST['price'] : '';  ?>"></td>
				<td><small class='errorText'><?php echo array_key_exists('price',$errors) ? $errors['price'] : ''; ?></small></td>
			</tr>
			<tr>
				<td></td><td><input type="submit" name="newart" value="Submit Artwork" formnovalidate></td>
			</tr>
		</table>
	</form>

<?php } } else print "<h2><a href='./login.php'>Log In to see your artwork info.</a></h2>"; ?>

</div></div>

<?php
mysqli_close($db);
include "frontFooter.php";
?>