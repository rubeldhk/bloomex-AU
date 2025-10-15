
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
class HTML_CompanyGroupPages {

    //============================================= Location OPTION ===============================================
    static function showCompanyGroupPages(&$rows, &$pageNav, $option, $lists) {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Company Group Manager</th>
                </tr>
                <tr>
                    <td align="right" style="padding:0px 20px 10px 0px;">
                        <b>Filter By:&nbsp;</b>
                        <input type="text" value="<?php echo $lists['filter_key']; ?>" name="filter_key" size="30" />
                        <?php echo $lists['filter_groups'] ?>
                    </td>

                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th width="20">#</th>
                    <th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
                    <th width="40%" nowrap="nowrap" align="left">Company Name</th>
                    <th width="30%" nowrap="nowrap" align="left">Company Domain</th>
                    <th width="20%" nowrap="nowrap" align="left">Company Group Name</th>
                    <th width="5%" align="left">&nbsp;</th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);
                    $link = 'index2.php?option=com_company_groups&task=editA&hidemainmenu=1&id=' . $row->id;
                    $checked = mosHTML::idBox($i, $row->id);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td align="left"><a href="<?php echo $link; ?>" title="Edit Company"><b style="font:bold 11px Tahoma;"><?php echo $row->company_name; ?></b></a></td>
                        <td align="left"><?php echo $row->company_domain; ?></td>
                        <td align="left"><?php echo $row->company_croup_name; ?></td>

                        <td>&nbsp;</td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script>
            $(function() {
                $('.upload_xlsx').click(function(){
                    $( "#dialog" ).dialog();
                    $( "#fileToUploadform" ).show();
                    $(".ui-dialog").css("width", 800);
                })
                $('.parse_file').click(function(){
                    $('.parse_file').val('Processing...').attr('disabled','disabled');
                    var file_data = $('#xlsxfileform').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file', file_data);
                    form_data.append('option', "<?php echo $option; ?>");
                    form_data.append('task', "parse_xlsx");
                    $.ajax({
                        url: './index2.php',
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        async: true,
                        success: function(data){
                            {
                                if(data){
                                    if(data[0]=='success'){
                                        var step=parseFloat((100*20/data[1]).toFixed(1))

                                        $('.w3-progress-container').show()
                                        add_domains(step,data[1]);
                                        $(".parsing_error").html('');
                                        $("#myBarprocess_count").html(data[1]);
                                    }else{
                                        $(".parsing_error").html(data[0]);
                                        $('.parse_file').val('Upload').removeAttr('disabled');
                                    }

                                }
                            }
                        }
                    });

                })
                var pos_progress=0;
                function add_domains(step,count){
                    $.post("./index2.php", {option: "<?php echo $option;?>",task: "add_domains"}, function(result){
                        if(result){
                            var  obj = JSON.parse(result);
                            $(".parsing_result").append(obj.result);
                            if($.trim(obj.result)!='Process Finished'){
                                $("#myBarprocess").show();
                                $("#myBarprocess_text").html(obj.header_text);
                                obj.new+=parseInt($("#myBarprocess_new").text());
                                obj.exist+=parseInt($("#myBarprocess_exist").text());
                                $("#myBarprocess_new").html(obj.new);
                                $("#myBarprocess_exist").html(obj.exist);
                                move_process(pos_progress,parseFloat((pos_progress+step).toFixed(1)))
                                pos_progress += step;
                                pos_progress=parseFloat(pos_progress.toFixed(1))
                                var p = parseFloat((pos_progress/step*20).toFixed(1));
                                if(p>count){
                                    $("#myBarprocess_cur").html(count);
                                }else{
                                    $("#myBarprocess_cur").html(p);
                                }
                                add_domains(step,count)
                            }else{
                                $("#myBarprocess_text").html(obj.result);
                                pos_progress=0
                                $('.parse_file').val('Upload').removeAttr('disabled');
                            }
                        }
                    });


                }
                function move_process(width,width_max) {
                    var elem = document.getElementById("myBar");
                    var id = setInterval(frame, 20);
                    function frame() {
                        if ((parseFloat(width.toFixed(1)) >= 100) || (parseFloat(width.toFixed(1))==parseFloat(width_max.toFixed(1)))) {
                            clearInterval(id);
                        } else {
                            width=parseFloat(width.toFixed(1))+0.1;
                            elem.style.width = parseFloat(width) + '%';
                        }
                    }
                }

            })
        </script>

        <div id="dialog" title="Parse Xlsx File">
            <form style="display: none;" id="fileToUploadform">
                <input type="file" name="fileToUpload" id="xlsxfileform">
                <input type="button" class="parse_file" value="Upload" name="submit">
                <a target="_blank" href="https://media.bloomex.ca/bloomex.com.au/company_groups_correct_format.xlsx" download>
                    <button type="button">Download Correct Format</button>
                </a>
            </form><br>
            <div id="myBarprocess">
                <div style="float: left">
                    <span id="myBarprocess_text"></span>
                </div>
                <div style="float: right">
                    <span id="myBarprocess_cur">0</span> From <span id="myBarprocess_count">N/A</span>
                    <br><span><span id="myBarprocess_new">0</span> New<br><span id="myBarprocess_exist">0</span> Exist</span>
                </div>
            </div><br>
            <div class="w3-progress-container">
                <div id="myBar" class="w3-progressbar w3-green" style="width:0%"></div>
            </div>
            <div class="parsing_result"></div>
            <div style="color:red" class="parsing_error"></div>
        </div>

        <?php
    }

    function editCompanyGroupPages(&$row, $option, &$lists) {
        global $mosConfig_live_site;
        ?>
        <script language="javascript" type="text/javascript">

            function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform(pressbutton);
                    return;
                }

                // do field validation
                if (form.company_name.value == "") {
                    alert("Please enter Name!");
                    return;
                }

                if (form.company_domain.value == "") {
                    alert("Please enter company domain!");
                    return;
                }

                submitform(pressbutton);
            }

        </script>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Company Shopper  Group :
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New'; ?>
                        </small>
                    </th>
                </tr>
            </table>

            <table style="width: 50%;float: left;" class="adminform">
                <tr>
                    <th colspan="2">Company  Detail</th>
                <tr>
                <tr>
                    <td width="15%"><b>Company Name:</b></td>
                    <td><input class="inputbox" type="text" name="company_name" size="40" maxlength="255" value="<?php echo $row->company_name; ?>" /></td>
                </tr>
                <tr>
                    <td><b>Company Domain:</b></td>
                    <td><input class="inputbox" type="text" name="company_domain" size="40" maxlength="255" value="<?php echo $row->company_domain; ?>" /></td>
                </tr>
                <tr>
                    <td><b>Company Invoice:</b></td>
                    <td><input class="inputbox" type="checkbox" name="company_invoice" <?php echo  ($row->company_invoice)?'checked':''; ?> /></td>
                </tr>
                <tr>
                    <td><b>Shopper Groups:</b></td>
                    <td><?php echo $lists['groups']; ?></td>
                </tr>


            </table>




            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="" />
            <input type="hidden" id="id" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
        </form>
        <?php
    }


}
?>