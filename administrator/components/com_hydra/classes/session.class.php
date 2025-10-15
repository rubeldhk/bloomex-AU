<?php
/**
* $Id: session.class.php 18 2007-04-15 16:20:00Z eaxs $
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


class HydraSession
{
	// the joomla user id
	var $jid;
	
	// the hydra user id
	var $hid;
	
	// the user profile
	var $profile;
	
	
	
	/**
	* @author  Tobias Kuhn ( Wed Dec 13 23:15:47 CET 2006 )
	* @name    HydraSession
	* @version 1.1 
	* @param   $my
	* @return  void
	* @desc    constructor
	**/
	function HydraSession($my, &$database)
	{
		global $hydra_debug, $mainframe, $mosConfig_live_site, $option;
		
		$hydra_debug->logNotice('Including File: ['.__FILE__.']');

        // fixed session in frontend  
        if($my->id AND(!strstr($_SERVER['PHP_SELF'], 'administrator')) ) {
            session_name( md5($mosConfig_live_site) );
            session_start([
                'cookie_path' => '/',
                'cookie_lifetime' => 0,
                'cookie_secure' => true,
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict',
            ] );
            $mainframe = new mosMainFrame( $database, $option, '.' );
            $mainframe->initSession();
        
            if( !isset( $_SESSION['session_userstate'] ) ) {
                $_SESSION['session_userstate'] = array();
            }
        }
        
        
		$this->jid = $my->id;
		$this->hid = $this->getHydraId($database);
		$this->profile = $this->getProfile($this->hid, $database);

		$_SESSION['hydra_profile'] = $this->profile;
		
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Wed Dec 13 23:17:41 CET 2006 )
	* @name    getHydraId
	* @version 1.1 
	* @param   $database
	* @return  int $id
	* @desc    gets the hydra-id from a user
	**/
	function getHydraId($database)
	{
		global $hydra_debug;
		
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Retrieving Hydra userID...');
		
		$query = "SELECT id FROM #__hydra_users WHERE jid = '$this->jid'";
		       $database->setQuery($query);
		       $id = intval($database->loadResult());

		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - ... ID = "'.$id.'"'); 
		        
		return $id;       
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 14:11:09 CEST 2006 )
	* @name    getProfile
	* @version 1.0 
	* @param   int $id
	* @param   $database
	* @return  array $profile_array
	* @desc    returns the user-profile
	**/
	function getProfile($id, $database)
	{
		global $hydra_debug;
		
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Loading profile');
		
		$profile_array = array();
		
		
		// is the profile already set?
		if (!empty($_SESSION['hydra_profile'])) {
			
		  $profile_array = $_SESSION['hydra_profile'];
		  return $profile_array;
		}	
		
		// reload the profile if the session is empty		 
		$query = "SELECT parameter, value FROM #__hydra_profile"
		       . "\n WHERE user_id = '$id'";
		       $database->setQuery($query);
		       $profiles = $database->loadObjectList();

		         
		for ($i = 0, $n = count($profiles); $i < $n; $i++)
		{
			$profile = $profiles[$i];
			
			$profile_array[$profile->parameter] = $profile->value;
		}
		
		return $profile_array;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Tue Sep 26 14:12:00 CEST 2006 )
	* @name    setProfile
	* @version 1.0 
	* @param   string $parameter
	* @param   string $value
	* @return  void
	* @desc    updates the user-profile
	**/
	function setProfile($parameter, $value)
	{
		global $database, $hydra_debug;

		if (!isset($this->profile[$parameter])) {	
			
			$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Adding parameter to profile:'.htmlentities($parameter).'->'.htmlentities($value));
					
			$query = "INSERT INTO #__hydra_profile VALUES ('', '$this->hid', '$parameter', '$value')";
			       $database->setQuery($query);
			       $database->query();
			       
		}
		else {
			
			$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Updating profile:'.htmlentities($parameter).'->'.htmlentities($value));
			
			$query = "UPDATE #__hydra_profile SET value = '$value' WHERE user_id = '$this->hid' AND parameter = '$parameter'";
			       $database->setQuery($query);
			       $database->query();	      
		}
		
		$this->profile[$parameter] = $value;
		$_SESSION['hydra_profile'][$parameter] = $value;
	}
	
	
	
	/**
	* @author  Tobias Kuhn ( Mon Dec 25 13:42:34 CET 2006 )
	* @name    profile
	* @version 1.2 
	* @param   string $parameter
	* @param   string $alternative
	* @param   bool $set
	* @return  unknown
	* @desc    checks the user-profile for a specific param, if it's not set $alternative or false is returned
	**/
	function profile($parameter, $alternative = false, $set = false)
	{		
		global $hydra_debug;
		
		$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Requesting profile parameter :"'.htmlentities($parameter).'"');
		
		if (@!empty($this->profile[$parameter]) OR (@$this->profile[$parameter] === 0)) {
			
			$hydra_debug->logNotice(__CLASS__.'::'.__FUNCTION__.' - Parameter value is: "'.htmlentities($this->profile[$parameter]).'"');
			
			return $this->profile[$parameter];
		}
		else {
			
			$hydra_debug->logWarning(__CLASS__.'::'.__FUNCTION__.' - Parameter "'.htmlentities($parameter).'" does not exist!');
			
			if ($alternative) {
				
				$hydra_debug->logWarning(__CLASS__.'::'.__FUNCTION__.' - Returning alternate value "'.htmlentities($alternative).'"');
				
				if ($set) { 
					
					$this->setProfile($parameter, $alternative); 
					
				}
				return $alternative;
			}
			else {
				return false;
			}
			
		}
	}
}
?>