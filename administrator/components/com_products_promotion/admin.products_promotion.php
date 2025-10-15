<?php

class productsPromotion
{
    static $names = array('Product', 'Sku', 'Product Image', 'Week Day','Date Range', 'Discount', 'Category', 'Public','Edit', 'Delete');
    static $week_days = array('Monday', 'Tuesdays', 'Wednesdays', 'Thursdays','Fridays', 'Saturdays', 'Sundays');


    function getActiveProducts()
    {
        global $database;
        $query = "SELECT product_id,concat(product_name,' (',product_sku,')') as product
                FROM jos_vm_product where product_publish = 'Y'";
        $database->setQuery($query);
        $products = $database->loadObjectList();
        return mosHTML::selectList($products, "product_id", "size='1' class='inputbox d-block form-control selectpicker product_or_category_list product_list'  data-live-search='true'", "product_id", "product");

    }

    function getActiveCategories()
    {
        global $database;
        $query = "SELECT category_id,category_name
                FROM jos_vm_category where category_publish = 'Y'";
        $database->setQuery($query);
        $categories = $database->loadObjectList();
        return mosHTML::selectList($categories, "category_id", "size='1' class='d-none inputbox form-control selectpicker product_or_category_list category_list'  data-live-search='true'", "category_id", "category_name");
    }

    function &createTemplate()
    {
        global $option, $mosConfig_absolute_path;
        require_once($mosConfig_absolute_path
            . '/includes/patTemplate/patTemplate.php');
        $tmpl = &patFactory::createTemplate($option, true, false);
        $tmpl->setRoot(dirname(__FILE__) . '/tmp');
        return $tmpl;
    }


    // -----------------------------------------------------------------------------------------------------------------------------
    function handleRequest()
    {
        global $database;

        if(!isset($_POST['func']))
            return true;

        $return = [];
        $return['result'] = false;
        $return['error'] = '';
        $query = '';
        switch ($_POST['func']){
            case 'delete':
                $query = "DELETE FROM jos_vm_products_promotion where id='{$_POST['prom_id']}'";
            break;
            case 'update_promotion_list':
                exit(self::view());
            case 'add':
            case 'update':
                $public = (isset($_POST['publish']) && $_POST['publish'] == 'true') ? 1 : 0;

                if($_POST['date_range_or_week_day'] == 'week_day'){
                    $_POST['start_promotion'] = '';
                    $_POST['end_promotion'] = '';
                } else {
                    $_POST['week_day'] = '';
                }
                if($_POST['product_or_category'] == 'product'){
                    $_POST['category_id'] = '';
                } else {
                    $_POST['product_id'] = '';
                }

                if($_POST['func'] == 'add') {
                    $query = "INSERT INTO `jos_vm_products_promotion`
                        (
                            `product_id`,
                            `category_id`,
                            `public`, 
                            `start_promotion`, 
                            `end_promotion`, 
                            `week_day`, 
                            `discount`
                        ) 
                        VALUES (
                            '" . $database->getEscaped($_POST['product_id']) . "',
                            '" . $database->getEscaped($_POST['category_id']) . "',
                            '$public',
                            '" . $database->getEscaped($_POST['start_promotion']) . "',
                            '" . $database->getEscaped($_POST['end_promotion']) . "',
                            '" . $database->getEscaped($_POST['week_day']) . "',
                            '" . $database->getEscaped($_POST['discount']) . "'
                        )";
                } else {
                    $query = "UPDATE `jos_vm_products_promotion` SET
                            `product_id` = '" . $database->getEscaped($_POST['product_id']) . "',
                            `category_id` = '" . $database->getEscaped($_POST['category_id']) . "',
                            `public` = '$public', 
                            `start_promotion` = '" . $database->getEscaped($_POST['start_promotion']) . "', 
                            `end_promotion` = '" . $database->getEscaped($_POST['end_promotion']) . "', 
                            `week_day` = '" . $database->getEscaped($_POST['week_day']) . "', 
                            `discount` = '" . $database->getEscaped($_POST['discount']) . "'
                            where id=".$_POST['prom_id'];
                }

                break;
        }
        if ($query) {
            $database->setQuery($query);
            if ($database->query()) {
                $return['result'] = true;
                $return['msg'] = "Process Executed Successful.";
            } else {
                $return['error'] = $database->getErrorMsg();
            }
        }

        echo json_encode($return);
        exit;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function view()
    {
        global $database;

        $html = '<form action="/administrator/index2.php?option=com_products_promotion" method="post">';
        $html .= '<div class="form-group col-md-2 col-md-offset-9 padding0">
                    <input type="text" style="float: right" class="form-control" name="keyword" value="' . ($_POST['keyword'] ?? '') . '" placeholder="Search">
                </div>
                <div class="form-group col-md-1 padding0">
                    <button type="submit" name="search" style="width: 100%" class=" btn btn-primary">Search</button>
                </div>';
        $html .= '</form>';
        $htmlTable = '<table class="adminlist"><tr>';
        foreach (self::$names as $value) {
            $htmlTable .= "<th>$value</th>";
        }
        $htmlTable .= '</tr>';

        $where = '';
        if (isset($_POST['keyword']) && isset($_POST['search'])) {
            $where = 'WHERE 
              p.product_name LIKE "%' . $database->getEscaped($_POST['keyword']) . '%" OR
              c.category_name LIKE "%' . $database->getEscaped($_POST['keyword']) . '%" OR
              p.product_sku LIKE "%' . $database->getEscaped($_POST['keyword']) . '%"';
        }
        $query = "SELECT pp.*,
p.product_name as product,
p.product_sku as sku,
p.product_thumb_image,
p.product_full_image,
c.category_name as category 
FROM jos_vm_products_promotion  as pp 
left join jos_vm_product as p on p.product_id=pp.product_id
left join jos_vm_category as c on c.category_id=pp.category_id $where
ORDER BY pp.category_id DESC ";

        $database->setQuery($query);
        $result = $database->loadObjectList();

        if ($result) {
            foreach ($result as $item) {
                $htmlTable .= "<tr class='item' prom_id='{$item->id}'>";
                foreach (self::$names as $value) {
                    switch ($value) {
                        case 'Edit':
                            $htmlTable .= "<td>
                                            <input type='button' class='btn btn-info edit' name='edit' prom_id='{$item->id}' value='$value' />
                                           </td>";
                            break;
                        case 'Delete':
                            $htmlTable .= "<td>
                                            <input type='button' class='btn btn-danger delete' name='delete' prom_id='{$item->id}' value='$value' />
                                           </td>";
                            break;
                        case 'Product Image':

                            if (file_exists($_SERVER['DOCUMENT_ROOT'].'/components/com_virtuemart/shop_image/product/'.$item->product_thumb_image)) {
                                $productImage = $item->product_thumb_image;

                            } else {
                                $productImage = $item->product_full_image;
                            }

                            $htmlTable .= "<td>"
                                .($item->product_thumb_image?"<img src='/components/com_virtuemart/shop_image/product/{$productImage}' style='width: 100px'>":"").
                                "</td>";

                            break;
                        case 'Public':
                            $publicValue = $item->{strtolower($value)};
                            $htmlTable .= "<td>
                                                " . (($publicValue == '1') ? 'Yes' : 'No') . "
                                          </td>";
                            break;
                        case 'Date Range':
                            $htmlTable .= "<td>
                                            " . (($item->start_promotion!='0000-00-00')?$item->start_promotion:'') . "  -  " . (($item->end_promotion!='0000-00-00')?$item->end_promotion:'') . "
                                          </td>";
                            break;
                        case 'Week Day':
                            $htmlTable .= "<td>
                                 " . self::$week_days[$item->week_day] . "
                                </td>";
                            break;
                        case 'Product':
                            $htmlTable .= "<td>
                                        <a href='/administrator/index2.php?page=product.product_form&product_id={$item->product_id}&option=com_virtuemart' target='__blank'>{$item->product}</a>
                                        </td>";
                            break;
                        case 'Category':
                            $htmlTable .= "<td>
                                        <a href='/administrator/index2.php?page=product.product_category_form&category_id={$item->category_id}&option=com_virtuemart' target='__blank'>{$item->category}</a>
                                        </td>";
                            break;


                        case 'Discount':
                            $htmlTable .= "<td>{$item->{strtolower($value)}}%</td>";
                            break;
                        default:
                            $htmlTable .= "<td>{$item->{strtolower($value)}}</td>";
                            break;
                    }
                }
                $htmlTable .= "<input type='hidden' class='hidden_product_id' value='{$item->product_id}'>";
                $htmlTable .= "<input type='hidden' class='hidden_category_id' value='{$item->category_id}'>";
                $htmlTable .= "<input type='hidden' class='hidden_week_day' value='{$item->week_day}'>";
                $htmlTable .= "<input type='hidden' class='hidden_start_promotion' value='{$item->start_promotion}'>";
                $htmlTable .= "<input type='hidden' class='hidden_end_promotion' value='{$item->end_promotion}'>";
                $htmlTable .= "<input type='hidden' class='hidden_discount' value='{$item->discount}'>";
                $htmlTable .= "<input type='hidden' class='hidden_public' value='{$item->public}'>";
                $htmlTable .= '</tr>';
            }
        }


        $html .= $htmlTable . '</table>';


        return $html;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function handle()
    {
        self::handleRequest();
        $tmpl = &self::createTemplate();
        $tmpl->setAttribute('body', 'src', 'products_promotion.html');
        $tmpl->addVar('body', 'action', $_SERVER['REQUEST_URI']);
        $tmpl->addVar('body', 'products', self::getActiveProducts());
        $tmpl->addVar('body', 'categories', self::getActiveCategories());
        $tmpl->addVar('body', 'view', self::view());
        $tmpl->displayParsedTemplate('form');
    }

}

productsPromotion::handle();
?>