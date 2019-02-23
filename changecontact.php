<?php
// changecontact.php
// author James Secor
// January 2018
include "frontHeader.php";

$username = $_SESSION['username'];
?>

<div id="right_col">
<div class='headings'>Contact Info Change: <?php echo $username; ?></div>
<div class="center">

<?php
if(isLoggedIn()) {
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
		echo "<div class='row'><div class='col-md-12 form-label center-it'>Update Successful</div>";
		echo "<div class='spacer'>&nbsp;</div>";
		echo "<div class='col-md-12 center-it'><a class='links' href='./contacts.php'>Back to Contacts</a></div></div>";
	}
	
	
} else {
	
?>
<form id="updateinfo" method="post" action="" autocomplete='off'>
	<div class="row">
		<div class="col-md-2 col-md-offset-5">
			<small class="errorText"><?php if($error) echo $error; ?> </small>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5 form-label">Name</div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5"><?php echo $_SESSION['username'];?></div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5 form-label">Website</div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5">
			<input type="text" name="newwebsite">
		</div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5 form-label">Phone Number</div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5">
			<input type="text" name="newphone">
		</div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5 form-label">Email</div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5">
			<input type="email" name="newemail">
		</div>
	</div>
	<div class="spacer">&nbsp;</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-5">
			<input type="submit" name="submitNewInfo" value="Submit New Info">
		</div>
	</div>
	<div class="spacer">&nbsp;</div>
	<div class="spacer">&nbsp;</div>
	<div class="row">
		<div class="col-md-3 col-md-offset-5">
			<a class="links" href="./contacts.php">Back to Contacts</a>
		</div>
	</div>
</form>

</div></div>
<?php
} 
} else {
?>
<table>
	<tr>
		<td><a href="./login.php?page=changecontact">Log In to Continue</a></td>
	</tr>
</table>
<?php
}
include "frontFooter.php";
?>