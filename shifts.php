<?php
// artwork.php
include "frontHeader.php";
?>

<script>

$(document).ready(function() {
	$(".shifts").hide();	
	var shifts = [];
	
	$("#date").on("change", function() {
		$(".shifts").show();
	});
	
	$("#take-shift").on("click", function() {
		var shiftStr = $("#date").val();
		var validShift = false;
		if($("#early").is(":checked")) {
			shiftStr += ",11-2";
			validShift = true;
		} 
		if($("#late").is(":checked")) {
			shiftStr += ",2-5";
			validShift = true;
		}	
		if(validShift) {
			shifts.push(shiftStr);
		}
		console.log(shifts);
	});	
});
</script>
<div class="row">
	<div class="text-center">
		<h1>Add shift</h1>
	</div>
</div>
<div class="row">
	<div class="text-center">
		<div>Select Date</div>
		<div><input type="date" id="date"/></div>
		<div class="shifts" >
			<input type="checkbox" id="early"/>
			<span id="eleven-two">11am-2pm</span>
		</div>
		<div class="shifts" >
			<input type="checkbox" id="late"/>
			<span id="two-five">2-5pm</span>
		</div>

		
		
		<div><button id="take-shift">Take Shift</button></div>
	</div>
</div>

<?php
include "frontFooter.php";
?>