<?php
require "../../includes/frontConfig.php";
require "../../includes/frontConnect.php";
$result = mysqli_query($db, "Select id, showName from shows where current = true limit 1");
if(!$result) {
    echo ("Unable to fetch current show.");
} else {
    $currentShow = mysqli_fetch_assoc($result);
    $currentShowName = $currentShow['showName'];
    $currentShowId = $currentShow['id'];
}
?>