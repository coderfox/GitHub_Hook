<?php
error_reporting( E_ALL );
ini_set( 'display_errors' , 1 );
include_once './api.php';
try{
    $r = ob_get_contents();
    $json = json_decode( $_POST[ 'payload' ] , true );
    $f_config = fopen( @'./config.json' , 'r' );
    $config = json_decode( fread( $f_config , filesize( @'./config.json' ) ) , true );
    fclose( $f_config );
    $config = $config[ 'repos' ];
    if( $_SERVER[ 'HTTP_X_GITHUB_EVENT' ] == 'push' ){
        $commits = $json[ 'commits' ];
        $repo = $json[ 'repository' ][ 'name' ];
        $weibo = $config[ $repo ][ 'commit' ];
        foreach( $commits as $value ){
            $c_uid = $config[ 'users' ][$value[ 'committer' ][ 'username' ]];
            $a_uid = $config[ 'users' ][$value[ 'author' ][ 'username' ]];
            $c_arr=WeiboPHP_cURL_Get( 'https://api.weibo.com/2/users/show.json' , array( 
                    'source' => 3100975615 , 
                    'uid' => $c_uid
            ) );
            $c_uname=$c_arr['screen_name'];
            $a_arr=WeiboPHP_cURL_Get( 'https://api.weibo.com/2/users/show.json' , array(
                    'source' => 3100975615 ,
                    'uid' => $a_uid
            ) );
            $a_uname=$a_arr['screen_name'];
            $params = array( 
                    '%branch' => $json[ 'ref' ] , 
                    '%url' => $value[ 'url' ] , 
                    '%message_1' => preg_replace( '/\n.*/' , '' , $value[ 'message' ] ) , 
                    '%message' => str_replace( '\n' , ' , ' , $value[ 'message' ] ) , 
                    '%committer' => $config[ 'users' ][ $c_uname ] , 
                    '%sha1' => substr( $value[ 'id' ] , 0 , $config[ $repo ][ 'sha1' ] ) , 
                    '%author' => $config[ 'users' ][ $a_uname ] 
            );
            foreach( $params as $key => $value1 ){
                $weibo = str_replace( $key , $value1 , $weibo );
            }
            /*
             * //xiaofu fox $sina = new WeiboPHP( 'fox.q@foxmail.com' , 'CoderFox19990903' , '3100975615' , 'b4ecbc14a8ce97bc355345589c8f48a4' , 'http://cloud.cotr.me/' ); var_dump( $sina -> HTTPPost( 'statuses/update.json' , array( 'status' => $weibo ) ) ); //cotr $sina = new WeiboPHP( 'jukewg@sina.com' , 'rtkg^~kN!pnS5e-o' , '3100975615' , 'b4ecbc14a8ce97bc355345589c8f48a4' , 'http://cloud.cotr.me/' ); var_dump( $sina -> HTTPPost( 'statuses/update.json' , array( 'status' => $weibo ) ) );
             */
            foreach($config['senders'] as $value2){
                $sina = new WeiboPHP( $value2['name'] , $value2['password'] ,
                         $value2['key'] , $value2['secret'] ,
                         $value2['uri'] );
                var_dump( $sina -> HTTPPost( 'statuses/update.json' , array( 'status' => $weibo ) ) );
            }
        }
    }
} catch( Exception $ex ){
    echo $ex -> getMessage();
}