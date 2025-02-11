<style>
	.cc_db_row { clear: both; margin: 0px; }
	.cc_db_row:nth-child(odd) .cc_db_item { background-color: #bbbbbb; }
	.cc_db_header_row  { position: sticky; top: 0; }
	.cc_db_header_row .cc_db_item { background-color: #bbbbbb; position: sticky; top: 0; font-weight: 800; border-bottom: solid 2px; }
	.cc_db_item { float: left; padding: 5px 5px; height: 20px; }
	.cc_db_window { clear: both; height: 50%; overflow-y: scroll; resize: both; border: solid 2px; }
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
	xhr.open('GET', '<?php echo plugins_url() ?>/craftcation-plugin/includes/cc-tickets.php?action=cc_ticket_drop_table');
	xhr.send(null);
}
	
	function cc_ticket_deleteRow_button(id) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {"action": "cc_ticket_deleteRow", "element_id": id},
			success: function (data) {
				window.location.reload();
			}
		});
	}
	function cc_ticket_dropTable_button(id) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {"action": "cc_ticket_dropTable"},
			success: function (data) {
//				console.log('data', data);
				window.location.reload();
			}
		});
	}
	function cc_new_user(prenom, nom, email) {		
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_new_user",
				"prenom": prenom,
				"nom": nom,
				"email": email,
			},
			success: function (data) {
				
				const delay = (delayInms) => {
					return new Promise(resolve => setTimeout(resolve, delayInms));
				};

				const sample = async () => {
					let delayres = await delay(1000);
				};
				sample();
				
				window.location.reload();
			}
		});
	}
	
</script>

<div class="wrap">
	<h1>Craftcation Ticket Page</h1>
	<p>All Craftcation information is stored within this tab and it's sub-menus.</p>
</div>

<div class="wrap">
	<h3>Ticket Database</h3>
	<?php // cc_ticket_displayTable_Filters(); ?>
	<?php cc_ticket_displayTable(); ?>
</div>

<script>
	function ImportCSV() {
		var file = document.getElementById('file-select').files[0];
		var reader = new FileReader(); // File reader to read the file 
		var sheet = '';
        
        // This event listener will happen when the reader has read the file
        reader.addEventListener('load', function() {
			var lines = reader.result.split("\r");
			var result = [];

			var headers=lines[0].split(",");
			for(var i=1;i<lines.length;i++){

			  var obj = {};
			  var currentline=lines[i].split(",");

			  for(var j=0;j<headers.length;j++){
				  obj[headers[j]] = currentline[j];
			  }

			  result.push(obj);
			}
			
			for(var i=0;i<result.length;i++){
				cc_new_user(result[i].first, result[i].last, result[i].email);
			}
			
			window.location.reload();
        });
		
        var csvContents = reader.readAsText(file); // Read the uploaded file
	}
	function csvJSON(csv){ //var csv is the CSV file with headers
	  var lines = result.split("\n");
	  var result = [];

	  // NOTE: If your columns contain commas in their values, you'll need
	  // to deal with those before doing the next step 
	  // (you might convert them to &&& or something, then covert them back later)
	  // jsfiddle showing the issue https://jsfiddle.net/
	  var headers=lines[0].split(",");

	  for(var i=1;i<lines.length;i++){

		  var obj = {};
		  var currentline=lines[i].split(",");

		  for(var j=0;j<headers.length;j++){
			  obj[headers[j]] = currentline[j];
		  }

		  result.push(obj);
	  }

	  return result; //JavaScript object
//	  return JSON.stringify(result); //JSON
	}
</script> 

<div class="wrap">
	<h3>Tools</h3>
	
	<a href="javascript:cc_new_user( 'John', 'Smith', 'johnsmith@gmail.com' );">Purchase Ticket ("John")</a><br>
	<a href="javascript:cc_new_user( 'Jane', 'Doe', 'janedoe_123abc321@gmail.com' );">Purchase Ticket ("Jane")</a><br>
	<a href="javascript:cc_new_user( 'Jason', 'Elliott', 'leechdemon@gmail.com' );">Purchase Ticket ("Jason")</a><br>
	<hr>
	<a href="javascript:cc_ticket_dropTable_button();">Drop Table</a><br>
	<hr>
	<form id="file-form" action="javascript:ImportCSV();">
<!--	<form id="file-form" action="javascript:csv();" method="POST">-->
		<input type="file" id="file-select" name="csv">
		<button type="submit" id="upload-button">Upload</button>
	</form>
	<?php // cc_ticket_displayTable_Filters(); ?>
	<?php // cc_ticket_displayTable(); ?>
</div>