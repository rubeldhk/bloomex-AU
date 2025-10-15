<?php
defined('_VALID_MOS') or die('Restricted access');

class HTML_LocalPartner_Orders {

    function viewDeclinePage() {
        global $mainframe;
        $partner_id = trim(mosGetParam($_REQUEST, "partner_id"));
        $order_id = trim(mosGetParam($_REQUEST, "order_id"));
        $confirm = trim(mosGetParam($_REQUEST, "confirm"));
        $key = trim(mosGetParam($_REQUEST, "key"));
        //  $mainframe->addCustomHeadTag("<script type=\"text/javascript\" src=\"" . $GLOBALS['mosConfig_live_site'] . "/templates/bloomex7/js/jquery-1.10.2.js\"></script>");
        //  $mainframe->addCustomHeadTag("<script type=\"text/javascript\" src=\"" . $GLOBALS['mosConfig_live_site'] . "/templates/bloomex7/js/jquery-ui-1.10.4.custom.min.js\"></script>");
        //  $mainframe->addCustomHeadTag("<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $GLOBALS['mosConfig_live_site'] . "/templates/bloomex7/css/jquery-ui.css\" />");
        $mainframe->addCustomHeadTag('<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />');
        $mainframe->addCustomHeadTag('<script src="http://code.jquery.com/jquery-1.10.2.js"></script>');
        $mainframe->addCustomHeadTag('<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>');
        ?>

        <div id="container-partner" title="Decline order" class="">
            <form name="parthner" id="parthner" method="POST" action="/index.php">
                <label for="comment">Please leave your comment here:</label>
                <textarea  class="ui-widget ui-state-default ui-corner-all" style="width:300px;height:120px;background:#fff" name="comment"/></textarea><br/>
                <input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" id="onok"  value="OK"/>
                <input type="hidden" name="option" value="com_localpartner_orders" />
                <input type="hidden" name="order_id" value="<?php echo $order_id ?>" />
                <input type="hidden" name="partner_id" value="<?php echo $partner_id ?>" />
                <input type="hidden" name="key" value="<?php echo $key ?>" />
                <input type="hidden" name="confirm" value="<?php echo $confirm ?>" />
            </form>
        </div>
        <script>
            $j(function() {
                $j("#container-partner").dialog(
                        {
                            modal: true,
                            closeOnEscape: false,
                            beforeclose: function(event, ui) {
                                return false;
                            },
                            open: function(event, ui) {
                                $j(".ui-dialog-titlebar-close", ui).hide();
                            },
                            dialogClass: "noclose"
                        });
            });
        </script>
        <?php
    }

    function viewConfirmPage() {
        global $mainframe;
        $partner_id = trim(mosGetParam($_REQUEST, "partner_id"));
        $order_id = trim(mosGetParam($_REQUEST, "order_id"));
        $confirm = trim(mosGetParam($_REQUEST, "confirm"));
        $key = trim(mosGetParam($_REQUEST, "key"));
        ?>

        <div id="container-partner" title="Confirm order" class="">
            <div align="center">
                <h2>Accept order</h2>
                <form name="parthner" id="parthner" method="POST" action="/index.php">
                    <label for="comment">Please leave your comment (if any) here:</label><br/>
                    <textarea style="width:300px;height:120px;background:#fff" name="comment"/></textarea><br/>
                    <input type="submit" id="onok"  value="Accept"/>
                    <input type="hidden" name="option" value="com_localpartner_orders" />
                    <input type="hidden" name="order_id" value="<?php echo $order_id ?>" />
                    <input type="hidden" name="partner_id" value="<?php echo $partner_id ?>" />
                    <input type="hidden" name="key" value="<?php echo $key ?>" />
                    <input type="hidden" name="confirm" value="<?php echo $confirm ?>" />
                </form>
            </div>
        </div>
        <?php
    }

    function viewPageFinal($confirm) {
        global $mosConfig_live_site;
        ?>
        <script>
            window.onload = function()
            {
                var message = '<?php
        switch ($confirm) {
            case "1":
                echo "Order Confirmed Successfull. Thank you";
                break;
            case "-1":
                echo "Order Declined Successfull. Thank you";
                break;
            default :
                echo "Sorry, you do not have permission to change status of this order. Please contact us. Thank you ";
                break;
        }
        ?>'
                alert(message);
                document.location = "/index.php";
            }
        </script>


        <?php
    }

}
?>