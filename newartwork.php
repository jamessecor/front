<?php
// newartwork.php
// author James Secor
// January 2018
include "frontHeader.php";
?>

<div id="right_col">
	<div class='headings'>Submit New Artwork</div>
	<div class='center'>
	<?php
	if(isLoggedIn()) {
		$errors = array();
		$validInputs = false;

		$username = '';
		$title = '';
		$year = 0;
		$media = '';
		$price = 0;
		
		include "currentShow.php";
		
		if(isset($_POST['newart'])) {
			// Set username to artist's name
			if(!adminIsUser()) $_POST['username'] = $_SESSION['username'];
			
			// Name	
			if(!empty($_POST['username'])) {
				$username = trim($_POST['username']);
				if(strlen($username) == 0)
					$errors['username'] = "Please Enter a Name";		
			} else {
				$errors['username'] = "Please Enter a Name";
			}

			// Title
			if(!empty($_POST['title'])) {
				$title = addslashes(trim($_POST['title']));
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
				//$media = trim($_POST['media']);
				$media = addslashes(trim($_POST['media']));
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
				$errors['price'] = "Please Enter a Price";
			}
			
			
			// showNumber
			// Opened up so members can put in work for past shows
			// if(!adminIsUser()) $_POST['showNumber'] = $currentShow;
				
			if(!empty($_POST['showNumber'])) {
				$showNumber = trim($_POST['showNumber']);
				if(strlen($showNumber) == 0) {
					$errors['showNumber'] = "Please Enter Show Number";
				} 
			} else {
				$errors['showNumber'] = "Please Enter Show Number";
			}
			
			// Check for errors on form
			if(count($errors) == 0) {
				$validInputs = true;
			}
		} 

		if($validInputs) {
			// Query to select personID
			$selectArtistID = "SELECT personID FROM people WHERE CONCAT(firstname, ' ', lastname) = '$username';";
			// Send query to database
			$result = mysqli_query($db, $selectArtistID);
			if(!$result)	// This should not happen
				die("Artist not found");
			else {
				$data = mysqli_fetch_assoc($result);
				$artistID = $data['personID'];
			}
			
			// Query to insert data
			// TODO: buyerID should not be hard-coded to 1
			$query = "INSERT INTO artwork (artistID, buyerID, artworkID, title, yearMade, medium, price, showNumber) 
					VALUES ($artistID, NULL, NULL, '$title', '$year', '$media', '$price', $showNumber);";
					  
			// Send query to database
			$result = mysqli_query($db, $query);
			
			if(!$result)
				die("<table><tr><td>$price: Data Entry Error. <a href=''>Please try again.</a></td></tr>");
			else 
				print "<table><tr><td colspan='10'>Your artwork has been submitted.</td></tr>";
			print "<tr>
						<td><a href='./artwork.php'>Back to Artwork</a></td>
						<td><a href='./editartwork.php'>Edit Artwork</a></td>
						<td><a href='./newartwork.php'>Submit Another</a></td>
				   </tr></table>";
		} else {
			?>
			<form id="login" method="post" action="" autocomplete='off'>
				<table>
					<tr>
						<td>Name</td>
						<?php 
						if(labelCreatorIsUser()) { ?>
							<td><select name="username">
							<?php
							$query = "SELECT CONCAT(firstname, ' ', lastname) AS 'username' FROM people WHERE member = 1 OR member = $currentShow ORDER BY member, username;";
							$result = mysqli_query($db, $query);
							if(!$result) {
								$errors['username'] = "Error in SQL statement." . mysqli_error($db);
							} else {
								$numrows = mysqli_num_rows($result);
								for($i = 0; $i < $numrows; $i++) {
									$row = mysqli_fetch_assoc($result);
									if($row) {
										$username = $row['username'];
										if($username === $_SESSION['username'])
											echo "<option value='$username' selected ='selected'>$username</option>";
										else
											echo "<option value='$username'>$username</option>";
									}
								}
							}
							?>
						</select></td>
						<td><small class='errorText'><?php echo array_key_exists('username',$errors) ? $errors['username'] : ''; ?></small></td>
						<?php
						} else {
							print "<td>$_SESSION[username]</td>";
						}?>
						
					</tr>
					<tr>
						<td>Title</td>
						<td><input type='text' name='title' value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '';  ?>"></td>
						<td><small class='errorText'><?php echo array_key_exists('title',$errors) ? $errors['title'] : ''; ?></small></td>
					</tr>
					<tr>
						<td>Date</td>
						<td><select name='year'>
								<option value=''>Year Completed</option>
								<?php
								// Drop-down with 15 years back
								for($i = 0, $y = date('Y'); $i < 25; $i++, $y--) {
									if(isset($_POST['year']) && $_POST['year']==$y)
										echo "<option value='$y' selected='selected'>$y</option>";
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
						<td><input type='text' name='media' value="<?php echo isset($_POST['media']) ? htmlspecialchars($_POST['media']) : '';  ?>" placeholder='ex: oil on canvas'></td>
						<td><small class='errorText'><?php echo array_key_exists('media',$errors) ? $errors['media'] : ''; ?></small></td>
					</tr>
					<tr>
						<td>Price</td>
						<td><input type='text' name='price' value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '';  ?>"></td>
						<td><small class='errorText'><?php echo array_key_exists('price',$errors) ? $errors['price'] : ''; ?></small></td>
					</tr>
					<tr>
						<td>Show Number</td>
						<td><?php // if(adminIsUser()) { ?>
							<input type='text' name='showNumber' value=<?php echo "\"$currentShow\" placeholder=\"current show: $currentShow\""; ?>>
							<?php // } else { echo $currentShow; } ?>
						</td>
						<td><small class='errorText'><?php echo array_key_exists('showNumber',$errors) ? $errors['showNumber'] : ''; ?></small></td>
					<tr>
						<td class='errorText' colspan=2>Preview Label Before Submitting</td>
					</tr>
					<tr>
						<td></td><td><input type="submit" name="newart" value="Submit Artwork" formnovalidate></td>
					</tr>
				</table>
			</form>
			
			<!-- New -->
			<hr>
			<table>
				<tr>
					<td><button id="previewButton">Preview Label</button></td>
				</tr>
				<tr>
					<td id="labelPreview"></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td><a class="links" href='./artwork.php'>Back to Artwork</a></td>
				</tr>
			</table>
			<script>
			$(document).ready(function() {
				$("#previewButton").on("click", function() {
					// Get label info
					var title  = document.getElementsByName("title")[0].value;
					<?php if(labelCreatorIsUser()) { ?>
					var artist = document.getElementsByName("username")[0].value;
					<?php } else { ?>
					var artist = "<?php echo $_SESSION['username']; ?>";
					<?php } ?>
					var year   = document.getElementsByName("year")[0].value;
					var media  = document.getElementsByName("media")[0].value;
					var price  = document.getElementsByName("price")[0].value;					
					var labelText = "";
					
					// Display message if info is incomplete
					if(title==="" || artist==="" || year==="" || media==="" || price==="") {
						labelText += "<p class=\"errorText\"><em>Incomplete label info.</em></p>";
					}
					
					// Show label preview
					labelText += "<p>" + title + "<br>" + artist + "&nbsp; &nbsp;" + year + "<br>" + media + "<br>";
					if($.isNumeric(price)) {
						labelText += "$";
					}
					labelText +=  price + "</p>";					
					$("#labelPreview").html(labelText);
				});
			});
			</script>
			<!-- End New -->
		<?php 
		} 
	} else {	
?>
<table>
	<tr>
		<td><a href="./login.php?page=newartwork">Log In to Continue</a></td>
	</tr>
</table>
	</div>
</div>

<?php
}
include "frontFooter.php";
?>