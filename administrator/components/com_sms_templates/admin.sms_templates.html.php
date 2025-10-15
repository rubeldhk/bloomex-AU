<?php

defined('_VALID_MOS') or die('Restricted access');

Class HTML_ComSmsTemplates {
    
  static  public function edit_new($option, $row = false, $statuses,$variables_all,  $template_types, $recipient_types) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Information</a></li>
                    <li><a href="#tabs-2">Default variables</a></li>
                </ul>
                <div id="tabs-1">
            <table class="adminlist">
                        <tr>
                            <td>
                                Type:
                            </td>
                            <td>
                                <select name="template_type">
                                    <?php
                                    foreach ($template_types as $type_k => $type_v) {
                                        ?>
                                        <option value="<?php echo isset($type_k)?$type_k:''; ?>"<?php echo (isset($type_k) && isset($row->template_type) && $type_k == $row->template_type ? ' selected' : ''); ?>>
                                            <?php echo $type_v; ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Recipient:
                            </td>
                            <td>
                                <select name="recipient_type">
                                    <?php
                                    foreach ($recipient_types as $recipient_type_k => $recipient_type_v) {
                                        ?>
                                        <option value="<?php echo isset($recipient_type_k)?$recipient_type_k:''; ?>"<?php echo (isset($row->recipient_type) && isset($recipient_type_k) && $recipient_type_k == $row->recipient_type ? ' selected' : ''); ?>>
                                            <?php echo $recipient_type_v; ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Order status:
                            </td>
                            <td>
                                <select name="order_status_code">
                                    <?php
                                    foreach ($statuses as $status) {
                                            ?>
                                            <option value="<?php echo isset($status->order_status_code) ? $status->order_status_code : ''; ?>"<?php echo(isset($status->order_status_code) && isset($row->order_status_code) && $status->order_status_code == $row->order_status_code ? ' selected' : ''); ?>>
                                                <?php echo $status->order_status_name; ?>
                                            </option>
                                            <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Title
                            </td>
                            <td>
                                <input type="text" name="title" value="<?php echo isset($row->title)?$row->title:'';?>">
                            </td>
                        </tr><tr>
                            <td>
                                Template
                            </td>
                            <td>

                                <textarea class="text_area" id="template" name="template"><?php echo isset($row->template)?$row->template:'';?></textarea>

                            </td>
                        </tr>
                    </table>
                </div>
                <div id="tabs-2">
                    <table class="adminlist">
                        <thead>
                        <tr>
                            <th>

                            </th>
                            <th>
                                Variable
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (sizeof($variables_all) > 0) {
                            foreach ($variables_all as $variable_all) {
                                ?>
                                <tr>
                                    <td width="20px">
                                        <img class="copy" type="2" variable_id="<?php echo $variable_all->id; ?>" src="/administrator/images/com_emails_copy1.png" title="Click to copy"/>
                                    </td>
                                    <td id="variable_<?php echo $variable_all->id; ?>" title="<?php echo $variable_all->value; ?>">
                                        <?php echo $variable_all->variable; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />

            <script src="/templates/bloomex7/js/jquery.min.js" type="text/javascript"></script>
            <script src="/templates/bloomex7/js/jquery-ui-1.10.3.custom.min.js"></script>
            <link rel="stylesheet" href="/templates/bloomex7/css/smoothness/jquery-ui-1.10.3.custom.css">
            <style>
                table.adminlist a {
                    color: #C64934 !important;
                }
                table.adminlist th {
                    background-image: none !important;
                    background-color: #e2e2e2 !important;
                }
                img.copy {
                    cursor: pointer;
                }
                img.copy:hover {
                    opacity: 0.8;
                    background-color: #c3bfbf;
                    border-radius: 10px;
                }
            </style>
            <script type="text/javascript">
                $( document ).ready(function() {

                    $('#tabs').tabs();


                });
            </script>
        </form>
        <?php
    }
    
   static public function default_list($option, $sms_templates_types, $recipient_types, $rows, $pageNav) {
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
                        Title
                    </th>
                    <th style="text-align: left;" width="20%">
                        Type
                    </th>
                    <th style="text-align: left;" width="20%">
                        Recipient
                    </th>
                    <th style="text-align: left;" width="10%">
                        Order Status Code
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
                                <?php echo htmlspecialchars($row->title); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($sms_templates_types[$row->template_type]); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($recipient_types[$row->recipient_type]); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->status_name); ?>
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

