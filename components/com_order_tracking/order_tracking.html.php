<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_order_tracking {

    function viewPage($data,$order_id=''){
        global $mosConfig_live_site;
        ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
        <div class="container">
            <div class="row">
                <h1 class="text-center">Delivery Information for Order <?php echo $order_id; ?></h1>
                <div  class="text-center"><?php echo $data; ?></div>
            </div>
        </div>
        <?php
    }
}
?>