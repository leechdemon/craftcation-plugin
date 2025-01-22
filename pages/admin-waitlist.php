<style>
	.cc_db_row { clear: both; margin: 0px; }
	.cc_db_row:nth-child(odd) .cc_db_item { background-color: #bbbbbb; }
	.cc_db_header_row  { position: sticky; top: 0; }
	.cc_db_header_row .cc_db_item { background-color: #bbbbbb; position: sticky; top: 0; font-weight: 800; border-bottom: solid 2px; }
	.cc_db_item { float: left; padding: 5px 5px; height: 20px; }
	.cc_db_window { clear: both; height: 50%; overflow-y: scroll; resize: both; border: solid 2px; }
</style>
<div class="wrap">
	<h1>Craftcation Waitlist Page</h1>
	<p>All Craftcation information is stored within this tab and it's sub-menus.</p>
</div>

<div class="wrap">
	<h3>Waitlist Database</h3>
	<?php // cc_workshop_displayTable_Filters(); ?>
	<?php // cc_workshop_displayTable(); ?>
</div>

<div class="wrap">
	<h3>Tools</h3>
	
<!--	<a href="javascript:cc_new_user( 'John', 'Smith', 'johnsmith@gmail.com' );">Purchase Ticket ("John")</a><br>-->
<!--	<a href="javascript:cc_new_user( 'Jane', 'Doe', 'janedoe_123abc321@gmail.com' );">Purchase Ticket ("Jane")</a><br>-->
<!--	<a href="javascript:cc_new_user( 'Jason', 'Elliott', 'leechdemon@gmail.com' );">Purchase Ticket ("Jason")</a><br>-->
<!--	<hr>-->
<!--
	<a href="javascript:cc_workshop_insert_button('<?php echo get_current_user_id() ?>', 'aa');">Insert Workshop Selections</a><br>
	<a href="javascript:cc_workshop_update_button('<?php echo get_current_user_id() ?>', 'bb');">Update Workshop Selections</a><br>
-->
<!--	<hr>-->
	<?php // cc_workshop_displayTable_Filters(); ?>
	<?php // cc_workshop_displayTable(); ?>
</div>