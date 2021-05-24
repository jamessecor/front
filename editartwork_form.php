<?php
$editQuery = "SELECT a.artworkID, a.title, a.medium, a.yearMade, a.price, a.showNumber, a.filename
    FROM artwork a
    WHERE a.artworkID = '$selectedArtworkID';";
				
$editResult = mysqli_query($db, $editQuery);
if(!$editResult) {
    die("Database error here.");
} else {
    // This is the current info, to be edited
    $editWork = mysqli_fetch_assoc($editResult);
    
    // Get $workID, the artworkID to update
    $workID = $editWork['artworkID'];	
    
    if(isset($_POST['editwork']) || isset($_POST['deletework'])) {
        if(isset($_POST['deletework'])) {
            $disabled="disabled";
            $submitButtonValue = "Delete";
            $submitButtonName = "submitdeletion";
            $formAction = "deleteartwork.php";
        } else {
            $submitButtonValue = "Update";
            $submitButtonName = "updatework";
            $formAction = "updateartwork.php";
        }
    ?>
    <hr>					
    <form id="updateform" method="post" action="<?php echo $formAction; ?>">
        <table>
            <tr>
                <th>Image</th>
                <td><img width="180em" src="../frontUploads/<?php echo $editWork['filename']; ?>" alt="No Image"></td>
            </tr>
            <tr>
                <th>Title</th>
                <td><input type="text" name="updatetitle" value="<?php echo htmlspecialchars($editWork['title']);?>" <?php echo $disabled; ?>></td>
                <td><small class='errorText'><?php echo array_key_exists('updatetitle',$errors) ? $errors['updatetitle'] : ''; ?></small></td>
            </tr>
            <tr>
                <th>Medium</th>
                <td><input type="text" name="updatemedium" value="<?php echo htmlspecialchars($editWork['medium']);?>" <?php echo $disabled; ?>></td>
            </tr>
            <tr>
                <th>Year</th>
                <td><input type="text" name="updateyear" value="<?php echo "$editWork[yearMade]";?>" <?php echo $disabled; ?>></td>
            </tr>
            <tr>
                <th>Price</th>
                <td><input type="text" name="updateprice" value="<?php echo htmlspecialchars($editWork['price']);?>" <?php echo $disabled; ?>></td>
            </tr>
            <tr>
                <th>Show Number</th>
                <td>
                <select name='updateshownumber' <?php echo $disabled; ?>>
                <?php // get shows
                $result = mysqli_query($db, "SELECT * from shows;");
                while($show = mysqli_fetch_assoc($result)) {
                    if($show['id'] == $editWork['showNumber']) {
                        echo "<option value='$show[id]' selected>$show[showName]</option>";
                    } else {
                        echo "<option value='$show[id]'>$show[showName]</option>";
                    }
                }
                ?>
                </select>
                
                </td>
            </tr>
            <?php if(isset($_POST['deletework'])) { ?>
                <tr>
                    <td colspan='10'><small class='errorText'>Selecting Delete will remove this piece from the database.</small></td>
                </tr>
            <?php } ?>
            <tr>
                <td></td><td><input type="submit" value="<?php echo "$submitButtonValue"; ?>" name="<?php echo "$submitButtonName"; ?>"></td>
            </tr>
            <tr>
                <td>
                    <input type="hidden" name="artworkid" value="<?php echo $workID; ?>" display="none">
                    <input type="hidden" name="oldtitle" value="<?php echo htmlspecialchars($editWork['title']); ?>">
                    <input type="hidden" name="filename" value="<?php echo $editWork['filename']; ?>">
                </td>
            </tr>
        </table>
    </form>
<?php 
    } 
} 
?>