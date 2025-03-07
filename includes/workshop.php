<?php 
require_once plugin_dir_path(__FILE__) . '../craftcation.php';
//require_once plugin_dir_path(__FILE__) . 'waitlist-js.php';

/* Used on workshop page to link to coordinating presenter */
function PresenterByID( $atts ) {
	$Presenter = get_post($atts['tid']);
	
	return '<div class="PresenterByID" style="width: 50%; text-align: center; margin: auto;"><a href="'.get_the_permalink($atts['tid']).'"><h4>'.$Presenter->post_title.'</h4><img style="" src="'. get_the_post_thumbnail_url( $atts['tid'] ).'"></a></div>';
} add_shortcode('PresenterByID', 'PresenterByID');
/* Used on workshop page to link to coordinating presenter */
function PresenterNameByID( $atts ) {
	foreach( explode( ',', $atts['tid'] ) as $key => $tid ) {
		$Presenter = get_post($tid);

		if($key != 0) { $output .= ', '; }
		$output .= '<a href="'.get_the_permalink($tid).'">'.$Presenter->post_title.'</a>';
	}
	return $output;
} add_shortcode('PresenterNameByID', 'PresenterNameByID');

/* Used on presenter page to link to coordinating workshops */
function WorkshopsByID( $atts ) {
	$query_args = array(
		'post_type'   => 'product',
		'posts_per_page' => -1,
		'meta_query'  => array(
			array(
				'value'   => $atts['tid'],
				'compare' => 'LIKE',
				'key'     => 'presenter_id',
			),
		)
	);
	
	$query = new WP_Query($query_args);

	if ( $query->have_posts() ) {
		$links = '<div class="WorkshopByID">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$links .= '<a class="WorkshopItem" href="'.get_the_permalink().'">'.get_the_title().'</a>';
		}
		$links .= '</div>';
		wp_reset_query();
		return $links;
	}
	else { return 'No results found.'; }
	
} add_shortcode('LinkWorkshops', 'WorkshopsByID');

function CSV_Image( $url ) {
	return explode( ')', explode( '(', $url )[1] )[0];
}

function WorkshopFilterDropdowns( $atts ) {
	$Output = '';
	
	$atts = shortcode_atts( array(
        'tax' => '',
    ), $atts );
	
	$tax = $atts['tax'];
	$terms = get_terms( array(
		'taxonomy'   => $tax,
		'hide_empty' => false,
	) );
	
	$Output .= '<div class="workshop-filter"><h5>'.$tax.'</h5>';
	
	foreach( $terms as $term ) {
		$Output .= '<a href="/workshops/'.$term->slug.'">' .$term->name. '</a><br>';
	}
	$Output .= '</div>';
	
	return $Output;
} add_shortcode('WorkshopFilterDropdowns', 'WorkshopFilterDropdowns');

function cc_workshop_query($query) {
	/* This function only runs when an Elementor widget calls for Query ID 'cc_workshop_query' */
	
	/* Set up query */
	$query->set('order', 'ASC');
	$tax_query = array ('relation' => 'AND');	

	/* Workshops only... */
	$include_slugs = get_term( esc_attr( get_option('cc_workshop_tags') ) )->slug;
	$tax_query []= array(
		'taxonomy' => 'product_tag',
		'field' => 'slug',
		'terms' => $include_slugs,
	);

	/* exclude tags...*/
	$exlude_slugs = array( 'workshop-session' );
	$tax_query []= array(
		'taxonomy' => 'product_tag',
		'field' => 'slug',
		'terms' => $exlude_slugs,
		'operator' => 'NOT IN',
	);
	
	/* assign tag_query */
	$query->set('tax_query', $tax_query);
	
} add_action( 'elementor/query/cc_workshop_query', 'cc_workshop_query' );
//function workshop_remove_hyphens($title) {
//	$output = $title;
//	
//	$newTitle = explode(' &#8211; ',$title);
//	if( $newTitle[1] ) { $output = $newTitle[0]; }
//	
//	return $output;
//} add_action('the_title','workshop_remove_hyphens');

function DisplayTimeslotsBySlug( $atts ) {
	$filter = shortcode_atts( array(
        'id' => '',
    ), $atts );
	extract($filter);
	
	$term_list = get_the_terms( $id, 'timeslot');

	$slugs = array_column($term_list, 'slug');
	asort($slugs);
	$names = array_column($term_list, 'name');
	asort($names);

	foreach( $slugs as $key => $term ) {
		$output .= '<a class="cc_workshop_timeslot" href="'.get_site_url().'/timeslot/'.$slugs[$key].'">'.$names[$key].'</a>';
	}
		
	return $output;
} add_shortcode( 'DisplayTimeslotsBySlug', 'DisplayTimeslotsBySlug' );

function session_autosave($post_id) {
    $tagString = get_term( esc_attr(get_option('cc_session_tags')) )->name;
//	if (get_post_type($post_id) == 'product' &&  has_term( $tagString, 'tag', $post_id ) ) {
	if ( has_term( $tagString, 'product_tag', $post_id ) ) {
		update_post_meta($post_id, "_manage_stock", "yes");
//		update_post_meta($post_id, "_stock", 0);
	}
	return;
} add_action('save_post', 'session_autosave');
