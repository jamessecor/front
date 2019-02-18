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
			<a class="links" href="./updateCurrentShow.php">Update Current Show Number</a>
		</div>
	</div>

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