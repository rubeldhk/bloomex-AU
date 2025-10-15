<?php

defined('_VALID_MOS') or die('Restricted access');

Class HTML_ComBadEmails {
    
    public function edit_new($option, $row = false) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <td>
                        Domain:
                    </td>
                    <td>
                        <input type="text" name="email" value="<?php echo isset($row->email) ? $row->email : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Reason:
                    </td>
                    <td>
                        <input type="text" name="reason" value="<?php echo isset($row->reason) ? $row->reason : ''; ?>">
                    </td>
                </tr>         
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }
    
    public function default_list($option, $rows, $pageNav, $search) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <th width="2%">
                        #
                    </th>
                    <th width="2%">
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo sizeof($rows); ?>);" />
                    </th>
                    <th style="text-align: left;" width="20%">
                        Domain
                    </th>
                    <th style="text-align: left;" width="20%">
                        Reason
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
                            <a href="./index2.php?option=<?php echo $option; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
                                <?php echo htmlspecialchars($row->email); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->reason); ?>
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
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
        </form>
        <?php
    }
    
}

