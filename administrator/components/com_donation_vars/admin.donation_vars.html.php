<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

Class HTML_donation_vars
{
    function edit_donation_vars($row)
    {
        ?>

        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
            <tr>
                <th class="edit">
                    Item:
                    <small>
                        <?php echo $row ? 'Edit' : 'New'; ?>
                    </small>
                </th>
            </tr>
            </table>

            <table width="100%" class="adminform">
                <tr>
                    <td  style="width: 50%">
                        <table width="100%" class="adminform">
                            <tr>
                                <th colspan="2">
                                    Item Details
                                </th>
                            </tr>
                            <tr>
                                <td width="100%">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td>
                                                Name English:
                                            </td>
                                            <td>
                                                <input class="text_area" type="text" name="name" size="30" maxlength="100" id="name" value="<?php echo (isset($row->name) ? $row->name : ''); ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Price:
                                            </td>
                                            <td>
                                                <input class="text_area" type="text" name="price" size="30" maxlength="100" id="price" value="<?php echo (isset($row->price) ? $row->price : ''); ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Text English:
                                            </td>
                                            <td>
                                                <textarea name="donation_text"><?php echo (isset($row->text) ? $row->text : ''); ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Warehouse:
                                            </td>
                                            <td>
                                                <?php echo $row->warehouse_list; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Published:
                                            </td>
                                            <td>
                                                <?php echo $row->published; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        </table>
                    </td>
                    <td style="vertical-align: top;">
                        <table width="100%" border="1" class="adminform">
                            <tr>
                                <th colspan="2">
                                     Donates
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Order Id</strong>
                                </td>
                                <td>
                                    <strong>Donate Price</strong>
                                </td>
                            </tr>
                            <?php
                            if($row->used_donates){
                            $p=0;
                            $i=0;
                                foreach($row->used_donates as $r){?>
                                    <tr>
                                        <td>
                                            <a href="/administrator/index2.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart&order_id_filter=<?php echo $r->order_id; ?>" target="_blank"><?php echo $r->order_id; ?></a>
                                        </td>
                                        <td>
                                            <?php
                                            $i++;
                                            $p+=$r->donation_price;
                                            echo '$'.number_format($r->donation_price, 2, '.', ' ');
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                            <tr>
                                <td>
                                    Total Orders <strong><?php echo $i;?></strong>
                                </td>
                                <td>
                                    Total Donated <strong>$<?php echo number_format($p, 2, '.', '');?></strong>
                                </td>
                            </tr>
                            <?php }?>
                        </table>

                    </td>
                </tr>
            </table>


            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="option" value="com_donation_vars" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />

        </form>

        <?php
    }
    
    function default_donation_vars($rows, $pageNav,$search)
    {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading" style="width: 300px;float: left; margin-bottom: 15px;">
                <tr>
                        <td align="left">
                        Filter:
                        </td>
                        <td>
                        <input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
                        </td>
                </tr>
            </table>
            <table class="adminlist">
            <tr>
                <th width="2%">
                    #
                </th>
                <th width="2%">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo sizeof($rows); ?>);" />
                </th>
                <th style="text-align: left;" width="20%">
                    Name
                </th>
                <th style="text-align: left;" width="10%">
                    Price
                </th>
                <th style="text-align: left;" width="20%">
                    Warehouse
                </th>
                <th style="text-align: left;" width="10%">
                    Orders Count
                </th>
                <th style="text-align: left;" width="20%">
                    Total Donated Price
                </th>
                <th style="text-align: left;" width="10%">
                    Published
                </th>
            </tr>
            <?php
            $i = 0;
            if($rows){

            foreach ($rows AS $row)
            {

                
                $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                ?>
                <tr class="row<?php echo $i; ?>">
                    <td align="center">
                        <?php echo $pageNav->rowNumber($i); ?>
                    </td>
                    <td align="center">
                        <?php echo $checked; ?>
                    </td>
                    <td align="left">
                        <a href="index2.php?option=com_donation_vars&task=edit&hidemainmenu=1&id=<?php echo $row->id; ?>" title="Edit">
                            <?php echo $row->name; ?>
                        </a>
                    </td>
                    <td align="left">
                        <?php echo $row->price; ?>
                    </td>
                    <td align="left">
                        <?php echo $row->warehouse_name?$row->warehouse_name:'Default'; ?>
                    </td>
                    <td align="left">
                        <?php echo $row->orders_count; ?>
                    </td>
                    <td align="left">
                        <?php echo $row->total_donated_price; ?>
                    </td>
                    <td align="left">
                        <?php echo $row->published?'Yes':'No'; ?>
                    </td>
                </tr>
                <?php
                $i++;
            }

            }
            ?>
            </table>
            <?php 
            echo $pageNav->getListFooter();
            ?>
            <input type="hidden" name="option" value="com_donation_vars" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
        </form> 
        <?php 
    }
}

?>
