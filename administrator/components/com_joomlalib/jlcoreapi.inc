<?php
/**
 * Redirects front & back end to jlcoreapi.class
 * 
 * Security is handeld in jlcoreapi itself so we don't have worry about it here.
 *
 * @package JL
 */

if(strpos(dirname(__FILE__), 'administrator') === false){
	/**
	 * @ignore
	 */
	require_once(dirname(__FILE__).'/classes/jlcoreapi.class');
} else {
	/**
	 * @ignore
	 */
	require_once(dirname(__FILE__).'/../../../components/com_joomlalib/classes/jlcoreapi.class');
}
?>