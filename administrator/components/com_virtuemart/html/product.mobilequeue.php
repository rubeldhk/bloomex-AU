<?php

mm_showMyFileName( __FILE__ );
global $database, $ps_product, $ps_product_category;

if ($task == 'move') 
{
    ob_get_clean();
    
    $id = isset($_POST['id_mobile_queue']) ? (int)$_POST['id_mobile_queue'] : 0;
    $updown = $_POST['updown'];
    
    $row = null;
    
    if ($id)
    {
        $query = "SELECT * FROM `tbl_mobile_queue` WHERE `id_mobile_queue`=".$id;
    
        $database->setQuery($query);
        $row = $database->loadRow();
        
        if ($row)
        {

            $query = "SELECT * FROM `tbl_mobile_queue` WHERE `id_category`=".$row[2]." AND `queue` ".($updown == 'up' ? '<' : '>')." ".$row[3]." ORDER BY `queue` LIMIT 1";
    
            $database->setQuery($query);
            $new_row = $database->loadRow();


            $query = "UPDATE `tbl_mobile_queue` SET `queue`=".$row[3]." WHERE `id_mobile_queue`=".$new_row[0]."";

            $database->setQuery($query);
            $database->query();

            $query = "UPDATE `tbl_mobile_queue` SET `queue`=".$new_row[3]." WHERE `id_mobile_queue`=".$row[0]."";

            $database->setQuery($query);
            $database->query();
        }
    }
    exit(0);
}

if ($task == 'save') 
{
    ob_get_clean();
    $category_id = isset($_POST['category_id_new']) ? (int)$_POST['category_id_new'] : 0;
         
    foreach ($_POST['queues'] AS $k => $v)
    {
        $query = "UPDATE `tbl_mobile_queue` SET `queue`=".$v." WHERE `id_mobile_queue`=".$k."";

        $database->setQuery($query);
        $database->query();  
    }
    
    mosRedirect( "index2.php?option=com_virtuemart&page=product.mobilequeue&category_id=".$category_id, "" );
    exit(0);
}

if ($task == 'move_new') 
{
    ob_get_clean();
    
    $id = isset($_POST['id_mobile_queue']) ? (int)$_POST['id_mobile_queue'] : 0;
    $queue = isset($_POST['queue']) ? (int)$_POST['queue'] : 0;
    
    $row = null;
    
    if ($id)
    {
        $query = "UPDATE `tbl_mobile_queue` SET `queue`=".$queue." WHERE `id_mobile_queue`=".$id."";

        $database->setQuery($query);
        $database->query();  
    }
    
    exit(0);
}

if ($task == 'all')
{
    ob_get_clean();
     
    $query = "SELECT `category_id` FROM `jos_vm_category`";
   
    $database->setQuery($query);
    $c_rows = $database->loadObjectList();
    
    foreach ($c_rows as $c_row)
    {
        $query = "SELECT `p`.`product_id`, `p`.`product_sku`, `p`.`product_name`, `q`.`queue`, `c`.`category_id` FROM `jos_vm_product_category_xref` as `x`
            LEFT JOIN `jos_vm_category` as `c` ON `c`.`category_id`=`x`.`category_id`
            LEFT JOIN `jos_vm_product` as `p` ON `p`.`product_id`=`x`.`product_id`
            LEFT JOIN `tbl_mobile_queue` as `q` ON `q`.`id_product`=`x`.`product_id` AND `q`.`id_category`=`x`.`category_id`
        WHERE `x`.`category_id`=".$c_row->category_id." GROUP BY `p`.`product_id`";

        $database->setQuery($query);
        $rows = $database->loadObjectList();

        $i = 1;

        foreach ($rows as $row)
        {
            $query = "SELECT * FROM `tbl_mobile_queue` WHERE `id_category`=".$row->category_id." ORDER BY `queue` DESC LIMIT 1";

            $database->setQuery($query);
            $new_row = $database->loadRow();

            if ($new_row)
            {
                $queue = $new_row[3]+1;
            }
            else
            {
                $queue = $i;
            }

            if ($row->queue < 1)
            {
                $query = "INSERT INTO `tbl_mobile_queue` (`id_product`, `id_category`, `queue`) VALUES (".$row->product_id.", ".$row->category_id.", ".$queue.")";

                $database->setQuery($query);
                $database->query();
            }

            $i++;
        }
    }
    exit(0);
}

$category_id = isset($_GET['category_id']) ? (int)$_REQUEST['category_id'] : 0;

?>
Category: 
<select class="inputbox" id="category_id" name="category_id" onchange="window.location='<?php echo $_SERVER['PHP_SELF'] ?>?option=com_virtuemart&page=product.mobilequeue&category_id='+document.getElementById('category_id').options[selectedIndex].value;">
<option value=""><?php echo _SEL_CATEGORY ?></option>
<?php $ps_product_category->list_tree( $category_id );  ?>
</select>
<?php


if ($category_id > 0)
{
    $query = "SELECT `p`.`product_id`, `p`.`product_sku`, `p`.`product_name`, `q`.`queue`, `c`.`category_id` FROM `jos_vm_product_category_xref` as `x`
        LEFT JOIN `jos_vm_category` as `c` ON `c`.`category_id`=`x`.`category_id`
        LEFT JOIN `jos_vm_product` as `p` ON `p`.`product_id`=`x`.`product_id`
        LEFT JOIN `tbl_mobile_queue` as `q` ON `q`.`id_product`=`x`.`product_id` AND `q`.`id_category`=`x`.`category_id`
    WHERE `x`.`category_id`=".$category_id." GROUP BY `p`.`product_id`";
   
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    
    $i = 1;
    
    foreach ($rows as $row)
    {
        $query = "SELECT * FROM `tbl_mobile_queue` WHERE `id_category`=".$row->category_id." ORDER BY `queue` DESC LIMIT 1";
    
        $database->setQuery($query);
        $new_row = $database->loadRow();
        
        if ($new_row)
        {
            $queue = $new_row[3]+1;
        }
        else
        {
            $queue = $i;
        }
        
        if ($row->queue < 1)
        {
            $query = "INSERT INTO `tbl_mobile_queue` (`id_product`, `id_category`, `queue`) VALUES (".$row->product_id.", ".$row->category_id.", ".$queue.")";
    
            $database->setQuery($query);
            $database->query();
        }
        
        $i++;
    }
    
    $query = "SELECT `p`.`product_id`, `p`.`product_sku`, `p`.`product_name`, `q`.`queue`, `q`.`id_mobile_queue`, `c`.`category_id`, `pr`.`product_price`, `pr`.`saving_price` FROM `jos_vm_product_category_xref` as `x`
        INNER JOIN `jos_vm_category` as `c` ON `c`.`category_id`=`x`.`category_id`
        INNER JOIN `jos_vm_product` as `p` ON `p`.`product_id`=`x`.`product_id`
        LEFT JOIN `tbl_mobile_queue` as `q` ON `q`.`id_product`=`x`.`product_id` AND `q`.`id_category`=`x`.`category_id`
        LEFT JOIN `jos_vm_product_price` as `pr` ON `pr`.`product_id`=`x`.`product_id`
    WHERE `x`.`category_id`=".$category_id." GROUP BY `p`.`product_id` ORDER BY `q`.`queue` ASC";

    $database->setQuery($query);
    $rows = $database->loadObjectList();
    
    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    //$pageNav = new mosPageNav($total, $limitstart, $limit);
    $n = sizeof($rows);
    $total = $n;
    $limit = $n;
    $limitstart = 0;
    
    //$pageNav = new mosPageNav(sizeof($rows), 0, sizeof($rows));
    $i = 1;
    ?>
    <script type="text/javascript">
        
        function MobileQueueMove(id_mobile_queue, updown)
        {
            $.ajax({
            url: 'index2.php',
            type: "POST",
            data: {option: 'com_virtuemart', page: 'product.mobilequeue', task: 'move', id_mobile_queue: id_mobile_queue, updown: updown},
            success: function(data)
            {
                window.location.reload();
            },
            dataType: 'html'
            });
        }
        
        $(document).ready(function() {
            /*$(".new_queue").change(function() 
            {
                id_mobile_queue = $(this).attr('id');
                queue = $(this).attr('value');
                
                $('#l_'+id_mobile_queue).show();
                
                $.ajax({
                    url: 'index2.php',
                    type: "POST",
                    data: {option: 'com_virtuemart', page: 'product.mobilequeue', task: 'move_new', id_mobile_queue: id_mobile_queue, queue: queue},
                    success: function(data)
                    {
                        window.location.reload();
                    },
                    dataType: 'html'
                });
            });*/
            
            $('#new_button').click(function()
            {       
                $('#adminForm').submit();
                window.location.reload();
            });
        });
        
    </script>
    
    <form action="index2.php?option=com_virtuemart&page=product.mobilequeue" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <tr>
            <th width="10%">
                Product
            </th>
            <th width="10%">
                Price
            </th>
            <th align="center" width="5%"><!--colspan="2"-->
                <input type="submit" id="" value="Save"> Queue
            </th>
        </tr>
    <?php
    foreach ($rows as $row)
    {
        ?>
        <tr>
            <td>
                <a href="index2.php?page=product.product_form&product_id=<?php echo $row->product_id;?>&option=com_virtuemart" target="_blank"><?php echo $row->product_name;?></a><br/>[SKU: <?php echo $row->product_sku;?>]<!-- | <?php echo $row->queue;?>-->
            </td>
            <td>
                $<?php echo number_format($row->product_price*1.1 - $row->saving_price, 2); ?>   
            </td>
            <td>
                <input type="number" class="new_queue" name="queues[<?php echo $row->id_mobile_queue; ?>]" id="<?php echo $row->id_mobile_queue; ?>" value="<?php echo $row->queue; ?>">
                <span id="l_<?php echo $row->id_mobile_queue; ?>" style="display: none;">Loading...</span>
            </td> 
            <!--
            <td align="right">
                <?php 
                if ($i > 1) 
                {
                    ?>
                    <a href="#move_up" onclick="MobileQueueMove('<?php echo $row->id_mobile_queue; ?>', 'up')"><img src="images/uparrow.png" width="12" height="12" border="0" alt="Move Up"></a>
                    <?php
                }
                ?>
            </td>
            
            <td align="left">
                <?php
                if ($i != $n)
                {
                    ?>
                    <a href="#move_down" onclick="MobileQueueMove('<?php echo $row->id_mobile_queue; ?>', 'down')"><img src="images/downarrow.png" width="12" height="12" border="0" alt="Move Down"></a>
                    <?php
                }
                ?>
            </td>
            -->
        </tr>
        <?php
        $i++;
    }
    ?>
    </table>
    <input type="hidden" name="option" value="com_virtuemart">
    <input type="hidden" name="sectionid" value="0">
    <input type="hidden" name="task" value="save">
    <input type="hidden" name="boxchecked" value="0">
    <input type="hidden" name="hidemainmenu" value="0">
    <input type="hidden" name="redirect" value="0">
    <input type="hidden" name="category_id_new" value="<?php echo $category_id; ?>">
    </form>
    <?php
}