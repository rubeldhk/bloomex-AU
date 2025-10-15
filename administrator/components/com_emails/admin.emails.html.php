<?php

defined('_VALID_MOS') or die('Restricted access');

Class HTML_ComEmails {
    
    public function edit_new($option, $row = false, $statuses, $variables_all, $change_history, $email_types, $recipient_types) {
        mosCommonHTML::loadCKeditor();
        mosCommonHTML::loadBootstrap();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Information</a></li>
                    <li><a href="#tabs-2">Default variables</a></li>
                    <li><a href="#tabs-3">Change History</a></li>
                </ul>
                <div id="tabs-1">
                    <table class="adminlist">
                        <tr>
                            <td>
                                For Foreign Orders:
                            </td>
                            <td>
                                <input type="checkbox" name="for_foreign_orders" value="1" <?php echo ($row->for_foreign_orders)?'checked':'';?> />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Type:
                            </td>
                            <td>
                                <select name="email_type">
                                    <?php
                                    foreach ($email_types as $email_type_k => $email_type_v) {
                                        ?>
                                        <option value="<?php echo $email_type_k; ?>"<?php echo ($email_type_k == $row->email_type ? ' selected' : ''); ?>>
                                            <?php echo $email_type_v; ?>
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
                                        <option value="<?php echo $recipient_type_k; ?>"<?php echo ($recipient_type_k == $row->recipient_type ? ' selected' : ''); ?>>
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
                                        <option value="<?php echo $status->order_status_code; ?>"<?php echo ($status->order_status_code == $row->order_status_code ? ' selected' : ''); ?>>
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
                                Subject:
                            </td>
                            <td>
                                <input type="text" name="email_subject" value="<?php echo $row->email_subject; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Template
                            </td>
                            <td>
                                <textarea class="text_area" id="email_html" name="email_html">
                                    <?php echo $row->email_html;?>
                                </textarea>
                                <script> CKEDITOR.replace("email_html");</script>
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
                                        <td id="variable_<?php echo $variable_all->id; ?>">
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

                <div id="tabs-3">

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>
                                username
                            </th>
                            <th>
                                datetime
                            </th>
                            <th>
                                action
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (sizeof($change_history) > 0) {
                            foreach ($change_history as $c) {
                                ?>
                                <tr>
                                    <td>
                                        <a class="click_to_show" id="<?php echo $c->id; ?>"><?php echo $c->username; ?></a>
                                    </td>
                                    <td>
                                        <?php echo $c->datetime; ?>
                                    </td>
                                    <td>
                                        <?php echo $c->action ; ?>
                                    </td>
                                </tr>
                                <tr class="content_details email_<?php echo $c->id; ?>">
                                    <td colspan="3"><?php echo $c->email_html; ?></td>
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
                ins{
                    color: #3ac73a;
                    font-weight: bold;
                }
                del{
                    color: red;
                    font-weight: bold;
                }
                .content_details{
                    display: none;
                }
                table.adminlist a,.click_to_show {
                    color: #C64934 !important;
                }
                table.adminlist th {
                    background-image: none !important;
                    background-color: #e2e2e2 !important;
                }
                img.copy,.click_to_show {
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
                    $('body').on('click', '.click_to_show', function(event) {
                        $('.email_'+$(this).attr('id')).toggle("fade", 1000)
                    });
                    $('#tabs').tabs();
                    
                    $('body').on('click', 'img.copy', function(event) {
                        var selection = window.getSelection();
                        selection.empty();
                        
                        if ($(this).attr('type') == '1') {
                            var copyText = document.getElementById('variable_'+$(this).attr('variable_id'));
                            copyText.select();
                        }
                        else {
                            var range = document.createRange();
                            range.selectNode(document.getElementById('variable_'+$(this).attr('variable_id')));
                            window.getSelection().addRange(range);
                        }
                        
                        document.execCommand('Copy');
                        
                        var selection = window.getSelection();
                        selection.empty();
                        /*
                        function CopyToClipboard(containerid) {
                        if (document.selection) { 
                            var range = document.body.createTextRange();
                            range.moveToElementText(document.getElementById(containerid));
                            range.select().createTextRange();
                            document.execCommand("Copy"); 

                        } else if (window.getSelection) {
                            var range = document.createRange();
                             range.selectNode(document.getElementById(containerid));
                             window.getSelection().addRange(range);
                             document.execCommand("Copy");
                             alert("text copied") 
                        }}*/
                    });

                });
            </script>
        </form>
        <?php
    }
    
    public function default_list($option, $email_types, $recipient_types, $rows, $pageNav, $search) {
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
                        Subject
                    </th>
                    <th style="text-align: left;" width="20%">
                        Type
                    </th>
                    <th style="text-align: left;" width="20%">
                        Recipient
                    </th>
                    <th style="text-align: left;" width="5%">
                        Order Status Code
                    </th>
                    <th style="text-align: left;" width="5%">
                        For Foreign Orders
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
                                <?php echo htmlspecialchars($row->email_subject); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($email_types[$row->email_type]); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($recipient_types[$row->recipient_type]); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->status_name); ?>
                        </td>
                        <td>
                            <?php echo $row->for_foreign_orders?'<img src="/images/tick.png">':''; ?>
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

