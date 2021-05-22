<?php 
include 'frontHeader.php';
if(labelCreatorIsUser()) {
    $result = mysqli_query($db, "DELETE FROM shows where id = $_GET[id]");
    if(!$result) {
        die("Unable to delete show.");
    }
}
header('Location: labels.php');
?>