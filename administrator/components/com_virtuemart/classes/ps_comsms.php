<?php

class SMSParam 
{
    public $CellNumber;
    public $AccountKey;
    public $MessageBody;
}

class ps_comsms 
{

    private $templates = [
            'C' => 'Bloomex Order #{orderId} has been confirmed',
            'Z' => 'Bloomex Order #{orderId} is in-transit to recipient',
            'D' => 'Bloomex Order #{orderId} has been delivered',
            'R' => 'Bloomex Order #{orderId} has been refunded',
            'X' => 'Bloomex Order #{orderId} has been canceled',
    ];

    function __construct($type = '') {
        if (!class_exists('database')) {
            //we are using joomla libraries
            define('_VALID_MOS', 1);

            //have to declare mosconfig variables and database as global
            global $database, $mosConfig_mailfrom, $mosConfig_smtphost, $mosConfig_smtppass, $mosConfig_smtpuser, $mosConfig_smtpport, $mosConfig_smtpprotocol, $mosConfig_live_site, $mosConfig_mailfrom;
            global $mosConfig_user_adm, $mosConfig_user, $mosConfig_password_adm, $mosConfig_password, $mosConfig_host, $mosConfig_db, $mosConfig_dbprefix;
            if (!$mosConfig_live_site) {
                include dirname(__FILE__) . "/../../../../configuration.php";
            }
            $mosConfig_user = $mosConfig_user_adm ?? $mosConfig_user;
            $mosConfig_password = $mosConfig_password_adm ?? $mosConfig_password;
            include_once dirname(__FILE__) . "/../../../../includes/database.mysqli.php";
            $database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
        }
    }
    
    public function send(int $orderId, string $orderStatusCode) 
    {
        global $mosConfig_limit_sms_sender_AccountKey;


        $template = $this->getStatusTemplate($orderId, $orderStatusCode) ?? '';
        $phoneNumber = $this->getPhoneNumber($orderId) ?? '';

        if ($template && $phoneNumber) {
            $client = new SoapClient('http://smsgateway.ca/SendSMS.asmx?WSDL');
            $parameters = new SMSParam;
            $parameters->CellNumber = $phoneNumber;
            $parameters->AccountKey = $mosConfig_limit_sms_sender_AccountKey;
            $parameters->MessageBody = $template;
            $parameters->Reference = $orderId;
            $result = $client->SendMessageWithReferenceExtended($parameters);

        }
    }



    /**
     * Gets status template
     * @param int $orderId
     * @param string $orderStatusCode
     * @return string|boolean
     */
    private function getStatusTemplate($orderId, $orderStatusCode) {

        return str_replace('{orderId}', $orderId, $this->templates[$orderStatusCode]);
    }

    /**
     * Gets correct phone number to send sms
     * @param int $orderId
     * @return mixed
     */
    private function getPhoneNumber($orderId) {
        global $database;

        $obj = false;
        $query = "SELECT `phone_number` 
        FROM `tbl_track_order_status` 
        WHERE `order_id`=" . $database->getEscaped($orderId) . "";

        $database->setQuery($query);
        $database->loadObject($obj);

        if (!$obj) {
            return false;
        }
        return $obj->phone_number;
    }

}
