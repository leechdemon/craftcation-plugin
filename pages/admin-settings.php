<style>
	.cc_db_row { clear: both; margin: 5px; }
	.cc_db_row:nth-child(odd) .cc_db_item { background-color: #bbbbbb; }
	.cc_db_header_row  { position: sticky; top: 0; }
	.cc_db_header_row .cc_db_item { background-color: #bbbbbb; position: sticky; top: 0; font-weight: 800; }
	.cc_db_item { float: left; padding: 5px 10px; }
	.cc_db_window { clear: both; height: 50%; overflow-y: scroll; resize: both; }
	.cc_setting_indent { margin-left: 1rem; }
	.cc_setting_indent p.submit input { margin-left: -1rem; }
</style>
<script>
function cc_tag_selector() { 
	console.log( document.getElementById("cc_ticket_tags_selector").value );
}
</script>

<div class="wrap">
	<h1>Craftcation Settings Page</h1>
</div>

<div class="wrap">
	<h3>Ticket Options</h3>
	
	<?php
		$productTags = get_terms( array(
			'taxonomy'   => 'product_tag',
			'hide_empty' => false,
		) );
		
		$ticketTagIDs = explode(',', esc_attr(get_option('cc_ticket_tags')) );
		$tagString = get_term( $ticketTagIDs[0] )->name;
	?>
	<p class="cc_setting_indent">Tickets will include any orders containing products tagged with "<strong><?php echo $tagString; ?></strong>".</p>
	<form class="cc_setting_indent" method="post" action="options.php">
		<?php settings_fields( 'cc-ticket-settings-group' ); ?>
		<?php do_settings_sections( 'cc-ticket-settings-group' ); ?>

		<select id="cc_ticket_tags" name="cc_ticket_tags">
			<?php
				echo '<option value="0">-- Select Product Tag --</option>';

				foreach($productTags as $tag) {
					if( $tag->term_id == esc_attr( get_option('cc_ticket_tags') ) ) { $isSelected = ' selected="true"'; $isChecked = " - 	&#10004;"; } else { $isChecked = ''; $isSelected = ''; }
					echo '<option value="'.$tag->term_id.'"'.$isSelected.'>'.$tag->name.$isChecked.'</option>';
				}
			?>
		</select>
				
	    <?php submit_button(); ?>
	</form>
</div>

<div class="wrap">
	<h3>Workshop Options</h3>
		<?php
			$workshopTagIDs = explode(',', esc_attr(get_option('cc_workshop_tags')) );
			$tagString = get_term( $workshopTagIDs[0] )->name;
		?>
	<p class="cc_setting_indent">Workshops include any products tagged with "<strong><?php echo $tagString; ?></strong>".</p>
	<form class="cc_setting_indent" method="post" action="options.php">
		<?php settings_fields( 'cc-workshop-settings-group' ); ?>
		<?php do_settings_sections( 'cc-workshop-settings-group' ); ?>

		<select id="cc_workshop_tags" name="cc_workshop_tags">
			<?php
				echo '<option value="0">-- Select Product Tag --</option>';

				foreach($productTags as $tag) {
					if( $tag->term_id == esc_attr( get_option('cc_workshop_tags') ) ) { $isSelected = ' selected="true"'; $isChecked = " - 	&#10004;"; } else { $isChecked = ''; $isSelected = ''; }
					echo '<option value="'.$tag->term_id.'"'.$isSelected.'>'.$tag->name.$isChecked.'</option>';
				}
			?>
		</select>
				
	    <?php submit_button(); ?>
	</form>
</div>

<div class="wrap">
	<h3>Session Options</h3>
		<?php
			$sessionTagIDs = explode(',', esc_attr(get_option('cc_session_tags')) );
			$tagString = get_term( $sessionTagIDs[0] )->name;
		?>
	<p class="cc_setting_indent">Sessions include any products tagged with "<strong><?php echo $tagString; ?></strong>".</p>
	<form class="cc_setting_indent" method="post" action="options.php">
		<?php settings_fields( 'cc-session-settings-group' ); ?>
		<?php do_settings_sections( 'cc-session-settings-group' ); ?>

		<select id="cc_session_tags" name="cc_session_tags">
			<?php
				echo '<option value="0">-- Select Product Tag --</option>';

				foreach($productTags as $tag) {
					if( $tag->term_id == esc_attr( get_option('cc_session_tags') ) ) { $isSelected = ' selected="true"'; $isChecked = " - 	&#10004;"; } else { $isChecked = ''; $isSelected = ''; }
					echo '<option value="'.$tag->term_id.'"'.$isSelected.'>'.$tag->name.$isChecked.'</option>';
				}
			?>
		</select>

		<?php
			$sessionIgnoreTagIDs = explode(',', esc_attr(get_option('cc_session_ignore_tags')) );
			$tagString = get_term( $sessionIgnoreTagIDs[0] )->name;
			echo '<p>Workshop Selection will IGNORE any products tagged with "<strong>'.$tagString.'</strong>".</p>';
		?>
			<select id="cc_session_ignore_tags" name="cc_session_ignore_tags">
			<?php
				echo '<option value="0">-- Select Product Tag --</option>';

				foreach($productTags as $tag) {
					if( $tag->term_id == esc_attr( get_option('cc_session_ignore_tags') ) ) { $isSelected = ' selected="true"'; $isChecked = " - 	&#10004;"; } else { $isChecked = ''; $isSelected = ''; }
					echo '<option value="'.$tag->term_id.'"'.$isSelected.'>'.$tag->name.$isChecked.'</option>';
				}
			?>
		</select>
				
	    <?php submit_button(); ?>
	</form>
</div>

<div class="wrap">
	<h3>Waitlist Options</h3>
	<form class="cc_setting_indent" method="post" action="options.php">
		<?php settings_fields( 'cc-waitlist-settings-group' ); ?>
		<?php do_settings_sections( 'cc-waitlist-settings-group' ); ?>

		How long should a customer's space on the waitlist be reserved?
		<br><input id="waitlist_duration_24h" name="cc_duration" type="radio"><label>24 Hours</label>
		<br><input id="waitlist_duration_5m" name="cc_duration" type="radio"><label>5 Minutes</label>
		<br><input id="waitlist_duration_30s" name="cc_duration" type="radio"><label>30 Seconds</label>
		<br><input id="waitlist_duration_c" name="cc_duration" type="radio"><label>Custom</label>
		<br><input id="cc_waitlist_duration" name="cc_waitlist_duration" value="<?php echo esc_attr( get_option('cc_waitlist_duration') ) ?>">
		
		<script>
			/* Add Event Listeners */
			const waitlist_duration_24h = document.getElementById("waitlist_duration_24h");
			waitlist_duration_24h.addEventListener("change", (event) => {
				document.getElementById( "cc_waitlist_duration" ).value = "-24 hours";
				document.getElementById( "cc_waitlist_duration" ).style.display = "none";
			});
			const waitlist_duration_5m = document.getElementById("waitlist_duration_5m");
			waitlist_duration_5m.addEventListener("change", (event) => {
				document.getElementById( "cc_waitlist_duration" ).value = "-5 minutes";
				document.getElementById( "cc_waitlist_duration" ).style.display = "none";
			});
			const waitlist_duration_30s = document.getElementById("waitlist_duration_30s");
			waitlist_duration_30s.addEventListener("change", (event) => {
				document.getElementById( "cc_waitlist_duration" ).value = "-30 seconds";
				document.getElementById( "cc_waitlist_duration" ).style.display = "none";
			});
			const waitlist_duration_c = document.getElementById("waitlist_duration_c");
			waitlist_duration_c.addEventListener("change", (event) => {
				document.getElementById( "cc_waitlist_duration" ).style.display = "block";
			});

			/* Preselect */
			var duration = "<?php echo esc_attr( get_option('cc_waitlist_duration') ) ?>";
			if( duration == "-24 hours" ) { document.getElementById("waitlist_duration_24h").click(); }
			else if( duration == "-5 minutes" ) { document.getElementById("waitlist_duration_5m").click(); }
			else if( duration == "-30 seconds" ) { document.getElementById("waitlist_duration_30s").click(); }
			else { document.getElementById("waitlist_duration_c").click(); }
		</script>
		
		<?php
			$waitlistIgnoreTagIDs = explode(',', esc_attr(get_option('cc_waitlist_ignore_tags')) );
			$tagString = get_term( $waitlistIgnoreTagIDs[0] )->name;
			echo '<p>Waitlists will be DISABLED on any products tagged with "<strong>'.$tagString.'</strong>".</p>';
		?>
			<select id="cc_waitlist_ignore_tags" name="cc_waitlist_ignore_tags">
			<?php
				echo '<option value="0">-- Select Product Tag --</option>';

				foreach($productTags as $tag) {
					if( $tag->term_id == esc_attr( get_option('cc_waitlist_ignore_tags') ) ) { $isSelected = ' selected="true"'; $isChecked = " - 	&#10004;"; } else { $isChecked = ''; $isSelected = ''; }
					echo '<option value="'.$tag->term_id.'"'.$isSelected.'>'.$tag->name.$isChecked.'</option>';
				}
			?>
		</select>
				
	    <?php submit_button(); ?>
	</form>
</div>

<?php

//$orderId = '1454';
//$order = new WC_Order( $orderId );
//Test($order);
//
//$orderId = '1454';
//$order = wc_get_order( $orderId );
//foreach($order->get_items() as $item) {
//	$product = wc_get_product( $item->get_product_id() );
//	$tagIDs = $product->get_tag_ids();
//	foreach($tagIDs as $tagID) {
//		if( $tagID == '' ) {
//			cc_ticket_insert($orderId, $order->get_user_id(), $orderId.','.$orderId);
//		}
//	}
//}

//cc_ticket_insert($orderId, $order->get_user_id(), $orderId.','.$orderId);
?>