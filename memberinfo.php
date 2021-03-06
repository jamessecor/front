<?php
include "frontHeader.php";

// Open right_col
print "<div id='right_col'>";

if(isLoggedIn()) {
	/*   TODO: Add most recent dues paid
	
	// ==========================================
	// Individual Dues Status
	// ==========================================	
	
		$id = $_SESSION['username']; // 'First Last'
		$q = "SELECT p.personID, p.balance, m.p
			  FROM people p WHERE personID = '$id'; ";
	
	
	*/
	// Get member info from database
	$username = $_SESSION['username'];
	print "<div class='headings'>Membership Info for $username</div>";
	
	// Center Table
	print "<div class='center'>";
	
	// Begin db queries =======================================================================
	// Get Date Joined and Dues Balance
	$query = "SELECT joinDate, balance from people WHERE CONCAT(firstname, ' ', lastname) = '$username';";
	$result = mysqli_query($db, $query);
	if(!$result) 
		die("Connection error" . mysqli_error($db));
	else {
		$r = mysqli_fetch_assoc($result);
		$joinDate = $r['joinDate'];
		$balance = $r['balance'];
	}
		
	// Get total sales
	$query = "SELECT sum(price) FROM artwork
			  WHERE buyerID IS NOT NULL
			  AND artistID = (SELECT personID FROM people WHERE CONCAT(firstname, ' ', lastname) = '$username');";
	
	// Send sum query to db				
	$result = mysqli_query($db, $query);
	if($result) {
		$sum_array = mysqli_fetch_array($result);
		$sum = $sum_array[0];
		if(!$sum) 
			$sum = 0;		
		$mcut = $sum * .85;
	}
	// End db queries =======================================================================
	$memberinfo = array('Name'=>$username,'Join Date'=>$joinDate, 'Dues Balance'=>'$' . $balance, 'Total Sales'=>'$' . $sum, 'Your Cut'=>'$' . $mcut);
?>
	<table id="memberinfo">
	<?php
		print "<tr><th>Description</th><th>Info</th></tr>";
		foreach($memberinfo as $key => $value) {
			print "<tr><td>$key</td><td>$value</td></tr>";
		}
	?>
	</table>
<?php
} else {
	print "<div class='headings'>Membership Info</div>";
	print "<div class='center'>";
	print "<h2><a href='./login.php?page=memberinfo'>Log In to see your membership info.</a></h2>";
}
// Close .center and #right_col
print "</div></div>";

include "frontFooter.php";
?>