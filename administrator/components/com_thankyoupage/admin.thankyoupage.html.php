<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

Class ThankYouPage_Images
{
    function display($rows)
    {
//        echo '<pre>';
//            print_r($rows);
//        echo '</pre>';
        ?>
        <script type="text/javascript">
        
            function Move(id, updown)
            {
                $.ajax({
                url: 'index2.php',
                type: "POST",
                data: {option: 'com_thankyoupage', task: 'move', id: id, updown: updown},
                success: function(data)
                {
                    window.location.reload();
                },
                dataType: 'html'
                });
            }
        
        </script>
        
        <form action="index2.php" method="post" name="adminForm">
        <table class="adminlist">
            <tr>
                <th width="5%" align='left'>
                    Id
                </th>
                <th width="5%" class="title">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
                </th>
                <th width="13%" align='left'>
                    Image
                </th>
                <th width="13%" align='left'>
                    URL
                </th>  
                <th width="5%" align="center">
                    Published
                </th>
                <th colspan="2" width="5%">
                    Queue
                </th>
            </tr>
            
            <?php
            $n = sizeof($rows);
            if ($n > 0)
            {
                $i = 1;
 
                foreach ($rows as $row)
                {
                    $checked = mosCommonHTML::CheckedOutProcessing( $row, $i );
                    
                    $img = $row->publish ? 'tick.png' : 'publish_x.png';
                    $task = $row->publish ? 'unpublish' : 'publish';
                    $alt = $row->publish ? 'Published' : 'Unpublished';
                    ?>
                    <tr>
                        <td>
                            <!--<a href="index2.php?option=com_thankyoupage&amp;task=edit&amp;id=<?php echo $row->id; ?>"><?php echo $row->id; ?></a>-->
                            <?php echo $row->id; ?>
                        </td>
                        <td>
                            <?php echo $checked; ?>
                        </td>
                        <td>
                            <?php echo $row->image_link; ?>
                        </td>
                        <td>
                            <?php echo $row->url; ?>
                        </td>
                        <td align="center">
                            <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
                                <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                            </a>
                        </td>
                        <td align="right">
                            <?php 
                            if ($i > 1) 
                            {
                                ?>
                                <a href="#move_up" onclick="Move('<?php echo $row->id; ?>', 'up')"><img src="images/uparrow.png" width="12" height="12" border="0" alt="Move Up"></a>
                                <?php
                            }
                            ?>
                        </td>
                        <td align="left">
                            <?php
                            if ($i != $n)
                            {
                                ?>
                                <a href="#move_down" onclick="Move('<?php echo $row->id; ?>', 'down')"><img src="images/downarrow.png" width="12" height="12" border="0" alt="Move Down"></a>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            
            ?>
        </table>
        <input type="hidden" name="option" value="com_thankyoupage" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="hidemainmenu" value="0">		
        </form>
        <?php
    }
    
    function edit($row = '')
    {
        //<!--value="<?php echo isset($row[1]) ? $row[1] : ''; "-->
        ?>
        <!--<form enctype="multipart/form-data" action="index2.php?option=com_thankyoupage&task=save&id=<?php echo isset($row[0]) ? $row[0] : ''; ?>" method="post">-->
            <form enctype="multipart/form-data" action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <td>Image</td>
                    <td><input type="file" name="image"></td>
                <tr/>
                <tr>
                    <td>URL</td>
                    <td><input type="text" maxlength="255" name="url" value="<?php echo isset($row[2]) ? $row[2] : ''; ?>"></td>
                </tr>
                <!--
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Save">
                    </td>
                </tr>
                -->
            </table>
            
            <input type="hidden" name="option" value="com_thankyoupage">
            <input type="hidden" name="task" value="">
            <input type="hidden" name="id" value="<?php echo isset($row[0]) ? $row[0] : ''; ?>">
        </form>
        <?php
    }
}
