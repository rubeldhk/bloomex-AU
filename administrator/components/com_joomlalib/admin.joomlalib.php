<?php
/**
 * Enter description here...
 *
 * @package JL
 */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
 * @ignore 
 */
require_once( $mainframe->getPath( 'admin_html' ) );

/**
 * @ignore 
 */
require_once('jlcoreapi.inc');
JLCoreApi::import('jlapp');
JLCoreApi::import('jllog');
JLCoreApi::internalImport('jlcfg');
$baseUrl = 'index2.php?option=com_joomlalib';

$jllog = new JLLog('jl');

switch($act) 
{
	/* jlconf switch */
	case "jlconf":
		$jlCfg = new JLCfg($jllog, $option, $act, $task);
	
		if(!$jlCfg->loadFromDB()) {
			$msg = "Unable to load JoomlaLib configuration from the database.";
			$jllog->l(10, $msg);
			echo $msg;
		}
		
		switch ($task)
		{
			case "saveedit":
				if(!$jlCfg->loadFromForm()) {
					$msg = "Please fix any errors above and save again.";
					echo $msg;
				}
				
				$jlCfg->saveConfiguration("$baseUrl&act=$act");
				
				/* It worked. Redirect */
				$msg = 'Configuration Saved';
				mosRedirect($baseUrl.'&act=jlconf', $msg);
				break;
			
			default:
				break;
		}
		
		showJLConfiguration($jlCfg);
		break;
	
	/* jllog switch */
	case "jllog":
		switch ($task){
			case 'dumplog':
				/* dumplog from viewlog page */
				$appId = mosGetParam($_REQUEST, 'appId', null);
				dumpJLLog($appId);
			break;
			case 'clean':
				/* clean call from maintance page */
				$type = (int) mosGetParam($_REQUEST, 'type', null);
				$msg = "Succesfully Cleaned.";
				switch($type){
					case 1:
						print 'Global Clean';
						JLLogHelperClean::removeLogGlobal();
					break;
					case 2:
						$per = (int) mosGetParam($_REQUEST, 'per', 100);
						JLLogHelperClean::cleanLogGlobal($per);
					break;
					case 3:
						$appId = (int) mosGetParam($_REQUEST, 'appId', 0);
						JLLogHelperClean::removeLog($appId);
					break;
					case 5:
						JLLogHelperClean::removeOrpans();
					break;
				}
				mosRedirect($baseUrl.'&act=jllog&task=maintain', $msg);
			break;
			/* pages from here */
			case 'maintain':
				/*maintain page, cleaning and deleting */
				maintainJLLog();
			break;
			case 'view': 
			default:
				/* log viewer, with filter and page-ing */
				JLLogHelperView::browser($option);
		}
		break;
	
	/* jlapp switch */
	case "jlapp":
		switch ($task){
			case 'remove':
				$cid = mosGetParam($_REQUEST, 'cid', array(0));
				$msg = 'Succesfully Removed.';
				foreach($cid as $id){
					if(!JLApp::removeApp($id)){
						$msg = 'Failed to Remove Application!';
					}
				}
				mosRedirect($baseUrl.'&act=jlapp&task=view', $msg);
			break;
			/* for now limit save and edit to first selected */
			case 'saveedit':
				$cid = mosGetParam($_REQUEST, 'cid', array(0));
				$id = (int) $cid[0];
				$description = mosGetParam($_REQUEST, 'appdescshort', '');
				$msg = (!JLApp::setDesc($id, $description)) ? 'Failed to Save!': 'Succesfully Saved.';
				mosRedirect($baseUrl.'&act=jlapp&task=view', $msg);
			break;
			case 'edit':
				$cid = mosGetParam($_REQUEST, 'cid', array(0));
				$id = (int) $cid[0];
				editApp($id);
			break;
			case 'view': 
			default:
				viewApp();
		}
		break;
		
	case "about":
	default:
		JoomlaLibHTML::aboutPage();
		break;
}

/**
 * Adjust application description
 *
 * @param integer $id
 */
function editApp($id){
	global $database;
		
	$query = "SELECT jlappid, apphandle, appdescshort, appdesclong FROM #__jl_app WHERE jlappid=$id";
	$database->setQuery($query);
	$row = $database->loadRow();
	JoomlaLibHTML::defaultHeader('Edit Application');
	JoomlaLibHTML::editApp($row);
}

/**
 * View applications registered with JLapp
 *
 * @param string $orderBy
 */
function viewApp($orderBy='jlappid'){
	global $database, $mosConfig_list_limit, $mosConfig_absolute_path, $mainframe;
	require_once( $mosConfig_absolute_path.'/administrator/includes/pageNavigation.php' );
	$option = "com_joomlalib";
	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 		= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$search 		= $database->getEscaped( trim( strtolower( $search ) ) );
	
	$query = "SELECT COUNT(*) FROM #__jl_app";
	if (isset( $search ) && $search!= "") {
		$query .= " WHERE apphandle LIKE '%$search%' OR appdescshort LIKE '%$search%'";
	}
	$database->setQuery($query);
	$total = $database->loadResult();
	
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );	
	
	$query = "SELECT jlappid, apphandle, appdescshort, appdesclong FROM #__jl_app";
	if (isset( $search ) && $search!= "") {
		$query .= " WHERE apphandle LIKE '%$search%' OR appdescshort LIKE '%$search%'";
	}
	$query .= " LIMIT $pageNav->limitstart, $pageNav->limit";
	
	$database->setQuery($query);
		
	$objList = $database->loadObjectList($orderBy);
	if(null === $objList) {
		$msg = $database->getErrorMsg();
		print $msg;
		return;
	}
	ksort($objList);

	JoomlaLibHTML::viewApp($objList, $pageNav, $search);
}

function maintainJLLog(){
	JoomlaLibHTML::defaultHeader('JLLog Maintance');
	list($totalCount, $totalSize, $applications) = JLLogHelper::getStats();
	JLCoreApi::import('JLApp');
	
	$purge = array();
	$purge[] = mosHTML::makeOption( 0, '--- Select Application ---' );
	$clean = array();
	$clean[] = mosHTML::makeOption( 0, '--- Select Application ---' );
	
	foreach ($applications as $id => $info){
		$info['desc'] = JLApp::getAppInfo($id, 'appdescshort');
		$applications[$id] = $info;
		$text = $info['desc'].' ('.round($info['size']).'Kb )';
		$purge[] = mosHTML::makeOption( $id, $text );
		
		$clean[] = mosHTML::makeOption( $id, $info['desc'] );
	}
	$lists = array();
	$jumps = array(0,5,10,25,50,75, 100);
	$options = array();
	foreach($jumps as $percentage){
		$text = $percentage.'% ('.round(($totalSize/100) * (100 - $percentage)).'Kb )';
		$options[] = mosHTML::makeOption( $percentage, $text );
	}
	$lists['cleanDatabase'] = mosHTML::selectList($options, 'per', 'class="inputbox" size="1"', 'value', 'text', 100);
	
	//$lists['cleanApp'] = mosHTML::selectList($clean, 'appId', 'class="inputbox" size="1"', 'value', 'text', 0);
	$lists['purgeApp'] = mosHTML::selectList($purge, 'appId', 'class="inputbox" size="1"', 'value', 'text', 0);
	
	JoomlaLibHTML::JLLogMaintain($totalCount, $totalSize, $applications, $lists);
}

function cleanJLLog($type, $id=null){
	
}

function dumpJLLog($appId){

}

/**
 * Show the JL Configuration page
 *
 * @param JLCfg Configuration object to show page from
 */
function showJLConfiguration($jlCfg)
{
	?>
	<script language="javascript" type="text/javascript">
	    function submitbutton(pressbutton) 
	    {
	    	var form = document.adminForm;
	      	if (pressbutton == 'cancel') 
	      	{
	        	submitform( pressbutton );
	        	return;
	      	} 
	      	else 
	      	{
	        	submitform( pressbutton );
	      	}
	    }
	    </script>
	    
	<?php    
	  	
	echo "<h1>JoomlaLib</h1>\n";
	$jlCfg->showAdminForm();
}

function testBackTrace($level, $count, $jllog){
	$str = $jllog->l($level, 'Backtrace test at level %d at count %d', $level, $count);
	return $str;
}
?>