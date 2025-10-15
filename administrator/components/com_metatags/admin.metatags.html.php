<?php

Class HTML_ComMetaTags {
    
    public function edit_new($option, $row) {
        mosCommonHTML::loadCKeditor();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist" >
                <tr>
                    <td>
                        Url*:
                    </td>
                    <td>
                        <input type="text" name="url" value="<?php echo isset($row->url) ? $row->url : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Page type:
                    </td>
                    <td>
                        <select name="page_type">
                            <option value="0" <?php echo (isset($row->page_type) AND $row->page_type == '0') ? 'selected' : ''; ?>>Default</option>
                            <option value="1" <?php echo (isset($row->page_type) AND $row->page_type == '1') ? 'selected' : ''; ?>>Category</option>
                            <option value="2" <?php echo (isset($row->page_type) AND $row->page_type == '2') ? 'selected' : ''; ?>>Product</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        For city:
                    </td>
                    <td>
                        <input type="checkbox" name="city" value="1" <?php echo (isset($row->city) AND $row->city== '1') ? 'checked' : ''; ?>>
                    </td>
                </tr>
                <tr>
                    <td>
                        Landing type:
                    </td>
                    <td>
                        <select name="landing_type">
                            <option value="0" <?php echo (isset($row->landing_type) AND $row->landing_type == '0') ? 'selected' : ''; ?>>Default</option>
                            <option value="1" <?php echo (isset($row->landing_type) AND $row->landing_type == '1') ? 'selected' : ''; ?>>Old florist online</option>
                            <option value="2" <?php echo (isset($row->landing_type) AND $row->landing_type == '2') ? 'selected' : ''; ?>>Old baskets</option>
                            <option value="3" <?php echo (isset($row->landing_type) AND $row->landing_type == '3') ? 'selected' : ''; ?>>Old sympathy</option>
                            <option value="3" <?php echo (isset($row->landing_type) AND $row->landing_type == '4') ? 'selected' : ''; ?>>Old flower delivery</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Title:
                    </td>
                    <td>
                        <input type="text" name="title" value="<?php echo isset($row->title) ? $row->title : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Meta Description
                    </td>
                    <td>
                        <textarea class="text_area" id="description" rows="8" name="description"><?php echo isset($row->description) ? $row->description : ''; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        Keywords
                    </td>
                    <td>
                        <textarea class="text_area" id="keywords" rows="8" name="keywords"><?php echo isset($row->keywords) ? $row->keywords : ''; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        H1
                    </td>
                    <td>
                        <input type="text" name="h1" value="<?php echo isset($row->h1) ? $row->h1 : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                         Description
                    </td>
                    <td>
                        <textarea class="text_area" id="description_text" rows="8" name="description_text"><?php echo isset($row->description_text) ? $row->description_text : ''; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        Footer Description
                    </td>
                    <td>
                        <textarea class="text_area" id="description_text_footer" rows="8" name="description_text_footer"><?php echo isset($row->description_text_footer) ? $row->description_text_footer : ''; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        Comment
                    </td>
                    <td>
                        <textarea class="text_area" id="comment" name="comment"><?php echo isset($row->comment) ? $row->comment : ''; ?></textarea>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo isset($row->id) ? $row->id : ''; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <script type="text/javascript">
            CKEDITOR.replace('description_text');
            CKEDITOR.replace('description_text_footer');
        </script>
        <style>
            table.adminlist input[type="text"] {
                width: 90%;
            }
            table.adminlist textarea{
                width: 90%;
            }
            <?php
            /*
            table.adminlist a {
                color: #C64934 !important;
            }
            table.adminlist th {
                background-image: none !important;
                font-weight: bold;
                font-size: 15px;
            }
            table.adminlist input[type="text"] {
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
            * 
            */
            ?>
        </style>
        <?php
    }
    
    public function default_list($option, $rows, $pageNav, $search) {
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
                    <th>
                        URL
                    </th>
                    <th>
                        Page type
                    </th>
                    <th>
                        For city
                    </th>
                    <th>
                        Landing type
                    </th>
                    <th>
                        Title
                    </th>
                    <th>
                        Meta Description
                    </th>
                    <th>
                        Keywords
                    </th>
                    <th>
                        H1
                    </th>
                    
                    <th>
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
                            <?php
                            if ((int)$row->page_type == 0) echo 'Default';
                            if ((int)$row->page_type == 1) echo 'Category';
                            if ((int)$row->page_type == 2) echo 'Product';
                            ?>
                        </td>
                        <td>
                            <?php
                            if ((int)$row->city == 0) echo 'No';
                            if ((int)$row->city == 1) echo 'Yes';
                            ?>
                        </td>
                        <td>
                            <?php
                            if ((int)$row->landing_type == 0) echo 'Default';
                            if ((int)$row->landing_type == 1) echo 'Old florist online';
                            if ((int)$row->landing_type == 2) echo 'Old baskets';
                            if ((int)$row->landing_type == 3) echo 'Old sympathy';
                            if ((int)$row->landing_type == 4) echo 'Old flower delivery';
                            ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->title); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->description); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->keywords); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->h1); ?>
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

?>

