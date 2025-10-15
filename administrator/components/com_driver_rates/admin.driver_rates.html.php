<?php
defined('_VALID_MOS') or die('Restricted access');

Class HTML_ComDriverRates {

    public static function postalcode_edit_new($option, $row = false, $r_rows, $id_rate) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <td>
                        Postalcode:
                    </td>
                    <td>
                        <input type="text" name="postalcode" value="<?php echo isset($row->postalcode) ? $row->postalcode : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Rate:
                    </td>
                    <td>
                        <select name="id_rate">
                            <option value="">Choose...</option>
                            <?php
                            foreach ($r_rows as $r_obj) {
                                ?>
                                <option value="<?php echo $r_obj->id; ?>" <?php echo ((((isset($row->id_rate) AND ($row->id_rate == $r_obj->id))) OR ($id_rate == $r_obj->id)) ? 'selected' : ''); ?>><?php echo $r_obj->name; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>      
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="warehouse" value="<?php echo $_REQUEST['warehouse'] ?? ''; ?>"/>
            <input type="hidden" name="default_id_rate" value="<?php echo isset($id_rate) ? $id_rate : ''; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }

    public static function edit_new($option, $row = false, $wh_rows) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <td>
                        Name:
                    </td>
                    <td>
                        <input type="text" name="name" value="<?php echo isset($row->name) ? $row->name : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Warehouse:
                    </td>
                    <td>
                        <select name="warehouse_id">
                            <option value="">Choose...</option>
                            <?php
                            foreach ($wh_rows as $wh_obj) {
                                ?>
                                <option value="<?php echo $wh_obj->warehouse_id; ?>" <?php echo (((isset($row->warehouse_id) AND ($row->warehouse_id == $wh_obj->warehouse_id))) ? 'selected' : ''); ?>><?php echo $wh_obj->warehouse_name; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Rate:
                    </td>
                    <td>
                        <input type="text" name="rate" value="<?php echo isset($row->rate) ? $row->rate : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Rate for Driver:
                    </td>
                    <td>
                        <input type="text" name="rate_driver" value="<?php echo isset($row->rate_driver) ? $row->rate_driver : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Is GoFor:
                    </td>
                    <td>
                        <input type="checkbox" name="is_gofor" <?php echo ($row->is_gofor) ? 'checked' : ''; ?>>
                    </td>
                </tr>
                <tr>
                    <td>
                        Comment:
                    </td>
                    <td>
                        <textarea  name="comment"><?php echo isset($row->comment) ? $row->comment : ''; ?></textarea>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="warehouse" value="<?php echo $_REQUEST['warehouse'] ?? ''; ?>"/>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }

    public static function postalcodes_list($option, $row, $rows, $pageNav, $id_rate) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <div class="title">
                <?php echo $row->name; ?>
            </div>
            <table class="adminlist">
                <tr>
                    <th width="2%">
                        #
                    </th>
                    <th width="2%">
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo sizeof($rows); ?>);" />
                    </th>
                    <th style="text-align: left;" width="20%">
                        Postalcode
                    </th>
                </tr>
                <?php
                $i = 0;
                foreach ($rows as $row) {
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="row<?php echo $i; ?>">
                        <td align="center">
                            <?php echo $pageNav->rowNumber($i); ?>
                        </td>
                        <td align="center">
                            <?php echo $checked; ?>
                        </td>
                        <td>
                            <a href="./index2.php?option=<?php echo $option; ?>&amp;task=postalcode_edit&amp;id=<?php echo $row->id; ?>&amp;id_rate=<?php echo $id_rate; ?>">
                                <?php echo htmlspecialchars($row->postalcode); ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </table>
            <?php
            //  echo $pageNav->getListFooter();
            ?>
            <input type="hidden" name="warehouse" value="<?php echo $_REQUEST['warehouse'] ?? ''; ?>"/>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id_rate" value="<?php echo $id_rate; ?>" />
            <input type="hidden" name="task" value="postalcodes_list" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
        </form>
        <?php
    }

    public static function default_list($option, $warehouse_id_selected, $warehouses, $rows, $pageNav, $search = '') {
        ?>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="/templates/bloomex_adaptive/css/smoothness/jquery-ui-1.10.3.custom.css">
        <form action="index2.php" method="post" name="adminForm" >
            <div class="filter">
                Warehouse
                <select id="warehouse" name="warehouse">
                    <option value="">All</option>
                    <?php
                    foreach ($warehouses as $warehouse) {
                        ?>
                        <option value="<?php echo $warehouse->warehouse_id; ?>" <?php echo ($warehouse_id_selected == $warehouse->warehouse_id) ? 'selected' : ''; ?>><?php echo $warehouse->warehouse_name; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <label>Search
                    <input name="search" id="search" placeholder='Zone name, Postal code...' value='<?php echo $search; ?>'>
                </label>
                <button type="submit">Filter</button>
                <button type="button" onclick="reset_search()">Reset</button>
            </div>
            <script>
                function reset_search() {
                    document.querySelector('#warehouse').value = '';
                    document.querySelector('#search').value = '';
                    document.querySelector('[name="adminForm"]').submit();
                }
            </script>
            <p class="text-warning">move rows to re-order rates</p>
            <table class="adminlist" id="tblRates">
                <tr>
                    <th width="20">
                        id
                    </th>
                    <th width="20">
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo sizeof($rows); ?>);" />
                    </th>

                    <th style="text-align: left;" >
                        Name
                    </th>
                    <th style="text-align: left;" >
                        Warehouse
                    </th>
                    <th  width="40" >
                        Go For
                    </th>
                    <th style="text-align: left;" >
                        Rate
                    </th>      
                    <th style="text-align: left;" >
                        Rate for Driver
                    </th>      
                    <th style="text-align: left;" >
                        Comment
                    </th>
                    <th style="text-align: left;" >
                        Postal Codes
                    </th>
                </tr>
                <?php
                $i = 0;
                foreach ($rows as $row) {
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="row<?php echo $i; ?>">
                        <td align="center">
                            <input type="hidden" class="order" rateid="<?php echo htmlspecialchars($row->id); ?>" value="<?php echo $pageNav->rowNumber($i); ?>">
                            <?php echo $row->id; ?>
                        </td>
                        <td align="center">
                            <?php echo $checked; ?>
                        </td>
                        <td>
                            <a href="./index2.php?option=<?php echo $option; ?>&amp;task=rate_edit&amp;id=<?php echo $row->id; ?>&warehouse=<?php echo $warehouse_id_selected; ?>">
                                <?php echo htmlspecialchars($row->name); ?>
                            </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->warehouse_name); ?>
                        </td>
                        <td>
                            <?php echo ($row->is_gofor) ? 'Yes' : ''; ?>
                        </td>
                        <td>
                            $<?php echo htmlspecialchars($row->rate); ?>
                        </td>
                        <td>
                            $<?php echo htmlspecialchars($row->rate_driver); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->comment); ?>
                        </td>
                        <td> [
                            <a href="./index2.php?option=<?php echo $option; ?>&amp;task=postalcodes_list&amp;id_rate=<?php echo $row->id; ?>">
                                Add/Remove
                            </a>
                            ]
                            <?php
                            if ($row->pc) {
                                $pc_a = explode(',', $row->pc);
                                $pc_ids = explode(',', $row->pc_ids);


                                foreach ($pc_a as $k => $v) {
                                    ?>
                                    <a href ="/administrator/index2.php?option=com_driver_rates&task=postalcode_edit&id=<?php echo $pc_ids[$k]; ?>" target="_blank" title="edit in new tab"><?php echo $v; ?></a>
                                    <?php
                                }
                            }
                            ?>
                        </td>

                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </table>
            <?php
            //echo $pageNav->getListFooter();
            ?>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
        </form>
        <script type="text/javascript">
            jQuery(document).ready(function () {

                jQuery('#warehouse').change(function (event) {
                    jQuery('form[name="adminForm"]').submit();
                });

                $("#tblRates").sortable({
                    items: 'tr:not(tr:first-child)',
                    cursor: 'pointer',
                    axis: 'y',
                    dropOnEmpty: false,
                    start: function (e, ui) {
                        ui.item.addClass("selected");
                    },
                    stop: function (e, ui) {
                        if (jQuery('#warehouse').val() == '') {
                            alert('please choose warehouse before re-ordering rates');
                            return false;
                        }
                        ui.item.removeClass("selected");
                        let orderArr = [];
                        $(this).find("tr").each(function (index) {
                            if (index > 0) {
                                $(this).find("td").eq(0).find('input').val(index);
                                $(this).find("td").eq(0).find('span').text(index);

                                let element = {};
                                element.id = $(this).find("td").eq(0).find('input').attr('rateid');
                                element.order = index;
                                orderArr.push(element);
                            }
                        });
                        $.post("index2.php",
                                {option: "com_driver_rates",
                                    task: "reorder",
                                    warehouse_id: jQuery('#warehouse').val(),
                                    orderArrJson: JSON.stringify(orderArr)
                                }
                        );
                    }
                });
            });
        </script>
        <style>
            #tblRates tr{
                cursor: move;
            }
            .text-warning{
                color: #C64934;
                font-size: 16px;
                margin: 5px;
            }

        </style>
        <?php
    }

}
