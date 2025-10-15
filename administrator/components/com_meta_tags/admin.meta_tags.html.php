<?php

defined('_VALID_MOS') or die('Restricted access');

Class HTML_MetaTags {
    
    public function edit_new($option, $row = false) {
        ?>
        <form action="index2.php" method="post" name="adminForm">

                    <table class="adminlist" >
                        <tr>
                            <th>
                                Url*:
                            </th>
                            <td>
                                <input type="text" name="url" value="<?php echo $row->url; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Title:
                            </th>
                            <td>
                                <input type="text" name="title" value="<?php echo $row->title; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                H1
                            </th>
                            <td>
                                <input type="text" name="h1" value="<?php echo $row->h1; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Description
                            </th>
                            <td>
                                <textarea class="text_area" id="description" rows="8" name="description"><?php echo $row->description;?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Keywords
                            </th>
                            <td>
                                <textarea class="text_area" id="keywords" rows="8" name="keywords"><?php echo $row->keywords;?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Comment
                            </th>
                            <td>
                                <textarea class="text_area" id="comment" name="comment"><?php echo $row->comment;?></textarea>
                            </td>
                        </tr>
                    </table>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />

            <style>
                table.adminlist a {
                    color: #C64934 !important;
                }
                table.adminlist th {
                    background-image: none !important;
                    font-weight: bold;
                    font-size: 15px;
                }
                table.adminlist input{
                    line-height: 24px;
                    font-size: 14px;
                    width: 90%;
                    padding: 11px;
                }
                table.adminlist textarea{
                    width: 90%;
                    padding: 11px;
                    font-size: 14px;
                    background: transparent !important;
                }
            </style>
        </form>
        <?php
    }
    
    public function default_list($option,  $rows, $pageNav, $search) {
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
                    <th style="text-align: left;" width="10%">
                        URL
                    </th>
                    <th style="text-align: left;" width="20%">
                        Tytle
                    </th>
                    <th style="text-align: left;" width="15%">
                        H1
                    </th>
                    <th style="text-align: left;" width="20%">
                        Description
                    </th>
                    <th style="text-align: left;" width="20%">
                        Keywords
                    </th>
                    <th style="text-align: left;" width="10%">
                        Comment
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
                                <?php echo htmlspecialchars($row->url); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->title); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->h1); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->description); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->keywords); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->comment); ?>
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

