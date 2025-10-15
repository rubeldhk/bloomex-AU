<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

Class HTML_extensions
{
    function edit_extension($row)
    {
        ?>

        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
            <tr>
                <th class="edit">
                    Extension Item:
                    <small>
                        <?php echo $row ? 'Edit' : 'New'; ?>
                    </small>
                </th>
            </tr>
            </table>


                        <table width="100%" class="adminform">
                            <tr>
                                <th colspan="2">
                                    Item Details
                                </th>
                            </tr>
                            <tr>
                                <td width="100%">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td>
                                                Extension:
                                            </td>
                                            <td>
                                                <input class="text_area" type="text" name="ext" size="30" maxlength="100" id="ext" value="<?php echo $row->ext; ?>" <?php echo ($row ? 'readonly' : ''); ?> />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Abandonment:
                                            </td>
                                            <td>
                                                <select name="abandonment">
                                                    <option <?php echo ($row->abandonment ? '' : 'selected'); ?> value="0">Off</option>
                                                    <option  <?php echo ($row->abandonment ? 'selected' : ''); ?> value="1">On</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Occassion:
                                            </td>
                                            <td>
                                                <select name="occassion">
                                                    <option <?php echo ($row->occassion ? '' : 'selected'); ?> value="0">Off</option>
                                                    <option  <?php echo ($row->occassion ? 'selected' : ''); ?> value="1">On</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Call Back:
                                            </td>
                                            <td>
                                                <select name="call_back">
                                                    <option <?php echo ($row->call_back ? '' : 'selected'); ?> value="0">Off</option>
                                                    <option  <?php echo ($row->call_back ? 'selected' : ''); ?> value="1">On</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Access:
                                            </td>
                                            <td>
                                                <select name="access">
                                                    <option <?php echo ($row->access ? '' : 'selected'); ?> value="0">Off</option>
                                                    <option  <?php echo ($row->access ? 'selected' : ''); ?> value="1">On</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        </table>

            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="option" value="com_extensions" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />

        </form>

        <?php
    }
    
    function default_extension($search, $rows, $pageNav)
    {
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
                <th style="text-align: left;" width="20%">
                    Extension
                </th>
                <th style="text-align: left;" width="20%">
                    Abandonment
                </th>
                <th style="text-align: left;" width="20%">
                    Occassion
                </th>
                <th style="text-align: left;" width="20%">
                    Call Back
                </th>
                <th style="text-align: left;" width="20%">
                    Access
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
                        <a href="index2.php?option=com_extensions&task=edit&hidemainmenu=1&id=<?php echo $row->id; ?>" title="Edit Extension">
                            <?php echo $row->ext; ?>
                        </a>
                    </td>
                    <td align="left">
                        <?php echo ($row->abandonment == 1) ? 'On' : 'Off'; ?>
                    </td>
                    <td align="left">
                        <?php echo ($row->occassion == 1) ? 'On' : 'Off'; ?>
                    </td>
                    <td align="left">
                        <?php echo ($row->call_back == 1) ? 'On' : 'Off'; ?>
                    </td>
                    <td align="left">
                        <?php echo ($row->access == 1) ? 'On' : 'Off'; ?>
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
            <input type="hidden" name="option" value="com_extensions" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="hidemainmenu" value="0" />
            <input type="hidden" name="boxchecked" value="0" />
        </form> 
        <?php 
    }
}

?>
