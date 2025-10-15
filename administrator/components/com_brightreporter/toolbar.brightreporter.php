<?php
/****************************************************************************************************
 * Package : Brightcode Reporter
 * Author : Theo van der Sluijs
 * Llink : http://www.brightcode.eu
 * Copyright (C) : 2007 Brightcode.eu
 * Email : info@brightcode.eu
 * Date : October 2007
 * Package Code License :  Commercial License / http://www.brightcode.eu
 * Joomla! API Code License : http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * JavaScript Code & CSS : Commercial License / http://www.brightcode.eu
 ****************************************************************************************************
 * Copyrights (c) 2007
 * All rights reserved. Brightcode.eu
 *
 * This program is Commercial software.
 * Unauthorized reproduction is not allowed.
 * Read the complete license model on our site before using this product
 * http://www.brightcode.eu
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.
 *
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *****************************************************************************************************/

// ensure this file is being included by a parent file
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') || $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_dailymessage'))) {
    mosRedirect('index2.php', _NOT_AUTH);
}

// include support libraries
require_once( $mainframe->getPath( 'toolbar_html' ) );

switch($act)
{
    case 'reports':
        switch ($task) {
            case 'new':
                TOOLBAR_brightreporter::_editreport();
                break;
            case 'edit':
                TOOLBAR_brightreporter::_editreport();
                break;
            case 'editfield':
                TOOLBAR_brightreporter::_editreport();
                break;
            case 'PrepReport':
                TOOLBAR_brightreporter::_prepreport();
                break;
            case 'apply':
                TOOLBAR_brightreporter::_editreport();
                break;
            case 'deletefield':
                TOOLBAR_brightreporter::_editreport();
                break;
            case 'save':
                TOOLBAR_brightreporter::_reports();
                break;
            case 'showreport':
                TOOLBAR_brightreporter::_showreport();
                break;
            case 'showexcell':
                TOOLBAR_brightreporter::_showexcel();
                break;
            default:
                TOOLBAR_brightreporter::_reports();
                break;
        }
        break;

    case 'config':
        switch ($task) {
            case "savesettings":
                TOOLBAR_brightreporter::_CONFIG_MENU();
                break;
            default:
                TOOLBAR_brightreporter::_CONFIG_MENU();
                break;
        }
        break;

    case 'about':
        TOOLBAR_brightreporter::_about();
        break;

    default:
        TOOLBAR_brightreporter::_reports();
        break;

}
?>
