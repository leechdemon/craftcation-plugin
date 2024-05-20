<style>
	.cc_db_row { clear: both; margin: 5px; }
	.cc_db_row:nth-child(odd) .cc_db_item { background-color: #bbbbbb; }
	.cc_db_header_row  { position: sticky; top: 0; }
	.cc_db_header_row .cc_db_item { background-color: #bbbbbb; position: sticky; top: 0; font-weight: 800; }
	.cc_db_item { float: left; padding: 5px 10px; }
	.cc_db_window { clear: both; height: 50%; overflow-y: scroll; resize: both; }
</style>
<script>
	function cc_ticket_drop() { 
	document.getElementById("cc_db_window").innerHTML = '<div style="width: 100%; text-align: center;">Loading...</a>';
	
	var xhr = new XMLHttpRequest(); 
	console.log("cc_ticket_drop_table:");
	xhr.onload = function () {

		// Process our return data
		if (xhr.status >= 200 && xhr.status < 300) {
			// Runs when the request is successful
//			location.reload();
			document.getElementById("cc_db_window").innerHTML = xhr.responseText;
		} else {
//			location.reload();
			document.getElementById("cc_db_window").innerHTML = "There was an issue retrieving the database. Please reload the page and try again.";
		} 

	};
	xhr.open('GET', '<?php echo plugins_url() ?>/craftcation/includes/cc-tickets.php?action=cc_ticket_drop_table');
	xhr.send(null);
}
</script>

<div class="wrap">
	<h1>Craftcation Admin Page</h1>
	<p>All Craftcation information is stored within this tab and it's sub-menus.</p>
</div>

<div class="wrap">
	<h3>Ticket Database</h3>
	<?php // cc_ticket_displayTable_Filters(); ?>
	<?php cc_ticket_displayTable(); ?>
</div>