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

class TOOLBAR_brightreporter
{
    function _reports() {
        global $my;
        mosMenuBar::startTable();
        if($my->gid){
            if($my->usertype=='Super Administrator') {
                ?>
                <img src="/administrator/report_show.png" style="display: none;">
                <img src="/administrator/report_hide.png" style="display: none;">
                <?php
//                mosMenuBar::customicon('report_showhide', 'report_hide.png', 'report_hide.png', 'Unpublished', false, false, false);
//                mosMenuBar::spacer();
                mosMenuBar::addNew();
                mosMenuBar::spacer();
                mosMenuBar::editListX();
//                mosMenuBar::spacer();
//                mosMenuBar::publishList();
//                mosMenuBar::spacer();
//                mosMenuBar::unpublishList();
                mosMenuBar::spacer();
                mosMenuBar::deleteList();
                mosMenuBar::spacer();

                ?>
                <script type="text/javascript">
                    jQuery(function() {

                        jQuery('.report_showhide').click(function(e){
                            e.preventDefault();

                            jQuery(this).toggleClass('show');
                            jQuery('.hide_report').toggle();

                            if (jQuery(this).hasClass('show')) {
                                jQuery(this).find('img').attr('src', '/administrator/images/report_hide.png');
                            }
                            else {
                                jQuery(this).find('img').attr('src', '/administrator/images/report_show.png');
                            }

                            return true;
                        });

                    });
                </script>
                <style>
                    a.report_showhide {
                        font-size: 10px;
                    }
                </style>
                <?php
            }
        }
        mosMenuBar::help( 'help.brightreporter.html', true );
        mosMenuBar::endTable();
    }

    function _showreport() {
        mosMenuBar::startTable();
        mosMenuBar::back();
        mosMenuBar::spacer();
        mosMenuBar::cancel('showlist');
        mosMenuBar::endTable();
    }

    function _prepreport() {
        mosMenuBar::startTable();
        mosMenuBar::spacer();
        mosMenuBar::cancel('showlist');
        mosMenuBar::endTable();
    }

    function _about() {
        mosMenuBar::startTable();
        mosMenuBar::cancel('showlist');
        mosMenuBar::spacer();
        mosMenuBar::help( 'help.brightreporter.html', true );
        mosMenuBar::endTable();
    }

    function _editreport() {
        mosMenuBar::startTable();
        mosMenuBar::save('saverun', 'Save & Run' );
        mosMenuBar::spacer();
        mosMenuBar::save('save');
        mosMenuBar::spacer();
        mosMenuBar::apply('apply');
        mosMenuBar::spacer();
        mosMenuBar::cancel('showlist');
        mosMenuBar::spacer();
        mosMenuBar::help( 'help.brightreporter.html', true );
        mosMenuBar::endTable();
    }

    function _showexcel() {
        mosMenuBar::startTable();
        mosMenuBar::back();
        mosMenuBar::spacer();
        mosMenuBar::cancel('showlist');
        mosMenuBar::endTable();
    }

    function _CONFIG_MENU() {
        mosMenuBar::startTable();
        mosMenuBar::save( 'savesettings', 'Save' );
        mosMenuBar::spacer();
        mosMenuBar::back();
        mosMenuBar::spacer();
        mosMenuBar::help( 'help.brightreporter.html', true );
        mosMenuBar::endTable();
    }

    function defaultButtons()
    {
        mosMenuBar::startTable();
        mosMenuBar::addNew();
        mosMenuBar::spacer();
        mosMenuBar::deleteList();
        mosMenuBar::spacer();
        mosMenuBar::help( 'help.brightreporter.html', true );
        mosMenuBar::endTable();
    }
}
?>