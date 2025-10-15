<?php
if (empty($_SERVER['HTTPS'])) {
    global $mosConfig_lang;
    require_once 'Corners.php';
    $Corners = new Corners();
    //$html  = $Corners->corner();
    //$html .= $Corners->corner();
    $html = $Corners->newCorner();
    echo $html;
}

?>

<script>
   $j('#closeCorners').click(function(){
       $j('#closeCorners').css('display', 'none');
       $j('#newCornres').css('display', 'none');
   });
   $j(document).ready (function(){
        checkCorners();
        $j('#newCornres').css('top', $j(window).height()/2 - 205 );
        $j(window).resize(function(){
           if( $j('#closeCorners').css('display') !== 'none' ){
                checkCorners();
           }
        });
        function checkCorners(){
            $j('#newCornres').css('top', $j(window).height()/2 - parseInt($j('#newCornres').css('height'))/2 );
            $j('#newCornres').css('display', ( ( ( $j(window).width() - parseInt($j('#new-content').css('width')) )/2 < 243 ) ? 'none' : 'block') );
        }
    });
</script>
