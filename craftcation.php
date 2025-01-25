<?php
/*
Plugin Name: Craftcation
Description: A plugin to create and manage Ticket and Workship Databases, and tools to assist in the Craftcation ticketing process.
Author: Leechdemon
Version: 1.2.9
*/

require_once plugin_dir_path(__FILE__) . 'tools.php';

//require_once plugin_dir_path(__FILE__) . 'includes/db-tickets.php';
//require_once plugin_dir_path(__FILE__) . 'includes/db-workshops.php';
/* Remove JS? Used for WorkshopDB? (deprecated) */
//require_once plugin_dir_path(__FILE__) . 'includes/db-workshops-js.php';

require_once plugin_dir_path(__FILE__) . 'includes/cpt-presenter.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-terms.php';
require_once plugin_dir_path(__FILE__) . 'includes/db-waitlist.php';
require_once plugin_dir_path(__FILE__) . 'includes/workshop.php';

global $cc_db_version, $cc_workshop_db_version, $cc_waitlist_db_version;
//$cc_db_version = '1.2.9';
//$cc_workshop_db_version = '1.2.9';
$cc_waitlist_db_version = '1.3.00';

function cc_update_db_check() {
    global $cc_waitlist_db_version;
//    global $cc_db_version, $cc_workshop_db_version;
    
//	if ( get_site_option( 'cc_db_version' ) != $cc_db_version ) {
//        cc_ticket_install();
//    }
//	if ( get_site_option( 'cc_workshop_db_version' ) != $cc_workshop_db_version ) {
//        cc_workshop_install();
//    }
	if ( get_site_option( 'cc_waitlist_db_version' ) != $cc_waitlist_db_version ) {
        cc_waitlist_install();
    }
} add_action( 'plugins_loaded', 'cc_update_db_check' ); 