<?php
require "../../includes/frontConfig.php";
require "../../includes/frontConnect.php";

// Validate Title
if(!empty($_POST['updatetitle'])) {
    $newTitle = trim($_POST['updatetitle']);
    if(strlen($newTitle) == 0)
        $errors['updatetitle'] = "Enter a title";
} else {
    $errors['updatetitle'] = "Enter a title";						
}
// Validate Medium
if(!empty($_POST['updatemedium'])) {
    $newMedium = trim($_POST['updatemedium']);
    if(strlen($newMedium) == 0)
        $errors['updatemedium'] = "Enter a medium";
} else {
    $errors['updatemedium'] = "Enter a medium";						
}

// Validate Year
if(!empty($_POST['updateyear'])) {
    $newYear = $_POST['updateyear'];
    if(strlen($newYear) == 0)
        $errors['updateyear'] = "Enter a year";
} else {
    $errors['updateyear'] = "Enter a year";						
}

// Validate Price
if(!empty($_POST['updateprice'])) {
    $newPrice = trim($_POST['updateprice']);
    if(strlen($newPrice) == 0)
        $errors['updateprice'] = "Enter a price";
} else {
    $errors['updateprice'] = "Enter a price";						
}

// Validate Show Number
if(!empty($_POST['updateshownumber'])) {
    $newShowNumber = $_POST['updateshownumber'];
    if(strlen($newShowNumber) == 0)
        $errors['updateshownumber'] = "Enter a shownumber";
} else {
    $errors['updateshownumber'] = "Enter a shownumber";						
}

$artworkID = $_POST['artworkid'];

// Update Query
$queryNewTitle = addslashes($newTitle);
$queryNewMedium = addslashes($newMedium);
$queryNewPrice = addslashes($newPrice);

$updateQuery = "UPDATE artwork SET title   = '$queryNewTitle', 
                medium     = '$queryNewMedium',
                yearMade   = '$newYear',
                price      = '$queryNewPrice',
                showNumber = '$newShowNumber'
        WHERE artworkID = $artworkID;";
$updateResult = mysqli_query($db, $updateQuery);
if(!$updateResult) {
    die("Update Error. Unable to access the database.");
} else {
    header("Location: editartwork.php?artworkUpdated=$newTitle");
}
 ?>