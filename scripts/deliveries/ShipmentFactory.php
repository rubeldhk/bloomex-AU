<?php

global $mosConfig_absolute_path;
require_once( $mosConfig_absolute_path . "/scripts/deliveries/nzpost/ShipmentNzPost.php" );

class ShipmentFactory
{
    /**
     * @throws Exception
     */
    public static function build($carrier, $orderId)
    {

        switch($carrier)
        {
            case 'NzPost':
                $shipment = new ShipmentNzPost($orderId);
                break;
            default:
                throw new \Exception("Invalid carrier type given.");
        }
        return $shipment;
    }
}
