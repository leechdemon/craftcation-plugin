<?php 
require_once plugin_dir_path(__FILE__) . '../craftcation.php';

global $wpdb, $cc_db_version, $cc_ticket_table_name;
$cc_ticket_table_name = $wpdb->prefix . 'cc_tickets';

function cc_ticket_install() { // Sets up DB
	global $wpdb, $cc_db_version, $cc_ticket_table_name;
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $cc_ticket_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		ticketNumber mediumint(9) NOT NULL,
		ticketCustomerId mediumint(9) NOT NULL,
		purchaseOrderNumber text NOT NULL,
		purchaseOrderType text NOT NULL,
		purchaseCustomerId mediumint(9) NOT NULL,
		paymentOrderNumbers text NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	add_option( 'cc_db_version', $cc_db_version );
}
function cc_ticket_insert($orderNumber = '', $customerId = '', $paymentOrderNumbers = '', $orderType = 'ticket') { // Adds order to DB
	global $wpdb, $cc_db_version, $cc_ticket_table_name;
	
	$wpdb->insert( 
		$cc_ticket_table_name, 
		array( 
			'ticketNumber' => $customerId, 
			'ticketCustomerId' => $customerId, 
			'purchaseOrderNumber' => $orderNumber, 
			'purchaseOrderType' => $orderType, 
			'purchaseCustomerId' => $customerId, 
			'paymentOrderNumbers' => $paymentOrderNumbers.','.$paymentOrderNumbers,
		)
	);
}
function cc_ticket_import() {
	$userprenom = $_POST['prenom'];
	$usernom = $_POST['nom'];
	$username = $_POST['email'];
	
	/* If no user exists, build a new one */
	$userdata = array(      
		'user_login' => $username,
		'nickname' => $username,
		'user_nicename' => $username,
		'user_email' => $username,
		'first_name' => $userprenom,
		'last_name' => $usernom,
//		'user_registered' => date( 'Y-m-d H:i:s' ),
		'display_name' => $username,
//		'show_admin_bar_front' => false,
//		'role' => 'subscriber',
//		'admin_color' => "fresh",
//		'rich_editing' => "true", 
	);
	
	$the_user = get_user_by('login', $username);
	if ( !$the_user ) { $the_user = get_user_by('email', $username); }
	
	if ( $the_user ) {
		$user_id = $the_user->ID;
		
		if (!is_wp_error( $user_id )){
		   $push_id = array('ID' => $user_id);
		   $merge = array_merge($userdata, $push_id);
		   $user_id = wp_update_user( $merge );
		   update_user_meta( $user_id, 'region', $region );
		}
		else {                                       
			$html_update = "Broken";
		}
	 } else {
		// create new user
		$user_id = wp_insert_user($userdata);
		wp_new_user_notification( $user_id, null, '' );
		update_user_meta( $user_id, 'region', $region );
	 }	

//	echo $user_id;
	/* Now that we have our user, buy a ticket. */
	cc_ticket_insert('123', $user_id, '123');
} add_action( 'wp_ajax_cc_ticket_import', 'cc_ticket_import' );
function cc_ticket_deleteRow() { // Adds order to DB
	global $wpdb, $cc_ticket_table_name;
	require_once plugin_dir_path(__FILE__) . '../craftcation.php';
	
	$ThisThing = $wpdb->delete( 
		$cc_ticket_table_name, 
		array( 
			'id' => $_POST['element_id'], 
		)
	);
} add_action( 'wp_ajax_cc_ticket_deleteRow', 'cc_ticket_deleteRow' );
function cc_ticket_drop() { // Drops table (admin)(broken)
	global $wpdb, $cc_db_version, $cc_ticket_table_name;
	/* Not working yet */

	//	cc_ticket_install_data();
//	$sql = "DROP TABLE IF EXISTS $cc_ticket_table_name";
//	$wpdb->query($sql); 
	
//	echo json_encode( $response );
}
function cc_ticket_displayTable_Filters()  { // Displays buttons at the top. (admin)(broken) )
	echo '<div class="wrap">';
		echo '<a class="" style="display: inline-flex; padding: 0.5rem 1rem; font-weight: 800; background-color: #444444; border-radius: 2rem;" href="javascript:cc_ticket_drop();">Drop Table</a>';
	echo '</div>';
}
function cc_ticket_getTickets($jsonMode = 'false') { // Returns object/JSON of Ticket DB 
	global $wpdb, $cc_db_version, $cc_ticket_table_name;
	
	if($jsonMode == 'true') {
		echo json_encode( $wpdb->get_results( "SELECT * FROM " .$cc_ticket_table_name ) );
	}
	else {
		return $wpdb->get_results( "SELECT * FROM " .$cc_ticket_table_name );
	}
}
function cc_ticket_displayTable()  { // Displays Ticket DB
	global $wpdb, $cc_db_version, $cc_ticket_table_name;
	$tickets = cc_ticket_getTickets();
//	$width = [10,10,10,10,10,10]; 	
	
	
	echo '<div id="cc_db_window" class="cc_db_window" style="width: 90%; height: 50%;">';

	/* Display Headers */
	echo '<div class="cc_db_header_row cc_db_row">';
	foreach( $tickets[0] as $key => $item) {
		if($key == 'id') {
			echo '<div class="cc_db_item '.$key.'" style="width: 5%;">'.$key.'</div>';
		} else {
			echo '<div class="cc_db_item '.$key.'" style="width: 10%;">'.$key.'</div>';
		}
	}
	echo '</div>'; // End header row

	/* Display Results */
	foreach( $tickets as $ticket ) {
		/* Get TicketId from DB */
		$ticketId = '';
		foreach( $ticket as $key => $item) { if($key == 'id') $ticketId = $item; }
		echo '<div id="ticketRow_'.$ticketId.'" class="cc_db_row">';	
		
		foreach( $ticket as $key => $item) {
			if($key == 'id') { 
				echo '<div class="cc_db_item '.$key.'" style="width: 5%;">';
					echo '<button onclick="javascript:cc_ticket_deleteRow_button(\''.$item.'\');">Delete [x]</button>';
			} else {
				echo '<div class="cc_db_item '.$key.'" style="width: 10%;">';

				if($key == 'purchaseOrderNumber') { 
					echo '<a href="/wp-admin/post.php?post='.$item.'&action=edit">'.$item.'</a>';
				} else if($key == 'ticketCustomerId' or $key == 'purchaseCustomerId') {
					$prettyname = $item;
					$first = get_userdata( $item )->first_name;
					$last = get_userdata( $item )->last_name;
					
					if($first && $last) {
						$prettyname = $first .' '. $last;
					}
					else {
						if($first) { $prettyname = $first; }
						if($last) { $prettyname = $last; }
					}
					
					echo '<img src="'.get_avatar_url( $item ).'" style="height: 100%; margin-right: 0.5rem;">';
					echo '<a href="/wp-admin/user-edit.php?user_id='.$item.'" style="vertical-align: super;">'.$prettyname.'</a>';
				} else if($key == 'paymentOrderNumbers') {
					$paymentOrderNumbers = explode(',', $item);

					$p = 0;
					foreach( $paymentOrderNumbers as $paymentOrderNumber ) {
						if( $p != 0 ) { echo ', '; } else { $p++; }

						echo '<a href="/wp-admin/post.php?post='.$paymentOrderNumber.'&action=edit">'.$paymentOrderNumber.'</a>';
					}
				} else {
					echo $item;
				}
			}

			echo '</div>';
		}
		
		echo '</div>';
	}
	
	echo '</div>';
}
function cc_ticket_purchase_action( $order_id ) { // Add Order->DB every time a matching product is in a "Processing" order
	$order = wc_get_order($order_id);
	$ticketTagIDs = explode(',', esc_attr(get_option('cc_ticket_tags')) );
		
	foreach( $order->get_items() as $item ) { /* For each product... */
		$addToTicketDB = false;
		$product = wc_get_product( $item->get_product_id() );
		$productTagIDs = $product->get_tag_ids();
		foreach($productTagIDs as $productTagID) { /* For each product Tag... */	

			foreach($ticketTagIDs as $ticketTagID) { /* For each Tag in the Options... */
				if( $productTagID == $ticketTagID ) { $addToTicketDB = true; }
			}

		}
		
		for( $q = 0; $q < $item->get_quantity(); $q++ ) { /* For each quantity of line item...  */
			if( $addToTicketDB ) { /* This should run exactly ONCE per line item qty - excluding duplicate tag matches. */
				cc_ticket_insert($order_id, $order->get_user_id(), $order_id);
			}
		}
	}
	
} add_action( 'woocommerce_order_status_processing', 'cc_ticket_purchase_action', 10, 1 );


function cc_ticket_options() {
	register_setting( 'cc-ticket-settings-group', 'cc_ticket_tags' );
} add_action( 'admin_init', 'cc_ticket_options');
if($_GET['action'] == 'cc_ticket_drop_table') {
	global $wpdb, $cc_db_version, $cc_ticket_table_name;
//	require_once WP_PLUGIN_DIR . '/craftcation-plugin/craftcation.php';
//	header( 'Content-Type: application/json' );
//	cc_ticket_install_data();
//	cc_ticket_drop();
	echo 'aa';
//	echo json_encode( [1,2,3] );
//	echo ;
	echo $cc_ticket_table_name;
//	$response = $wpdb->get_results( "SELECT * FROM leechdemon_" .$cc_ticket_table_name );
	//	echo cc_ticket_getTickets('true');
//	Test($response);
//	echo json_encode( $response );
//	echo json_encode( cc_ticket_getTickets() );
}