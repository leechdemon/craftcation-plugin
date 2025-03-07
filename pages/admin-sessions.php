<?php

$sessionTagIDs = explode(',', esc_attr(get_option('cc_session_tags')) );
$tagString = get_term( $sessionTagIDs[0] )->name;

?>

<?php $newURL = '/wp-admin/edit.php?s&post_type=product&product_tag='.$tagString.'&orderby=title&order=asc'; ?>
<?php header('Location: '.$newURL); ?>