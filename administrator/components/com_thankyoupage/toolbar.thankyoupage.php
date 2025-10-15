<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ( $task ) {
	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_Testimonial::_EDIT();
		break;

	default:
		TOOLBAR_Testimonial::_DEFAULT();
		break;
}

?>