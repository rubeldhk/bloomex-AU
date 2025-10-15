<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');


class mosThirdPartyFlorist extends mosDBTable
{
    /** @var int Primary key */
    var $id = null;
    /** @var string */
    var $name = null;
    /** @var string */
    var $email = null;
    /** @var string */
    var $phone = null;
    /** @var string */
    var $note = null;
    /** @var string */
    var $price = null;
    /** @var int */
    var $type = null;

    /**
     * @param database A database connector object
     */
    function __construct()
    {
        global $database;
        parent::__construct('tbl_third_party_florist', 'id', $database);
    }
}