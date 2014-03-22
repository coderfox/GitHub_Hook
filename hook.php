<?php
error_reporting( E_ALL );
ini_set( 'display_errors' , 1 );
include_once 'api.php';
// Payload Json Array
$json = json_decode( $_POST[ 'payload' ] , true );
$f_config = fopen( 'config.json' , 'r' );
$config_text = fread( $f_config , filesize( 'config.json' ) );
// Config Array
$config = json_decode( $config_text , true );
fclose( $f_config );
echo $_SERVER[ 'HTTP_X_GITHUB_EVENT' ];
if( $_SERVER[ 'HTTP_X_GITHUB_EVENT' ] == 'push' ){
    // PUSH event
    include_once 'push.php';
} else if( $_SERVER[ 'HTTP_X_GITHUB_EVENT' ] == 'create' ){
    // CREATE event
    include_once 'create.php';
} else if( $_SERVER[ 'HTTP_X_GITHUB_EVENT' ] == 'release' ){
    // RELEASE
    $repo = $json[ 'repository' ][ 'name' ];
    if( $json[ 'action' ] == 'published' ){
        // PUBLISHED
        include_once 'release_published.php';
    }
}