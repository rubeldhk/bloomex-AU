<?php

ini_set('max_file_uploads', '30');
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
die('Direct Access to this location is not allowed.');

/**
 * @package HelloWorld
 */
class SmmTools
{
    function open($data)
    {
        mosCommonHTML::loadBootstrap(true);
        ?>

        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading ">
                <tr>
                    <th colspan="2">
                        SMM Manager
                    </th>
                </tr>
            </table>
            <table class=" table_top">

                <tr>
                    <td style="font-weight: bold;font-size: 22px;width: 60%">
                        Show free gift radio buttons Yes/No
                    </td>
                    <td style="vertical-align: top;width: 10%">
                        <select style="font-weight: bold;padding: 5px;font-size: 15px;"
                                name="show_free_gift_radio_buttons" class="show_free_gift_radio_buttons form-control">
                            <option <?php if (!$data->show_free_gift_radio_buttons) {
                                echo 'selected';
                            } ?> value="0">No
                            </option>
                            <option <?php if ($data->show_free_gift_radio_buttons) {
                                echo 'selected';
                            } ?> value="1">Yes
                            </option>
                        </select>
                    </td>
                    <td>
                        <div class="inline-inner-inputs">
                            <input type="text" name="free_gift_radio_first_product_id" placeholder="Product Id"
                                   class="free_gift_radio_first_product_id form-control"
                                   value="<?php echo $data->free_gift_radio_first_product_id; ?>">
                            <input type="text" name="free_gift_radio_first_product_name" placeholder="Product Name"
                                   class="free_gift_radio_first_product_name form-control"
                                   value="<?php echo $data->free_gift_radio_first_product_name; ?>">
                            <input type="text" name="free_gift_radio_second_product_id" placeholder="Product Id"
                                   class="free_gift_radio_second_product_id form-control"
                                   value="<?php echo $data->free_gift_radio_second_product_id; ?>">
                            <input type="text" name="free_gift_radio_second_product_name" placeholder="Product Name"
                                   class="free_gift_radio_second_product_name form-control"
                                   value="<?php echo $data->free_gift_radio_second_product_name; ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;font-size: 22px;">
                        Show free gift top popup Yes/No
                    </td>
                    <td style="vertical-align: top">
                        <select style="font-weight: bold;padding: 5px;font-size: 15px;" name="free_gift_top_popup"
                                class="free_gift_top_popup form-control">
                            <option <?php if (!$data->free_gift_top_popup) {
                                echo 'selected';
                            } ?> value="0">No
                            </option>
                            <option <?php if ($data->free_gift_top_popup) {
                                echo 'selected';
                            } ?> value="1">Yes
                            </option>
                        </select>
                    </td>
                    <td>
                        <div class="inline-inner-inputs">
                            <input type="text" name="free_gift_popup_first_product_id" placeholder="Product Id"
                                   class="free_gift_popup_first_product_id form-control"
                                   value="<?php echo $data->free_gift_popup_first_product_id; ?>">
                            <input type="text" name="free_gift_popup_second_product_id" placeholder="Product Id"
                                   class="free_gift_popup_second_product_id form-control"
                                   value="<?php echo $data->free_gift_popup_second_product_id; ?>">
                        </div>

                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;font-size: 22px;">
                        Show mobile coupon popup Yes/No
                    </td>
                    <td style="vertical-align: top">
                        <select style="font-weight: bold;padding: 5px;font-size: 15px;" name="mobile_coupon_popup"
                                class="mobile_coupon_popup form-control">
                            <option <?php if (!$data->mobile_coupon_popup) {
                                echo 'selected';
                            } ?> value="0">No
                            </option>
                            <option <?php if ($data->mobile_coupon_popup) {
                                echo 'selected';
                            } ?> value="1">Yes
                            </option>
                        </select>
                    </td>
                    <td>
                    </td>
                </tr>


            </table>
            <table class=" table_bottom">
                <tr>
                    <td style="font-weight: bold;font-size: 22px;">
                        Show Search Keywords Yes/No
                    </td>
                    <td style="vertical-align: top">
                        <select style="font-weight: bold;padding: 5px;font-size: 15px;" name="show_search_keywords"
                                class="show_search_keywords form-control">
                            <option <?php if (!$data->show_search_keywords) {
                                echo 'selected';
                            } ?> value="0">No
                            </option>
                            <option <?php if ($data->show_search_keywords) {
                                echo 'selected';
                            } ?> value="1">Yes
                            </option>
                        </select>
                    </td>
                    <td>
                        <div class="inline-inner-inputs">
                            <div class="form-group">
                                <input id="keyword_name" type="text" class="form-control" placeholder="Keywords Name">
                                <input id="keyword_tag" type="text" class="form-control" placeholder="Keyword Tag">

                                <button type="button" class="btn-success form-control" id="keyword_add_btn">Add Keyword</button>
                            </div>
                            <ul class="keyword_list list-group"></ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="button" name="submit" class="submit_button btn btn-danger" value="Save">
                    </td>
                </tr>
            </table>
        </form>
        <style>
            .table_bottom {
                width: 100%;
                float: left;
                border-collapse: separate;
                border-spacing: 10px;
            }
            .table_top {
                width: 50%;
                float: left;
                border-collapse: separate;
                border-spacing: 10px;
            }
            table.table_bottom td,table.table_top td{
                vertical-align: top;
            }

            .inline-inner-inputs {
                display: -webkit-inline-box;
            }

            .inline-inner-inputs input {
                margin-right: 10px;
                margin-bottom: 10px;
            }
            .keyword_delete{
                font-weight: bold;
                cursor: pointer;
                margin-left: 10px;
                display: block;
                float: right;
                position: absolute;
                right: 3px;
                top: 5px;
            }
            ul.keyword_list {
                width: 505px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                flex-wrap: nowrap;
                gap: 5px;
                flex-flow: row wrap;
            }
            ul.keyword_list li {
                height: 70px;

            }
            .keyword_list{
                margin: 0 10px;
            }
        </style>
        <script>

            $('#keyword_add_btn').click(function(){
                if($('#keyword_name').val()!='' || $('#keyword_tag').val()!=''){
                    var value = {};
                    value.keyword_name = $('#keyword_name').val();
                    value.keyword_tag = $('#keyword_tag').val();
                    var items =  getFromLocal('keywordsForSearch');
                    items.push(value);
                    $('#keyword_name').val('');
                    $('#keyword_tag').val('');
                    loadList(items);
                    storeToLocal('keywordsForSearch', items);
                }
            });

            function loadList(items){
                $('.keyword_li').remove();
                if(items.length > 0) {
                    for(var i = 0; i < items.length; i++) {
                        $('.keyword_list').append('<li class= "keyword_li list-group-item">' + items[i].keyword_name + ' (' + items[i].keyword_tag + ')<span class="keyword_delete glyphicon glyphicon-remove-circle" aria-hidden="true"></span></li>');
                    }
                }
            };

            function storeToLocal(key, items){
                localStorage[key] = JSON.stringify(items);
            }

            function getFromLocal(key){
                if(localStorage[key])
                    return JSON.parse(localStorage[key]);
                else
                    return [];
            }
            $('.keyword_list').delegate(".keyword_delete", "click", function(event){
                event.stopPropagation();
                var items = getFromLocal('keywordsForSearch');

                var index = $('.keyword_delete').index(this);

                $('.keyword_li').eq(index).remove();
                items.splice(index, 1);
                storeToLocal('keywordsForSearch', items);
            });

            $(document).ready(function () {
                storeToLocal('keywordsForSearch', <?php echo (isset($data->keywords) && $data->keywords != '') ? json_encode(json_decode($data->keywords,true)) : '[]';?>);
                loadList(getFromLocal('keywordsForSearch'));

                $('.submit_button').click(function () {
                    var mobile_coupon_popup = $('.mobile_coupon_popup').val();
                    var free_gift_top_popup = $('.free_gift_top_popup').val();
                    var show_free_gift_radio_buttons = $('.show_free_gift_radio_buttons').val();
                    var free_gift_popup_first_product_id = $('.free_gift_popup_first_product_id').val();
                    var free_gift_popup_second_product_id = $('.free_gift_popup_second_product_id').val();
                    var free_gift_radio_first_product_id = $('.free_gift_radio_first_product_id').val();
                    var free_gift_radio_first_product_name = $('.free_gift_radio_first_product_name').val();
                    var free_gift_radio_second_product_id = $('.free_gift_radio_second_product_id').val();
                    var free_gift_radio_second_product_name = $('.free_gift_radio_second_product_name').val();
                    var show_search_keywords = $('.show_search_keywords').val();
                    var keywords = getFromLocal('keywordsForSearch');
                    $('.submit_button').val('Please Wait ... ');
                    $.post("index2.php", {
                            option: 'com_smm_tools',
                            mobile_coupon_popup: mobile_coupon_popup,
                            free_gift_top_popup: free_gift_top_popup,
                            free_gift_popup_first_product_id: free_gift_popup_first_product_id,
                            free_gift_popup_second_product_id: free_gift_popup_second_product_id,
                            show_free_gift_radio_buttons: show_free_gift_radio_buttons,
                            free_gift_radio_first_product_id: free_gift_radio_first_product_id,
                            free_gift_radio_first_product_name: free_gift_radio_first_product_name,
                            free_gift_radio_second_product_id: free_gift_radio_second_product_id,
                            free_gift_radio_second_product_name: free_gift_radio_second_product_name,
                            show_search_keywords: show_search_keywords,
                            keywords: keywords,
                            task: 'save'
                        },
                        function (data) {
                            $('.submit_button').val('Save')
                        })
                })
            });
        </script>
        <?php
    }
}