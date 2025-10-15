<?php

defined('_VALID_MOS') or die('Restricted access');

Class HTML_ComAliases {
    
    public function edit_new($option, $row = false, $redirection_types, $page_types) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <td>
                        From:
                    </td>
                    <td>
                        <input type="text" name="from" value="<?php echo (isset($row->from) ?  $row->from : ''); ?>" size="70">
                    </td>
                </tr>
                <tr>
                    <td>
                        To:
                    </td>
                    <td>
                        <input type="text" name="to" value="<?php echo (isset($row->to) ? $row->to : ''); ?>" size="70">
                    </td>
                </tr>
                <tr>
                    <td>
                        Redirect:
                    </td>
                    <td>
                        <select name="status">
                            <?php
                            foreach ($redirection_types as $redirection_type_k => $redirection_type_v) {
                                ?>
                                <option value="<?php echo $redirection_type_k; ?>"<?php echo (((isset($row->status)) AND ($redirection_type_k == $row->status)) ? ' selected' : ''); ?>>
                                    <?php echo $redirection_type_v; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Page type:
                    </td>
                    <td>
                        <select name="type">
                            <?php
                            foreach ($page_types as $page_type_k => $page_type_v) {
                                ?>
                                <option value="<?php echo $page_type_k; ?>"<?php echo (((isset($row->type)) AND ($page_type_k == $row->type)) ? ' selected' : ''); ?>>
                                    <?php echo $page_type_v; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
              
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo (isset($row->id) ? $row->id : ''); ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            
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
        </form>
        <?php
    }
    
    public function default_list($option, $redirect_types, $page_types, $rows, $pageNav, $search, $search_type) {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table style="float: left" class="head_table">
                <tr>
                    <td align="left">
                        Filter:
                    </td>
                    <td>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="text_area" onChange="document.adminForm.submit();" />
                    </td>
                    <td>
                        <select name="search_type" onChange="document.adminForm.submit();">
                            <option value="-1" selected>All</option>
                            <?php
                            foreach ($page_types as $page_type_k => $page_type_v) {
                                ?>
                                <option value="<?php echo $page_type_k; ?>"<?php echo ($page_type_k === $search_type ? ' selected' : ''); ?>>
                                    <?php echo $page_type_v; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>

            <input type="button" class="get_pages_statuses" style="float: right;cursor: pointer;" value="Update Pages Statuses">
            <div class="script_progress">
                <progress class="script_bar" max="<?php echo $pageNav->total; ?>" value="0"></progress>
                <label><div class="script_percent">0 of <?php echo $pageNav->total; ?></div ></label>
            </div>
 <script type="text/javascript">

     var pages_count = '<?php echo $pageNav->total; ?>';
     var pages_count_per_script = 100;
     var script_running_times = Math.floor(pages_count/pages_count_per_script)+1;
     var script_bar = $('.script_bar');
     var script_percent = $('.script_percent');

                        jQuery(".get_pages_statuses").click(function() {
                             $('.get_pages_statuses').attr('disabled',true).val('Processing...');
                             $('.script_progress').show();
                            for(var i=0;i <= script_running_times;i++){

                                var processed_pages_count = i*pages_count_per_script;

                                if(processed_pages_count>pages_count){
                                    processed_pages_count=pages_count;
                                }
                                $('.script_percent').html(processed_pages_count + " of "+pages_count)

                                $('.script_bar').css({width:i*100/script_running_times+'%'})

                                $.ajax({
                                    url: "index3.php",
                                    data: {
                                        option:'com_aliases',
                                        task:'update_pages_codes',
                                        start_page:processed_pages_count,
                                        process_page_limit:pages_count_per_script
                                    },
                                    type: 'post',
                                    async:false,
                                    success: function (data) {
                                        {
                                            $('.get_pages_statuses').removeAttr('disabled').val(data)
                                        }
                                    }
                                });

                            }
                        });
  </script>
            <table class="adminlist">
                <tr>
                    <th width="2%">
                        #
                    </th>
                    <th width="2%">
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo sizeof($rows); ?>);" />
                    </th>
                    <th style="text-align: left;" width="20%">
                        From
                    </th>
                    <th style="text-align: left;" width="20%">
                        To
                    </th>
                    <th style="text-align: left;" width="20%">
                        Type
                    </th>
                    <th style="text-align: left;" width="10%">
                        Page type
                    </th>
                    <th style="text-align: left;" width="10%">
                        Page Status Code
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
                                <?php echo htmlspecialchars($row->from); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->to); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($redirect_types[$row->status]); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($page_types[$row->type]); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars(($row->status_code)?$row->status_code:''); ?>
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
        <style>
            .script_progress {
                position: relative;
                width: 400px;
                border: 1px solid #ddd;
                height: 20px;
                margin-bottom: 10px;
                display: none;
            }
            .script_bar {
                background-color: #B4F5B4;
                width: 0%;
                height: 20px;
                left: 0;
                float: left;
            }
            .script_percent {
                display: inline-block;
                margin-top: 3px;
            }
        </style>
        <?php
    }
    
}

