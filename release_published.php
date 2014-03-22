<?php
// #GitHub##WebHook# %sender - Released < %tag > : %title < %url > %author
$weibo = $config[ 'repos' ][ $repo ][ 'release' ];
$params = array( 
        '%url' => $json[ 'release' ][ 'html_url' ] , 
        '%tag' => $json[ 'release' ][ 'tag_name' ] , 
        '%title' => $json[ 'release' ][ 'name' ] 
);
$a_uid = $config[ 'users' ][ $json[ 'release' ][ 'author' ][ 'login' ] ];
$s_uid = $config[ 'users' ][ $json[ 'sender' ][ 'login' ] ];
$value2 = $config[ 'senders' ][ 0 ];
$sina = new WeiboPHP( $value2[ 'name' ] , $value2[ 'password' ] , $value2[ 'key' ] , $value2[ 'secret' ] , $value2[ 'uri' ] );
$s_arr = $sina -> HTTPGet( 'users/show.json' , array( 
        'uid' => $s_uid 
) );
$s_uname = '@' . $s_arr[ 'name' ];
$a_arr = $sina -> HTTPGet( 'users/show.json' , array( 
        'uid' => $a_uid 
) );
$a_uname = '@' . $a_arr[ 'name' ];
$params[ '%sender' ] = $s_uname;
$params[ '%author' ] = $a_uname;
foreach( $params as $key => $value1 ){
    echo "<strong>$key=>$value1;</strong><br/>";
    $weibo = str_replace( $key , $value1 , $weibo );
    echo "<strong>\$weibo:</strong>" , json_encode( $weibo ) , "<br/>";
}
foreach( $config[ 'repos' ][ $repo ][ 'senders' ] as $value3 ){
    $value2 = $config[ 'senders' ][ $value3 ];
    $sina = new WeiboPHP( $value2[ 'name' ] , $value2[ 'password' ] , $value2[ 'key' ] , $value2[ 'secret' ] , $value2[ 'uri' ] );
    $sina -> HTTPPost( 'statuses/update.json' , array( 
            'status' => $weibo 
    ) );
}