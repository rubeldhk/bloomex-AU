<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_CCARDS {
    function edit_view($row) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
            <tr>
                <td>
                    Mask
                </td>
                <td>
                    <input type="text" value="<?php echo $row->mask; ?>" name="mask">
                </td>
            </tr>
            <tr>
                <td>
                    User email
                </td>
                <td>
                    <input type="email" value="<?php echo $row->email; ?>" name="email">
                </td>
            </tr>
            <tr>
                <td>
                    Block
                </td>
                <td>
                    <select name="block">
                        <option value="0">Choose</option>
                        <option value="0" <?php echo ($row->block == '0' ? 'selected' : ''); ?>>No</option>
                        <option value="1" <?php echo ($row->block == '1' ? 'selected' : ''); ?>>Yes</option>
                    </select>
                </td>
            </tr>
            </table>

            <input type="hidden" name="option" value="com_ccards" />
            <input type="hidden" name="ccard_id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form> 
        <?php
    }
    
    function default_view($rows, $pageNav, $search, $orderby) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading" style="width: 300px; float: left; margin-bottom: 15px;">
                <tr>
                    <td align="left">
                        Filter:
                    </td>
                    <td>
                        <input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
                    </td>
                    <td>
                        Ordering:
                    </td>
                    <td>
                        <select name="orderby" onChange="document.adminForm.submit();">
                            <option value="u.email ASC" <?php if ($orderby == 'u.email ASC') { echo 'selected'; }?>>
                                Email ascending
                            </option>
                            <option value="u.email DESC" <?php if ($orderby == 'u.email DESC') { echo 'selected'; }?>>
                                Email descending
                            </option>
                            <option value="c.mask ASC" <?php if ($orderby == 'c.mask ASC') { echo 'selected'; }?>>
                                Mask ascending
                            </option>
                            <option value="c.mask DESC" <?php if ($orderby == 'c.mask DESC') { echo 'selected'; }?>>
                                Mask descending
                            </option>
                        </select>
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
                <th style="text-align: left;" width="30%">
                    Mask
                </th>
                <th style="text-align: left;" width="30%">
                    User
                </th>
                <th style="text-align: left;" width="10%">
                    Block
                </th>
            </tr>
            <?php
            $i = 0;
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
                        <?php echo $row->mask; ?>
                    </td>
                    <td align="left">
                        <a target="_blank" href="index2.php?page=admin.user_form&option=com_virtuemart&user_id=<?php echo $row->user_id; ?>"><?php echo $row->email; ?></a>
                    </td>
                    <td align="left">
                        <?php
                        if ($row->block == '0') {
                            ?>
                            <!--<a href="index2.php?option=com_ccards&task=publish&cid=<?php echo $row->id; ?>">-->
                            <?php
                            $img = 'publish_x.png';
                            $alt = 'Unblocked';
                        } 
                        else { 
                            ?>
                            <!--<a href="index2.php?option=com_ccards&task=unpublish&cid=<?php echo $row->id; ?>">-->
                            <?php
                            $img = 'tick.png';
                            $alt = 'Blocked';
                        }
                        ?>
                        <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                        <!--</a>-->
                    </td>
                </tr>
                <?php
                $i++;
            }
            ?>
            </table>
            <?php 
            echo $pageNav->getListFooter();
            ?>
            <input type="hidden" name="option" value="com_ccards" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
        </form> 
        <?php 
    }
}

