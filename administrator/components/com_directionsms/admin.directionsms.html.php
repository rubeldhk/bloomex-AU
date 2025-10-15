<?php
defined('_VALID_MOS') or die('Restricted access');

Class HTML_directionsms {
    
    public function edit($option, $my, $row) {
        $vars_a = array(
            '{order_id}',
            '{company}',
            '{first_name}',
            '{middle_name}',
            '{last_name}',
            '{phone}',
            '{suite}',
            '{street_number}',
            '{street_name}',
            '{city}',
            '{state}',
            '{zip}',
            '{user_email}'
        );
        ?>
        <style>
        </style>
        <div style="text-align: left;">
            Variables:<br/><?php echo implode('<br/>', $vars_a); ?>
        </div>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <td>
                        Template
                    </td>
                    <td>
                        <textarea name="template"><?php echo ($row) ? $row->template : ''; ?></textarea>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="id" value="<?php echo ($row) ? $row->id : ''; ?>" />
        </form>
        <?php
    }
    
    public function default_list($option, $my, $rows, $pageNav) {
        ?>
        <style>
        </style>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist routes">
                <tr>
                    <th style="text-align: left;" width="5%">
                    </th>
                    <th style="text-align: left;" width="20%">
                        ID
                    </th>
                    <th style="text-align: left;" width="20%">
                        Template
                    </th>
                    <th style="text-align: left;" width="5%">
                        Publish
                    </th>
                    <th style="text-align: left;" width="5%">
                        Username
                    </th>
                    <th style="text-align: left;" width="10%">
                        Date
                    </th>
                </tr>
                <?php
                $i = 0;
                foreach ($rows as $row) {
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="row<?php echo $i; ?>">
                        <td>
                            <?php echo mosCommonHTML::CheckedOutProcessing($row, $i) ?>
                        </td>
                        <td>
                            <a class="route" href="index2.php?option=<?php echo $option; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
                                #<?php echo $row->id; ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->template); ?>
                        </td>
                        <td>
                            <?php
                            echo vmCommonHTML::getYesNoIcon($row->publish);
                            ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->username); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->datetime); ?>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter();?>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
        </form>
        <script type="text/javascript">
        </script>
        <?php
    }
}

