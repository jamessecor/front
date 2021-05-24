<?php
require "../../includes/frontConfig.php";
require "../../includes/frontConnect.php";

$artworkID = $_POST['artworkid'];
$title = ($_POST['oldtitle']);

// Query for deletion
$deletionQuery = "DELETE FROM artwork WHERE artworkID = '$artworkID';";

// Delete Work
$deleteWork = mysqli_query($db, $deletionQuery);

// Tell user what happened
if(!$deleteWork)
    die("Deletion Failed. Try again or contact label people");
else {
    header("Location: editartwork.php?artworkDeleted=$title");
}
?>