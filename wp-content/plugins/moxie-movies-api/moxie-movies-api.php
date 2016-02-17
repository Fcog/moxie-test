<?php
/*
Plugin Name: Moxie Movies API
Description: JSON API for the movie custom type
Author: Francisco Giraldo
Version: 1.0
Author URI: http://www.franciscogiraldo.com
License: GPL2 or later
*/
defined( 'ABSPATH' ) or die( 'Access denied' );

/*
* Movie Custom Type Definition
*/

function custom_post_type() {

	$labels = array(
		'name'                => _x( 'Movies', 'Post Type General Name', 'moxie' ),
		'singular_name'       => _x( 'Movie', 'Post Type Singular Name', 'twentythirteen' ),
		'menu_name'           => __( 'Movies', 'moxie' ),
		'parent_item_colon'   => __( 'Parent Movie', 'moxie' ),
		'all_items'           => __( 'All Movies', 'moxie' ),
		'view_item'           => __( 'View Movie', 'moxie' ),
		'add_new_item'        => __( 'Add New Movie', 'moxie' ),
		'add_new'             => __( 'Add New', 'moxie' ),
		'edit_item'           => __( 'Edit Movie', 'moxie' ),
		'update_item'         => __( 'Update Movie', 'moxie' ),
		'search_items'        => __( 'Search Movie', 'moxie' ),
		'not_found'           => __( 'Not Found', 'moxie' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'moxie' ),
	);
	
	$args = array(
		'label'               => __( 'movies', 'moxie' ),
		'description'         => __( 'Movie data', 'moxie' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
		'taxonomies'          => array( 'category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'show_in_rest' => true
	);
	
	// Registering your Custom Post Type
	register_post_type( 'movies', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/

add_action( 'init', 'custom_post_type', 0 );

?>
