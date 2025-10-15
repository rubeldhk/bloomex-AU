<?php
/**
* @version $Id: mod_login.php 3131 2006-04-16 16:22:09Z stingrey $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

// url of current page that user will be returned to after login
$url = mosGetParam( $_SERVER, 'REQUEST_URI', null );
// if return link does not contain https:// & http:// and to url
if ( strpos($url, 'http:') !== 0 && strpos($url, 'https:') !== 0 ) {
	// check to see if url has a starting slash
	if (strpos($url, '/') !== 0) {
		// adding starting slash to url
		$url = '/'. $url;
	}
	
	$url = mosGetParam( $_SERVER, 'HTTP_HOST', null ) . $url;

	// check if link is https://
	if ( isset( $_SERVER['HTTPS'] ) && ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) ) {
		$return = 'https://'. $url;
	} else {
	// normal http:// link
		$return = 'http://'. $url;
	}
} else {
	$return = $url;
}
// converts & to &amp; for xtml compliance
$return 				= str_replace( '&', '&amp;', $return );

$registration_enabled 	= $mainframe->getCfg( 'allowUserRegistration' );
$message_login 			= $params->def( 'login_message', 0 );
$message_logout 		= $params->def( 'logout_message', 0 );
$pretext 				= $params->get( 'pretext' );
$posttext 				= $params->get( 'posttext' );
$login 					= $params->def( 'login', $return );
$logout 				= $params->def( 'logout', $return );
$name 					= $params->def( 'name', 1 );
$greeting 				= $params->def( 'greeting', 1 );

if ( $my->id ) {
// Logout output
// ie HTML when already logged in and trying to logout
	if ( $name ) {
		$query = "SELECT name"
		. "\n FROM #__users"
		. "\n WHERE id = $my->id"
		;
		$database->setQuery( $query );
		$name = $database->loadResult();
	} else {
		$name = $my->username;
	}	
	?>
	<form action="<?php echo sefRelToAbs( 'index.php?option=logout' ); ?>" method="post" name="logout">	
	<?php
	if ( $greeting ) {
		echo _HI;
		echo $name;
	}
	  $db = new ps_DB;
        $q = "SELECT name FROM #__users WHERE id=".$my->id;
        $db->query($q);
        $db->next_record() 
	?>
	<br />
	<div align="center">
<!--MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM-->	
        <table id="login" cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
        <td><img src="modules/images/logouttop.gif" border="0" width="164" height="23" alt="" align="left"></td>
           	</tr>
	<tr>
        <td style="background-color: white;border-left: 5px solid rgb(213,188,229);border-right: 7px solid rgb(213,188,229);padding-left: 5px;padding-right: 7px;font-family: verdana, arial, sans serif;color: rgb(52,35,91);font-size: 10px;" align="right">
        <center><?php echo "HI,<strong>".$db->f("name")."</strong>"; ?></center><br>
        <?php switch ($mosConfig_lang) {	
         case 'french':  ?>
         <input type="image" src="modules/images/logoutgo_fr.gif" type="submit" name="Submit" value="<?php echo _BUTTON_LOGOUT; ?>" border="0" width="75" height="17" alt="">
         <?php break;
       	 case 'english': ?>	
         <input type="image" src="modules/images/logoutgo.gif" type="submit" name="Submit" value="<?php echo _BUTTON_LOGOUT; ?>" border="0" width="60" height="17" alt="">
         <?php break;
         default: ?>
         <input type="image" src="modules/images/logoutgo.gif" type="submit" name="Submit" value="<?php echo _BUTTON_LOGOUT; ?>" border="0" width="60" height="17" alt="">
         <?php break;
                     	 } ?>	
         </td>
	 </tr>    
	 <tr>
	 <td ><img src="modules/images/loginbottom.gif" border="0" width="164" height="4" alt="" align="left"></td>
	 </tr></table>
	</br>
<!--MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM-->	
	</div>

	<input type="hidden" name="option" value="logout" />
	<input type="hidden" name="op2" value="logout" />
	<input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>" />
	<input type="hidden" name="return" value="<?php echo sefRelToAbs( $logout ); ?>" />
	<input type="hidden" name="message" value="<?php echo $message_logout; ?>" />
	</form>
	<?php
} else {
// Login output
// ie HTML when not logged in and trying to login
	?>
	<form action="<?php echo sefRelToAbs( 'index.php' ); ?>" method="post" name="login" >
	<?php 	echo $pretext;	?>

<!--MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM	-->
        <table id="login" cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr><td colspan="2" >
        <?php 
         switch ($mosConfig_lang) {	
         case 'french':  ?>
         <img src="modules/images/logintop_fr.gif" border="0" width="164" height="23" alt=""></td>
         <?php break;
       	 case 'english': ?>	
         <img src="modules/images/logintop.gif" border="0" width="164" height="23" alt=""></td>
         <?php break;
         default: ?>
         <img src="modules/images/logintop.gif" border="0" width="164" height="23" alt=""></td>
         <?php break; 	 } ?>	
	</tr>
	<tr class="loginTR">
	 <td colspan="2" ><br>&nbsp;&nbsp;<?php echo _USERNAME; ?><br>
         <input type="text" id="mod_login_username" class="inputLogin" name="username" size="25" maxlength="40" alt="username"></td>			
        </tr>
	<tr class="loginTR">
	 <td colspan="2" >&nbsp;&nbsp;<?php echo _PASSWORD; ?><br>
         <input type="password" id="mod_login_password" class="inputLogin" name="passwd" size="25" alt="password" maxlength="256"></td>
	</tr>
	<tr class="loginTR">
	 <td colspan="2" align="right" style="padding-top: 5px;">
         <input type="hidden" name="option" value="login" />
         <input type="image" src="modules/images/logingo.gif" type="submit" name="Submit" value="<?php echo _BUTTON_LOGIN; ?>" border="0" width="18" height="18" alt="">
         </td>
	</tr>    
	<tr class="loginTR2">
	 <td style="vertical-align: top; width: 18px; padding-right: 0px; border-right: 0px;">
          <input type="checkbox" id="mod_login_remember" name="remember" value="yes" style="height: 18px; vertical-align: top" alt="<?php echo _REMEMBER_ME; ?>" checked></td>
	 <td style="padding-left: 0px; border-left: 0px;"><?php echo _REMEMBER_ME; ?></td>
	</tr>
	<tr class="loginTR2">
	 <td style="vertical-align: top; width: 18px; padding-right: 0px; border-right: 0px;">&nbsp;</td>
	 <td style="padding-left: 0px; border-left: 0px; padding-bottom: 5px;">
         <a href="<?php echo sefRelToAbs( 'index.php?option=com_registration&amp;task=lostPassword' ); ?>">
         <?php echo _LOST_PASSWORD; ?>
        </a></td>
	</tr>
	<tr>
	<td colspan="2" ><img src="modules/images/loginbottom.gif" border="0" width="164" height="4" alt=""></td>
	</tr>
	<tr>
	 <td colspan="2">
          <a href="<?php echo sefRelToAbs( 'index.php?option=com_registration&amp;task=register' ); ?>">
          <?php switch ($mosConfig_lang) {	
           case 'french':  ?>
           <img src="modules/images/signin_fr.gif" border="0" width="164" height="22" alt="">
           <?php break;
           case 'english': ?>	
           <img src="modules/images/signin.gif" border="0" width="164" height="22" alt="">
           <?php break;
              default: ?>
           <img src="modules/images/signin.gif" border="0" width="164" height="22" alt="">
              <?php break;
           } ?>	
            </a></td>
	</tr>
        </table>
<!--MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM-->

	<?php
	echo $posttext;
	?>

	<input type="hidden" name="option" value="login" />
	<input type="hidden" name="op2" value="login" />
	<input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>" />
	<input type="hidden" name="return" value="<?php echo sefRelToAbs( $login ); ?>" />
	<input type="hidden" name="message" value="<?php echo $message_login; ?>" />
	</form>
	<?php
}
?>