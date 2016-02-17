<?php
define( 'WP_USE_THEMES', false );

/** Loads the WordPress Environment and Template */
require_once( '../../../wp-blog-header.php' );

class Movie {

    private $id;
    private $title;
    private $poster_url;
    private $rating;
    private $year_release;
    private $short_description;

    /**
     * Load all movies
     * 
     * @return array
     * */
    public static function loadAll(){

        $data = array();

        $args = array(
            'post_type' => 'movies',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'caller_get_posts'=> 1
        );

        $query = new WP_Query( $args );

        $posts = $query->get_posts();

        foreach( $posts as $post ) {

            $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

            $data['data'][] = array(
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'poster_url' => $featured_image[0],
                    'rating' => get_post_meta ( $post->ID, 'rating', true),
                    'year' => get_post_meta ( $post->ID, 'year_released', true),
                    'short_description' => str_replace( '"' , "'" , $post->post_content )
                );
        }

        return $data;
    }
}
?>