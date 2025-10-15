<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* Based on AkoBook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
* @Achim Raji (aka cybergurk) - David Jardin (aka SniperSister) - Cedric May - Siegmund Langsch (aka langsch2)
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class mosEasybook extends mosDBTable {
  var $gbid=null;
  var $gbip=null;
  var $gbname=null;
  var $gbmail=null;
  var $gbloca=null;
  var $gbpage=null;
  var $gbvote=null;
  var $gbtext=null;
  var $gbdate=null;
  var $gbcomment=null;
  var $gbedit=null;
  var $gbeditdate=null;
  var $published=null;
  var $gbicq=null;
  var $gbaim=null;
  var $gbmsn=null;
  var $gbyah=null;
  var $gbskype=null;

  function __construct( &$db ) {
      parent::__construct( '#__easybook', 'id', $db );
  }

}
?>