<script>
	function cc_workshop_drop() { 
	document.getElementById("cc_db_window").innerHTML = '<div style="width: 100%; text-align: center;">Loading...</a>';
	
	var xhr = new XMLHttpRequest(); 
	console.log("cc_workshop_drop_table:");
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
	xhr.open('GET', '<?php echo plugins_url() ?>/craftcation-plugin/includes/cc-workshop.php?action=cc_workshop_drop_table');
	xhr.send(null);
}
	function cc_workshop_insert_button(id, workshopSelections) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_workshop_insert",
				"id": id,
				"workshopSelections": workshopSelections
			},
			success: function (data) {
				window.location.reload();
			}
		});
	}
	function cc_workshop_update_button(id, workshopSelections) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_workshop_update",
				"id": id,
				"workshopSelections": workshopSelections
			},
			success: function (data) {
				window.location.reload();
			}
		});
	}
	function cc_workshop_update_button_front(id) {
		var Timeslots = document.getElementsByClassName('timeslot');
		var workshopSelections = '{ ';
		
		for( var t in Timeslots) {
			if( Timeslots[t].value ) {
				var thisT =  parseInt(t) + 1;
				if(t > 0) workshopSelections += ', ';
				workshopSelections += '"'+thisT+'"' +' : '+ '"'+Timeslots[t].value+'"';
//				workshopSelections[ Timeslots[t].id.split("_")[1] ] = Timeslots[t].value;
			}
		}
		workshopSelections += " }";
//		workshopSelections = workshopSelections.filter(item => item !== null && item !== undefined && item !== '');
		
//		console.log( workshopSelections );
//		console.log( JSON.parse (workshopSelections) );
		cc_workshop_insert_button(id, workshopSelections );
		cc_workshop_update_button(id, workshopSelections );
	}
	function cc_workshop_deleteRow_button(id) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {"action": "cc_workshop_deleteRow", "element_id": id},
			success: function (data) {
				window.location.reload();
			}
		});
	}
	function cc_workshop_dropTable_button(id) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {"action": "cc_workshop_dropTable"},
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