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
defined('_VALID_MOS') or die('Restricted access');

/**
 * @package Joomla
 * @subpackage Category
 */
class HTML_Disp {

    static function default_list($lists) {
        ?>
        <div style="float: right;">
            <button onclick="download_list()">Download List</button>
            <input type="file" id="upload_list" />
            <button id="upload-button" onclick="upload_list()"> Upload </button>
        </div>
        <div style="float: right;">
            <p>Update Imported Products (AUD) Price from USD price exchanged by USD-AUD current rate<br>If product doesn't have USD price it means it is local product</p>
            <p>USD dollar rate: <input class="dollar_rate" value="" PLACEHOLDER="1.45"> <button onclick="Update_Imported_Product_Price()">Update</button></p>
        </div>
        <script type="text/javascript">
            function Update_Imported_Product_Price() {
                jQuery("#add_product").html('Loading...');
                jQuery.ajax({
                    data:
                            {
                                option: 'com_ingredients',
                                task: 'update_price',
                                rate: jQuery('.dollar_rate').val()
                            },
                    type: "POST",
                    dataType: "html",
                    url: "index3.php",
                    success: function (data)
                    {
                        jQuery("#add_product").html(data + ' Updating page...');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                });
            }
            function download_list() {
                jQuery("#add_product").html('Loading...');
                jQuery.ajax({
                    data:
                            {
                                option: 'com_ingredients',
                                task: 'download_list'
                            },
                    type: "POST",
                    url: "index3.php",
                    success: function (data)
                    {
                        //Convert the Byte Data to BLOB object.
                        var blob = new Blob([data], {type: "application/octetstream"});
                        var url = window.webkitURL;
                        link = url.createObjectURL(blob);
                        var a = $("<a />");
                        a.attr("download", 'ingredient-list-au.csv');
                        a.attr("href", link);
                        $("body").append(a);
                        a[0].click();
                        $("body").remove(a);

                    }

                });
                jQuery("#add_product").html('');
            }
            function upload_list() {
                var fileInput = document.getElementById("upload_list")
                var reader = new FileReader();
                reader.onload = function () {
                    console.log(reader.result);
                    jQuery.ajax({
                        data:
                                {
                                    option: 'com_ingredients',
                                    task: 'upload_list',
                                    ingredient_list: reader.result
                                },
                        type: "POST",
                        url: "index3.php",
                        dataType: "html",
                        success: function (data)
                        {

                            jQuery("#add_product").html(data + ' Updating page...');
                            setTimeout(function () {
                                location.reload();
                            }, 3000);

                        }
                    });
                };
                // start reading the file. When it is done, calls the onload event defined above.
                reader.readAsBinaryString(fileInput.files[0]);


            }
            function AddIngredient()
            {
                var product_name = jQuery("#product_name").val();
                var landing_price = jQuery("#landing_price").val();
                var foreign_price = jQuery("#foreign_price").val();
                var bold = jQuery("#bold").attr("checked") ? 1 : 0;
                var type = jQuery("#type").val();

                jQuery("#add_product").html('Loading...');

                jQuery.ajax({
                    data:
                            {
                                option: 'com_ingredients',
                                task: 'add_new',
                                product_name: product_name,
                                landing_price: landing_price,
                                foreign_price: foreign_price,
                                type: type,
                                bold: bold
                            },
                    type: "POST",
                    dataType: "html",
                    url: "index3.php",
                    success: function (data)
                    {
                        jQuery("#add_product").html('<font color="green">Success</font> Updating page...');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                });
            }

            function DeleteIngredient(product_id)
            {

                jQuery("#add_product").html('Loading...');

                jQuery.ajax({
                    data:
                            {
                                option: 'com_ingredients',
                                task: 'delete_ing',
                                product_id: product_id
                            },
                    type: "POST",
                    dataType: "html",
                    url: "index3.php",
                    success: function (data)
                    {
                        jQuery("#add_product").html('<font color="green">Success</font> Updating page...');
                    }
                });

                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
        </script>
        <div id="add_product">
            <table class="adminlist">
                <thead>
                    <tr><th colspan="6">Add new</th></tr>
                    <tr>
                        <th>
                            Name
                        </th>
                        <th>
                            Price AUD
                        </th>
                        <th>
                            Price USD (If Imported Product)
                        </th>
                        <th>
                            Add-on
                        </th>
                        <th>
                            Type
                        </th>
                        <th>Save</th>
                    </tr>
                </thead>
                <tr>
                    <td>
                        <input type="text" name="product_name" id="product_name" size="100"/>
                    </td>
                    <td>
                        <input type="text" name="landing_price" id="landing_price" size="20"/>
                    </td>
                    <td>
                        <input type="text" name="foreign_price" id="foreign_price" size="20"/>
                    </td>
                    <td>
                        <input type="checkbox" name="bold" id="bold" value="1">
                    </td>
                    <td>
                        <select name="type" id="type">
                            <option value="FLOWER/PLANT">FLOWER/PLANT</option>
                            <option value="GOURMET">GOURMET</option>
                            <option value="HARDGOOD">HARDGOOD</option>
                        </select>
                    </td>
                    <td>
                        <input type="button" onclick="AddIngredient();" value="Add"/>
                    </td>
                </tr>
            </table>

        </div>
        <table class="adminlist">

            <?php
            if (sizeof($lists) > 0) {
                echo '<thead>
                    <tr>
                        <th>
                            Name
                        </th>
                        <th>
                            Price AUD
                        </th>
                        <th>
                            Price USD (If Imported Product)
                        </th>
                        <th>
                            Add-on
                        </th>
                         <th>
                            type
                        </th>
                        <th>
                            Delete
                        </th>
                    </tr>
                </thead>';

                foreach ($lists as $list) {

                    echo '<tr>
                        <td>
                            <a href="index2.php?option=com_ingredients&amp;task=edit_ing&amp;id=' . $list->igo_id . '">' . $list->igo_product_name . '</a>
                        </td>
                        <td>
                            ' . $list->landing_price . '
                        </td>
                       
                        <td>
                            ' . $list->foreign_price . '
                        </td>
                        <td>
                            ' . (($list->bold == '1') ? 'Yes' : 'No') . '
                        </td>
                         <td>
                            ' . ($list->type) . '
                        </td>
                        <td>
                            <input type="button" onclick="DeleteIngredient(' . $list->igo_id . ');" value="Delete"/>
                        </td>
                    </tr>';
                }
            } else {
                echo 'Sorry, no ingredients';
            }
            ?>
        </table>
        <?php
    }

        static function edit_ing($list) {
            ?>

            <div id="add_product"></div>

            <table class="adminlist">
                <thead>
                    <tr><th colspan="7">Edit</th></tr>
                    <tr>
                        <th>
                            Id
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Price CAD
                        </th>
                        <th>
                            Price USD (If Imported Product)
                        </th>
                        <th>
                            Add-on
                        </th>
                        <th>
                            Type
                        </th>
                        <th>Save</th>
                    </tr>
                </thead>
                <tr>
                    <td>
                        <input type="text" name="product_id" id="product_id" size="10" value="<?php echo $list->igo_id; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="product_name" id="product_name" size="100" value="<?php echo $list->igo_product_name; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="landing_price" id="landing_price" value="<?php echo $list->landing_price; ?>" size="20"/>
                    </td>
                    <td>
                        <input type="text" name="foreign_price" id="foreign_price" value="<?php echo $list->foreign_price; ?>" size="20"/>
                    </td>
                    <td>
                        <input type="checkbox" name="bold" id="bold" value="1" <?php echo ($list->bold == '1') ? 'checked' : ''; ?>/>
                    </td>
                    <td>
                        <select name="type" id="type">
                            <option value="FLOWER/PLANT">FLOWER/PLANT</option>
                            <option value="GOURMET">GOURMET</option>
                            <option value="HARDGOOD">HARDGOOD</option>
                        </select>
                    </td>
                    <td>
                        <input type="button" onclick="SaveIngredient(<?php echo $list->igo_id; ?>);" value="Save"/>
                    </td>
                </tr>
            </table>

        </div>

        <?php
    }

}
?>
