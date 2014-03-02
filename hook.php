<?php
error_reporting( E_ALL );
ini_set( 'display_errors' , 1 );
include_once './api.php';
try{
    $r = ob_get_contents();
    $json = json_decode( $_POST[ 'payload' ] , true );
    $f_config = fopen( 'config.json' , 'r' );
    $config_text = fread( $f_config , filesize( 'config.json' ) );
    $config = json_decode( $config_text , true );
    fclose( $f_config );
    if( $_SERVER[ 'HTTP_X_GITHUB_EVENT' ] == 'push' ){
        $commits = $json[ 'commits' ];
        $repo = $json[ 'repository' ][ 'name' ];
        $weibo = $config[ 'repos' ][ $repo ][ 'commit' ];
        foreach( $commits as $value ){
            $c_uid = $config[ 'users' ][ $value[ 'committer' ][ 'username' ] ];
            $a_uid = $config[ 'users' ][ $value[ 'author' ][ 'username' ] ];
            $value2 = $config[ 'senders' ][ 0 ];
            $sina = new WeiboPHP( $value2[ 'name' ] , $value2[ 'password' ] , $value2[ 'key' ] , $value2[ 'secret' ] , $value2[ 'uri' ] );
            $c_arr = $sina -> HTTPGet( 'users/show.json' , array( 
                    'source' => 211160679 , 
                    'uid' => $c_uid 
            ) );
            $c_uname = '@' . $c_arr[ 'name' ];
            $a_arr = $sina -> HTTPGet( 'users/show.json' , array( 
                    'source' => 211160679 , 
                    'uid' => $a_uid 
            ) );
            $a_uname = '@' . $a_arr[ 'name' ];
            $params = array( 
                    '%branch' => $json[ 'ref' ] , 
                    '%url' => $value[ 'url' ] , 
                    '%message_1' => preg_replace( '/\n.*/' , '' , $value[ 'message' ] ) , 
                    '%message' => str_replace( "\n" , ' , ' , $value[ 'message' ] ) , 
                    '%committer' => $c_uname , 
                    '%sha1' => substr( $value[ 'id' ] , 0 , $config[ 'repos' ][ $repo ][ 'sha1' ] ) , 
                    '%author' => $a_uname 
            );
            foreach( $params as $key => $value1 ){
                $weibo = str_replace( $key , $value1 , $weibo );
            }
            foreach( $config[ 'repos' ][ $repo ][ 'senders' ] as $value3 ){
                $value2 = $config[ 'senders' ][ $value3 ];
                $sina = new WeiboPHP( $value2[ 'name' ] , $value2[ 'password' ] , $value2[ 'key' ] , $value2[ 'secret' ] , $value2[ 'uri' ] );
                var_dump( $sina -> HTTPPost( 'statuses/update.json' , array( 
                        'status' => $weibo 
                ) ) );
            }
        }
    }
} catch( Exception $ex ){
    echo $ex -> getMessage();
}
