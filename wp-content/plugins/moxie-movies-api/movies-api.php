<?php
require_once( '../../../wp-load.php' );
require_once( 'models/movie.class.php' );

//get cached api
$transName = Movie::TRANSIENT;
$cacheTime = 10;

if( false === ( $moviesData = get_transient( $transName ) ) ){

  $moviesData = Movie::loadAll();

  set_transient($transName, $moviesData, 60 * $cacheTime);
}

header('Content-Type: application/json');
echo json_encode($moviesData);
?>