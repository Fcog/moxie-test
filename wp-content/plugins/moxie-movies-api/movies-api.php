<?php
require_once( 'models/movie.class.php' );

$data = Movie::loadAll();

header('Content-Type: application/json');
echo json_encode($data);
?>