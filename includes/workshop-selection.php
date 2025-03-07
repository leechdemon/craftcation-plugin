<?php 
require_once plugin_dir_path(__FILE__) . '../craftcation.php';
//require_once plugin_dir_path(__FILE__) . 'waitlist-js.php';

function Process_WorkshopSelectionUpdates( $atts ) {
	$filter = shortcode_atts( array(
        'prefix' => 'ws_',
    ), $atts, 'WorkshopSelection' );
	
	extract($filter);
	if( isset( $_POST[ $prefix.'order' ] ) ) {
		
		unset($_POST[$prefix.'order'] );
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
			$search = $prefix.'timeslot';
			
			Test($t[0]);
			Test($t[1]);
			Test($t[2]);
			
			if( $t[0] == explode('_',$prefix)[0] && $t[1] == 'timeslot' ) {
				/* Process Timeslot items */
				Test("---");
				
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
						$order_req[ $t[2] ] = $item;

						/* If we're adding an item, and there's already a workshopSelection... */
						if( $workshopSelection[ $t[2] ] ) {
							$hasRefunds = true;
							$refund_req[ $t[2] ] = $workshopSelection[ $t[2] ];
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
} add_shortcode('Process_WS_Updates', 'Process_WorkshopSelectionUpdates');
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
	ob_start();
	
	$filter = shortcode_atts( array(
        'prefix' => 'ws_',
    ), $atts, 'WorkshopSelection' );
	extract($filter);
	
//	$Output = '';
	/* if we have a user... */
	if( is_user_logged_in() ) {
		/* Build list of workshopsSelection */
		$w = get_workshopSelection();
		$workshops = [];
		$workshopIgnoreTagId = esc_attr( get_option('cc_workshop_ignore_tags') );
		foreach( $w[0] as $key => $workshop ) {
			$noWorkshopSelection = has_term( $workshopIgnoreTagId, 'product_tag', $workshop['id'] );
			if( $prefix == "ws_" ) {
				if( $noWorkshopSelection ) { array_push( $workshops, $workshop ); }
			} else { 
				if ( !$noWorkshopSelection ) { array_push( $workshops, $workshop ); }
			}
		}	
		$slots = $w[1];
		$orders = $w[2];
		$workshopSelection = $w[3];
		$waitlistSelection = $w[4];
		
		/* Display Things */
		/* Display Things */
		echo '<style>
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

		if( $prefix == "waitlist_" ) {
			echo '<div class="waitlist_schedule">';
				echo '<h2 class="waitlist_title">Current Waitlists</h2>';
				echo '<div id="'.$prefix.'workshop_headers" class="waitlist_timeslot">
					<div class="waitlist_item workshop_label waitlist_time">Timeslot</div>
					<div class="waitlist_item workshop_label waitlist_name">Workshop</div>
					<div class="waitlist_item workshop_label waitlist_position">Position</div>
					<div class="waitlist_item workshop_label waitlist_change">Change Waitlist</div>
				</div>';
			
				foreach( $waitlistSelection as $waitlist ) {
					$CurrentWorkshop = '<a href="'.get_permalink( $waitlist ).'"><img src="'.get_the_post_thumbnail_url( $waitlist ).'">'.get_post( $waitlist )->post_title.'</a>';
					echo '<div class="waitlist_timeslot">';
						echo '<p class="waitlist_item waitlist_time">'.get_the_terms(get_post( $waitlist ), 'timeslot')[0]->name.'</p>';
						echo '<p class="waitlist_item waitlist_name">'.$CurrentWorkshop.'</p>';
						echo '<p id="'.$prefix.$waitlist.'_position" class="waitlist_item waitlist_position">'.cc_waitlist_getPosition($waitlist).'</p>';
						echo '<p class="waitlist_item waitlist_change">'.DisplayWaitlistButton($waitlist, $prefix).'</p>';
	//					echo "<script>cc_waitlist_getStatus(".$waitlist.", '".$prefix."');</script>";
					echo '</div>';
				}
			echo '</div>';
		}
		else {


			/* Draw the workshop selections */
			echo '<div class="workshop-selections">Workshop Selections:<br>'. json_encode($workshopSelection) .'</div>';
	//json_encode($workshopSelection) .'</div>';

			/* Draw the Slots */
			echo '<div class="workshop_schedule">';
			$selectionTitle = 'Workshop';
			if( $prefix == "ah_" ) { $selectionTitle = 'After-Hours'; }
			echo '<h2 class="waitlist_title">'.$selectionTitle.' Selections</h2>';
			
				/* Draw the Headers */
				echo '<div id="'.$prefix.'workshop_headers" class="workshop_timeslot">
					<div class="workshop_item workshop_label workshop_name">Timeslot</div>
					<div class="workshop_item workshop_label workshop_current">My Registration</div>
					<div class="workshop_item workshop_label timeslot">New Registration</div>
					<div class="workshop_item workshop_label workshop_notes">Workshop Notes</div>
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
				$cosmeticName = explode( ": ", $slot->name );
				if( $cosmeticName[1] ) { $cosmeticName = $cosmeticName[1]; }
				else { $cosmeticName = $cosmeticName[0]; }

				echo '<div id="'.$prefix.'workshop_timeslot_'.$s.'" class="workshop_timeslot" style="display: none;">
					<div class="workshop_item workshop_name">'.$cosmeticName.'</div>';
				if( strlen( $slot->name ) < 10 ) {
					// dosomething
					$timeslotHasWorkshops = true;
					echo '<script>document.getElementById("'.$prefix.'workshop_timeslot_'.$s.'").classList.add("workshop_item_solo");</script>';
				} else {
				echo '<div class="workshop_item workshop_current">'.$CurrentWorkshop.'</div>
					<select id="'.$prefix.'timeslot_'.$s.'" name="'.$prefix.'timeslot_'.$s.'" class="workshop_item timeslot" form="'.$prefix.'workshopSelection">
						<option value="0">--- Select Workshop ---</option>';
						foreach( $workshops as $workshop ) {						
							if( get_the_terms( $workshop['id'], 'timeslot' )[0]->slug == $s ) { 
								$timeslotHasWorkshops = true;

								$IsGrayedOut = $IsSoldOut = $IsSelected = $IsStarred = '';
								if( $workshop['id'] == $Selection_id ) { $IsSelected = ' selected="true"'; $IsStarred = ' **'; }
								if( !$workshop['is_in_stock'] ) { $IsGrayedOut = ' class="item_grayedout"'; $IsSoldOut = ' (Sold Out)'; } 

								echo '<option value="'.$workshop['id'].'"'.$IsSelected.$IsGrayedOut.'>';
								echo $workshop['name'].$IsStarred.$IsSoldOut.'</option>';
							}
						}
					echo '</select>';			

					echo '<div class="workshop_item workshop_notes">';
					foreach( $workshops as $workshop ) {
						if( get_the_terms( $workshop['id'], 'timeslot' )[0]->slug == $s ) { 
							$IsSelected = ' style="display:none;"';
							if( $workshop['id'] == $Selection_id ) { $IsSelected = ''; }

							echo '<div id="'.$prefix.'workshop_notes_item_'.$workshop['id'].'" class="workshop_notes_'.$s.'"'.$IsSelected.'>';

							/* Conditionally display the Waitlist button */
							if( !$workshop['is_in_stock'] ) {
								/* hide, if "noWaitlist" */
								if( has_term('noWaitlist', 'product_tag', $workshop['id'] ) ) { /* do something */ }
								else { echo DisplayWaitlistButton( $workshop['id'], $prefix ); }
							}
							echo '</div>';
						}
					}
					echo '<script>
						try {
							const selectElement_'. $workshop['id'].' = document.getElementById("'.$prefix.'timeslot_'.$s.'");

							selectElement_'. $workshop['id'].'.addEventListener("change", (event) => {
								var workshopNotes = document.getElementsByClassName( "workshop_notes_'.$s.'" );
								for( var n = 0; n < workshopNotes.length; n++ ) {
									workshopNotes[n].style.display = "none";
								}

								document.getElementById( "'.$prefix.'workshop_notes_item_"+event.target.value ).style.display = "flex";
							});
						} catch (error) {
							/* do something */
						}		
					</script>';
					echo '</div>';
				}
				if( $timeslotHasWorkshops ) { echo '<script>document.getElementById("'.$prefix.'workshop_timeslot_'.$s.'").style.display = "flex";</script>'; }
				echo '</div>';


			} /* End Timeslot */
			echo '<form action="#" method="post" id="'.$prefix.'workshopSelection">
				<input type="hidden" name="'.$prefix.'order" id="'.$prefix.'order" value="'.$prefix.'order">
				<input type="submit" value="Save Workshop Selections" class="btn">
			</form>';

			foreach( $waitlistSelection as $waitlist ) {
				echo "<script>cc_waitlist_getStatus(".$waitlist.");</script>";
			}
			echo "</div>";
		}
		
		return ob_get_clean();
	}
	else {
		echo 'Please log in.';
		return ob_get_clean();
	}
} add_shortcode('WorkshopSelection', 'DisplayWorkshopSelection');
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
		'orderby'	=> 'title',
		'order'	=> 'ASC',
	);
	$w = wc_get_products( $args );
	
	$workshops = array();
	foreach( $w as $key => $workshop ) {
		$workshops[$key]= $workshop->get_data();
		$workshops[$key]['presenter_id'] = $workshop->get_meta('presenter_id');
		$workshops[$key]['is_in_stock'] = $workshop->is_in_stock();
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
			foreach( $waitlists as $key => $waitlist ) {
				/* If customer=waitlist=workshop match... */
				if( $waitlist->customerId == get_current_user_id() && $waitlist->workshopId == $workshops[$w]['id'] ) {

					/* If this item hasn't been removed... */
					if( $waitlist->removalDate == '' ) {
						/* Add to the waitlistSelection */
						$waitlistSelection[$key] = $workshops[$w]['id'];
					}
				}
			}
		}
		arsort($workshopSelection);
	} else {
		
	}
	
	return [ $workshops, $slots, $orders, $workshopSelection, $waitlistSelection ];
}

