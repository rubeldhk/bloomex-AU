<?php

defined('_VALID_MOS') or die('Restricted access');

Class HTML_footerLinks {
    
    public function edit_new($option, $row = false,$categories_list) {
        mosCommonHTML::loadCKeditor();
                        
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <td width="10%">
                        Name:
                    </td>
                    <td >
                        <input value="<?php echo $row->name; ?>" name="name">
                    </td>
                </tr>
                <tr>
                    <td>
                        Type:
                    </td>
                    <td >
                        <select name="type" class="text_type">
                            <option value="">--select type--</option>
                            <option value="default" <?php echo (($row->type=='default')?'selected':'');?>>default</option>
                            <option value="category" <?php echo (($row->type=='category')?'selected':'');?>>category</option>
                        </select>
                    </td>
                </tr>
                <tr class="ref"  <?php echo (($row->type=='category' || $row->type=='default')?'style="display: none"':'');?>>
                    <td >
                        Ref:
                    </td>
                    <td >
                        <input value='<?php echo $row->ref; ?>' name="ref">
                    </td>
                </tr>
                <tr class="categories" <?php echo (($row->type=='category')?'':'style="display: none"');?>>
                    <td>
                        Categories:
                    </td>
                    <td>
                        <?php echo $categories_list; ?>
                        <input type="button" id="clearCategories" value="Clear Selected values">
                    </td>
                </tr>
                <tr>
                    <td>
                        Html:
                    </td>
                    <td>
                        <textarea id="html" name="html" cols="100" rows="10"><?php echo (isset($row->html) ? $row->html : ''); ?></textarea>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="id" value="<?php echo (isset($row->id) ? $row->id : ''); ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
        </form>

        <script type="text/javascript">
            CKEDITOR.replace('html');
            jQuery(document).ready(function(){

                jQuery('#clearCategories').click(function(e) {
                    e.preventDefault();

                    jQuery('select[name="categories[]"]').find('option:selected').attr('selected', false);
                    jQuery('select[name="categories[]"]').change();
                });

                jQuery('.text_type').change(function () {
                    if(jQuery(this).val()=='category'){
                        jQuery('.categories').show()
                        jQuery('.ref').hide()
                    }else{
                        jQuery('.categories').hide()
                        jQuery('.ref').show()
                    }
                })
            });
        </script>
        <?php
    }
    
    public function default_list($option, $rows, $pageNav) {
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
                        Name
                    </th>
                    <th style="text-align: left;" width="20%">
                        Type
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
                                <?php echo htmlspecialchars($row->name); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row->type); ?>
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

