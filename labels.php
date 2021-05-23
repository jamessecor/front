<?php
include "frontHeader.php";
include "currentShow.php";

print "<div id='right_col'>";
print "<div class='headings'>Labels</div>";
print "<div class='center'>";


$errors = array();

// Check that admin is logged in
if(labelCreatorIsUser()) {
	?>
	<div class="row">
		<div class="col-md-3 col-md-offset-3 center-it">
			<a class="links" href="./createLabels.php">Create Labels</a>
		</div>
		<div class="col-md-4 center-it">
			<a class="links" href="./addShow.php">Add Show</a>
		</div>
		<form action="createShows.php">
			<input type="submit" value="Create Shows"/>
		</form>
	</div>
	<?php 
	$result = mysqli_query($db, "SELECT * FROM shows");
	if(!$result) {
		echo mysqli_error($db);
	} else {
		?><table><tr><th>Show</th><th>Current?</th></tr><?php
		while($row = mysqli_fetch_assoc($result)) {
			if($row) { ?>
				<tr>
					<td><?php echo $row['showName']; ?></td>
					<td align="center"><?php echo $row['current'] ? "Yes" : "No"; ?></td>
					<td>
						<form action="addShow.php">
							<input type="hidden" name="id" value="<?php echo $row['id']; ?>"/>
							<input type="submit" value="Edit" />
						</form>
					</td>
					<td>
						<form action="deleteShow.php">
							<input type="hidden" name="id" value="<?php echo $row['id']; ?>"/>
							<input type="submit" value="Delete" />
						</form>
					</td>
				</tr>
			<?php }
		}
		?></table><?php
	}
	?>
	</div>
<?php
} else {
?>
	<div class="row">
		<div class="col-md-12 center-it">
			<a class="links" href="./login.php?page=labels">Log In to Continue</a>
		</div>
	</div>
	
<?php
}
print "</div></div>";
include "frontFooter.php";
?>