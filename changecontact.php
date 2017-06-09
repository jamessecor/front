<?php
include "frontHeader.php";

$username = $_SESSION['username'];
?>

<div id="right_col">
<div class='headings'>Contact Info Change: <?php echo $username; ?></div>
<div class="center">

<?php
$error = false;
$go = false;

if(isset($_POST['submitNewInfo'])) {
	$website=trim($_POST['newwebsite']);
	$phone  =trim($_POST['newphone']);
	$email  =trim($_POST['newemail']);
	
	if(!($website || $phone || $email)) {
		$error = "Please Enter Info for Update";
	}
	if($error == false) {
		$go = true;
	}
} 

if($go) {
	// Create array to hold info
	$s = array();
	if($website) {
		$s[] = "website = '$website'";
	}
	if($phone) {
		$s[] = "phone = '$phone'";
	}
	if($email) {
		$s[] = "email = '$email'";
	}
	
	// Create String in the form of "website = 'blah', phone = '4735357' "
	$str = "";
	foreach($s as $val) {
		$str = $str . $val . ", ";
	}
	// Get rid of the last comma
	$str = substr($str, 0, -2);
	
	$query = "UPDATE people SET $str WHERE CONCAT(firstname,' ',lastname) = '$username';";
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("INSERT error:" . mysqli_error($db));
	} else {
		echo "<table><tr><th>Update Successful</th></tr>";
		echo "<tr><td><a href='./contacts.php'>Back to Contacts</a></td></tr></table>";
	}
	
	
} else {
	
?>
<form id="updateinfo" method="post" action="" autocomplete='off'>
	<table>
		<tr>
			<td colspan=2><small class="errorText"><?php if($error) echo $error; ?> </small></td>
		<tr>
			<td>Name</td>
			<td><?php echo $_SESSION['username']; ?>
		</tr>
		<tr>
			<td>Website</td>
			<td><input type="text" name="newwebsite"></td>
		</tr>
		<tr>
			<td>Phone Number</td>
			<td><input type="text" name="newphone"></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input type="email" name="newemail"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="submitNewInfo" value="Submit New Info"> </td>
		</tr>
		<tr><td></td><td><a href="./contacts.php">Back to Contacts</a></td></tr>
	</table>
</form>

</div></div>
<?php
}
include "frontFooter.php";
?>