<?php
include "frontHeader.php";
include "currentShow.php";

print "<div id='right_col'>";
print "<div class='headings'>Create Show Labels</div>";
print "<div class='center'>";

function updateCurrentShow($newNumber) {
	// Open file
	$filename = "currentShow.php";
	$fileptr = fopen($filename, "w") or die("Unable to open file.");
	$newFileText = "<?php $" . "currentShow = $newNumber; ?>";
	
	// Try to update the file
	if(fwrite($fileptr, $newFileText)) {
		$heading = "Successful Update!";
		$field = "New Show Number";
		$number = $newNumber;
	} else {
		$heading = "Update Failed";
		$field = "Current Show Number";
		$number = $currentShow;
	}
	fclose($fileptr);
	?>
	<table>
		<tr>
			<th colspan='2'><?php echo $heading; ?></th>
		</tr>		
		<tr>
			<td><?php echo $field; ?></td>
			<td><?php echo $number; ?></td>
		</tr>		
	</table>
	<?php	
}

$errors = array();
$valid = false;

// Check that label creator is logged in
if(labelCreatorIsUser()) {
	if(isset($_POST['updateShowNumber'])) {
		if(!empty($_POST['showNumber'])) {
			$show = $_POST['showNumber'];
			if(strlen($show) == 0)
				$errors['showNumber'] = "Please Enter a show number.";
			elseif(!is_numeric($show))
				$errors['showNumber'] = "Show number must be a number.";
		} else {
			$errors['showNumber'] = "Enter a valid show number.";
		}	
		
		if(count($errors) == 0) {
			$valid = true;
		}
	
	}
	
	if($valid) {
		updateCurrentShow($show);
	} else {
?>
	<div class='form'>
	<form method="post" action="" autocomplete='off'>
		<table>
			<tr>
				<th colspan='2'>Update Show Number</th>
			</tr>
			<tr>
				<td>Current Show Number</td>
				<td><?php echo $currentShow; ?></td>
			</tr>
			<tr>
				<td>New Show Number</td>
				<td><input type="text" name="showNumber"></td>
			</tr>
			<tr>
				<td><small class='errorText'><?php echo array_key_exists('showNumber',$errors) ? $errors['showNumber'] : ''; ?></small></td>
			</tr>
			<tr>
				<td></td><td><input type='submit' name='updateShowNumber' value="Update Show Number"></td>
			</tr>
		</table>
	</form>
		<?php
	}
?>
	<table>
		<tr>
			<td><a href="./labels.php">Back to Labels</a></td>
		</tr>		
	</table>
<?php
} else {
?>
<table>
	<tr>
		<td><a href="./login.php?page=updateCurrentShow">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
print "</div></div>";
include "frontFooter.php";
?>