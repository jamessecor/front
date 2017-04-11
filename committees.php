<?php
include "frontHeader.php"
?>

<div id="right_col">
<div class='headings'>Committees</div>
<div class='center'>
<?php
if(!isLoggedIn()) {
	print "<h2><a href='./login.php'>Log In to see your committee info.</a></h2>";
} else {
		
	if(!isset($_POST['join']) && !isset($_POST['leave'])) {
	?>
	<form method='post' action="">
		<input type='submit' name='join' value='Join Committee'>
		<input type='submit' name='leave' value='Leave Committee'>
	</form>

	<?php 
	} else if(isset($_POST['join'])) { 
		if(isset($_POST['joinNow'])) {
			print "<div class='center'>";
			print "Success: Joined";
			print "</div>";
		} else {
	?>
		<form method='post' action="">
			<select>
				<option value='one'>one</option>
			</select>
			<input type='submit' name='joinNow' value='Join'>
		</form>
	<?php
		}
	} else { ?>
		<form method='post' action="">
			<select>
				<option value='one'>one</option>
			</select>
			<input type='submit' name='leaveNow' value='Leave Committee'>
		</form>

<?php
	}
}
?>

</div>





<?php
include "frontFooter.php";
?>