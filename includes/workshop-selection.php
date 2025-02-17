<?php 
require_once plugin_dir_path(__FILE__) . '../craftcation.php';
//require_once plugin_dir_path(__FILE__) . 'waitlist-js.php';

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
					$stockCheck = wc_get_product( $item );
					$waitlists = cc_waitlist_getLists( $item );
					
					/* If it's still in stock... (or waitlist position is available)*/
					if ( $stockCheck->is_in_stock() || ( $waitlists[0]->customerId == get_current_user_id() && $waitlists[0]->notificationDate != '' ) ) { 
						/* Add the item to the order */
						$hasItems = true;
						$order_req[ $t[1] ] = $item;

						/* If we're adding an item, and there's already a workshopSelection... */
						if( $workshopSelection[ $t[1] ] ) {
							$hasRefunds = true;
							$refund_req[ $t[1] ] = $workshopSelection[ $t[1] ];
						}
						
						if( $waitlists[0]->customerId == get_current_user_id() ) {
							date_default_timezone_set('America/Detroit');
							$removalDate = date( 'm/d/Y H:i:s', time() );

							cc_waitlist_remove( $removalDate, $waitlists[0]->workshopId, $waitlists[0]->customerId, $waitlists[0]->waitlistDate );
						}
					}

				}

			}
		}
		/* End Order/Refund Requests */
		
		/* Add New Items */
		if( $hasItems ) {
			WorkshopSelection_AddOrder( $order_req );
		}
		/* Refund Old Items */
		if( $hasRefunds ) {
			WorkshopSelection_RefundItems( $refund_req );
		}	
	}

	return $Output;
} add_shortcode('Process_WorkshopSelectionUpdates', 'Process_WorkshopSelectionUpdates');
function WorkshopSelection_AddOrder( $order_req ) {
	$args = array(
		'status' => 'wc-complete',
		'customer_id' => get_current_user_id(),
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
		'customer_id' => get_current_user_id(),
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
						
						if( $restockItems == false ) {
							cc_waitlist_process( $item['product_id'] );
						}

					}
					
				}
			}
		}
		
	}
}

function DisplayWorkshopSelection( $atts ) {
	require_once plugin_dir_path(__FILE__) . 'waitlist-js.php';
	
	$Output = '';
	/* if we have a user... */
	if( is_user_logged_in() ) {
		/* Build list of workshopsSelection */
		$w = get_workshopSelection();
		$workshops = $w[0];
		$slots = $w[1];
		$orders = $w[2];
		$workshopSelection = $w[3];
		$waitlistSelection = $w[4];
		
		/* Display Things */
		/* Display Things */
		$Output .= '<style>
			.workshop-selections, .get_response { background: #DDD; padding: 0.5rem; margin: 0rem; border: solid 1px red; }
			.workshop-selections { display: none; }
			.get_response { display: none; }
//		
//			.workshop_schedule { display: grid; margin: 1rem 2rem; }
//			.workshop_timeslot { width: 100%; background-color: #666; clear: both; padding: 0.5rem; color: white; font-weight: 500; }
//			.workshop_timeslot:nth-child(even) { background-color: #999; }
//			.workshop_item { width: 25%; float: left; padding: 0rem 0.5rem; }
//			.workshop_item img { float: left; width: 3.5rem; margin-right: 0.5rem; }
//
//			.workshop_label { font-weight: 800; }
//			option.item_grayedout { color: #ccc !important; }
		</style>';
		
		/* Draw the workshop selections */
		$Output .= '<div class="workshop-selections">Workshop Selections:<br>'. json_encode($workshopSelection) .'</div>';
//json_encode($workshopSelection) .'</div>';

		/* Draw the Slots */
		$Output .= '<div class="workshop_schedule">';
			/* Draw the Headers */
			$Output .= '<div id="workshop_headers" class="workshop_timeslot">
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
					$CurrentWorkshop = '<a href="'.get_permalink( $Selection_id ).'"><img src="'.get_the_post_thumbnail_url( $Selection_id ).'">'.get_post( $Selection_id )->post_title.'</a>';
				}
	   		}

			$timeslotHasWorkshops = false;
			$Output .= '<div id="workshop_timeslot_'.$s.'" class="workshop_timeslot" style="display: none;">
				<div class="workshop_item">'.$slot->name.'</div>';
			if( strlen( $slot->name ) < 10 ) {
				// dosomething
				$timeslotHasWorkshops = true;
				$Output .= '<script>document.getElementById("workshop_timeslot_'.$s.'").classList.add("workshop_item_solo");</script>';
			} else {
			$Output .= '<div class="workshop_item">'.$CurrentWorkshop.'</div>
				<select id="timeslot_'.$s.'" name="timeslot_'.$s.'" class="workshop_item timeslot" form="workshopSelection">
					<option value="0">--- Select Workshop ---</option>';
					foreach( $workshops as $workshop ) {
						$workshopIgnoreTagId = esc_attr( get_option('cc_workshop_ignore_tags') );
						$noWorkshopSelection = has_term( $workshopIgnoreTagId, 'product_tag', $workshop['id'] );
						
						if( get_the_terms( $workshop['id'], 'timeslot' )[0]->slug == $s && !$noWorkshopSelection ) { 
							$timeslotHasWorkshops = true;
							
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
						$IsSelected = ' style="display:none;"';
						if( $workshop['id'] == $Selection_id ) { $IsSelected = ''; }
						
						$Output .= '<div id="workshop_notes_item_'.$workshop['id'].'" class="workshop_notes_'.$s.'"'.$IsSelected.'>';
						
						/* Conditionally display the Waitlist button */
						if( $workshop['stock_quantity'] < 1 ) {
							/* hide, if "noWaitlist" */
							if( has_term('noWaitlist', 'product_tag', $workshop['id'] ) ) { /* do something */ }
							else { $Output .= DisplayWaitlistButton( $workshop['id'] ); }
						}
						$Output .= '</div>';
					}
				}
				$Output .= '<script>
					try {
						const selectElement_'. $workshop['id'].' = document.getElementById("timeslot_'.$s.'");

						selectElement_'. $workshop['id'].'.addEventListener("change", (event) => {
							var workshopNotes = document.getElementsByClassName( "workshop_notes_'.$s.'" );
							for( var n = 0; n < workshopNotes.length; n++ ) {
								workshopNotes[n].style.display = "none";
							}

							document.getElementById( "workshop_notes_item_"+event.target.value ).style.display = "flex";
						});
					} catch (error) {
						/* do something */
					}		
				</script>';
				$Output .= '</div>';
			}
			if( $timeslotHasWorkshops ) { $Output .= '<script>document.getElementById("workshop_timeslot_'.$s.'").style.display = "flex";</script>'; }
			$Output .= '</div>';
			
			
		} /* End Timeslot */
		$Output .= '<form action="#" method="post" id="workshopSelection">
			<input type="hidden" name="order" id="order" value="order">
			<input type="submit" value="Save Workshop Selections" class="btn">
		</form>';
		
		foreach( $waitlistSelection as $waitlist ) {
			$Output .= "<script>cc_waitlist_getStatus(".$waitlist.");</script>";
		}

		return $Output;				
	}
	else {
		$Output = 'Please log in.';
		return $Output;
	}
} add_shortcode('DisplayWorkshopSelection', 'DisplayWorkshopSelection');
function get_workshopSelection() {
	$workshopTagIDs = explode(',', esc_attr(get_option('cc_workshop_tags')) );

	/* I'm not sure why this is better thanjust using the workshop tags from above. Maybe if the tag was deleted? But this is pre-release, so probably not. Maybe to remove typos, or to have access to the tag, IE "getTagBy(cc_workshop_tags)"? */
	$workshopTagName = '';
	$productTags = get_terms( array(
			'taxonomy'	=> 'product_tag',
			'orderby'	=> 'slug',
			'limit' => -1,
			'hide_empty' => false,
	) );
	foreach($productTags as $productTag) {
		foreach($workshopTagIDs as $tagID) {
			if($tagID == $productTag->term_id) { $workshopTagName = $productTag->name; }
		}
	}	
	
	/* Build list of All Workshops */
	$args = array(
    	'product_tag' => array( $workshopTagName ),
    	'limit' => -1,
	);
	$w = wc_get_products( $args );
	
	$workshops = array();
	foreach( $w as $key => $workshop ) {
		$workshops[$key]= $workshop->get_data();
		$workshops[$key]['presenter_id'] = $workshop->get_meta('presenter_id');
	}

	/* Build list of All Slots, Workshop Selections */
	$slots = get_terms( array(
			'taxonomy'	=> 'timeslot',
			'orderby'	=> 'slug',
			'limit' => -1,
			'hide_empty' => false,
	) );
	
	if( is_user_logged_in() ) {
		/* Build list of All Orders for current customer, status = "Processing" */
		$args = array(
			'customer_id' => get_current_user_id(),
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

		/* get Waitlists */
		$waitlists = cc_waitlist_getLists();

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
			foreach( $waitlists as $waitlist ) {
				/* If customer=waitlist=workshop match... */
				if( $waitlist->customerId == get_current_user_id() && $waitlist->workshopId == $workshops[$w]['id'] ) {

					/* If this item hasn't been removed... */
					if( $waitlist->removalDate == '' ) {
						/* Add to the waitlistSelection */
						$waitlistSelection []= $workshops[$w]['id'];
					}
				}
			}
		}
		arsort($workshopSelection);
	} else {
		
	}
	
	return [ $workshops, $slots, $orders, $workshopSelection, $waitlistSelection ];
}