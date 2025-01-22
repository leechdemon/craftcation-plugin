<?php 
require_once plugin_dir_path(__FILE__) . '../craftcation.php';

function Workshop_Post_Type() {
	$Display = 'Workshop';
	$Displays = $Display . 's';
	$Lower = 'workshop';
	$Lowers = $Lower . 's';
	$Icon = 'dashicons-feedback';

	$labels = array(
		'name'                  => _x( $Displays, 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( $Display, 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( $Displays, 'text_domain' ),
		'name_admin_bar'        => __( $Display, 'text_domain' ),
		'archives'              => __( $Display. ' List', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All '. $Displays, 'text_domain' ),
		'add_new_item'          => __( 'Add New ' .$Displays, 'text_domain' ),
		'add_new'               => __( 'Add ' .$Display, 'text_domain' ),
		'new_item'              => __( 'New ' .$Display, 'text_domain' ),
		'edit_item'             => __( 'Edit ' .$Display, 'text_domain' ),
		'update_item'           => __( 'Update ' .$Display, 'text_domain' ),
		'view_item'             => __( 'View ' .$Display, 'text_domain' ),
		'view_items'            => __( 'View ' .$Displays, 'text_domain' ),
		'search_items'          => __( 'Search ' .$Display, 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( $Display. ' Photo', 'text_domain' ),
		'set_featured_image'    => __( 'Set '.$Lower.' photo', 'text_domain' ),
		'remove_featured_image' => __( 'Remove '.$Lower.' photo', 'text_domain' ),
		'use_featured_image'    => __( 'Use as '.$Lower.' photo', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into '.$Lower, 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this '.$Lower, 'text_domain' ),
		'items_list'            => __( $Display. ' list', 'text_domain' ),
		'items_list_navigation' => __( $Display. ' list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter '.$Lower.' list', 'text_domain' ),
	);
	$rewrite = array(
		'slug'                  => $Lowers,
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( $Display, 'text_domain' ),
		'description'           => __( $Display.' Profiles', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'editor', 'title', 'thumbnail', 'revisions', 'page-attributes' ),
		'taxonomies'            => array( 'wscategory', 'craft', 'timeslot', 'difficulty'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 4,
		'menu_icon'             => $Icon,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite' => array('slug' => $Lowers,'with_front' => false),
		'capability_type'       => 'post',
	);
	register_post_type( $Lower, $args );
} add_action( 'init', 'Workshop_Post_Type', 0 );

/* Used on workshop page to link to coordinating presenter */
function PresenterByID( $atts ) {
	$Presenter = get_post($atts['tid']);
	
	return '<div class="PresenterByID" style="width: 50%; text-align: center; margin: auto;"><a href="'.get_the_permalink($atts['tid']).'"><h4>'.$Presenter->post_title.'</h4><img style="" src="'. get_the_post_thumbnail_url( $atts['tid'] ).'"></a></div>';
} add_shortcode('PresenterByID', 'PresenterByID');
/* Used on workshop page to link to coordinating presenter */
function PresenterNameByID( $atts ) {
	$Presenter = get_post($atts['tid']);
	
	return '<a href="'.get_the_permalink($atts['tid']).'">'.$Presenter->post_title.'</a>';
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

function Process_WorkshopSelectionUpdates() {
	if( isset( $_POST['order'] ) ) {
		unset($_POST ['order'] );
		$hasItems = false;
		$hasRefunds = false;
		
		/* Build workshopSelection, etc */
		$w = get_workshopSelection();
		$workshops = $w[0];
		$slots = $w[1];
		$orders = $w[2];
		$workshopSelection = $w[3];
		unset($w);

		/* Build order_request */
		foreach( $_POST as $key => $item ) {
			$t = explode('_',$key);
			if( $t[0] == 'timeslot' ) {
				/* Process Timeslot items */
				
				/* If workshopSelection item is unchanged/not a duplicate, prevent it from being added to the New Order. */
				$notDuplicate = true;
				foreach( $workshopSelection as $w => $workshopSelection_slot ) {
					foreach( $workshopSelection_slot as $slotItem ) {
						if( $slotItem == $item ) {
							$notDuplicate = false;
						}
					}
				}
								
				/* Check for form errors... */
				if( $item > 0 && $notDuplicate ) { 

					$inStock = false;
					$stockCheck = wc_get_product( $item );
					/* If it's still in stock... */
					if ( $stockCheck->is_in_stock() ) { 
						/* Add the item to the order */
						$hasItems = true;
						$order_req[ $t[1] ] = $item;

						/* If we're adding an item, and there's already a workshopSelection... */
						if( $workshopSelection[ $t[1] ] ) {
							$hasRefunds = true;
							$refund_req[ $t[1] ] = $workshopSelection[ $t[1] ];
						}
					}

				}

			}
		}
		/* End Order/Refund Requests */
//		echo 'orderReq: '. json_encode($order_req) .'<br>';
//		echo 'refundReq: '. json_encode($refund_req) .'<br>';
		
		/* Add New Items */
		if( $hasItems ) {
			WorkshopSelection_AddOrder( $order_req );
		}
		/* Refund Old Items */
		if( $hasRefunds ) {
			WorkshopSelection_RefundItems( $refund_req );
		}	
	}
	
	/* Process Waitlist changes */
	if( isset( $_GET['waitlist'] ) ) {
		$Output .= '<script>console.log("'.$_GET['waitlist'].'");</script>';
	}

	return $Output;
} add_shortcode('Process_WorkshopSelectionUpdates', 'Process_WorkshopSelectionUpdates');
function WorkshopSelection_AddOrder( $order_req ) {
	$args = array(
		'status' => 'wc-complete',
		'customer_id' => wp_get_current_user()->id,
	);
	$order = wc_create_order( $args );

	foreach( $order_req as $product_id ) {
		$order->add_product( get_product( $product_id ) , 1 );
	}
	
	$order->payment_complete();
	return $order;
}
function WorkshopSelection_RefundItems( $refund_req ) {
	/* Build list of All Orders for current customer, status = "Processing" */
	$args = array(
		'customer_id' => wp_get_current_user()->id,
//		'status' => 'processing',
		'limit' => -1,
//			'order' => 'DESC',
//			'order' => 'ASC',
		'return' => 'ids',
		'meta_query' => array(
			array(
				'key' => '_product_id', // Meta key to filter by product ID
				'value' => $refund_req, // Product IDs to filter
				'compare' => 'IN', // Use 'IN' to find orders that contain any of the specified product IDs
			),
    	),
	);
	$orders = wc_get_orders($args);

	/* For every timeslot we're discussing... */
	foreach( $refund_req as $slot ) {

		/* For every customer order... */
		foreach( $orders as $o ) {
			/* Search every line item... */
			$order = wc_get_order( $o );
			$items = $order->get_items();
			foreach( $order->get_items() as $item_id => $item ) {
				/* for each item in this timeslot */
				foreach( $slot as $refund_req_item ) {
					/* If this item matches the current refund_req_item */
					if( $item['product_id'] == $refund_req_item ) {
						/* If the product is in stock, restock as expected. */
						/* If the product is sold out, do not restock it. */
						$stockCheck = wc_get_product( $item['product_id'] );
						if ( $stockCheck->is_in_stock() ) { $restockItems = true; }
						else { $restockItems = false; }

						/* Refund it */
						$line_items[ $item_id ] = array( 'qty' => 1, );
						$refund = wc_create_refund( array(
//							'amount'         => 0,
							'reason'         => '',
							'order_id'       => $o,
							'line_items'     => $line_items,
							'restock_items'  => $restockItems,
						));	

					}
					
				}
			}
		}
		
	}
}

function DisplayWorkshopSchedule( $atts ) {
	/* if we have a user... */
	if( wp_get_current_user()->id > 0 ) {
		/* Build list of workshopsSelection */
		$w = get_workshopSelection();
		$workshops = $w[0];
		$slots = $w[1];
		$orders = $w[2];
		$workshopSelection = $w[3];
		
		/* Display Things */
		/* Display Things */
		$Output .= '<style>
			.workshop-selections, .get_response { background: #DDD; padding: 0.5rem; margin: 0rem; border: solid 1px red; }
//			.workshop-selections { display: none; }
//			.get_response { display: none; }
		
			.workshop_schedule { display: grid; margin: 1rem 2rem; }
			.workshop_timeslot { width: 100%; background-color: #666; clear: both; padding: 0.5rem; color: white; font-weight: 500; }
			.workshop_timeslot:nth-child(even) { background-color: #999; }
			.workshop_item { width: 18%; float: left; padding: 0rem 0.5rem; }
			.workshop_item:first-child { width: 30%; }
			.workshop_label { font-weight: 800; }
			option.item_grayedout { color: #ccc !important; }
		</style>';
		
		/* Draw the workshop selections */
		$Output .= '<div class="workshop-selections">Workshop Selections:<br>'. json_encode($workshopSelection) .'</div>';
//json_encode($workshopSelection) .'</div>';

		/* Draw the Slots */
		$Output .= '<div class="workshop_schedule">';
			/* Draw the Headers */
			$Output .= '<div class="workshop_timeslot">
				<div class="workshop_item workshop_label">Timeslot</div>
				<div class="workshop_item workshop_label">My Registration</div>
				<div class="workshop_item workshop_label">New Registration</div>
				<div class="workshop_item workshop_label">Workshop Notes</div>
			</div>';
		foreach( $slots as $slot ) {
			$s = $slot->slug;
			$CurrentWorkshop = '(Select a Workshop)';
			$Selection_id = '';

			if( isset($workshopSelection[$s] ) ) {
				if( count( $workshopSelection[$s] ) > 1 ) {
					$CurrentWorkshop = 'Error<span style="font-size: x-small; padding-left: 0.25rem;">(Multiple classes purchased for this timeslot.)</span>';
				} elseif( $workshopSelection[$s][0] ) {
					/* If there's only 1 selection, it is  "Selected". */
					$Selection_id = $workshopSelection[$s][0];
					$CurrentWorkshop = '<a href="'.get_permalink( $Selection_id ).'">'.get_post( $Selection_id )->post_title.'</a>';
				}
	   		}

			$Output .= '<div class="workshop_timeslot">
				<div class="workshop_item">'.$slot->name.'</div>
				<div class="workshop_item">'.$CurrentWorkshop.'</div>
				<select id="timeslot_'.$s.'" name="timeslot_'.$s.'" class="workshop_item timeslot" form="workshopSelection">
					<option value="0">--- Select Workshop ---</option>';
					foreach( $workshops as $workshop ) {
						if( get_the_terms( $workshop['id'], 'timeslot' )[0]->slug == $s ) { 
							$IsGrayedOut = $IsSoldOut = $IsSelected = $IsStarred = '';
							if( $workshop['id'] == $Selection_id ) { $IsSelected = ' selected="true"'; $IsStarred = ' **'; }
							if( $workshop['stock_quantity'] < 1 ) { $IsGrayedOut = ' class="item_grayedout"'; $IsSoldOut = ' (Sold Out)'; } 

							$Output .= '<option value="'.$workshop['id'].'"'.$IsSelected.$IsGrayedOut.'>';
							$Output .= $workshop['name'].$IsStarred.$IsSoldOut.'</option>';
						}
					}
				$Output .= '</select>';
			
				$Output .= '<div class="workshop_item">';
				foreach( $workshops as $workshop ) {
					if( get_the_terms( $workshop['id'], 'timeslot' )[0]->slug == $s ) { 
//						$Output .= '<div id="workshop_notes_item_'.$workshop['id'].'" class="workshop_notes_'.$s.'" style="display:none;">'.$workshop['name'].'</div>';
						$IsSelected = ' style="display:none;"';
						if( $workshop['id'] == $Selection_id ) { $IsSelected = ''; }
						
//						$Output .= '<div id="workshop_notes_item_'.$workshop['id'].'" class="workshop_notes_'.$s.'"'.$IsSelected.'>'.substr($workshop['name'],0,1).' - '.$workshop['stock_quantity'].' spaces</div>';
						$Output .= '<div id="workshop_notes_item_'.$workshop['id'].'" class="workshop_notes_'.$s.'"'.$IsSelected.'>';
						if( $workshop['stock_quantity'] < 1 ) {
							$Output .= DisplayWaitlistButton( $workshop['id'] );
						}
						$Output .= '</div>';
					}
				}
				$Output .= '<script>
				
					const selectElement'.$s.' = document.getElementById("timeslot_'.$s.'");

					selectElement'.$s.'.addEventListener("change", (event) => {
						var workshopNotes = document.getElementsByClassName( "workshop_notes_'.$s.'" );
						for( var n = 0; n < workshopNotes.length; n++ ) {
							workshopNotes[n].style.display = "none";
						}
						
						document.getElementById( "workshop_notes_item_"+event.target.value ).style.display = "block";
					});
				
				
				</script>';
				$Output .= '</div>';
			$Output .= '</div>';
		}
		$Output .= '<form action="#" method="post" id="workshopSelection">
			<input type="hidden" name="order" id="order" value="order">
			<input type="submit" value="Save Workshop Selections" class="btn">
		</form>';

		return $Output;				
	}
	else {
		return 'Please log in.';
	}
} add_shortcode('DisplayWorkshopSchedule', 'DisplayWorkshopSchedule');
function CSV_Image( $url ) {
	return explode( ')', explode( '(', $url )[1] )[0];
}
function get_workshopSelection() {
	/* Build list of All Workshops */
	$args = array(
		'type' => 'workshop',
	);
	$w = wc_get_products( $args );
	$workshops = array();
	foreach( $w as $key => $workshop ) {
		$workshops[$key]= $workshop->get_data();
		$workshops[$key]['presenter_id'] = $workshop->get_meta('presenter_id');
	}

	/* Build list of All Slots, Workshop Selections */
	$slots = get_terms('timeslot');

	/* Build list of All Orders for current customer, status = "Processing" */
	$args = array(
		'customer_id' => wp_get_current_user()->id,
//		'status' => 'processing',
		'limit' => -1,
//			'order' => 'DESC',
//			'order' => 'ASC',
		'return' => 'ids',
		'meta_query' => array(
			array(
				'key' => '_product_id', // Meta key to filter by product ID
				'value' => $product_ids, // Product IDs to filter
				'compare' => 'IN', // Use 'IN' to find orders that contain any of the specified product IDs
			),
    	),
	);
	$orders = wc_get_orders($args);
	
	/* For each workshop item (variable, large amount (100+?) */
	$workshopSelection = array();
	foreach($workshops as $w => $workshop) {

		/* For each customer order... */
		foreach($orders as $key => $order) {
			$thisOrder = new WC_Order( $order );

			/* ... For each line item... */
			foreach ( $thisOrder->get_items() as $item_id => $item ) {
				/* ...if it matches a Workshop Product ID... */
				if( $item['product_id'] == $workshops[$w]['id'] ) {
					$itemReturned = false;

					/* ... See if the item hasn't been returned yet... */
					foreach( $thisOrder->get_refunds() as $refund ) {
						foreach ( $refund->get_items() as $refunded_item ) {
							if ( $refunded_item['product_id'] === $item['product_id'] ) {


								if ( $refunded_item->get_quantity() != 0 ) {
									$itemReturned = true;
								}
							}
						}
					}

					if($itemReturned == false) { 
						foreach($slots as $slot) {
							if( get_the_terms( $workshops[$w]['id'], 'timeslot' )[0]->slug == $slot->slug ) {
								$workshopSelection[$slot->slug] []= $workshops[$w]['id'];
							}
						}
					}						
				}
			}
		}
	}
	arsort($workshopSelection);
	return [ $workshops, $slots, $orders, $workshopSelection ];
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
function DisplayWaitlistButton( $workshopId ) {
	$Output = '<a href="?waitlist='.$workshopId.'">Add me to Waitlist</a>';
	
	return $Output;
}
function workshop_cpt_autosave($post_id) {
    if (get_post_type($post_id) == 'product') {
		$product = wc_get_product($post_id);
        if($product->type == 'workshop') {
			/* Workshop auto-save features go here */
			
			update_post_meta($post_id, 'presenter_id', $post_id);
		}
    }
} add_action('save_post', 'workshop_cpt_autosave');