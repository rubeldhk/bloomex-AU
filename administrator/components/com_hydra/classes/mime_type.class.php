<?php
/**
* $Id: mime_type.class.php 16 2007-04-15 12:18:46Z eaxs $
* @package   Project Fork
* @copyright Copyright (C) 2006-2007 Tobias Kuhn. All rights reserved.
* @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
*
* Project Fork is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
**/

defined ( '_VALID_MOS' ) OR DIE( 'Direct access is not allowed' );



class HydraMime
{
	var $mime_types;
	
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 13:59:59 CEST 2006 )
	* @name    HydraMime
	* @version 1.0 
	* @param   void
	* @return  void
	* @desc    constructor
	**/
	function HydraMime()
	{
		$t = array ('image/png' => HL_MIME_PNG,
		            'image/jpeg' => HL_MIME_JPG,
		            'application/msword' => HL_MIME_WORD,
		            'application/pdf' => HL_MIME_PDF,
		            'text/html' => HL_MIME_HTML,
		            'text/css' => HL_MIME_CSS,
		            'image/gif' => HL_MIME_GIF,
		            'text/plain' => HL_MIME_TXT,
		            'application/x-executable' => HL_MIME_EXE,
		            'application/vnd.oasis.opendocument.text' => HL_MIME_ODT
		           );
		           
		$this->mime_types = $t;           
	}
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 28 09:58:12 CEST 2006 )
	* @name    formatType
	* @version 1.1 
	* @param   string $type
	* @return  string unknown
	* @desc    
	**/
	function formatType($type)
	{
		foreach ($this->mime_types AS $mtype => $format)
		{
			if ($mtype == $type) { return $format; }
		}
		
		$type = "<a onmouseover=\"return overlib('<table>".$type."</table>', BELOW, RIGHT);\" onmouseout=\"return nd();\" >".HL_MIME_UNKNOWN."</a>";
		
		return $type;
	}
}
?>