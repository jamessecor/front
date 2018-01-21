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
	<table>
		<tr>
			<td><a href="./createLabels.php">Create Labels</a></td>
			<td><a href="./updateCurrentShow.php">Update Current Show Number</a></td>
		</tr>
	</table>

	</div>
<?php
} else {
?>
<table>
	<tr>
		<td><a href="./login.php">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
print "</div></div>";
include "frontFooter.php";
?>