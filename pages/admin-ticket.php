<?php
$TicketTag = '';
$productTags = get_terms( 'product_tag' );
foreach($productTags as $tag) {
	if( $tag->term_id == esc_attr( get_option('cc_ticket_tags') ) ) { 
		$TicketTag = $tag->name;
	}
}
?>
	
<?php $newURL = '/wp-admin/edit.php?s&post_type=product&product_tag='.$TicketTag; ?>
<?php header('Location: '.$newURL); ?>