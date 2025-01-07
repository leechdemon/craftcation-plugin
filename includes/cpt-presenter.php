<?php 
require_once plugin_dir_path(__FILE__) . '../craftcation.php';

function Presenter_Post_Type() {
	$Display = 'Presenter';
	$Displays = $Display . 's';
	$Lower = 'presenter';
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
		'taxonomies'            => array( 'wscategory', 'craft' ),
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
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
	);
	register_post_type( $Lower, $args );
} add_action( 'init', 'Presenter_Post_Type', 0 );

function presenter_cpt_autosave($post_id) {
    if (get_post_type($post_id) == 'presenter') {
		/* Workshop auto-save features go here */
		
        update_post_meta($post_id, 'presenter_id', $post_id);
    }
} add_action('save_post', 'presenter_cpt_autosave');