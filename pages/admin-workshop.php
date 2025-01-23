<?php
$WorkshopTag = '';
$productTags = get_terms( 'product_tag' );
foreach($productTags as $tag) {
	if( $tag->term_id == esc_attr( get_option('cc_workshop_tags') ) ) { 
		$WorkshopTag = $tag->name;
	}
}
?>

<?php $newURL = '/wp-admin/edit.php?s&post_type=product&product_tag='.$WorkshopTag; ?>
<?php header('Location: '.$newURL); ?>