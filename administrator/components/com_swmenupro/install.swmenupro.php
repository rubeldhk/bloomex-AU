<?php

/**
* swmenupro v4.5
* http://swonline.biz
* Copyright 2006 Sean White
**/


function com_install () {
	global $mosConfig_absolute_path;
		
	if(file_exists($mosConfig_absolute_path."/modules/mod_swmenupro.php")){
		//unlink($mosConfig_absolute_path."/modules/mod_swmenupro.php");
		//sw_deldir($mosConfig_absolute_path."/modules/mod_swmenupro");	
	}
	if(file_exists($mosConfig_absolute_path."/modules/mod_swmenupro.xml")){
		unlink($mosConfig_absolute_path."/modules/mod_swmenupro.xml");
	}
	
	if(sw_copydirr($mosConfig_absolute_path."/administrator/components/com_swmenupro/modules",$mosConfig_absolute_path."/modules",0775,false)){
	rename($mosConfig_absolute_path."/modules/mod_swmenupro.sw",$mosConfig_absolute_path."/modules/mod_swmenupro.xml");
	$module_msg="Successfully Installed swMenuPro Module";
	}else{
	$module_msg="Could Not Install swMenuPro Module.  Please visit the swMenuPro Upgrade/Repair facility by clicking <a href=\"index2.php?option=com_swmenupro&task=upgrade\">here.</a>\n";
	}
	$msg="<div align=\"center\">\n";
	$msg.="<table cellpadding=\"4\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"100%\"> \n";
	$msg.="<tr><td align=\"center\"><img src=\"components/com_swmenupro/images/swmenupro_logo_small.gif\" border=\"0\"/></td></tr>\n";
	$msg.="<tr><td align=\"center\"><br /> <b>Module Status: ".$module_msg."</b><br /></td></tr>\n";
	$msg.="<tr><td align=\"center\">swMenuPro has been sucessfully installed.  Thankyou for purchasing. <br /> For support, please see the forums at <a href=\"http://www.swmenupro.com\">www.swmenupro.com</a> </td></tr>\n";
    $msg.="<tr> \n";
    $msg.="<td width=\"100%\" align=\"center\">\n";
	$msg.="<a href=\"http://www.swmenupro.com\" target=\"_blank\">	\n";
	$msg.="<img src=\"components/com_swmenupro/images/swmenupro_footer.png\" alt=\"swmenupro.com\" border=\"0\" />\n";
	$msg.="</a><br/> SWmenuPro &copy;2005 by Sean White\n";
	$msg.="</td> \n";
    $msg.="</tr> \n";
    $msg.="</table> \n";
    $msg.="</div> \n";	
	echo $msg;
    return ;
}



function sw_copydirr($fromDir,$toDir,$chmod=0775,$verbose=false)
/*
copies everything from directory $fromDir to directory $toDir
and sets up files mode $chmod
*/
{
	//* Check for some errors
	$errors=array();
	$messages=array();
	if (!is_writable($toDir))
	$errors[]='target '.$toDir.' is not writable';
	if (!is_dir($toDir))
	$errors[]='target '.$toDir.' is not a directory';
	if (!is_dir($fromDir))
	$errors[]='source '.$fromDir.' is not a directory';
	if (!empty($errors))
	{
		if ($verbose)
		foreach($errors as $err)
		echo '<strong>Error</strong>: '.$err.'<br />';
		return false;
	}
	//*/
	$exceptions=array('.','..');
	//* Processing
	$handle=opendir($fromDir);
	while (false!==($item=readdir($handle)))
	if (!in_array($item,$exceptions))
	{
		//* cleanup for trailing slashes in directories destinations
		$from=str_replace('//','/',$fromDir.'/'.$item);
		$to=str_replace('//','/',$toDir.'/'.$item);
		//*/
		if (is_file($from))
		{
			if (@copy($from,$to))
			{
				chmod($to,$chmod);
				touch($to,filemtime($from)); // to track last modified time
				$messages[]='File copied from '.$from.' to '.$to;
			}
			else
			$errors[]='cannot copy file from '.$from.' to '.$to;
		}
		if (is_dir($from))
		{
			if (@mkdir($to))
			{
				chmod($to,$chmod);
				$messages[]='Directory created: '.$to;
			}
			else
			$errors[]='cannot create directory '.$to;
			sw_copydirr($from,$to,$chmod,$verbose);
		}
	}
	closedir($handle);
	//*/
	//* Output
	if ($verbose)
	{
		foreach($errors as $err)
		echo '<strong>Error</strong>: '.$err.'<br />';
		foreach($messages as $msg)
		echo $msg.'<br />';
	}
	//*/
	return true;
}

function sw_deldir( $dir ) {
	$handle = opendir($dir);
  	     while (false!==($item = readdir($handle)))
  	     {
  	         if($item != '.' && $item != '..')
  	         {
  	             if(is_dir($dir.'/'.$item)) 
  	             {
  	                 sw_deldir($dir.'/'.$item);
  	             }else{
  	                 unlink($dir.'/'.$item);
  	             }
  	         }
  	     }
  	     closedir($handle);
  	     if(rmdir($dir))
  	     {
  	         $success = true;
  	     }
  	     return $success;
  	 }
?>
