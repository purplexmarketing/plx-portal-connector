<?php
// Register the Custom Snippet Post Type
function plx_register_cpt_snippet() {

  $labels = array(
    'name' 									=> _x( 'Snippets', 'plx_snippet' ),
    'singular_name' 				=> _x( 'Snippets', 'plx_snippet' ),
    'add_new' 							=> _x( 'Add Snippet', 'plx_snippet' ),
    'add_new_item' 					=> _x( 'Add New Snippet', 'plx_snippet' ),
    'edit_item' 						=> _x( 'Edit Snippet', 'plx_snippet' ),
    'new_item' 							=> _x( 'New Snippet', 'plx_snippet' ),
    'view_item' 						=> _x( 'View Snippet', 'plx_snippet' ),
    'search_items' 					=> _x( 'Search Snippets', 'plx_snippet' ),
    'not_found' 						=> _x( 'No snippets found', 'plx_snippet' ),
    'not_found_in_trash' 		=> _x( 'No snippets found in Bin', 'plx_snippet' ),
    'parent_item_colon' 		=> _x( 'Parent Snippet:', 'plx_snippet' ),
    'menu_name'							=> _x( 'Snippets', 'plx_snippet' ),
    'name_admin_bar'				=> _x( 'Snippet', 'plx_snippet' ),
    'all_items' 						=> _x( 'All Snippets', 'plx_snippet'),
  );

  $args = array(
    'labels' 								=> $labels,
    'hierarchical' 					=> false,
    'supports' 							=> array( 'title', 'editor' ),
    'taxonomies' 						=> array( 'plx-sn-groups' ),
    'public' 								=> false,
    'show_ui' 							=> true,
    'show_in_menu' 					=> false,
    'show_in_nav_menus'			=> false,
    'publicly_queryable' 		=> true,
    'exclude_from_search' 	=> true,
    'has_archive' 					=> false,
    'query_var' 						=> true,
    'can_export' 						=> false,
    'capability_type' 			=> 'post',
    'register_meta_box_cb'	=> 'plx_portal_add_snippet_metaboxes',

  );

  register_post_type( 'plx_snippet', $args );

}
add_action( 'init', 'plx_register_cpt_snippet' );

function plx_snippet_groups_taxonomy() {

	$labels = array(
		'name' 							=> _x( 'Snippet Groups', 'plx_snippet' ),
		'singular_name' 		=> _x( 'Snippet Group', 'plx_snippet' ),
		'search_items' 			=> _x( 'Search Snippet Groups', 'plx_snippet' ),
		'all_items'         => _x( 'All Snippet Groups', 'plx_snippet' ),
		'parent_item'       => _x( 'Parent Snippet Group', 'plx_snippet' ),
		'parent_item_colon' => _x( 'Parent Snippet Group:', 'plx_snippet' ),
		'edit_item'         => _x( 'Edit Snippet Group', 'plx_snippet' ),
		'update_item'       => _x( 'Update Snippet Group', 'plx_snippet' ),
		'add_new_item'      => _x( 'Add New Snippet Group', 'plx_snippet' ),
		'new_item_name'     => _x( 'New Snippet Group', 'plx_snippet' ),
		'menu_name'         => _x( 'Snippet Groups', 'plx_snippet' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'plx-snippet-groups' ),
	);

  register_taxonomy( 'plx-snippet-groups', 'plx_snippet_groups', $args );
}
add_action( 'init', 'plx_snippet_groups_taxonomy');

function plx_portal_snippet_shortcode() {

	global $post;

	if (get_post_status( $post->ID ) == 'auto-draft') {

		$html = '<p class="description">Shortcode will appear once snippet has been saved</p>';
		echo $html;

	} else {

		$html = '';

		$plx_shortcode_snippet = '[plx-snippet slug="' . $post->post_name . '"]';

		$html .= '<div class="plx-row">';
		$html .= '<div class="plx-col-6">';
		$html .= '<label for="plx-shortcode-snippet">Display this snippet using the following shortcode<label>';
		$html .= '<input type="text" id="plx-shortcode-snippet" onfocus="this.select();" readonly="readonly" class="widefat code" value="' . htmlentities($plx_shortcode_snippet, ENT_QUOTES) . '" /></p>';
		$html .= '</div>';
		$html .= '<div class="clear"></div>';
		$html .= '</div>';

		echo $html;

	}

}

function plx_portal_add_snippet_metaboxes() {

	global $post;

	add_meta_box('plx_snippet_shortcode', 'Shortcode', 'plx_portal_snippet_shortcode', 'plx_snippet', 'normal', 'low');

}

function plx_portal_get_snippet( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'slug'				=> ''
	), $atts );

	$snippet_slug = esc_attr($atts['slug']);

	$get_post_by_slug = get_posts(
		array(
			'name' => $snippet_slug,
			'post_type' => 'plx_snippet',
			'post_status' => 'publish',
			'posts_per_page' => 1
		)
	);

	return do_shortcode(get_post_field( 'post_content', $get_post_by_slug[0]->ID ));

}
add_shortcode('plx-snippet', 'plx_portal_get_snippet');

function plx_portal_snippet_hide_menu_items() {
	remove_menu_page( 'edit.php?post_type=plx_snippet' );
}

function plx_portal_snippet_check_admin() {
	if ( ! current_user_can('manage_options') ) {
		add_action( 'admin_menu', 'plx_portal_snippet_hide_menu_items' );
	}
}
add_action( 'plugins_loaded', 'plx_portal_snippet_check_admin' );
