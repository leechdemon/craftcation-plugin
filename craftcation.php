<?php
/*
Plugin Name: Craftcation
Description: A plugin to create and manage Ticket and Workship Databases, and tools to assist in the Craftcation ticketing process.
Author: Leechdemon
*/

require_once plugin_dir_path(__FILE__) . 'includes/cc-tools.php';
require_once plugin_dir_path(__FILE__) . 'includes/cc-tickets.php';

global $cc_db_version;
$cc_db_version = '1.0.3';

add_action( 'plugins_loaded', 'cc_update_db_check' ); function cc_update_db_check() {
    global $cc_db_version;
    
	if ( get_site_option( 'cc_db_version' ) != $cc_db_version ) {
        cc_ticket_install();
//        cc_ticket_install_data();
    }
}