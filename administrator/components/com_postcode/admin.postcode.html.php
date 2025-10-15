<?php
//
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_postcode
{
    function show($result)
    {
       ?>
        <form action="index2.php?option=com_postcode&task=add" enctype="multipart/form-data" method="post" name="insert-form">
        <table class="adminlist">
            <tr>
                <th width="13%" align='left'>
                    Postcodes
                </th>
                <th width="13%" align='left'>
                    First available date
                </th>
                <th width="13%" align='left'>
                    Reason
                </th>
                <th width="13%" align='left'>
                    Insert
                </th>
            </tr>
            <tr>
                <td align="left">
                    <textarea name="postcodes" cols="80" rows="3"></textarea>
                </td>
                <td align="left">
                     <input type="text" name="date_others" id="datepicker"/>
                </td>
                <td align="left">
                    <input type="text" name="reason"/>
                </td>
                <td align="left">
                    <input type="submit" name="insert" value="Insert" >
                </td>
            </tr>
        </table>
        </form>
        <table class="adminlist">
        <tr>
        <th width="13%" align='left'>
            Edit
        </th>
        <th width="13%" align='left'>
            Postcodes
        </th>
        <th width="13%" align='left'>
            Active
        </th>
        <th width="13%" align='left'>
            First available date
        </th>
        <th width="13%" align='left'>
            Reason
        </th>
        <th width="13%" align='left'>
            Delete
        </th>

        <?php

        if ($result)
        {
            foreach ($result as $item)
            {
                echo '</tr><tr>';
                echo '
                <td><a href="index2.php?option=com_postcode&amp;task=edit&amp;id='.$item->id.'">'.$item->id.'</a></td>
                <td>'.$item->postcodes.'</td>
                <td><input type="checkbox" id="active'.$item->id.'" onchange="ActiveCode('.$item->id.');" value="'.($item->active == 0 ? '1"' : '0" checked').'></td>
                <td>'.$item->date_others.'</td><td>'.$item->reason.'</td>
                <td><a href="index2.php?option=com_postcode&amp;task=delete&amp;id='.$item->id.'">Delete</a></td>';
            }
            echo '</tr></table>';
        }
    }

    function edit($result)
    {
        if ($result)
        {
        ?>
        <form action="index2.php?option=com_postcode&task=save&id=<?php echo $result->id; ?>" method="post" name="insert-form">
        <table class="adminlist">
            <tr>
                <th width="13%" align='left'>
                    Postcodes
                </th>
                <th width="13%" align='left'>
                    First available date
                </th>
                <th width="13%" align='left'>
                    Reason
                </th>
                <th align="left">
                    Update
                </th>
            </tr>
            <tr>
                <td align="left">
                    <textarea name="postcodes" cols="80" rows="10"><?php echo $result->postcodes;?></textarea>
                </td>
                <td align="left">
                    <input type="text" name="date_others" id="datepicker" value="<?php echo $result->date_others;?>" />
                </td>
                <td align="left">
                    <input type="text" name="reason" value="<?php echo $result->reason;?>" />
                </td>
                <td align="left">
                    <input type="submit" name="update" value="Update" >
                </td>
            </tr>
        </table>
        </form>
        <?php
        }
    }
}

?>