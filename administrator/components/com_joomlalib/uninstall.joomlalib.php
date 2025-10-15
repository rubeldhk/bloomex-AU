<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
function com_uninstall() {
	global $mosConfig_absolute_path;
	recursiveDelete($mosConfig_absolute_path.'/components/com_joomlalib/lock');
	recursiveDelete($mosConfig_absolute_path.'/components/com_joomlalib/cache');
	recursiveDelete($mosConfig_absolute_path.'/components/com_joomlalib/graphs');
	return 'Uninstall Succesfull.';
}

function recursiveDelete($obj){
	if(!is_writable($obj)){
		chmod($obj, 0777);
	}
	if (is_dir($obj)) {
		if ($fd = opendir($obj)) {
		    while (($child = readdir($fd)) !== false) {
				if ($child == '.' || $child == '..') {
				    continue;
				}
				recursiveDelete($obj.'/'.$child);
		    }
		    /* close and delete */
		    closedir($fd);
		    rmdir($obj);
		}
	} else {
		unlink($obj);
	}
}
?>