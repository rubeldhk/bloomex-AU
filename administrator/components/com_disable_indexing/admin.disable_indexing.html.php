<?php
/**
 * @version $Id: admin.Category.html.php 10002 2008-02-08 10:56:57Z willebil $
 * @package Joomla
 * @subpackage Category
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_VALID_MOS') or die('Restricted access');

/**
 * @package Joomla
 * @subpackage Category
 */
class HTML_DisableIndexing
{

    //============================================= POSTAL CODE OPTION ===============================================
    function showDisableIndexing(&$rows, &$pageNav, $option)
    {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Disabled For Indexing List Manager</th>
                </tr>
            </table>
            <table width="100%">
                <tr>
                    <td align="right">

                        <b>Url :</b>&nbsp;<input name="url" type="text" size="30" maxlength="30"
                                                 value="<?php echo isset($_REQUEST['url']) ? htmlspecialchars($_REQUEST['url']) : ''; ?>">&nbsp;&nbsp;
                        <input class="button" type="submit" name="search" value="Search">
                    </td>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th class="title">Url</th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);

                    ?>
                    <tr class="<?php echo "row$k"; ?>">

                        <td align="left"><?php echo $row->url; ?></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <div id="disableindexing_xlsx" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Parse CSV File</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-inline disabled_list_form" role="form" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="file" name="xlsxfileform" id="xlsxfileform">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success parse_file" name="parse_file">Upload
                                </button>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary get_file" name="get_file">Download Current
                                    List
                                </button>
                            </div>
                        </form>
                        <div class="disabled_list_loader"></div>
                        <div class="disabled_list_result"></div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function tryAgain() {
                //e.preventDefault();

                $('.disabled_list_result').html('').hide();
                $('.disabled_list_form').trigger('reset').show();
            }

            $(document).ready(function () {

                $('.get_file').click(function (e) {
                    e.preventDefault();

                    $('.disabled_list_form').hide();
                    $('.disabled_list_loader').show();

                    $.ajax({
                        url: './index2.php',
                        data: {
                            'option': '<?php echo $option; ?>',
                            'task': 'getCsv'
                        },
                        async: true,
                        cache: false,
                        method: 'GET',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function (data) {
                            $('.disabled_list_loader').hide();
                            tryAgain();
                            var a = document.createElement('a');
                            var url = window.URL.createObjectURL(data);
                            a.href = url;
                            a.download = 'list.csv';
                            a.click();
                            window.URL.revokeObjectURL(url);
                        },
                        error: function () {
                            console.log('Ajax error.');
                            $('.disabled_list_result').html('Error: Ajax error. <button type="button" class="btn btn-default disabled_list_try_again" onclick="tryAgain();">Try Again</button>').show();
                            $('.disabled_list_loader').hide();
                        }
                    });
                });

                $('.parse_file').click(function (e) {
                    e.preventDefault();

                    $('.disabled_list_form').hide();
                    $('.disabled_list_loader').show();

                    var file_data = $('#xlsxfileform').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file', file_data);
                    form_data.append('option', '<?php echo $option; ?>');
                    form_data.append('task', 'parseCsv');

                    $.ajax({
                        url: './index2.php',
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        async: true,
                        success: function (json) {
                            if (json.result) {
                                $('.disabled_list_result').text('Success: Added ' + json.sizeof_inserts + ' new rules! Page will be reload.').show();
                                document.location.reload(true);
                            } else {
                                $('.disabled_list_result').html('Error: ' + json.error + '. <button type="button" class="btn btn-default disabled_list_try_again" onclick="tryAgain();">Try Again</button>').show();
                            }
                            $('.disabled_list_loader').hide();
                        },
                        error: function () {
                            console.log('Ajax error.');
                            $('.disabled_list_result').html('Error: Ajax error. <button type="button" class="btn btn-default disabled_list_try_again" onclick="tryAgain();">Try Again</button>').show();
                            $('.disabled_list_loader').hide();
                        }
                    });
                });

            });
        </script>
        <?php
    }


}

?>
