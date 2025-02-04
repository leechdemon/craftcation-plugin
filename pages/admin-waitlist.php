<?php require_once plugin_dir_path(__FILE__) . '../includes/waitlist-js.php'; ?>

<style>
	.cc_db_row { clear: both; margin: 0px; overflow: hidden; }
	.cc_db_row:nth-child(odd) .cc_db_item { background-color: #bbbbbb; }
	.cc_db_header_row  { position: sticky; top: 0; }
	.cc_db_header_row .cc_db_item { background-color: #bbbbbb; position: sticky; top: 0; font-weight: 800; border-bottom: solid 2px; }
	.cc_db_item { display: flex; width: 15%; float: left; padding: 0.5rem 1rem; height: 2.0rem; }
	.cc_db_item.id { width: 5%; }
	.cc_db_item.customerId { width: 20%; }
	.cc_db_item.waitlistDate, .cc_db_item.notificationDate, .cc_db_item.removalDate { width: 10%; text-align: right; }
	.cc_db_window { clear: both; height: 50%; overflow-y: scroll; resize: both; border: solid 2px; }
	.customerId img, .workshopId img { height: 100%; width: auto; padding-right: 0.5rem; float: left; }
	.cc_db_row:hover .customerId img, .cc_db_row:hover .workshopId img { transform: scale(1.5) translate(-0.15rem); transition-duration: 0.15s; }
	span.cc_admin_waitlist_email { font-size: smaller; padding-left: 0.25rem; }
	.cc_db_item.id button { font-size: smaller; }
	.cc_db_row.removed { opacity: 20%; }
	.cc_db_row.removed:hover { opacity: unset; transition-duration: 0.15s; }
	
</style>
<div class="wrap">
	<h1>Craftcation Waitlist Page</h1>
	<p>All Craftcation information is stored within this tab and it's sub-menus.</p>
</div>

<div class="wrap">
	<h3>Waitlist Database</h3>
	<?php  //cc_waitlist_displayTable_Filters(); ?>
	<?php cc_waitlist_discover(); ?>
	<?php cc_waitlist_displayTable(); ?>
</div>

<div class="wrap">
	<h3>Tools</h3>
	
<!--	<a href="javascript:cc_new_user( 'John', 'Smith', 'johnsmith@gmail.com' );">Purchase Ticket ("John")</a><br>-->
<!--	<a href="javascript:cc_new_user( 'Jane', 'Doe', 'janedoe_123abc321@gmail.com' );">Purchase Ticket ("Jane")</a><br>-->
<!--	<a href="javascript:cc_new_user( 'Jason', 'Elliott', 'leechdemon@gmail.com' );">Purchase Ticket ("Jason")</a><br>-->
<!--	<hr>-->
	<a href="javascript:cc_waitlist_process('54248');">cc_waitlist_process('54248')</a><br><br>

<!--	<a href="javascript:cc_waitlist_notify('54248', '41005');">cc_waitlist_notify('54248')</a><br>-->
<!--	<a href="javascript:cc_waitlist_insert_button(<?php echo get_current_user_id() ?>, '1234');">Insert waitlist Selections</a><br>-->
<!--	<a href="javascript:cc_waitlist_update_button('<?php echo get_current_user_id() ?>', 'bb');">Update waitlist Selections</a><br>-->
<!--	<hr>-->
	<?php // cc_workshop_displayTable_Filters(); ?>
	<?php // cc_workshop_displayTable(); ?>
</div>