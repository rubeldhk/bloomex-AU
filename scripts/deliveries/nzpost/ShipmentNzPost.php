<?php

require_once('connectstonzpostapi.php');

class ShipmentNzPost
{
    public $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function printLabel($shipment_id)
    {

        $nzpost = new NZPost($this->orderId);
        $nzpost->print_label(['shipment_id' => $shipment_id,'order_id' => $this->orderId]);


    }

    public function make(): array
    {
        $return = array();

        $nzpost = new NZPost($this->orderId);

//        $response_options = $nzpost->getOptions();
//
//        if (!$response_options['success'] || !$response_options['services']) {
//            $return['success'] = false;
//            $return['error'] = 'No options for shipment ';
//            return $return;
//        }
        $data = $this->makeDataForRequest();

        $shipment = $nzpost->createshipment($data);

        if ($shipment['shipment_id']) {
            $return['success'] = true;
            $return['shipment_id'] = $shipment['shipment_id'];
        } else {
            $return['success'] = false;
            $return['error'] = $shipment['error'];

        }
        return $return;
    }

    /**
     * @return array
     */
    public function makeDataForRequest(): array
    {

        $data = array();
        $data['parcels'] = '1';
        $data['weights'] = '3';
        $data['lengths'] = '50';
        $data['widths'] = '20';
        $data['heights'] = '20';
//        $data['cpsr'] = 'on';
//        $data['option_type'] = $response_options['services'][0]['service_code'] . '|' . $response_options['services'][0]['carrier'];
        $data['option_type'] = 'CPOLPPS|CourierPost';
        $data['delivery_id'] = '10';
        $data['order_id'] = $this->orderId;
        $data['sender'] = $_SESSION['session_username'];

        return $data;
    }
}
