<?php require_once plugin_dir_path(__FILE__) . '../craftcation.php';
global $wpdb, $cc_workshop_db_version, $cc_workshop_table_name;
$cc_workshop_table_name = $wpdb->prefix . 'cc_workshops';

function cc_workshop_install() { // Sets up DB
	global $wpdb, $cc_workshop_db_version, $cc_workshop_table_name;
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $cc_workshop_table_name (
		id mediumint(9) NOT NULL,
		workshopSelections text NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	add_option( 'cc_workshop_db_version', $cc_workshop_db_version );
}
function cc_workshop_insert() { // Adds Workshop Selections to DB
	global $wpdb, $cc_workshop_db_version, $cc_workshop_table_name;
	
	$wpdb->insert( 
		$cc_workshop_table_name, 
		array( 
			'id' => $_POST['id'], 
			'workshopSelections' => $_POST['workshopSelections']
		)
	);
} add_action( 'wp_ajax_cc_workshop_insert', 'cc_workshop_insert' );
function cc_workshop_update() { // Adds Workshop Selections to DB
	global $wpdb, $cc_workshop_db_version, $cc_workshop_table_name;
	
	$wpdb->update( 
		$cc_workshop_table_name, 
		array( 
			'workshopSelections' => $_POST['workshopSelections']
		),
		array(
			'id' => $_POST['id'],
		)
	);
} add_action( 'wp_ajax_cc_workshop_update', 'cc_workshop_update' );

function cc_workshop_deleteRow() { // Deletes row from DB matching "Id".
	global $wpdb, $cc_workshop_table_name;
	require_once plugin_dir_path(__FILE__) . '../craftcation.php';
	
	$ThisThing = $wpdb->delete( 
		$cc_workshop_table_name, 
		array( 
			'id' => $_POST['element_id'], 
		)
	);
} add_action( 'wp_ajax_cc_workshop_deleteRow', 'cc_workshop_deleteRow' );
function cc_workshop_dropTable() { // Truncates the entire table.
	global $wpdb, $cc_workshop_table_name;
//	echo $cc_workshop_table_name;
//	require_once plugin_dir_path(__FILE__) . '../craftcation.php';
	
//	echo '1';
	$query = 'TRUNCATE TABLE ' . $cc_workshop_table_name;
//	echo json_encode( $query );
	$wpdb->query($query); 
	
	echo $query;
} add_action( 'wp_ajax_cc_workshop_dropTable', 'cc_workshop_dropTable' );
function cc_workshop_drop() { // Drops table (admin)(broken)
	global $wpdb, $cc_workshop_db_version, $cc_workshop_table_name;
	/* Not working yet */

	//	cc_workshop_install_data();
//	$sql = "DROP TABLE IF EXISTS $cc_workshop_table_name";
//	$wpdb->query($sql); 
	
//	echo json_encode( $response );
}
function cc_workshop_displayTable_Filters()  { // Displays buttons at the top. (admin)(broken) )
	echo '<div class="wrap">';
		echo '<a class="" style="display: inline-flex; padding: 0.5rem 1rem; font-weight: 800; background-color: #444444; border-radius: 2rem;" href="javascript:cc_workshop_drop();">Drop Table</a>';
	echo '</div>';
}
function cc_workshop_getWorkshops($jsonMode = 'false', $id = '') { // Returns object/JSON of Ticket DB 
	global $wpdb, $cc_workshop_db_version, $cc_workshop_table_name;
	
	if($id) { 
		return $wpdb->get_results( "SELECT * FROM " .$cc_workshop_table_name. " WHERE id = " .$id );
	}
	else { 
		if($jsonMode == 'true') {
			echo json_encode( $wpdb->get_results( "SELECT * FROM " .$cc_workshop_table_name ) );
		}
		else {
			return $wpdb->get_results( "SELECT * FROM " .$cc_workshop_table_name );
		}
	}
	
	
}
function cc_workshop_displayTable()  { // Displays Ticket DB
	global $wpdb, $cc_workshop_db_version, $cc_workshop_table_name;
	$workshops = cc_workshop_getWorkshops('table');
//	$width = [10,10,10,10,10,10]; 	
	
	
	echo '<div id="cc_db_window" class="cc_db_window" style="width: 100%; height: 50%;">';

	/* Display Headers */
	echo '<div class="cc_db_header_row cc_db_row">';
	foreach( $workshops[0] as $key => $item) {
		if($key == 'id') {
			echo '<div class="cc_db_item '.$key.'" style="width: 8%;">'.$key.'</div>';
		} else {
			echo '<div class="cc_db_item '.$key.'" style="width: 14%;">'.$key.'</div>';
		}
	}
	echo '</div>'; // End header row

	/* Display Results */
	foreach( $workshops as $workshop ) {
		/* Get TicketId from DB */
		$workshotId = '';
		foreach( $workshop as $key => $item) { if($key == 'id') $workshopId = $item; }
		echo '<div id="ticketRow_'.$ticketId.'" class="cc_db_row">';	
		
		foreach( $workshop as $key => $item) {
			if($key == 'id') { 
				echo '<div class="cc_db_item '.$key.'" style="width: 8%;">';
					echo '<button onclick="javascript:cc_workshop_deleteRow_button(\''.$item.'\');">Delete [x]</button>';
			} else {
				echo '<div class="cc_db_item '.$key.'" style="width: 14%;">';

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
function cc_workshop_purchase_action( $order_id ) { // Add Order->DB every time a matching product is in a "Processing" order
	$order = wc_get_order($order_id);
	$ticketTagIDs = explode(',', esc_attr(get_option('cc_workshop_tags')) );
		
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
				cc_workshop_insert($order_id, $order->get_user_id(), $order_id);
			}
		}
	}
	
} add_action( 'woocommerce_order_status_processing', 'cc_workshop_purchase_action', 10, 1 );