<?php
/**
 * @version		$Id: rokreporter.class.php 9 2007-04-13 04:08:48Z eddieajau $
 * @package		RokReporter
 * @copyright	(C) 2005 - 2007 New Life in IT Pty Ltd. All rights reserved.
 * @license		GNU General Public License
 */

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct access not allowed' );

define( 'JR_CAPTCHA', mosHash( $mainframe->getCfg( 'secret' ) . 'rokreporter' ) );

/**
 * Returns a table as an array
 * @param	string	The table name
 * @param	boolean	Include the column headings, default true
 */
function tableToArray( $table, $includeHeadings=true )
{
	global $database;

	$tableFields = $database->getTableFields( (array)$table );
	$columns = array_keys( $tableFields[$table] );

	$qb = new JQuery;
	
	$qb->select( implode( ',', $columns ) );
	$qb->from( $table );
	$database->setQuery( $qb->toString() );
	$rows = $database->loadRowList();

	if ($includeHeadings) {
		array_unshift( $rows, $columns );
	}
	return $rows;
}

/**
 * Converts a data array to a CSV style string
 * @param	array	2 dimensional data array
 * @return	string
 */
function arrayToCSV( $data )
{
	$CRLF = "\r\n";
	$nRows = count( $data );
	if ($nRows > 0)
	{
		$nCols = count( $data[0] );
		$lines = array();

		for ($row = 0; $row < $nRows; $row++)
		{
			for ($col = 0; $col < $nCols; $col++)
			{
				$cell = &$data[$row][$col];
				$cell = str_replace( "\r\n", "\n", $cell );
				$cell = str_replace( "\r", "\n", $cell );
				$cell = str_replace( "\n", $CRLF, $cell );
				$cell = '"' . str_replace( '"', '""', $cell ) . '"';
			}
			$lines[] = implode( ',', $data[$row] );
		}
		return implode( $CRLF, $lines );
	}
	return '';
}

/**
 * Abstract class for report writers
 * @package		RokReporter
 * @abstract
 */
class Report
{
	/** @var array An array of data rows */
	var $_data = null;
	/** @var int The total number of data rows (for pagination) */
	var $_rowcount = null;
	/** @var object Database connector */
	var $_db = null;
	/** @var string Default ordering column */
	var $_orderCol;
	/** @var string Default ordering direction */
	var $_orderDirn;

	/**
	 * Constructor
	 * @param	object	A database object
	 */
	function Report( &$db )
	{
		$this->_db = &$db;
		$this->_data = array();
	}

	/**
	 * Returns the interal database object
	 * @return	object
	 */
	function &getDBO()
	{
		return $this->_db;
	}

	/**
	 * Data getter/setter
	 * @param	array	An array of data
	 * @return	array	The current data array
	 */
	function &data( $value = null )
	{
		if ($value !== null) {
			$this->_data = $value;
		}
		return $this->_data; 
	}

	/**
	 * Row count getter/setter
	 * @param	int		The number of rows of data
	 * @return	int		The number of rows of data
	 */
	function &rowCount( $value = null )
	{
		if ($value !== null) {
			$this->_rowcount = $value;
		}
		return $this->_rowcount; 
	}

	/**
	 * Default ordering getter/setter
	 * @param	string	The column to order by
	 * @param	int		The direction
	 * @return	string	The number of rows of data
	 */
	function orderBy( $col = null, $dirn = null )
	{
		if ($col !== null) {
			$this->_orderCol = $col;
		}
		if ($dirn !== null) {
			$this->_orderDirn = (int) $dirn;
		}
		return $this->_db->getEscaped( $this->_orderCol ) . ' ' . ($this->_orderDirn ? 'DESC' : 'ASC');
	}

	/**
	 * An abstract function to be provided by the report to retrieve the data
	 * for the report
	 * @abstract
	 */
	function run( &$vars )
	{
	}

	/**
	 * Runs a query and sets the internal data array
	 * @param	string	The sql query to run
	 * @param	array	An arrau of system variables
	 */
	function query( $query, &$vars )
	{
		// set query with no limits to get row count
		$this->_db->setQuery( $query );
		$this->_db->query();
		$this->rowCount( $this->_db->getNumRows() );

		// set query with limits to retrieve data
		$this->_db->setQuery( $query, $vars['limitstart'], $vars['limit'] );
		$result = $this->_db->loadObjectList();

		if ($result) {
			$this->data( $result );
		}
	}

	/**
	 * Default function to renders the report
	 * @param	array	An array of system variables
	 * @param	array	An array of metadata variables
	 * @param	object	Page navigation
	 */
	function render( &$vars, &$metaData, &$pageNav )
	{
		$tmpl =& ReporterViews::createTemplate(
			'/reports/' . $this->id . '/list.html',
			array( 'adminlists.html', 'adminfilters.html' )
		);

		$tmpl->addVars( 'body', $vars );
		$tmpl->addVars( 'body', $metaData, 'meta_' );
		$tmpl->addObject( 'list-items', $this->data(), 'item_' );

		// setup the page navigation footer
		$pageNav->setTemplateVars( $tmpl, 'list-navigation' );

		$tmpl->displayParsedTemplate( 'form' );
	}

	/**
	 * An optional abstract function to be provided by the report to run custom
	 * operations on the listed data
	 * @abstract
	 */
	function tasker()
	{
	}
}

/**
 * Abstract class for report writers
 * @package		RokReporter
 */
class ReportHelper
{
	/**
	 * Loads the metadata from a file
	 * @static
	 */
	function getMetaData( $fileName )
	{
		if (!file_exists( $fileName )) {
			return array();
		}

		require_once( JPATH_SITE . '/includes/domit/xml_domit_lite_include.php' );

		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );
		if (!$xmlDoc->loadXML( $fileName, false, true )) {
			return array();
		}
		$root = &$xmlDoc->documentElement;
		$metaData = array();

		$element = &$root->getElementsByPath( 'name', 1 );
		$metaData['name'] = $element ? $element->getText() : 'Unknown';

		$element = &$root->getElementsByPath( 'description', 1 );
		$metaData['description'] = $element ? $element->getText() : 'Not supplied';

		return $metaData;
	}
}