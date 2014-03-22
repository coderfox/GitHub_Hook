<?php
$repo = $json[ 'repository' ][ 'name' ];
$weibo = $config[ 'repos' ][ $repo ][ 'create' ];
$params = array( 
        '%type' => $json[ 'ref_type' ] , 
        '%ref' => $json[ 'ref' ] 
);
$value2 = $config[ 'senders' ][ 0 ];
$s_uid = $config[ 'users' ][ $json[ 'sender' ][ 'login' ] ];
$sina = new WeiboPHP( $value2[ 'name' ] , $value2[ 'password' ] , $value2[ 'key' ] , $value2[ 'secret' ] , $value2[ 'uri' ] );
$s_arr = $sina -> HTTPGet( 'users/show.json' , array( 
        'uid' => $s_uid 
) );
$s_uname = '@' . $s_arr[ 'name' ];
$params[ '%sender' ] = $s_uname;
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