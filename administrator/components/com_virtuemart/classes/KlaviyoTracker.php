<?php


class KlaviyoTracker
{
    private $apiUrl;
    private $apiKey;

    private static $instance;

    public function __construct()
    {
        global $mosConfig_klaviyo_secret, $mosConfig_klaviyo_api_url;
        $this->apiKey = $mosConfig_klaviyo_secret;
        $this->apiUrl = $mosConfig_klaviyo_api_url;
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function sendCanceledOrder($email, $properties, $total)
    {
        date_default_timezone_set('Australia/Sydney');
        
        $data = [
             "data" => [
                 "type" => "event",
                 "attributes" => [
                     "properties" => $properties,
                     "time" => date("Y-m-d\TH:i:s", time()),
                     "value" => $total,
                     "value_currency" => "CAD",
                     "unique_id" => $properties['OrderId'],
                     "metric" => [
                         "data" => [
                             "type" => "metric",
                             "attributes" => [
                                 "name" => "Canceled Order"
                             ]
                         ]
                     ],
                     "profile" => [
                         "data" => [
                             "type" => "profile",
                             "attributes" => [
                                 "email" => $email,
                             ]
                         ]
                     ]
                 ]
             ]
        ];
        $this->sendEvent($email, $data);
    }

    public function sendPlaceOrder($email, $properties, $total)
    {
        date_default_timezone_set('Australia/Sydney');
        
        $data = [
            "data" => [
                "type" => "event",
                "attributes" => [
                    "properties" => $properties,
//                    "time" => date("Y-m-d\TH:i:s", time()),
                    "value" => $total,
                    "value_currency" => "AUD",
//                    "unique_id" => $this->genUuid(),
                    "unique_id" => $properties['OrderId'],
                    "metric" => [
                        "data" => [
                            "type" => "metric",
                            "attributes" => [
                                "name" => "Placed Order"
                            ]
                        ]
                    ],
                    "profile" => [
                        "data" => [
                            "type" => "profile",
                            "attributes" => [
                                "email" => $email
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->sendEvent($email, $data);
    }


    public function sendOrderedProduct($email, $properties, $total)
    {
        date_default_timezone_set('Australia/Sydney');
        
        $data = [
            "data" => [
                "type" => "event",
                "attributes" => [
                    "properties" => $properties,
//                    "time" => date("Y-m-d\TH:i:s", time()),
                    "value" => $total,
                    "value_currency" => "AUD",
                    "unique_id" => $properties['OrderId'],
                    "metric" => [
                        "data" => [
                            "type" => "metric",
                            "attributes" => [
                                "name" => "Ordered Product"
                            ]
                        ]
                    ],
                    "profile" => [
                        "data" => [
                            "type" => "profile",
                            "attributes" => [
                                "email" => $email,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->sendEvent($email, $data);
    }
    public function sendEvent($email, $data)
    {
        $url = $this->apiUrl ?? 'https://a.klaviyo.com/api/';
        $this->curlPost($url . 'events', $data );
    }

    public function curlPost($url, $data)
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $apiKey = $this->apiKey ?? 'pk_fa4b270a2d7e1130ca39e21b36997dae32';
            $headers = [];
            $headers[] = 'Authorization: Klaviyo-API-Key ' . $apiKey;
            $headers[] = 'Accept: application/vnd.api+json';
            $headers[] = 'Content-Type: application/vnd.api+json';
            $headers[] = 'Revision: 2025-04-15';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                //            echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
        } catch (\administrator\components\com_virtuemart\classes\Exception $e) {

        }

        return $result;
    }

    public function genUuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}