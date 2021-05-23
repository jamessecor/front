<?php
require "../../includes/frontConfig.php";
require "../../includes/frontConnect.php";

$result = mysqli_query($db, "SELECT DISTINCT showNumber FROM artwork");
if(!$result) {
    die("unable to get showNumbers");
} else {
    while($showNumber = mysqli_fetch_assoc($result)['showNumber']) {
        $query = "INSERT INTO shows (id, showName, current) VALUES ($showNumber, '$showNumber', false)";
        echo "<div>$query</div>";
        if(mysqli_query($db, $query)) {
            echo "<div>inserted $showNumber</div>";
        } else {
            echo "<div>unable to insert $showNumber</div>";
        }
    }
}
?>