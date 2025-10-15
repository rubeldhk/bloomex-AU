<?php

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class HTML_LLP 
{
    
    function default_view($ps_product_category, $rows)
    {
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>
                        Landing Pages Manager:
                    </th>
                </tr>
            </table>
            <table class="adminlist">
            <?php
            foreach ($rows as $row)
            {
                ?>
                <tr>
                    <td>
                        <span style="font-weight: bold; font-size: 20px; font-family: monospace;"><?php echo $row->name_llp; ?></span>
                        <br/>
                        Category:
                    </td>
                    <td>
                        <br/>
                        <select class="inputbox" name="category_id[]">
                            <option value=""><?php echo _SEL_CATEGORY ?></option>
                            <?php $ps_product_category->list_tree($row->category_id_llp);  ?>
                        </select>
                    </td>
                </tr>
                <?php
            }
            ?>
            </table>
            <input type="hidden" name="option" value="com_llp" />
            <input type="hidden" name="task" value="save" />
        </form>
    <?php
    }
    
}
?>