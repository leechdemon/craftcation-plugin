<?php
/*
Plugin Name: Craftcation
Description: A plugin to create and manage Ticket and Workship Databases, and tools to assist in the Craftcation ticketing process.
Author: Leechdemon
*/

require_once plugin_dir_path(__FILE__) . 'includes/tools.php';

require_once plugin_dir_path(__FILE__) . 'includes/db-tickets.php';
require_once plugin_dir_path(__FILE__) . 'includes/db-workshops.php';
/* Remove JS? Used for WorkshopDB? (deprecated) */
//require_once plugin_dir_path(__FILE__) . 'includes/db-workshops-js.php';

require_once plugin_dir_path(__FILE__) . 'includes/cpt-presenter.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-workshop.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-terms.php';

global $cc_db_version, $cc_workshop_db_version;
$cc_db_version = '1.0.3';
$cc_workshop_db_version = '1.0.3';

add_action( 'plugins_loaded', 'cc_update_db_check' ); function cc_update_db_check() {
    global $cc_db_version, $cc_workshop_db_version;
    
	if ( get_site_option( 'cc_db_version' ) != $cc_db_version ) {
        cc_ticket_install();
    }
	if ( get_site_option( 'cc_workshop_db_version' ) != $cc_workshop_db_version ) {
        cc_workshop_install();
    }
}

//add_action( 'wp_head', 'cc_display_query' ); function cc_display_query() {
//	global $wp_query;
//	$Output = json_encode( $wp_query );
//	echo "<script>console.log(".$Output.");</script>";
//}

add_action( 'wp_head', 'cc_css' ); function cc_css() {
	/* This should go in Elementor page templates instead, when possible. */
	
//	echo '<style>
//		.ws_term { width: 20%; display: contents; }
//		a.ws_term_sub, a.ws_term { display: block; float: left; text-align: center; color: white; padding: 10px; margin: 5px; }
//		a.ws_term { font-weight: 800; width: 100%; }
//		a.ws_term_sub { display: block; float: left; text-align: center; color: white; padding: 0px 10px; margin: 5px; }
//	</style>';
}
