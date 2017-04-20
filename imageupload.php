<?php
include "frontHeader.php";
require "../includes/frontConfig.php";
require "../includes/frontConnect.php";

print "<div id='right_col'>";
print "<div class='headings'>Image Upload</div>";
print "<div class='center'>";

if(isLoggedIn()) {
// Create dropdown with images for the user
	if(isset($_POST['upload'])) {
	} else {

?>

<form method='post' enctype='multipart/form-data'>
	<table>
		<tr>
			<td>Member:</td>
			<td><?php print "$_SESSION[username]";?></td>
		<tr>
			<td>Select Work:</td>
			<td><select name="artwork" value="<?php echo isset($_POST['artwork']) ? $_POST['artwork'] : '';  ?>">
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
		</tr>
		<tr>
			<td colspan=2><input type='file' name='filename'></td>
		</tr>
		<tr>
			<td><input type='submit' name='upload' value='Upload'></td>
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