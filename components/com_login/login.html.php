<?php 
/**
* @version $Id: login.html.php 4055 2006-06-19 20:00:59Z stingrey $
* @package Joomla
* @subpackage Users
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

/**
* @package Joomla
* @subpackage Users
*/
class loginHTML {

	function loginpage ( &$params, $image ) {
            global $mosConfig_lang, $mosConfig_live_site;

            // used for spoof hardening
            $validate = josSpoofValue(1);

            $direction	= mosGetParam($_REQUEST, "direction", "");

            if( !empty($direction) ) {
                    $return = $mosConfig_live_site . "/index.php?page=shop.browse&category_id=171&option=com_virtuemart&Itemid=124";
            }else{
                    $return = $params->get('login');
            }

            ?>
            <div class="container white">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <form class="form-inline" role="form" action="/index.php" method="post" name="login" id="login">
                            <div class="form-group">
                                <label class="sr-only" for="username">Email</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="passwd">Password</label>
                                <input type="password" class="form-control" name="passwd" id="passwd" placeholder="Password">
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember" class="inputbox" value="yes" /> <?php echo _REMEMBER_ME; ?>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-default"><?php echo _BUTTON_LOGIN; ?></button>
                            <input type="hidden" name="op2" value="login" />
                            <input type="hidden" name="return" value="<?php echo sefRelToAbs( $return ); ?>" />
                            <input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>" />
                            <input type="hidden" name="message" value="<?php echo $params->get( 'login_message' ); ?>" />
                            <input type="hidden" name="<?php echo $validate; ?>" value="1" />
                        </form>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <br/>
                        <a href="/lost-password/">
                            <?php echo _LOST_PASSWORD; ?>
                        </a>
                        <?php
                        /*
                        if ($params->get('registration')) {
                            ?>
                            <br/>
                            <?php echo _NO_ACCOUNT; ?>
                            <a href="/registration/">
                                <?php echo _CREATE_ACCOUNT;?>
                            </a>
                            <?php
                        }*/
                        ?>  
                    </div>
                </div>
            </div>
            <?php
  	}

	function logoutpage( &$params, $image ) {
		global $mosConfig_lang;

		$return = $params->get('logout');
                

		?>
		<form style="color: rgb(102, 51, 102);" action="/logout/" method="post" name="login" id="login">
                <div class="container white">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"></div>

                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <?php
                            if ( $params->get( 'page_title' ) ) {
                                ?>
                                <div class="componentheading<?php echo $params->get( 'pageclass_sfx' ); ?>">
                                    <h3 class="new_checkout_header">
                                        <?php echo $params->get( 'header_logout' ); ?>
                                    </h3></div>
                                <?php
                            }
                            ?>
                            <div>
                                <?php

                                if ( $params->get( 'description_logout' ) ) {
                                    echo $params->get( 'description_logout_text' );
                                    ?>
                                    <br/><br/>
                                    <?php
                                }
                                ?>
                            </div>
                            <div align="center">
                                <input type="submit" class="new_checkout_login_button btn btn-success" name="Submit" value="<?php echo _BUTTON_LOGOUT; ?>" />
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"></div>
                    </div>
                </div>
		<?php
		// displays back button
		mosHTML::BackButton ( $params );
		?>

		<input type="hidden" name="op2" value="logout" />
		<input type="hidden" name="return" value="<?php echo sefRelToAbs( $return ); ?>" />
		<input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>" />
		<input type="hidden" name="message" value="<?php echo $params->get( 'logout_message' ); ?>" />
		</form>
		<?php
	}
}
?>