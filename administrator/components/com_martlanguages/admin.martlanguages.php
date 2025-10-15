<?php
/**
* @version $Id: admin.martlanguages.php,v 1.6 2006/01/15 19:42:45 soeren_nb Exp $
* @package martlanguages
* @copyright (C) 2005 Soeren Eberhardt
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


// ensure user has access to this function
if (!$acl->acl_check( 'administration', 'config', 'users', $my->usertype )) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}
$tokenFile = $mosConfig_absolute_path ."/administrator/components/$option/languageTokens.arr";

require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mosConfig_absolute_path ."/administrator/components/$option/compat.php42x.php" );

$task = trim( strtolower( mosGetParam( $_REQUEST, "task", "" ) ) );
$cid = mosGetParam( $_REQUEST, "cid", array(0) );

if (!is_array( $cid )) {
	$cid = array(0);
}

switch ($task) {
	case "new":
	editLanguageSource( "", $option );
	break;

	case "edit_source":
	editLanguageSource( $cid, $option );
	break;

	case "save_source":
	saveLanguageSource( $option );
	break;

	case "remove":
	removeLanguage( $cid, $option );
	break;
	
	case "list_tokens":
	listLanguageTokens( $option );
	break;
	
	case "edit_tokens":
	editLanguageTokens( $option );
	break;

	case "save_tokens":
		ini_set( 'memory_limit', '32M');
		saveLanguageTokens( $option );
		listLanguageTokens( $option );
		break;

	case "cancel":
	mosRedirect( "index2.php?option=$option" );
	break;

	default:
	viewLanguages( $option );
	break;
}

/**
* Compiles a list of installed languages
*/
function viewLanguages( $option ) {
	global $languages;
	global $mainframe;
	global $mosConfig_lang, $mosConfig_absolute_path, $mosConfig_list_limit;

	$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
	$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );

	// get current languages
	$cur_language = $mosConfig_lang;
	$rows = array();
	$rowid = 0;
	$phpFilesInDir = getLanguageFileNames();
	
	foreach($phpFilesInDir as $phpfile) {

		$row = new StdClass();
		$row->id = $rowid;
		$row->language = basename( $phpfile, ".php" );

		// if current than set published
		if ($cur_language == $row->language) {
			$row->published	= 1;
		} else {
			$row->published = 0;
		}

		$rows[] = $row;
		$rowid++;
	}

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( count( $rows ), $limitstart, $limit );

	$rows = array_slice( $rows, $pageNav->limitstart, $pageNav->limit );

	HTML_martlanguages::showLanguages( $cur_language, $rows, $pageNav, $option );
}


function editLanguageSource( $p_lname, $option) {
	global $mosConfig_absolute_path, $tokenFile;
	$content = "";
	
	if( !empty( $p_lname )) {
		foreach( $p_lname as $language )
			$languagesArr[] = readLanguageIntoArray( $language );
	}
	else {
		$languagesArr = Array( "0" => Array( "languageCode" => "newLanguage")
							);
	}
	
	$englishLanguageArr = getTokenFile( $tokenFile );
	
	HTML_martlanguages::editLanguageSource( $englishLanguageArr, $languagesArr, $option );

}

function saveLanguageSource( $option, $langArray = Array(), $doRedirect = true ) {
	global $mosConfig_absolute_path, $tokenFile;
	if( empty( $langArray ))
		$languages = mosGetParam( $_POST, 'language', Array(0) );
	else
		$languages[0] = $langArray;

	if (empty( $languages )) {
		mosRedirect( "index2.php?option=$option&mosmsg=Operation failed: No language received." );
	}
	
	foreach( $languages as $language ) {
		$languageName = $language["languageCode"];
		
		$file = $mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/$languageName.php";
			/*
		if (is_writable( $file ) == false) {
			mosRedirect( "index2.php?option=$option&mosmsg=Operation failed: The file is not writable." );
		}
		*/
		$contents = "<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version \$Id: admin.martlanguages.php,v 1.6 2006/01/15 19:42:45 soeren_nb Exp $
* @package VirtueMart
* @subpackage languages
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
class vmLanguage extends vmAbstractLanguage {
";
		$eng_lang_loaded = false;
		foreach( $language as $token => $value ) {
			// not to process emty tokens means: removing them!
			if( $token != "languageCode" && !empty($token) ) {
				// Prevent situations like  &amp;uuml;
				//means don't encode HTML Entities again
				$value = str_replace( '&amp;', '&', $value );
				// Allow HTML Tags
				$value = str_replace( '&quot;', '"', $value );
				
				$value = str_replace( '\"', '"', $value );
				$value = str_replace( '&lt;', '<', $value );
				$value = str_replace( '&gt;', '>', $value );
				if (!get_magic_quotes_gpc()) {
					$value = addslashes( $value );
				}
				if( empty( $value )) {
					if( !$eng_lang_loaded ) {
						$englishLanguageArr = getTokenFile( $tokenFile );
					}
					$value = $englishLanguageArr[$token];
				}
				$contents .= "	var \$$token = '$value';\n";
			}
		}
		$contents .= "        
}
class phpShopLanguage extends vmLanguage { }

/** @global vmLanguage \$VM_LANG */
\$VM_LANG =new  vmLanguage();
?>";
		if( !file_put_contents( $file, $contents ) )
			if ( $doRedirect )
				mosRedirect( "index2.php?option=$option&mosmsg=Operation failed: Failed to write $file." );
			else
				return false;
	}
	if( $doRedirect )
		mosRedirect( "index2.php?option=$option" );
	else
		return true;

}

/**
* Remove the selected language
*/
function removeLanguage( $cid, $option, $client ) {
	global $mosConfig_lang, $mosConfig_absolute_path;

	$cur_language = $mosConfig_lang;
	$ok = true;
	$message = "";
	foreach( $cid as $language ) {
		if ($cur_language == $language) {
			echo "<script>alert(\"You can not delete language in use.\"); window.history.go(-1); </script>\n";
			exit();
		}

		$lang_path = $mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/$language.php";
		if( !unlink($lang_path))
			$ok = false;
			$message .= $language.",";
	}
	if( $ok )
		mosRedirect( "index2.php?option=$option&mosmsg=Successfully removed Language(s) $message" );
	else
		mosRedirect( "index2.php?option=$option&mosmsg=Failed to remove Language(s) $message" );
}
#########################
function listLanguageTokens( $option ) {
	global $tokenFile;
	$tokenArr = getTokenFile( $tokenFile );
	HTML_martlanguages::viewTokens( $tokenArr, $option );
}

########################
function editLanguageTokens( $option ) {
	global $tokenFile;
	$tokenArr = getTokenFile( $tokenFile );
	HTML_martlanguages::editTokens( $tokenArr, $option );
}

########################
function saveLanguageTokens( $option ) {
	global $tokenFile, $messages;
	
	$tokens = mosGetParam( $_POST, "tokens", Array(0) );
	
	if( !empty( $tokens )) {
	
		$newTokens = Array();
		$changedTokens = Array();
		$removedTokens = Array();
		$messages = Array();
		foreach( $tokens as $languageToken ) {
			
			// Get new tokens
			if( empty( $languageToken["current"] )) {
				$newTokens[] = Array( "value" => $languageToken["value"],
											"default_text" => $languageToken["default_text"]
										);
			}
			// Get renamed or removed tokens
			elseif( $languageToken["current"] != $languageToken["value"] ) {
				if( empty( $languageToken["value"] ) && !empty( $languageToken['current']))
					$removedTokens[] = Array( "value" => $languageToken["current"] );
				else
					$changedTokens[] = Array( "old_name" => $languageToken["current"],
											"new_name" => $languageToken["value"]
										);
			}
			// else: do nothing
		
		}
		
		// Update all language files if necessary
		if( !empty( $newTokens ) || !empty( $changedTokens ) || !empty( $removedTokens ))
			$langFiles = getLanguageFileNames();
			
		if( !empty( $newTokens ) || !empty( $removedTokens )) {
			// Add the new token(s) to ALL available language files
			// using the text from default_value
			foreach( $langFiles as $langFile ) {
				$langName = basename( $langFile, ".php" );
				
				$langArr = readLanguageIntoArray( $langName );
				foreach( $newTokens as $replacement ) {
					if( !array_key_exists( $replacement['value'], $langArr ))
						$langArr[$replacement['value']] = $replacement['default_text'];
				}
				foreach( $removedTokens as $removement ) {
					unset( $langArr[$removement['value']] );
				}
				// Finally write the language file back
				if( !saveLanguageSource( $option, $langArr, false ))
					$messages[] = "Failed writing $langName Language file while adding new tokens";
				else
					$messages[] = "Successfully added new tokens in $langName Language file";

			}
		}
		if( !empty( $changedTokens )) {
			// Change the modified token names
			// in all available language files
			foreach( $langFiles as $langFile ) {
				$langName = basename( $langFile, ".php" );
				$langContent = readLanguageIntoString( $langName );
				foreach( $newTokens as $replacement ) {
					if( !stristr( '$'.$replacement['new_name'].' ', $langContent )
					&& !stristr( '$'.$replacement['new_name'].'	', $langContent )
					&& !stristr( '$'.$replacement['new_name'].'=', $langContent ))
						$langContent = str_replace( '$'.$replacement['old_name'], $replacement['new_name'], $langContent );
				}
				// Finally write the language file back
				if( !writeStringIntoLanguage( $langName, $langContent ))
					$messages[] = "Failed writing the Language file $langFile while replacing tokens";
				else
					$messages[] = "Successfully replaced tokens in the Language file ($langFile)";
			}
		}
		if( !empty( $newTokens ) || !empty( $changedTokens ) || !empty( $removedTokens ))
			updateTokenFile( $tokenFile );
	}

}

#########################
function getTokenFile( $tokenFile ) {
	global $mosConfig_absolute_path;
	// have a look at the english language file
	if( !file_exists( $tokenFile )) {
		return updateTokenFile( $tokenFile );
	}
	else
		return unserialize( file_get_contents( $tokenFile ));
	
}
##########################
function updateTokenFile( $tokenFile ) {
	global $mosConfig_absolute_path;
	// have a look at the english language file
	$tokenArr = readLanguageIntoArray();
	unset( $tokenArr["languageCode"] );
	if( $tokenArr ) {
		file_put_contents( $tokenFile, serialize( $tokenArr ) );
		return $tokenArr;
	}
	else
		return false;
	
}
############################
# returns an Array of a language file
# array( "_PHPSHOP_BLABLA" => "English Meaning",
#		"_PHPSHOP_MOREBLABLA => "Another text",
#		....
#		"languageCode"=> "english"
#		)
###########################
function readLanguageIntoArray( $language="english" ) {
	global $mosConfig_absolute_path;
	if( file_exists( $mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/$language.php" )) {
		$source = file_get_contents($mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/$language.php");
		
		$tokens = token_get_all($source);
		
		$virtuemartlanguage = Array();
		foreach ($tokens as $token) {
			
			if( is_array( $token )) {
				
				list($id, $text) = $token;
				
				switch( $id ) {
					// $_PHPSHOP_BLABLA
					case T_VARIABLE: 
						if( $text != "\$_PHPSHOP_LANG" ) {
							$key = substr( $text, 1 );
						}
						break;
					case T_CONSTANT_ENCAPSED_STRING:
						if( !empty( $key )) {
							$value = substr( $text, 1, strlen( $text )-2 );
							$virtuemartlanguage[$key] = $value;
						}
						$key = "";
						break;
					default:
						break;
				}
			}
		}
		
		$virtuemartlanguage["languageCode"] = $language;
		return $virtuemartlanguage;
	}
	else
		return false;
}
function readLanguageIntoString( $language="english" ) {
	global $mosConfig_absolute_path;
	if( file_exists( $mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/$language.php" )) {
		$source = file_get_contents($mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/$language.php");
		
		return $source;
	}
	else
		return false;
}
function writeStringIntoLanguage( $language, $contents ) {
	global $mosConfig_absolute_path;
	$file = $mosConfig_absolute_path."/administrator/components/com_virtuemart/languages/$language.php";
	if( file_exists( $file )) {
		if( file_put_contents( $file, $contents ))
			return true;
		else 
			return false;
	}
	else
		return false;
}
function getLanguageFileNames() {
	global $mosConfig_absolute_path;
	
	// Read the template dir to find templates
	$languageBaseDir = mosPathName(mosPathName($mosConfig_absolute_path) . "administrator/components/com_virtuemart/languages");

	$phpFilesInDir = mosReadDirectory($languageBaseDir,'.php$');

	return $phpFilesInDir;
}
function martHTMLEntities( $text, $quote_style=ENT_COMPAT, $languageCode ) {
	$charset = getCharSet( $languageCode );
	if( $charset == "utf-8" )
		return htmlentities( $text, $quote_style, $charset );
	else
		return $text;
}

function martHTMLEntityDecode( $text, $quote_style=ENT_COMPAT, $languageCode ) {
	$charset = getCharSet( $languageCode );
	if( $charset == "utf-8" )
		return html_entity_decode( $text, $quote_style, $charset );
	else
		return $text;
}

function getCharSet( $languageFileName ) {
	switch( $languageFileName ) {
		// we only handle the special cases
		// all other ones are default
		
		case "estonian":
			return "utf-85";
			
		case "russian":
			return "KOI8-R";
		
		case "serbian":
		case "macedonian":
			return "cp1251";
		
		case "traditional_chinese":
			return "BIG5";
		
		case "simplified_chinese":
			return "GB2312";
		
		case "vietnamese_UTF-8":
			return "UTF-8";
			
		default:
			return "utf-8";
	}
}


?>