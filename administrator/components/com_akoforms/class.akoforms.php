<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* Class for Forms
**/
class mosAkoforms extends mosDBTable {
  var $id=null;
  var $title=null;
  var $text=null;
  var $sendmail=null;
  var $emails=null;
  var $savedb=null;
  var $showresult=null;
  var $published=null;
  var $publish_up=null;
  var $publish_down=null;
  var $ordering=null;
  var $target=null;
  var $targeturl=null;
  var $thanktitle=null;
  var $thanktext=null;

  function __construct( &$db ) {
      parent::__construct( '#__akoforms', 'id', $db );
  }
}

/**
* Class for Fields
**/
class mosAkoformsfields extends mosDBTable {
  var $id=null;
  var $catid=null;
  var $type=null;
  var $title=null;
  var $text=null;
  var $value=null;
  var $style=null;
  var $required=null;
  var $published=null;
  var $publish_up=null;
  var $publish_down=null;
  var $ordering=null;

  function __construct( &$db ) {
      parent::__construct( '#__akoforms_fields', 'id', $db );
  }
}

/**
* Class for Settings
**/
class mosAkoformsconfig extends mosDBTable {
  var $file_akf_mailsubject;
  var $file_akf_mailcharset;
  var $file_akf_mailsender;
  var $file_akf_mailemail;
  var $file_akf_mailheadcss;
  var $file_akf_mailrow1css;
  var $file_akf_mailrow2css;
  var $file_akf_layoutstart;
  var $file_akf_layoutrow;
  var $file_akf_layoutend;

  function mosAkoformsconfig() {
    $this->_alias = array(
      'akf_mailsubject' => 'file_akf_mailsubject',
      'akf_mailcharset' => 'file_akf_mailcharset',
      'akf_mailsender'  => 'file_akf_mailsender',
      'akf_mailemail'   => 'file_akf_mailemail',
      'akf_mailheadcss' => 'file_akf_mailheadcss',
      'akf_mailrow1css' => 'file_akf_mailrow1css',
      'akf_mailrow2css' => 'file_akf_mailrow2css',
      'akf_layoutstart' => 'file_akf_layoutstart',
      'akf_layoutrow'   => 'file_akf_layoutrow',
      'akf_layoutend'   => 'file_akf_layoutend'
    );
  }

  function getVarText() {
    $txt = '';
    foreach ($this->_alias as $k=>$v) {
      $txt .= "\$$v = '".addslashes( $this->$k )."';\n";
    }
    return $txt;
  }

  function bindGlobals() {
    foreach ($this->_alias as $k=>$v) {
      if(isset($GLOBALS[$v])) {
        $this->$k = $GLOBALS[$v];
      } else {
        $this->$k = "";
      }
    }
  }

}

?>