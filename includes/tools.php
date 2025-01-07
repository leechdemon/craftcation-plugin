<?php
global $wpdb;

if(!function_exists('Test')) { // PHP script to dump variable into JavaScript console on front-end.
	function Test($output, $with_script_tags = false) {
		$js_code = json_encode($output, JSON_HEX_TAG);
		if ($with_script_tags===false) { echo '<script>console.log("Test: " + ' .json_encode($js_code). ');</script>'; }
		else { echo "<pre>" .var_dump($js_code). "</pre>"; }
	 }
}
if(!function_exists('OutputTemplateSlug')) {
	function OutputTemplateSlug() {	
		$Display = false;	
		if($Display) {
			global $template;
			echo '<script>console.log("Template: ' .basename($template).'");</script>';
		}
	} add_action( 'wp_head', 'OutputTemplateSlug' );
}

function cc_admin_menu() {
	add_menu_page(
		'Craftcation Dashboard', // Title of the page
		'Craftcation', // Text to show on the menu link
		'manage_options', // Capability requirement to see the link
		'admin-settings-page', // The 'slug' - file to display when clicking the link
		'display_settings',
		'dashicons-tickets',
		6
	);
} add_action( 'admin_menu', 'cc_admin_menu' );
function display_settings() {
	$adminMenuSlug = 'admin-settings-page';
    require_once $adminMenuSlug . '.php'; //--> make sure you read up on paths and require to find your file.
}
function display_tickets() {
	$adminMenuSlug = 'admin-ticket-page';
    require_once $adminMenuSlug . '.php'; //--> make sure you read up on paths and require to find your file.
}
function display_workshops() {
	$adminMenuSlug = 'admin-workshop-page';
    require_once $adminMenuSlug . '.php'; //--> make sure you read up on paths and require to find your file.
}

function cc_admin_settings_menu() {
	$adminMenuSlug = 'admin-settings-page';
	$menuSlug = 'admin-settings-page';
	$menuTitle = 'Settings';
	
	add_submenu_page(
		$adminMenuSlug, // parent slug
		$menuTitle . ' Page', // page title
		$menuTitle, // menu title
		'manage_options', // Capability requirement to see the link
		$menuSlug, // The 'slug' - file to display when clicking the link
		'display_settings', // callback function
		'30' // position
	);	
}  add_action( 'admin_menu', 'cc_admin_settings_menu' );
function cc_admin_tickets_menu() {
	$adminMenuSlug = 'admin-settings-page';
	$menuSlug = 'admin-ticket-page';
	$menuTitle = 'Tickets';
	
	add_submenu_page(
		$adminMenuSlug, // parent slug
		$menuTitle . ' Page', // page title
		$menuTitle, // menu title
		'manage_options', // Capability requirement to see the link
		$menuSlug, // The 'slug' - file to display when clicking the link
		'display_tickets', // callback function
		'80' // position
	);	
}  add_action( 'admin_menu', 'cc_admin_tickets_menu' );
function cc_admin_workshops_menu() {
	$adminMenuSlug = 'admin-settings-page';
	$menuSlug = 'admin-workshop-page';
	$menuTitle = 'Workshops';
	
	add_submenu_page(
		$adminMenuSlug, // parent slug
		$menuTitle . ' Page', // page title
		$menuTitle, // menu title
		'manage_options', // Capability requirement to see the link
		$menuSlug, // The 'slug' - file to display when clicking the link
		'display_workshops', // callback function
		'80' // position
	);	
}  add_action( 'admin_menu', 'cc_admin_workshops_menu' );

function cc_shortcode( $atts, $content = "" ) {
	ob_start();
	
	if($atts['display_ticket_policy'] == 'true') {
		/* This is dependent on Tickets.php, FWIW... */
		
		$ticketTagIDs = explode(',', esc_attr(get_option('cc_ticket_tags')) );
		$in_cart = false;
		
		/* If the product is tagged with something matching ticketTagIDs, in_cart => true */
		foreach( WC()->cart->get_cart() as $cart_item ) {
			$product = wc_get_product( $cart_item['product_id'] );
			$productTagIDs = $product->get_tag_ids();
			foreach($productTagIDs as $productTagID) { /* For each product Tag... */	

				foreach($ticketTagIDs as $ticketTagID) { /* For each Tag in the Options... */
					if( $productTagID == $ticketTagID ) { $in_cart = true; }
				}
			}
		}

		if ( $in_cart ) {
			echo '<style>#place_order { display: none; } #place_order.activate { display: unset !important; }</style>';
			echo '<script>function returnPolicy_buttonUpdate() { if(document.getElementById("ticketPolicy_yes").checked ) { document.getElementById("place_order").classList.add("activate"); } else { document.getElementById("place_order").classList.remove("activate"); }}</script>';
			
			echo '<form id="craftcation_return_policy"><p style="font-weight: 800;">Have you read and approved our Ticket Policy? <a style="font-weight: normal !important;" href="/craftcation-conference/ticket-policy/" target="_blank">(view policy)</a></p>'; 
				echo '<br><label for="yes" style="padding-right: 1rem;">Yes</label>';
				echo '<input type="radio" id="ticketPolicy_yes" name="return_policy" value="yes">'; 
				echo '<br><label for="no" style="padding-right: 1rem;">No</label>';
				echo '<input type="radio" id="ticketPolicy_no" name="return_policy" value="no" checked="true">';
			echo '</form>';
			
			echo '<script>document.addEventListener("DOMContentLoaded", function() { returnPolicy_buttonUpdate(); }, false);</script>';
			echo '<script>document.getElementById("craftcation_return_policy").addEventListener("change", function() { returnPolicy_buttonUpdate(); });</script>';
		}
		else {
			echo '<script>document.getElementById("place_order").classList.add("activate");</script>';
		}
	}
	
	return ob_get_clean();
} add_shortcode( 'craftcation', 'cc_shortcode' );

