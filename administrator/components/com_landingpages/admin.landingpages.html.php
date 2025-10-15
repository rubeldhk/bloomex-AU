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
class HTML_LandingPages {

    //============================================= Location OPTION ===============================================
    function showLandingPages(&$rows, &$pageNav, $option, $lists) {
        global $mosConfig_live_site;
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Location Manager</th>
                </tr>
                <tr>
                    <td align="right" style="padding:0px 20px 10px 0px;">
                        <a href="#" id="update-links-button" style="float:left;margin-left:50px;color:#0C00CA;font-size:14px;">Update Links Database

                        </a>
                        <a href="#" id="update-geolocation-button" style="float:left;margin-left:50px;color:#0C00CA;font-size:14px;">Update Geolocation

                        </a>

                        <span style="color:gray;font-size:10px;text-decoration:none;float:left;margin-left:10px;font-size:14px" id="update-links-loader"></span>
                        <b>Filter By:&nbsp;</b>
                        <input type="text" value="<?php echo $lists['filter_key']; ?>" name="filter_key" size="30" />
                        <b>Has Not Default Values:&nbsp;</b>
                        <input type="checkbox" <?php echo $lists['not_default'];?> name="not_default" size="30" />

                    </td>
                    <script src="<?php echo $mosConfig_live_site ?>/templates/bloomex7/js/jquery-2.2.3.min.js"></script>
                    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                    <script type="text/javascript">
                        jQuery("#update-links-button").click(function() {
                            jQuery("#update-links-loader").html('loading, please wait');
                            jQuery.post("/updateseolinks.php?manager=landing", function(data) {
                                if (data == 'success manager landing') {
                                    jQuery("#update-links-loader").html('sucessfully updated');

                                } else {
                                    jQuery("#update-links-loader").html('Error');

                                }
                            })
                        });

                        jQuery("#update-geolocation-button").click(function() {
                            jQuery("#update-links-loader").html('loading, please wait');
                            jQuery.post("/updateseolinks.php?manager=landing_geo", function(data) {
                                if (data == 'success manager landing_geo') {
                                    jQuery("#update-links-loader").html('sucessfully updated');
                                } else {
                                    jQuery("#update-links-loader").html('Error');

                                }
                            })
                        });

                        $(function() {


                            $('.upload_list').click(function(){
                                $( "#dialog" ).dialog({
                                    width: 1200,
                                    left:50,
                                    close: function()
                                    {
                                        $("#dialog").hide();
                                    }
                                });
                                $( "#fileToUploadform" ).show();
                            })
                            $('.download_current_list').on('click',function(){
                                $('#fileToUploadform').hide();
                                $('.assignorder_loader').show();
                                $.ajax({
                                    data:
                                        {
                                            option: 'com_landingpages',
                                            task: 'get_current_list',
                                            startFrom: $('#startFrom').val(),
                                            rowsCount: $('#rowsCount').val()
                                        },
                                    url: "index2.php",
                                    async: true,
                                    cache: false,
                                    method: 'GET',
                                    xhrFields: {
                                        responseType: 'blob'
                                    },
                                    success: function (data) {
                                        $('#fileToUploadform').show();
                                        $('.assignorder_loader').hide();
                                        var a = document.createElement('a');
                                        var url = window.URL.createObjectURL(data);

                                        a.href = url;
                                        a.download = 'landings_list.xlsx';
                                        a.click();
                                        window.URL.revokeObjectURL(url);
                                    },
                                    error:function(request, error) {
                                        console.log(error,request)
                                        $('#fileToUploadform').show();
                                        $('.assignorder_loader').hide();
                                    }
                                });

                            })
                            $('.parse_file').click(function () {
                                $('.parse_file').attr('disabled',true);
                                $('.parsing_result').html('Please Wait...');
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
                                    success: function (data) {
                                        {
                                            if (data) {
                                                if (data[0] != 'invalid file') {
                                                    $('.parse_file').val('Upload').removeAttr('disabled');
                                                    $('.parsing_result').html(data[0]);
                                                } else {
                                                    $('.parsing_result').html(data[0] + " Please change file");
                                                    $('.parse_file').val('Upload').removeAttr('disabled');
                                                }
                                            } else {
                                                $('.parsing_result').html("Please change file");
                                                $('.parse_file').val('Upload').removeAttr('disabled');
                                            }
                                        }
                                    }
                                });

                            })
                        })
                    </script>
                </tr>
            </table>
            <table  class="adminlist">
                <tr>
                    <th width="20">#</th>
                    <th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
                    <th class="title">City</th>
                    <th width="20%" nowrap="nowrap" align="left">Province</th>
                    <th width="20%" nowrap="nowrap" align="left">Url</th>
                    <th width="20%" nowrap="nowrap" align="left">Telephone</th>
                    <th width="10%" nowrap="nowrap" align="center">Enable Location?</th>
                    <th width="10%" nowrap="nowrap" align="center">Not Default Values</th>

                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);
                    $link = 'index2.php?option=com_landingpages&task=editA&hidemainmenu=1&id=' . $row->id;
                    $checked = mosHTML::idBox($i, $row->id);

                    $not_default_values='';
                    if($row->category!=''){
                        $not_default_values.='Category<br>';
                    }
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td><a href="<?php echo $link; ?>" title="Edit Location"><b style="font:bold 11px Tahoma;"><?php echo $row->city; ?></b></a></td>
                        <td><?php echo $row->province; ?></td>
                        <td><?php echo $row->url; ?></td>
                        <td><?php echo $row->telephone; ?></td>
                        <td><?php
                            if ($row->enable_location > 0)
                                echo "<b>Yes</b>";
                            else
                                echo "No";
                            ?></td>

                        <td><?php echo ($not_default_values!='')?'<span class="tooltip">Not Default Values<span class="tooltiptext">'.$not_default_values.'</span></span>':''; ?></td>

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
        <style>
            .adminlist,.adminlist th{
                text-align: left !important;
            }
            .tooltip {
                position: relative;
                display: inline-block;
                border-bottom: 1px dotted black;
            }

            .tooltip .tooltiptext {
                visibility: hidden;
                width: 180px;
                background-color: black;
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 5px 0;
                right: 0;
                top: 20px;
                position: absolute;
                z-index: 1;
            }

            .tooltip:hover .tooltiptext {
                visibility: visible;
            }
        </style>
        <div id="dialog" title="Parse Xlsx File">
            <p>Upload file to update or add new (if ID is empty) landings</p>
            <p>URL is required and has to be unique</p>
            <p>Use start from and rows count variables to download landings list</p>
            <form style="display: none;" id="fileToUploadform">
                <input type="file" name="fileToUpload" id="xlsxfileform">
                <input type="button" class="parse_file" value="Upload" name="submit">
                <input type="number" id="startFrom" placeholder="Start From">
                <input type="number" id="rowsCount" placeholder="Rows count">
                <input type="button" class="download_current_list" value="Download Corrent List">
                <input style="display: none;" type="button" class="save_parsed_file" value="Save" name="save_parsed_file">
            </form><br>
            <div class="assignorder_loader" style="display: none;"></div>
            <div class="parsing_result"></div>
            <div style="color:red" class="parsing_error"></div>
        </div>
        <?php
    }


    function editLandingPages(&$row, $option, &$lists, $categories_list) {

        ?>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Location Manager:
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New'; ?>
                        </small>
                    </th>
                </tr>
            </table>
            <div class="wrapper">
                <div>
                    <table class="adminform">
                        <tr>
                            <th colspan="2">Location Detail</th>
                        <tr>
                        <tr>
                            <td width="15%"><b>City:</b></td>
                            <td><input class="inputbox" type="text" name="city" size="40" maxlength="255" value="<?php echo $row->city; ?>" /></td>
                        </tr>
                        <tr>
                            <td><b>Url:</b></td>
                            <td><input class="inputbox" type="text" name="url" size="40" maxlength="255" value="<?php echo $row->url; ?>" /></td>
                        </tr>
                        <tr>
                            <td><b>Province:</b></td>
                            <td><?php echo $lists['province']; ?></td>
                        </tr>
                        <tr>
                            <td><b>Telephone:</b></td>
                            <td><input class="inputbox" type="text" name="telephone" size="40" maxlength="255" value="<?php echo $row->telephone; ?>" /></td>
                        </tr>
                        <tr>
                            <td><b>Enable Location:</b></td>
                            <td><?php echo $lists['enable_location']; ?></td>
                        </tr>
                        <tr>
                            <td><b>Location Address:</b></td>
                            <td><input class="inputbox" type="text" name="location_address" size="40" maxlength="255" value="<?php echo $row->location_address; ?>" /></td>
                        </tr>
                        <tr>
                            <td><b>Location Postcode:</b></td>
                            <td><input class="inputbox" type="text" name="location_postcode" size="40" maxlength="255" value="<?php echo $row->location_postcode; ?>" /></td>
                        </tr>
                        <tr>
                            <td><b>Location Telephone:</b></td>
                            <td><input class="inputbox" type="text" name="location_telephone" size="40" maxlength="255" value="<?php echo $row->location_telephone; ?>" /></td>
                        </tr>
                    </table>
                    <?php
                    $nearby_cities='';
                    $hide_nearby_cities='';
                    $cities = array_map('trim', explode(',', $row->nearby_cities));
                    foreach($cities as $c){
                        if($c!='' && strlen($c)>5 && substr($c,0,5)=='hide_'){
                            $hide_nearby_cities.=substr($c,5).',';
                        }else{
                            $nearby_cities.=$c.',';
                        }
                    }
                    $nearby_cities = rtrim($nearby_cities,',');
                    $hide_nearby_cities = rtrim($hide_nearby_cities,',');
                    ?>
                    <table class="adminform" style="margin-top: 20px;">
                        <tr>
                            <th colspan="2">Nearby cities</th>
                        <tr>
                        <tr>
                            <td width="15%"><b>Cities:</b></td>
                            <td>
                                <textarea name="nearby_cities[]" rows="10" cols="60"><?php echo (isset($nearby_cities) ? $nearby_cities : ''); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td width="15%"><b>Hidden Cities:</b></td>
                            <td>
                                <textarea name="nearby_cities[]" rows="10" cols="60"><?php echo (isset($hide_nearby_cities) ? $hide_nearby_cities : ''); ?></textarea>
                            </td>
                        </tr>
                    </table>

                </div>
                <div>
                    <table class="adminform">
                        <tr>
                            <th colspan="3">Landing category</th>
                        <tr>
                        <tr>
                            <td width="30%">
                                Categories
                            </td>
                            <td>
                                <?php echo $categories_list; ?>
                            </td>
                            <td width="20%">
                                <input type="button" id="clearCategories" value="Clear Selected values">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
        </form>

        <script type="text/javascript" language="javascript">
            

            
            jQuery(document).ready(function(){
            
                jQuery('#clearCategories').click(function(e) {
                    e.preventDefault();
                    
                    jQuery('select[name="categories[]"]').find('option:selected').attr('selected', false);
                    jQuery('select[name="categories[]"]').change();
                });
                
                
            });

        </script>
        <style>
            .category{
                width: 100%;
                height: 150px;
            }
            .default_category{
                display: none;
            }
            
            .wrapper {
                display: flex;
            }
            .wrapper > div {
                flex: 50%;
            }
            
            .wrapper > div:nth-child(odd) {
                padding-right: 5px;
            }
            .wrapper > div:nth-child(even) {
                padding-left: 5px;
            }

        </style>
        <?php
    }


}
?>
