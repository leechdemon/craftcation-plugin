<script>
	function cc_waitlist_add_button( workshopId ) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_waitlist_getStatus",
				"workshopId": workshopId,
				"customerId": <?php echo get_current_user_id(); ?>,
				"waitlistDate": '',
				"notificationDate": '',
				"removalDate": ''
			},
			success: function (data) {
				if( data == 'unlisted' ) {
					var today = waitlist_dateFormat();

					jQuery.ajax({
						type: 'POST',
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						data: {
							"action": "cc_waitlist_insert",
							"workshopId": workshopId,
							"customerId": <?php echo get_current_user_id(); ?>,
							"waitlistDate": today,
							"notificationDate": '',
							"removalDate": ''
						},
						success: function (data) {
							document.getElementById( 'waitlist-icon-' + workshopId + '-add' ).style.display = 'none';
							document.getElementById( 'waitlist-icon-' + workshopId + '-remove' ).style.display = 'block';
						}
					});
				}
			}
		});
		
		
	}
	function cc_waitlist_getStatus_button( workshopId ) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_waitlist_getStatus",
				"workshopId": workshopId,
				"customerId": <?php echo get_current_user_id(); ?>,
				"waitlistDate": '',
				"notificationDate": '',
				"removalDate": ''
			},
			success: function (data) {
				return data;
			}
		});
	}
	function cc_waitlist_remove_button( workshopId ) {
		var today = waitlist_dateFormat();
		
		var waitlistDate = '';
		
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_waitlist_remove",
				"workshopId": workshopId,
				"customerId": <?php echo get_current_user_id() ?>,
				"waitlistDate": waitlistDate,
				"removalDate": today
			},
			success: function (data) {
				document.getElementById( 'waitlist-icon-' + workshopId + '-add' ).style.display = 'block';
				document.getElementById( 'waitlist-icon-' + workshopId + '-remove' ).style.display = 'none';
			}
		});
	}
	function cc_waitlist_update_button( customerId, workshopId ) {
		var today = waitlist_dateFormat();
		
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_waitlist_update",
				"workshopId": workshopId,
				"customerId": customerId,
				"waitlistDate": today,
				"notificationDate": today,
				"removalDate": today
			},
			success: function (data) {
				window.location.reload();
			}
		});
	}
	
	
	function waitlist_dateFormat() {
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = today.getFullYear();
		var hr = String(today.getHours()).padStart(2, '0');
		var min = String(today.getMinutes()).padStart(2, '0');
		var sec = String(today.getSeconds()).padStart(2, '0');

		return mm + '/' + dd + '/' + yyyy + ' ' + hr + ':' + min + ':' + sec;
	}
</script>