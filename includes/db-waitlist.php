<?php 
require_once plugin_dir_path(__FILE__) . '../craftcation.php';

global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;
$cc_waitlist_table_name = $wpdb->prefix . 'cc_waitlists';

function cc_waitlist_install() { // Sets up DB
	global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $cc_waitlist_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		workshopId mediumint(9) NOT NULL,
		customerId mediumint(9) NOT NULL,
		waitlistDate text NOT NULL,
		notificationDate text NOT NULL,
		removalDate text NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	add_option( 'cc_waitlist_db_version', $cc_waitlist_db_version );
}
function cc_waitlist_insert() { // Adds order to DB
	global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;
	
	$wpdb->insert( 
		$cc_waitlist_table_name, 
		array( 
			'workshopId' => $_POST['workshopId'], 
			'customerId' => $_POST['customerId'], 
			'waitlistDate' => $_POST['waitlistDate'], 
			'notificationDate' => $_POST['notificationDate'], 
			'removalDate' => $_POST['removalDate'],
		)
	);
} add_action( 'wp_ajax_cc_waitlist_insert', 'cc_waitlist_insert' );
function cc_waitlist_remove() { // Adds order to DB
	global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;
			
	$wpdb->update( 
		$cc_waitlist_table_name, 
		array( 
			'removalDate' => $_POST['removalDate'],
		),
		array( 
			'workshopId' => $_POST['workshopId'], 
			'customerId' => $_POST['customerId'], 
			'waitlistDate' => $_POST['waitlistDate'],
		)
	);
} add_action( 'wp_ajax_cc_waitlist_remove', 'cc_waitlist_remove' );
function cc_waitlist_notify( $customerId, $workshopId, $waitlistDate, $notificationDate) { // Adds order to DB
	global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;
		
	$wpdb->update( 
		$cc_waitlist_table_name, 
		array( 
			'notificationDate' => $notificationDate,
		),
		array( 
			'workshopId' => $workshopId, 
			'customerId' => $customerId, 
			'waitlistDate' => $waitlistDate,
		)
	);
	
	$user = new WP_User( $customerId );
	$workshop = wc_get_product( $workshopId );
	$workshopTitle = $workshop->get_name();
	$workshopImage = $workshop->get_image();
	
	$adminEmail = get_option( 'admin_email' ) ;
	$header = array(
		'MIME-Version: 1.0',
		'Content-type: text/html; charset=utf-8',
		"From: $adminEmail",
	);
	$subject = 'A Craftcation Workshop you waitlisted is available';
	$message = '<h2>A Workshop that you waitlisted for, '.$workshopTitle.', the Craftcation Conference has become available!</h2>';
	$message .= '<div style="width: 50%; margin: 2rem; border: solid 2px black;">';
		$message .= '<div style="width: 25%;">'.$workshopImage.'</div>';
		$message .= '<div style="width: 75%; font-weight: 800; font-size: larger;">'.$workshopTitle.'</div>';
	$message .= '</div>';
	$message .= '<a style="display: block;" href="https://www.craftcationconference.com/account/workshops?waitlist='.$workshopId.'">Update Workshops</a></div>';

	wp_mail( $user->user_email, $subject, $message, $header );	
} add_action( 'wp_ajax_cc_waitlist_notify', 'cc_waitlist_notify' );

//function cc_new_user() {
//	$userprenom = $_POST['prenom'];
//	$usernom = $_POST['nom'];
//	$username = $_POST['email'];
//	
//	/* If no user exists, build a new one */
//	$userdata = array(      
//		'user_login' => $username,
//		'nickname' => $username,
//		'user_nicename' => $username,
//		'user_email' => $username,
//		'first_name' => $userprenom,
//		'last_name' => $usernom,
////		'user_registered' => date( 'Y-m-d H:i:s' ),
//		'display_name' => $username,
////		'show_admin_bar_front' => false,
////		'role' => 'subscriber',
////		'admin_color' => "fresh",
////		'rich_editing' => "true", 
//	);
//	
//	$the_user = get_user_by('login', $username);
//	if ( !$the_user ) { $the_user = get_user_by('email', $username); }
//	
//	if ( $the_user ) {
//		$user_id = $the_user->ID;
//		
//		if (!is_wp_error( $user_id )){
//		   $push_id = array('ID' => $user_id);
//		   $merge = array_merge($userdata, $push_id);
//		   $user_id = wp_update_user( $merge );
//		   update_user_meta( $user_id, 'region', $region );
//		}
//		else {                                       
//			$html_update = "Broken";
//		}
//	 } else {
//		// create new user
//		$user_id = wp_insert_user($userdata);
//		wp_new_user_notification( $user_id, null, '' );
//		update_user_meta( $user_id, 'region', $region );
//	 }
//
////	echo $user_id;
//	/* Now that we have our user, buy a ticket. */ 
//	cc_waitlist_insert('', $user_id, '');
//} add_action( 'wp_ajax_cc_new_user', 'cc_new_user' );
function cc_waitlist_deleteRow() { // Deletes row from DB matching "Id".
	global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;	
	$wpdb->delete( 
		$cc_waitlist_table_name, 
		array( 
			'id' => $_POST['id'], 
		)
	);
} add_action( 'wp_ajax_cc_waitlist_deleteRow', 'cc_waitlist_deleteRow' );
//function cc_waitlist_dropTable() { // Truncates the entire table.
//	global $wpdb, $cc_waitlist_table_name;
////	echo $cc_waitlist_table_name;
////	require_once plugin_dir_path(__FILE__) . '../craftcation.php';
//	
////	echo '1';
//	$query = 'TRUNCATE TABLE ' . $cc_waitlist_table_name;
////	echo json_encode( $query );
//	$wpdb->query($query); 
//	
//	echo $query;
//} add_action( 'wp_ajax_cc_waitlist_dropTable', 'cc_waitlist_dropTable' );
//function cc_waitlist_displayTable_Filters()  { // Displays buttons at the top. (admin)(broken) )
//	echo '<div class="wrap">';
//		echo '<a class="" style="display: inline-flex; padding: 0.5rem 1rem; font-weight: 800; background-color: #444444; border-radius: 2rem;" href="javascript:cc_waitlist_drop();">Drop Table</a>';
//	echo '</div>';
//}
function cc_waitlist_getStatus() { // Returns object/JSON of individual waitlist item from DB 
	global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;
	
	$results = $wpdb->get_results( "SELECT * FROM " .$cc_waitlist_table_name );
	
	$status = "unlisted";
	if( $results[0]->customerId ) {
		foreach($results as $result) {
			/* If we're looking at the right waitlist item,... */
			if( $result->customerId == get_current_user_id() && $result->workshopId == $_POST['workshopId'] ) {

				/* determine current Status */
				if( $result->removalDate == '' ) { $status = $result->waitlistDate; }
//				else if( $result->notificationDate != '' ) { $status = "notified"; }
//				else { $status = $result->waitlistDate; }
			}
		}
	}
	echo json_encode( $status );
	wp_die();
} add_action( 'wp_ajax_cc_waitlist_getStatus', 'cc_waitlist_getStatus' );
function cc_waitlist_getLists($jsonMode = 'false') { // Returns object/JSON of Ticket DB 
	global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;
	
	if($jsonMode == 'true') {
		echo json_encode( $wpdb->get_results( "SELECT * FROM " .$cc_waitlist_table_name ) );
	}
	else {
		return $wpdb->get_results( "SELECT * FROM " .$cc_waitlist_table_name );
	}
}
function cc_waitlist_displayTable()  { // Displays Ticket DB
	global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;
	$waitlists = cc_waitlist_getLists();
	
	echo '<div id="cc_db_window" class="cc_db_window" style="width: 100%; height: 50%;">';

	/* Display Headers */
	echo '<div class="cc_db_header_row cc_db_row">';
	foreach( $waitlists[0] as $key => $item) {
		if($key == 'id') {
			echo '<div style="text-align: center;" class="cc_db_item '.$key.'">'.$key.'</div>';
		} else if($key == 'customerId') {
			echo '<div style="text-align: center;" class="cc_db_item '.$key.'">User</div>';
		} else if($key == 'workshopId') {
			echo '<div style="text-align: center;" class="cc_db_item '.$key.'">Workshop</div>';
		} else if($key == 'waitlistDate') {
			echo '<div style="text-align: center;" class="cc_db_item '.$key.'">Waitlist Date</div>';
		} else if($key == 'notificationDate') {
			echo '<div style="text-align: center;" class="cc_db_item '.$key.'">Notification Date</div>';
		} else if($key == 'removalDate') {
			echo '<div style="text-align: center;" class="cc_db_item '.$key.'">Removal Date</div>';
		} else {
			echo '<div class="cc_db_item '.$key.'">'.$key.'</div>';
		}
	}
	echo '</div>'; // End header row

	/* Display Results */
	foreach( $waitlists as $waitlist ) {
		/* Get TicketId from DB */
		$waitlistId = '';
		foreach( $waitlist as $key => $item) { if($key == 'id') $waitlistId = $item; }
		echo '<div id="waitlistRow_'.$waitlistId.'" class="cc_db_row'.$removed.'">';	
		
		foreach( $waitlist as $key => $item) {
			/* Row Display Logic */
			if($key == 'id') { 
				echo '<div class="cc_db_item '.$key.'">';
					echo '<button onclick="javascript:cc_waitlist_deleteRow_button(\''.$item.'\');">Delete [x]</button>';
			} else if($key == 'workshopId') { 
				echo '<div class="cc_db_item '.$key.'">';
					$workshop = wc_get_product( $item );
					$workshopImage = $workshop->get_image();
					$workshopName = $workshop->get_name();
					$url = '/wp-admin/post.php?action=edit&classic-editor&post='.$item;
					echo '<a href="'.$url.'">'.$workshopImage.'</a>';
					echo '<a href="'.$url.'">'.$workshopName.'</a>';
			} else if($key == 'customerId') { 
				echo '<div class="cc_db_item '.$key.'">';
					$user = new WP_User( $item );
//					echo get_avatar( $user->user_email );
					$url = '/wp-admin/edit.php?s&post_status=all&post_type=shop_order&action=-1&m=0&filter_action=Filter&paged=1&action2=-1&_customer_user='.$user->id;
					echo '<a href="'.$url.'">'.get_avatar( $user->user_email ).'</a>';
					echo '<a href="'.$url.'">'.$user->first_name.' '.$user->last_name.'<span class="cc_admin_waitlist_email">('.$user->user_email.')</sspan></a>';
			} else {
				echo '<div class="cc_db_item '.$key.'">';
					echo $item;
			}

			echo '</div>';
		}
		if( $waitlist->removalDate != '' ) {
//			echo "a";
			echo "<script>document.getElementById('waitlistRow_".$waitlist->id."').classList.add('removed')</script>";
		}
		
		echo '</div>';
	}
	
	echo '</div>';
}
function DisplayWaitlistButton( $workshopId ) {
	$Output = '<a id="waitlist-icon-'. $workshopId .'-add" style="display: block;" href="javascript:cc_waitlist_add_button(\''.$workshopId.'\');">Add to Waitlist</a>';
	$Output .= '<a id="waitlist-icon-'. $workshopId .'-remove" style="display: none;" href="javascript:cc_waitlist_remove_button(\''.$workshopId.'\');">Remove from Waitlist</a>';
	
	return $Output;
}
function cc_waitlist_getNext( $workshopId ) {
	$response = cc_waitlist_getLists();
	foreach( $response as $waitlistItem ) {
		if( $waitlistItem->workshopId == $workshopId && $waitlistItem->removalDate == '' ) {
			$ThisList []= $waitlistItem;
		}
	}
	return $ThisList[0];
}
function cc_waitlist_process( $workshopId ) {
	date_default_timezone_set('America/Detroit');
	$notificationDate = date( 'm/d/Y H:i:s', time() );
	
	/* Determine the next user */
	$nextCustomerRow = cc_waitlist_getNext( $workshopId );
	$customerId = $nextCustomerRow->customerId;
	$waitlistDate = $nextCustomerRow->waitlistDate;
	
	/* Notify the next user */
	cc_waitlist_notify( $customerId, $workshopId, $waitlistDate, $notificationDate );

	schedule_my_event();


} add_action( 'wp_ajax_cc_waitlist_process', 'cc_waitlist_process' );


// Hook into the WordPress init action to schedule the event
function schedule_my_event() {
    if ( ! wp_next_scheduled( 'cc_waitlist_update_hook' ) ) {
        wp_schedule_event( time(), 'five_minutes', 'cc_waitlist_update_hook' );
    }
}

// Define the custom interval for 5 minutes
function add_five_minutes_interval( $schedules ) {
    $schedules['five_minutes'] = array(
        'interval' => 5 * 60,  // 5 minutes in seconds
        'display'  => __( 'Once every 5 minutes' ),
    );
    return $schedules;
} add_filter( 'cron_schedules', 'add_five_minutes_interval' );

// Hook your callback function to the scheduled event
function my_custom_event_callback() {
    // Your code to execute every 5 minutes
    // For example, logging a message:
    error_log( 'Custom event triggered at ' . current_time( 'mysql' ) );
} add_action( 'cc_waitlist_update_hook', 'my_custom_event_callback' );

// Clear the scheduled event upon deactivation
function deactivate_my_event() {
    $timestamp = wp_next_scheduled( 'cc_waitlist_update_hook' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'cc_waitlist_update_hook' );
    }
} register_deactivation_hook( __FILE__, 'deactivate_my_event' );


?>

<?php
//function cc_waitlist_purchase_action( $order_id ) { // Add Order->DB every time a matching product is in a "Processing" order
//	$order = wc_get_order($order_id);
//	$waitlistTagIDs = explode(',', esc_attr(get_option('cc_waitlist_tags')) );
//		
//	foreach( $order->get_items() as $item ) { /* For each product... */
//		$addToTicketDB = false;
//		$product = wc_get_product( $item->get_product_id() );
//		$productTagIDs = $product->get_tag_ids();
//		foreach($productTagIDs as $productTagID) { /* For each product Tag... */	
//
//			foreach($waitlistTagIDs as $waitlistTagID) { /* For each Tag in the Options... */
//				if( $productTagID == $waitlistTagID ) { $addToTicketDB = true; }
//			}
//
//		}
//		
//		for( $q = 0; $q < $item->get_quantity(); $q++ ) { /* For each quantity of line item...  */
//			if( $addToTicketDB ) { /* This should run exactly ONCE per line item qty - excluding duplicate tag matches. */
//				cc_waitlist_insert($order_id, $order->get_user_id(), $order_id);
//			}
//		}
//	}
//	
//} add_action( 'woocommerce_order_status_processing', 'cc_waitlist_purchase_action', 10, 1 );
//
//
//if( isset($_GET['action']) && $_GET['action'] == 'cc_waitlist_drop_table') {
//	global $wpdb, $cc_waitlist_db_version, $cc_waitlist_table_name;
////	require_once WP_PLUGIN_DIR . '/craftcation-plugin/craftcation.php';
////	header( 'Content-Type: application/json' );
////	cc_waitlist_install_data();
////	cc_waitlist_drop();
//	echo 'aa';
////	echo json_encode( [1,2,3] );
////	echo ;
//	echo $cc_waitlist_table_name;
////	$response = $wpdb->get_results( "SELECT * FROM leechdemon_" .$cc_waitlist_table_name );
//	//	echo cc_waitlist_getLists('true');
////	Test($response);
////	echo json_encode( $response );
////	echo json_encode( cc_waitlist_getLists() );
//}