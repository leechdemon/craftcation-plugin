<style>
	.cc_db_row { clear: both; margin: 5px; }
	.cc_db_row:nth-child(odd) .cc_db_item { background-color: #bbbbbb; }
	.cc_db_header_row  { position: sticky; top: 0; }
	.cc_db_header_row .cc_db_item { background-color: #bbbbbb; position: sticky; top: 0; font-weight: 800; }
	.cc_db_item { float: left; padding: 5px 10px; }
	.cc_db_window { clear: both; height: 50%; overflow-y: scroll; resize: both; }
</style>
<script>
function cc_tag_selector() { 
	console.log( document.getElementById("cc_ticket_tags_selector").value );
}
</script>

<div class="wrap">
	<h1>Craftcation Settings Page</h1>
<!--	<p>All Craftcation information is stored within this tab and it's sub-menus.</p>-->
</div>

<div class="wrap">
	<h3>Ticket Options</h3>
	
	<?php
		$ticketTagIDs = explode(',', esc_attr(get_option('cc_ticket_tags')) );
		$productTags = get_terms( 'product_tag' );

		foreach($productTags as $productTag) {
			foreach($ticketTagIDs as $tagID) {
				if($tagID == $productTag->term_id) { $tagString .= $productTag->name; }
			}
		}	
	?>
	<p>Orders containing products tagged with "<strong><?php echo $tagString; ?></strong>" will be added to the Tickets DB.</p>
	<form method="post" action="options.php">
    <?php settings_fields( 'cc-ticket-settings-group' ); ?>
    <?php do_settings_sections( 'cc-ticket-settings-group' ); ?>

	<select id="cc_ticket_tags" name="cc_ticket_tags">
		<?php
			echo '<option value="">-- Select Product Tag --</option>';
		
			$productTags = get_terms( 'product_tag' );
			foreach($productTags as $tag) {
				if( $tag->term_id == esc_attr( get_option('cc_ticket_tags') ) ) { $isChecked = " - 	&#10004;"; } else { $isChecked = ''; }
				echo '<option value="'.$tag->term_id.'">'.$tag->name.$isChecked.'</option>';
			}
		?>
	</select>
		
<!--
	<a href="javascript:cc_tag_selector();">Add Tag</a>
	<div style="clear: both; width: 100%;">
	<?php
		foreach( $ticketTagIDs as $tagID ) {
			foreach( $productTags as $term ) {
				if( $term->term_id == $tagID) {
					echo '<div id="'.$tagID.'" style="width: auto; float: left;">'.$term->name.'</div>';
				}
			}
		}
	?>
	</div>
-->
		
    <?php submit_button(); ?>

</form>
<!--        	<input type="text" name="new_option_name" value="<?php echo esc_attr( get_option('cc_ticket_tags') ); ?>" />-->
	
<!--
	<form method="post" action="options.php"> 
	<label for="tag_selector">Product Tags to be added to the Ticket DB:</label>
	<select id="tag_selector" name="tag_selector">
		<?php
			echo '<option value="">-- Select Product Tag --</option>';
			
		 	$productTags = get_terms( 'product_tag' );
			foreach($productTags as $tag) {
				echo '<option value="'.$tag->term_id.'">'.$tag->name.'</option>';
			}
		?>
	</select>
	<a href="javascript:cc_tag_selector();">Add Tag</a>
-->
	
	
<!--	<p>Product Tags currently being added to the Ticket DB: 0</p>-->
	<?php // cc_ticket_displayTable_Filters(); ?>
	<?php // cc_ticket_displayTable(); ?>
	<?php
	

	
	
	
	?>
	
	
	
	
	
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