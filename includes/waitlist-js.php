<script>
	function cc_waitlist_add_button( workshopId, prefix, status ) {
		document.getElementById( prefix + 'waitlist-icon-' + workshopId + '-add' ).style.display = 'none';
		document.getElementById( prefix + 'waitlist-icon-' + workshopId + '-remove' ).style.display = 'none';
		
		if( !status ) {
			cc_waitlist_getStatus( workshopId, prefix, 'cc_waitlist_add_button' );
		} else if ( status == "unlisted" ) {
			cc_waitlist_add( workshopId );
		}
	}
	function cc_waitlist_add( workshopId, prefix ) {
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
				cc_waitlist_getStatus( workshopId, prefix );
			}
		});
	}
	function cc_waitlist_getNext( workshopId, callback ) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_waitlist_getNext",
				"workshopId": workshopId,
				"customerId": <?php echo get_current_user_id(); ?>,
				"waitlistDate": '',
				"notificationDate": '',
				"removalDate": ''
			},
			success: function ( data ) {
//				var status = JSON.parse(data);
//				if( status == "unlisted" ) {
//					document.getElementById( 'waitlist-icon-' + workshopId + '-add' ).style.display = 'block';
//					document.getElementById( 'waitlist-icon-' + workshopId + '-remove' ).style.display = 'none';
//				} else if (status != "" ) {
//					document.getElementById( 'waitlist-icon-' + workshopId + '-add' ).style.display = 'none';
//					document.getElementById( 'waitlist-icon-' + workshopId + '-remove' ).style.display = 'block';
//				}
				
				if( callback ) {
					eval(callback + "( " +workshopId+ ", " +data+ ");" );
				}
			}
		});
	}
	function cc_waitlist_getStatus( workshopId, prefix, callback ) {
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
			success: function ( data ) {
				var status = JSON.parse(data);
				var div;
				if( status == "unlisted" ) {
					div = document.getElementById( prefix + 'waitlist-icon-' + workshopId + '-add' );
					if( div ) { div.style.display = 'block'; }
					div = document.getElementById( prefix + 'waitlist-icon-' + workshopId + '-remove' );
					if( div ) { div.style.display = 'none'; }
				} else if (status != "" ) {
					div = document.getElementById( prefix + 'waitlist-icon-' + workshopId + '-add' );
					if( div ) { div.style.display = 'none'; }
					div = document.getElementById( prefix + 'waitlist-icon-' + workshopId + '-remove' );
					if( div ) { div.style.display = 'block'; }
				}
				
				if( callback ) {
					eval(callback + "( " +workshopId+ ", " +data+ ");" );
				}
			}
		});
	}
	function cc_waitlist_remove_button( workshopId, prefix, status ) {
		document.getElementById( prefix + 'waitlist-icon-' + workshopId + '-add' ).style.display = 'none';
		document.getElementById( prefix + 'waitlist-icon-' + workshopId + '-remove' ).style.display = 'none';

		if( !status ) {
			cc_waitlist_getStatus( workshopId, prefix, 'cc_waitlist_remove_button' );
		} else if ( status != "unlisted" && status != "" ) {
			cc_waitlist_remove( workshopId, prefix, status );
		}
	}
	function cc_waitlist_deleteRow_button( rowId ) {
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_waitlist_deleteRow",
				"id": rowId,
			},
			success: function (data) {
//				console.log(data);
				window.location.reload();
			}
		});
	}
	function cc_waitlist_remove( workshopId, prefix, waitlistDate ) {
		var today = waitlist_dateFormat();
		
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
				cc_waitlist_getStatus( workshopId, prefix );
			}
		});
	}
	function cc_waitlist_process( workshopId ) {		
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_waitlist_process",
				"workshopId": workshopId,
				"customerId": '',
				"waitlistDate": '',
				"notificationDate": '',
				"removalDate": ''
			},
			success: function (data) {
				window.location.reload();
			}
		});
	}
	function cc_waitlist_notify( workshopId, customerId, waitlistDate ) {
		var today = waitlist_dateFormat();
		
		jQuery.ajax({
			type: 'POST',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				"action": "cc_waitlist_notify",
				"workshopId": workshopId,
				"customerId": customerId,
				"waitlistDate": waitlistDate,
				"notificationDate": today,
				"removalDate": ''
			},
			success: function (data) {
//				console.log( data );
				window.location.reload();
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