<?php 
require_once plugin_dir_path(__FILE__) . '../craftcation.php';

function WSCategory_Taxonomy() {
	if(!taxonomy_exists('wscategory')) {
		$Display = 'Category';
	//	$Displays = $Display;
		$Displays = 'Categories';
		$Lower = 'wscategory';
	//	$Lowers = $Lower;
		$Lowers = 'wscategories';

		$labels = array(
			'name' => _x( $Displays, 'taxonomy general name' ),
			'singular_name' => _x( $Display, 'taxonomy singular name' ),
			'search_items' =>  __( 'Search ' .$Displays ),
			'all_items' => __( 'All '.$Displays ),
			'parent_item' => __( 'Parent '.$Display ),
			'parent_item_colon' => __( 'Parent '.$Display.':' ),
			'edit_item' => __( 'Edit '.$Display ), 
			'update_item' => __( 'Update '.$Display ),
			'add_new_item' => __( 'Add New '.$Display ),
			'new_item_name' => __( 'New '.$Display.' Name' ),
			'menu_name' => __( $Displays ),
		);
		
		$items = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'query_var' => false,
			'rewrite' => array('slug' => $Lower,'with_front' => false),
//			'rewrite' => array('slug' => 'workshops/'.$Lower),
//			'rewrite' => array('slug' => $Lower,'with_front' => false),
//			'rewrite' => array('slug' => 'category','with_front' => false),
		);

		register_taxonomy($Lower,array('presenter','workshop', 'product'),$items);
	}
} add_action( 'init', 'WSCategory_Taxonomy', 10 );
//function Craft_Taxonomy() {
//	if(!taxonomy_exists('craft')) {
//		$Display = 'Craft';
//	//	$Displays = $Display;
//		$Displays = $Display . 's';
//		$Lower = 'craft';
//	//	$Lowers = $Lower;
//		$Lowers = $Lower . 's';
//
//		$labels = array(
//			'name' => _x( $Displays, 'taxonomy general name' ),
//			'singular_name' => _x( $Display, 'taxonomy singular name' ),
//			'search_items' =>  __( 'Search ' .$Displays ),
//			'all_items' => __( 'All '.$Displays ),
//			'parent_item' => __( 'Parent '.$Display ), 
//			'parent_item_colon' => __( 'Parent '.$Display.':' ),
//			'edit_item' => __( 'Edit '.$Display ), 
//			'update_item' => __( 'Update '.$Display ),
//			'add_new_item' => __( 'Add New '.$Display ),
//			'new_item_name' => __( 'New '.$Display.' Name' ),
//			'menu_name' => __( $Displays ),
//		);
//
//		$items = array(
//			'hierarchical' => true,
//			'labels' => $labels,
//			'show_ui' => true,
//			'show_in_rest' => true,
//			'show_admin_column' => true,
//			'query_var' => false,
//			'has_archive' => true,
//			'rewrite' => array('slug' => $Lower,'with_front' => false),
////			'rewrite' => array('slug' => 'workshops/'.$Lower,'with_front' => false),
////			'rewrite' => array('slug' => 'workshops','with_front' => false),
//		);
//
//		register_taxonomy($Lower,array('presenter','workshop', 'product'),$items);
//	}
//} add_action( 'init', 'Craft_Taxonomy', 10 );
function Timeslot_Taxonomy() {
	if(!taxonomy_exists('timeslot')) {
		$Display = 'Timeslot';
	//	$Displays = $Display;
		$Displays = $Display . 's';
		$Lower = 'timeslot';
	//	$Lowers = $Lower;
		$Lowers = $Lower . 's';

		$labels = array(
			'name' => _x( $Displays, 'taxonomy general name' ),
			'singular_name' => _x( $Display, 'taxonomy singular name' ),
			'search_items' =>  __( 'Search ' .$Displays ),
			'all_items' => __( 'All '.$Displays ),
			'parent_item' => __( 'Parent '.$Display ), 
			'parent_item_colon' => __( 'Parent '.$Display.':' ),
			'edit_item' => __( 'Edit '.$Display ), 
			'update_item' => __( 'Update '.$Display ),
			'add_new_item' => __( 'Add New '.$Display ),
			'new_item_name' => __( 'New '.$Display.' Name' ),
			'menu_name' => __( $Displays ),
		);

		$items = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array('slug' => $Lower,'with_front' => false),
		);

		register_taxonomy($Lower,array('workshop', 'product'),$items);
	}
} add_action( 'init', 'Timeslot_Taxonomy', 10 );
function Difficulty_Taxonomy() {
	if(!taxonomy_exists('difficulty')) {
		$Display = 'Difficulty';
	//	$Displays = $Display;
		$Displays = 'Difficulties';
		$Lower = 'difficulty';
	//	$Lowers = $Lower;
		$Lowers = 'difficulties';

		$labels = array(
			'name' => _x( $Displays, 'taxonomy general name' ),
			'singular_name' => _x( $Display, 'taxonomy singular name' ),
			'search_items' =>  __( 'Search ' .$Displays ),
			'all_items' => __( 'All '.$Displays ),
			'parent_item' => __( 'Parent '.$Display ), 
			'parent_item_colon' => __( 'Parent '.$Display.':' ),
			'edit_item' => __( 'Edit '.$Display ), 
			'update_item' => __( 'Update '.$Display ),
			'add_new_item' => __( 'Add New '.$Display ),
			'new_item_name' => __( 'New '.$Display.' Name' ),
			'menu_name' => __( $Displays ),
		);

		$items = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array('slug' => $Lower,'with_front' => false),
		);

		register_taxonomy($Lower,array('workshop', 'product'),$items);
	}
} add_action( 'init', 'Difficulty_Taxonomy', 10 );
function TermsByID( $atts ) {
	/* Used on workshop page to link to coordinating crafts */	
	/* Used on workshop page to link to coordinating timeslots */	
	
	$atts = shortcode_atts( array(
//        'link' => true,
        'tid' => 0,
        'tax' => 'craft',
        'timeslot' => 0,
    ), $atts );
	
	$Output = '';
	
	if( $atts['tax'] != '' ) {
		$Colors  []= '#d6cf39';
		$Colors  []= '#ff7f62';
		$Colors  []= '#1ebcbf';
		$Colors  []= '#f16ba8';
		$Colors  []= '#1AB687';
	
		$Colors  []= '#d6cf39';
		$Colors  []= '#ff7f62';
		$Colors  []= '#1ebcbf';
		$Colors  []= '#f16ba8';
		$Colors  []= '#1AB687';

		$Terms = get_the_terms($atts['tid'],$atts['tax']);
		$Output .= '<div style="clear: both;"><strong style="float: left; padding-right: 0.25rem;">'.ucfirst($atts['tax']).':</strong>';
		foreach($Terms as $i=>$Term) {
			if($i != 0) {
				$Output .= ', ';
			}
			$Output .= '<a class="ws_term_sub" style="background-color: '.$Colors[$i].'" href="'.get_term_link( $Term ).'">'.$Term->name.'</a>';
		}
		$Output .= '</div>';
	}
	return $Output;
	
//	return '<div class="PresenterByID" style="width: 25%; text-align: center;"><a href="'.get_the_permalink($atts['tid']).'"><h4>'.$Terms->post_title.'</h4><img style="padding: 0.5rem 2rem;" src="'. get_the_post_thumbnail_url( $atts['tid'] ).'"></a></div>';
	
} add_shortcode('LinkTerms', 'TermsByID');
function TaxSidebar( $atts ) {
	/* Used on archive page */	
	
	$atts = shortcode_atts( array(
        'tid' => 0,
        'tax' => '',
        'timeslot' => 0,
    ), $atts );
	
	$Output = '<div class="ws_terms" style="width: 100%;">';
	if( $atts['tax'] != '' ) {
		$Terms = get_terms( array(
			'taxonomy'	=> $atts['tax'],
			'orderby'	=> 'slug',
		) );

		if(!function_exists('CheckIfActiveTax')) {
			function CheckIfActiveTax( $ThisTax, $ThisTerm ) {
				if ( is_tax($ThisTax, $ThisTerm->slug ) ) { return ' active'; }
			}
		}
		
		$TaxonomyName = get_taxonomy( $atts['tax'] )->label;
		$Output .= ''.$TaxonomyName.'';
		foreach($Terms as $i=>$Term) {
			if( $Term->parent == 0 ) {
				$Output .= '<ul>';
				$Output .= '<a class="'.CheckIfActiveTax( $atts['tax'], $Term).'" href="'.get_term_link( $Term ).'">'.$Term->name.'</a>';
				$firstPass = true;
				foreach($Terms as $T) {
					if( $T->parent ) {
						if( $T->parent == $Term->term_id ) {
							/* If there's subcategories, display an "All" link at the top. */
							if( $firstPass ) { 
								$firstPass = false;
								$Output .= '<li><a class="'.CheckIfActiveTax( $atts['tax'], $Term).'" href="'.get_term_link( $Term ).'">All '.$Term->name.'</a></li>';
							}

							$Output .= '<li><a class="'.CheckIfActiveTax( $atts['tax'], $T).'" href="'.get_term_link( $T ).'">'.$T->name.'</a></li>';
						}
					}
				}
				$Output .= '</ul>';
			}
		}
	}
	else { $Output .= '<a href="'.site_url().'/all-workshops">All Workshops</a>'; }
	$Output .= '</div>';

	return $Output;
	
//	return '<div class="PresenterByID" style="width: 25%; text-align: center;"><a href="'.get_the_permalink($atts['tid']).'"><h4>'.$Terms->post_title.'</h4><img style="padding: 0.5rem 2rem;" src="'. get_the_post_thumbnail_url( $atts['tid'] ).'"></a></div>';
	
} add_shortcode('TaxSidebar', 'TaxSidebar');
function AutoExpandTaxSidebar() {
	echo "<script>setTimeout(() => {
		var ws_terms = document.getElementsByClassName('ws_terms');
		
		/* Don't try this unless we find this class */
		if( ws_terms.length > 0 ) {
			var active = document.getElementsByClassName('active')[0];
			if( active.parentElement.tagName == 'LI' ) { var activeList = active.parentElement.parentElement; }
			else { var activeList = active.parentElement; }

			for (var i = 0; i < activeList.children.length; i++) {
				if( i == 0 ) { activeList.children[i].classList.add('active'); }
				else { activeList.children[i].style.display = 'list-item'; }
			}
		}
	}, '600');</script>";
} add_action('wp_footer','AutoExpandTaxSidebar');
