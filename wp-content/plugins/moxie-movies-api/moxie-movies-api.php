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

require_once( 'models/movie.class.php' );

/*
* Movie Custom Type definition
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
add_action( 'init', 'custom_post_type', 0 );
 
/*
* Movie Custom fields definition
*/

function moxie_movie_register_meta_boxes(){
  add_meta_box( 'year_created_meta', 'Year Created', 'moxie_movie_year_released_callback', 'movies', 'normal', 'high' );
  add_meta_box( 'rating_meta', 'Rating', 'moxie_movie_rating_callback', 'movies', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'moxie_movie_register_meta_boxes' );

function moxie_movie_year_released_callback(){
  global $post;
  $custom = get_post_custom( $post->ID );
  $year_released = $custom[ 'year_released' ][ 0 ];
  ?>
  <input name="year_released" value="<?php echo $year_released; ?>" >
  <?php
}

function moxie_movie_rating_callback() {
  global $post;
  $custom = get_post_custom( $post->ID );
  $rating = $custom[ 'rating' ][ 0 ];
  ?>
  <select name="rating" >
	  <option value="1" <?php if ($rating == 1) echo "selected"; ?>>1 star</option>
	  <option value="2" <?php if ($rating == 2) echo "selected"; ?>>2 stars</option>
	  <option value="3" <?php if ($rating == 3) echo "selected"; ?>>3 stars</option>
	  <option value="4" <?php if ($rating == 4) echo "selected"; ?>>4 stars</option>
	  <option value="5" <?php if ($rating == 5) echo "selected"; ?>>5 stars</option>
  </select>
  <?php
}

function moxie_movie_save_meta_box( $post_id ) {
	global $post;
 
  	update_post_meta( $post->ID, 'year_released', $_POST[ 'year_released' ] );
  	update_post_meta( $post->ID, 'rating', $_POST[ 'rating' ] );
}
add_action( 'save_post', 'moxie_movie_save_meta_box' );

/*
* Remove unnecesary fields for Movies custom type
*/
function moxie_movie_remove_meta_boxes() {
	remove_meta_box( 'postexcerpt' , 'movies' , 'normal' );
	remove_meta_box( 'postcustom' , 'movies' , 'normal' );
}
add_action( 'admin_menu', 'moxie_movie_remove_meta_boxes' );

/*
* Add API page
*/
function moxie_movie_init_external()
{
    global $wp_rewrite;
    $plugin_url = plugins_url( 'movies-api.php', __FILE__ );
    $plugin_url = substr( $plugin_url, strlen( home_url() ) + 1 );

    $wp_rewrite->add_external_rule( 'movies\.json$', $plugin_url, 'top' );
}
add_action( 'init', 'moxie_movie_init_external' );

/*
* Create shortcode
*/
function moxie_movie_wp_enqueue_scripts() {
    wp_register_script( 'load-angularjs', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0/angular.min.js', array(), '1.0.0', all );
    wp_register_script( 'load-angularjs-sanitize', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0/angular-sanitize.min.js', array(), '1.0.0', all );
    wp_register_script( 'load-custom-angularjs', plugins_url( '/js/custom.js', __FILE__ ), array(), '1.0.0', all );    
}
add_action( 'wp_enqueue_scripts', 'moxie_movie_wp_enqueue_scripts' );

function moxie_movie_display() {
	wp_enqueue_script( 'load-angularjs' );
	wp_enqueue_script( 'load-angularjs-sanitize' );
	wp_enqueue_script( 'load-custom-angularjs' );
    ?>
    <div ng-app="myapp">
    	<div ng-controller="MoxieMovieController">
    		<div ng-show="data == null">No movies available yet!</div>
    		<div ng-hide="data == null">
    			Order By: 
    			<select ng-model="orderBySelection">
    				<option value="title">Title</option>
    				<option value="year">Year</option>
    				<option value="rating">Rating</option>
    			</select>
    		</div>
    		<div ng-repeat="movie in data | orderBy: orderBySelection">
				<h2>{{ movie.title }}</h2>
				<img src="{{ movie.poster_url }}" alt="{{ movie.title }}">
				<div ng-bind-html="movie.short_description"></div>
				<div><label>Release Year: </label>{{ movie.year }}</div>
				<div><label>Rating: </label>{{ movie.rating }}</div>
				<hr \>
    		</div>
    	</div>
    </div>
    <?php
}
add_shortcode( 'moxie-movies', 'moxie_movie_display' );

/*
* Clears API cache when saving a movie
*/
function moxie_movie_save( $post_id, $post, $update ) {
	delete_transient( Movie::TRANSIENT );
}
add_action( 'save_post_movies', 'moxie_movie_save', 10, 3 );
?>
