<?php
/**
 * $Id: XmapPluginInstaller.php 113 2008-02-25 14:51:18Z root $
 * $LastChangedDate: 2008-02-25 08:51:18 -0600 (lun, 25 feb 2008) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

global $mosConfig_absolute_path;
require_once ($mosConfig_absolute_path. '/administrator/components/com_xmap/classes/XmapPlugin.php');
/**
* Plugin installer
* @package Xmap
*/
class XmapPluginInstaller extends mosInstaller {
	/**
	* Custom install method
	* @param boolean True if installing from directory
	*/
	function install( $p_fromdir = null ) {
		global $mosConfig_absolute_path, $database;

		if (!$this->preInstallCheck( $p_fromdir, 'xmap_ext' )) {
			return false;
		}

		$xmlDoc 	= $this->xmlDoc();
		$mosinstall 	=& $xmlDoc->documentElement;

		// Set some vars
		$e = &$mosinstall->getElementsByPath( 'name', 1 );
		$this->elementName( $e->getText() );
		if (!is_null($e)) {
			if ($e->getAttribute( 'published' ) == '1') {
				$published = 1;
			} else {
				$published = 0;
			}
		} else {
			$published 	= 0;
		}

		$this->elementDir( mosPathName( $mosConfig_absolute_path . '/administrator/components/com_xmap/extensions/') );

		if ($this->parseFiles( 'files', 'xmap_ext', 'No file is marked as extension file' ) === false) {
			return false;
		}

		$this->parseFiles( 'images' );

		// Insert extension in DB
		$query = "SELECT id FROM #__xmap_ext"
		. "\n WHERE extension = " . $database->Quote( $this->elementSpecial() )
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			$this->setError( 1, 'SQL error: ' . $database->stderr( true ) );
			return false;
		}

		$id = $database->loadResult();

		if (!$id) {
			// Insert extension in DB
			$query = "SELECT id FROM #__xmap_ext"
				. "\n WHERE extension = " . $database->Quote( $this->elementSpecial().'.bak' )
			;
			$database->setQuery( $query );
			if (!$database->query()) {
				$this->setError( 1, 'SQL error: ' . $database->stderr( true ) );
				return false;
			}
			$id = $database->loadResult();

			$row = new XmapPlugin( $database,$id );
			$row->published		= $published;
			if ( !$id ) {
				$row->params 	= '';
			}
			$row->extension		= $this->elementSpecial();
			$row->store();

		} else {
			$this->setError( 1, 'Plugin "' . $this->elementName() . '" already exists!' );
			return false;
		}
		if ($e = &$mosinstall->getElementsByPath( 'description', 1 )) {
			$this->setError( 0, $this->elementName() . '<p>' . $e->getText() . '</p>' );
		}

		return $this->copySetupFile('front');
	}

	/**
	* Custom install method
	* @param int The id of the extension
	*/
	function uninstall( $clientID,$id ) {
		global $database, $mosConfig_absolute_path;

		$id = intval( $id );

		$row = new XmapPlugin( $database,$id );

 		$basepath = $mosConfig_absolute_path . '/administrator/components/com_xmap/extensions/';

		$xmlfile = $basepath . $row->extension . '.xml';

		// see if there is an xml install file, must be same name as element
		if (file_exists( $xmlfile )) {
			$this->i_xmldoc = new DOMIT_Lite_Document();
			$this->i_xmldoc->resolveErrors( true );

			if ($this->i_xmldoc->loadXML( $xmlfile, false, true )) {
				$mosinstall =& $this->i_xmldoc->documentElement;
				// get the files element
				$files_element =& $mosinstall->getElementsByPath( 'files', 1 );
				if (!is_null( $files_element )) {
					$files = $files_element->childNodes;
					foreach ($files as $file) {
						// delete the files
						$filename = $file->getText();
						if (file_exists( $basepath . $filename )) {
							$parts = pathinfo( $filename );
							$subpath = $parts['dirname'];
							if ($subpath != '' && $subpath != '.' && $subpath != '..') {
								$result = deldir(mosPathName( $basepath . $subpath . '/' ));
							} else {
								$result = unlink( mosPathName ($basepath . $filename, false));
							}
						}
					}

					// remove XML file from front
					@unlink(  mosPathName ($xmlfile, false ) );
					$row->extension = $row->extension . '.bak';
					if (!$row->store()) {
						$msg = $database->stderr;
						die( $msg );
					}
					return true;
				}
			}
		}

	}
}
