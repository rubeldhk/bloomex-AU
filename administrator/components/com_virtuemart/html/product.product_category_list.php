<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 

mm_showMyFileName( __FILE__ );
global $ps_product_category;

mosCommonHTML::loadBootstrap4(true);

$categories = $ps_product_category->getCategories($keyword );

?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<style>
    .fa {
        margin-right: 5px;
    }
    .table {
        width: calc(100% - 25px);
        margin-left: 25px;
    }
    
    tr.find {
        background-color: #bfffdc;
    }
    .form-inline .form-group {
        display: block;
    }
</style>
<?php


function setCategory($categories, $level) {
    //str_repeat('___', $level)

    if (sizeof($categories) > 1) {
        ?>
        <tr>
            <td width="2%">
            <?php echo mosHTML::idBox( 0, $categories['info']->category_id, false, "category_id" );?>
            </td>
            <td width="3%">
                <a href="#item-<?php echo $categories['info']->category_id; ?><?php echo ($level > 0) ? '-'.$level : ''; ?>" data-toggle="collapse" class="tr_a">
                    <i class="fa fa-chevron-right"></i><?php echo $categories['info']->category_id; ?>
                </a> 
            </td>
            <td width="30%" class="category_name">
                <?php //echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level).'['.$level.']'; &nbsp;&nbsp;&nbsp;&nbsp;?> 
                <a href="./index2.php?option=com_virtuemart&page=product.product_category_form&category_id=<?php echo $categories['info']->category_id; ?>">
                    <?php echo $categories['info']->category_name; ?>
                </a>
            </td>
            <td width="40%" class="category_description">
                <?php echo strip_tags($categories['info']->category_description); ?>
            </td>
            <td width="20%" align="center">
                <a target="_blank" href="./index2.php?option=com_virtuemart&page=product.product_list&category_id=<?php echo $categories['info']->category_id; ?>">
                    <?php echo ps_product_category::product_count($categories['info']->category_id); ?> product(s)
                </a>
            </td>
            <td width="5%" align="center">
                <a href="./index2.php?option=com_virtuemart&page=product.product_category_list&category_id=<?php echo $categories['info']->category_id; ?>&func=changePublishState&task=<?php echo $categories['info']->category_publish == 'N' ? 'publish' : 'unpublish'; ?>">
                    <?php echo vmCommonHTML::getYesNoIcon($categories['info']->category_publish); ?>
                </a>
            </td>
        </tr>
        <tr id="item-<?php echo $categories['info']->category_id; ?><?php echo ($level > 0) ? '-'.$level : ''; ?>" class="collapse">
            <td colspan="5">
                <table class="table">
        <?php
    }
    else {
        ?>
        <tr>
            <td width="2%">
                <?php echo mosHTML::idBox( 0, $categories['info']->category_id, false, "category_id" );?>
            </td>
            <td width="3%">
                <?php echo $categories['info']->category_id; ?>
            </td>
            <td width="30%" class="category_name">
                <?php //echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level).'['.$level.']'; &nbsp;&nbsp;&nbsp;&nbsp;?> 
                
                <a href="./index2.php?option=com_virtuemart&page=product.product_category_form&category_id=<?php echo $categories['info']->category_id; ?>">
                    <?php echo $categories['info']->category_name; ?>
                </a>
            </td>
            <td width="40%" class="category_description">
                <?php echo strip_tags($categories['info']->category_description); ?>
            </td>
            <td width="20%" align="center">
                <a target="_blank" href="./index2.php?option=com_virtuemart&page=product.product_list&category_id=<?php echo $categories['info']->category_id; ?>">
                    <?php echo ps_product_category::product_count($categories['info']->category_id); ?> product(s)
                </a>
            </td>
            <td width="5%" align="center">
                <a href="./index2.php?option=com_virtuemart&page=product.product_category_list&category_id=<?php echo $categories['info']->category_id; ?>&func=changePublishState&task=<?php echo $categories['info']->category_publish == 'N' ? 'publish' : 'unpublish'; ?>">
                    <?php echo vmCommonHTML::getYesNoIcon($categories['info']->category_publish); ?>
                </a>
            </td>
        </tr>
        <?php
    }
    /*
    if (sizeof($categories) > 1) {
        ?>
        
        <a href="#item-<?php echo $categories['info']->category_id; ?><?php echo ($level > 0) ? '-'.$level : ''; ?>" class="list-group-item" data-toggle="collapse">
            <i class="fa fa-chevron-right"></i><?php echo $categories['info']->category_name; ?> <span onclick="alert('ewfwf'); return false;">Google</span>
        </a>
<div class="col-1"><a href="">test</a></div>
        <div class="list-group collapse" id="item-<?php echo $categories['info']->category_id; ?><?php echo ($level > 0) ? '-'.$level : ''; ?>">
        <?php
    }
    else {
        ?>
        <a href="#" class="list-group-item"><?php echo $categories['info']->category_name; ?></a>
        <?php
    }
    */
    
    
    unset($categories['info']);
    
    if (sizeof($categories) > 0) {
        foreach ($categories as $category) {
            setCategory($category, $level+1);
        }
        ?>
                </table>
            </td>
        </tr>
        <?php
    }
}

//
//echo '<pre>';
//    print_r($categories);
//echo '</pre>';

?>
<form class="form-inline">
    <div class="form-group mb-3">
        <label for="query" class="sr-only">Search</label>
        <input type="text" class="form-control" id="query" placeholder="Search">
    </div> 
</form>
<?php
$listObj = new listFactory();
$listObj->writeSearchHeader('', '', $modulename, '');
?>
<table class="adminlist" width="100%">
    <thead>
        <tr>
            <th width="2%">#</th>
            <th width="3%">ID</th>
            <th width="30%">Name</th>
            <th width="40%">Description</th>
            <th width="20%" style="text-align: center;">Products</th>
            <th width="5%" style="text-align: center;">Published</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($categories as $category) {
            echo setCategory($category, 0);
        }
        ?>
    </tbody>
</table>
<?php
$listObj->writeFooter('');
/*
?>
<div class="just-padding">
    <div class="list-group list-group-root well">
        <?php
        foreach ($categories as $category) {
            echo setCategory($category, 0);
        }
        ?>
    </div>
</div>
*/
?>
<script type="text/javascript">
    $(function() {
        $('.tr_a').on('click', function() {
            $('.fa', this)
            .toggleClass('fa-chevron-right')
            .toggleClass('fa-chevron-down');
        });
        
        jQuery('#query').keyup(function() {
            jQuery('tr').removeClass('find');
            jQuery('tr.collapse').removeClass('show');
            
            const query = jQuery(this).val().toLowerCase();
            
            if (query != '') {
                jQuery('.category_name, .category_description').each(function(k, v) {
                    if (jQuery(this).text().toLowerCase().indexOf(query) != -1) {
                        jQuery(this).parent('tr').addClass('find').focus();
                        jQuery(this).parents('tr.collapse').addClass('show');
                    }
                });
            }
        });

        var el = document.getElementById("query");
        el.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
            }
        });

  });
</script>
