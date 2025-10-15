<?php
/**
 * @version $Id: contact.class.php 10002 2008-02-08 10:56:57Z willebil $
 * @package Joomla
 * @subpackage Contact
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );


class mosSurvey extends mosDBTable {
    /** @var int Primary key */
    var $id 				= null;
    var $user_id 				= null;
    var $comments	= null;
    var $order_id	= null;
    var $place_order	= null;
    var $how_would_you_rate_the_bloomex_website	= null;
    var $how_would_you_rate_the_bloomex_product_selection_and_prices	= null;
    var $how_would_you_rate_the_bloomex_ordering_process	= null;
    var $how_closely_did_your_item_s_resemble_the_product_description	= null;
    var $how_would_you_rate_the_freshness_quality_and_appearance_of_your	= null;
    var $how_would_you_rate_your_delivery_experience	= null;
    var $how_would_you_rate_your_customer_service_experience	= null;
    var $survey_date	= null;
    var $how_was_your_experience_overall	= null;
    var $how_likely_are_you_to_recommend_bloomex_to_others	= null;


    /**
     * @param database A database connector object
     */
    function __construct() {
        global $database;
        parent::__construct( 'tbl_survey', 'id', $database );
    }
}

?>