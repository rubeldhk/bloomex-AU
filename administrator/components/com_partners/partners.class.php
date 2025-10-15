<?php
// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );


class mosPartners extends mosDBTable {
	/** @var int Primary key */
	var $partner_id 				= null;
	/** @var string */
	var $partner_name 				= null;
	/** @var string */
	var $partner_email			= null;
        /** @var string */
        var $partner_phone			= null;
	/** @var string */
	var $note	 		= null;
	/** @var string */
	var $partner_price			= null;

	/**
	* @param database A database connector object
	*/
	function __construct() {
		global $database;
        parent::__construct( 'tbl_local_parthners', 'partner_id', $database );
	}
}