<?php
include "frontHeader.php";
include "currentShow.php";

print "<div id='right_col'>";
print "<div class='headings'>Create Show Labels</div>";
print "<div class='center'>";

$errors = array();
$valid = false;

// Check that label creator is logged in
if(labelCreatorIsUser()) {
	if(isset($_POST['updateShow'])) {
		// id
		$id = false;
		if(!empty($_POST['id'])) {
			$id = $_POST['id'];
		}
		// Name
		if(!empty($_POST['showName'])) {
			$showName = addslashes($_POST['showName']);
			if(strlen($showName) == 0)
				$errors['showName'] = "Please Enter a show number.";
		} else {
			$errors['showName'] = "Enter a show name.";
		}
		// Current
		$current = !empty($_POST['current']) ? 'true' : 'false';

		if(count($errors) == 0) {
			$valid = true;
		}
	
	}
	
	if($valid) {
		if($id) {
			$query = "UPDATE shows SET showName = '$showName', current = $current WHERE id = $id";
			$removeCurrentQuery = "UPDATE shows set current = false WHERE id <> $id";
		} else {
			$query = "INSERT INTO shows (showName, current) VALUES ('$showName', $current);";
			$removeCurrentQuery = "UPDATE shows set current = false";
		}
		// Set current flag to false if this one is to be the current show
		if($current) {
			$removeCurrentResult = mysqli_query($db, $removeCurrentQuery);
			if(!$removeCurrentResult) {
				echo "Unable to set current to false for all other shows.";
			}
		}
		// Send add/update query to database
		$result = mysqli_query($db, $query);

		if(!$result)
			die("<table><tr><td>$showName: Data Entry Error.<a href=''>Please try again.</a></td></tr>");
		else { ?>
			<div class="row">
				<div class="col-md-4 col-md-offset-4 form-label-header center-it">
					Successful Update
				</div>
			</div>		
			<div class="row">
				<div class="col-md-2 col-md-offset-5 center-it">
					New Show Name
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-md-offset-5 form-label center-it">
					<?php echo $showName; ?>
				</div>		
			</div>
		<?php }
	} else {
?>
	<div class='form'>
	<form method="post" action="" autocomplete='off'>
		<div class="row">
			<div class="col-md-4 col-md-offset-4 center-it form-label-header">Update Show</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-md-offset-5 form-label">Current Show</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-md-offset-5 center-it"><?php echo $currentShow['showName'] ?></div>
		</div>
		<div class="row">
			<div class="col-md-3 col-md-offset-5 form-label">New Show Name</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-md-offset-5 center-it">
			<?php
			$result = false;
			if(isset($_GET['id'])) {
				// We're editing an existing show
				$result = mysqli_query($db, "select * from shows where id = $_GET[id]");
				if($result) {
					$show = mysqli_fetch_assoc($result);
				}
			}
			?>
				<input type="text" name="showName" value="<?php echo $result ? $show['showName'] : '' ?>">
				<input type="hidden" name="id" value="<?php echo $result ? $show['id'] : '' ?>">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-md-offset-5">
				<small class='errorText'><?php echo array_key_exists('showName',$errors) ? $errors['showName'] : ''; ?></small>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-md-offset-5 form-label">Make Current Show?</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-md-offset-5 center-it">
				<input type="checkbox" name="current">
			</div>
		</div>
		<div class="spacer">&nbsp;</div>
		<div class="row">
			<div class="col-md-2 col-md-offset-5">
				<input type='submit' name='updateShow' value="Update Show">
			</div>
		</div>
	</form>
		<?php
	}
?>
	<div class="spacer">&nbsp;</div>
	<div class="spacer">&nbsp;</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5">
			<a class="links" href="./labels.php">Back to Labels</a>
		</div>		
	</div>
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