<?php

include_once './config.php';
$type = $_POST['type'];
$id_info = (int) $_POST['id_info'];
$number_id = (int) $_POST['number_id'];

$return_html = array();
if ($type == 'abandonment') {
    $return_html['left'] = loadAbandonment($id_info, $mosConfig_live_site);
} elseif($type == 'ocassion') {
    $return_html['left'] = loadOcassion($id_info, $mosConfig_live_site);
}else{
    $number = $_POST['number'];
    $return_html['left'] = loadCallBack($number);
}


$return_html['right'] = '<strong><h2>Comments about call</h2></strong><div class="end_button_div">
    <textarea name="note" id="note"></textarea>    
        <p>
            <input type="button" onclick="reSetInterval(' . $id_info . ', \'' . htmlspecialchars($type) . '\', \'DONE\', \'SALE\');" value="SALE" class="btn btn-success end_button" >
        </p>
        <p>
            <input type="button" onclick="reSetInterval(' . $id_info . ', \'' . htmlspecialchars($type) . '\', \'DONE\', \'MESSAGE\');" value="MESSAGE"  class="btn btn-info end_button">
        </p>
        <p>
            <input type="button" onclick="reSetInterval(' . $id_info . ', \'' . htmlspecialchars($type) . '\', \'DONE\', \'END CALL\');" value="END CALL"  class="btn btn-success end_button">
        </p>
        <p>
            <input type="button" onclick="reSetInterval(' . $id_info . ', \'' . htmlspecialchars($type) . '\', \'NEVER\', \'NEVER\');" value="REMOVE" class=" btn btn-danger never_button">
        </p>
        <p>
            <input type="button" onclick="reSetInterval(' . $id_info . ', \'' . htmlspecialchars($type) . '\', \'DEFAULT\', \'CALLBACK\');" value="CALL BACK"  class="btn btn-warning later_button">
        </p>
        <p>
            <input type="button" id="downloadCallsMade" onclick="downloadCallsMade(\'' . htmlspecialchars($type) . '\');" value="TODAY\'S REPORT"  class="btn btn-primary end_button">
        </p>';

        $return_html['right'] .= ' <p>
            <input type="button" onclick="reSetInterval(' . $id_info . ', \'' . htmlspecialchars($type) . '\', \'DONE\', \'VOICEMAIL\');" value="VOICE MAIL"  class="btn btn-info voicemail_button">
        </p>';

$return_html['right'] .= '<p style="padding: 10px;visibility:hidden">
            <input type="button" onclick="reSetInterval(' . $id_info . ', \'' . htmlspecialchars($type) . '\', \'TIMEOUT\', \'TIMEOUT\');" value="TIME OUT"  class="btn btn-info  time_button">
        </p>     
        <p style="padding: 10px;visibility:hidden">
            <input type="button" onclick="reSetInterval(' . $id_info . ', \'' . htmlspecialchars($type) . '\', \'DONE\', \'POPUP\');" value="POP UP"  class="btn btn-info pop_up_button">
        </p> 
        <input type="hidden" name="id_info" id="id_info" value="'.$id_info.'"/>
        <input type="hidden" name="type" id="type" value="'.htmlspecialchars($type).'"/>
        <input type="hidden" name="number_id" id="number_id" value="'.$number_id.'"/>
    </div>';
$mysqli->close();
echo json_encode($return_html);

function loadCallBack($number) {
    $call_back_html= '<a target="_blank" href="https://bloomex.ca/administrator/index2.php?option=com_phoneorder&order_call_type=CallBack">
                    <img src="https://bloomex.ca/administrator/images/new_navi_icon/phone_order_manager.png" alt="Phone Order Manager" align="middle" border="0"> <span>Phone Order Manager</span>
                    </a>
                    <input type="hidden" id="number" value="'.$number.'">
                    <br><br>
                    
                     <div class="form-group">
                        <label for="order">Order:</label>
                        <select class="form-control" id="order">
                        <option value="0">No</option>
                        <option value="1">yes</option>
                        </select>
                    </div>
                    ';
    return $call_back_html;
}
function loadOcassion($id_info, $link,$mosConfig_live_site) {
    global $mysqli;
    
    $order_id = $id_info;
    $table_user = '';
    $table_details = '';
    
    $query_bill = "SELECT 
        `u`.`first_name`,
        `u`.`user_id`,
        `o`.`order_id`,
        `u`.`last_name`,
        `u`.`user_email`,
        `ui`.`city`,
        `ui`.`address_1`,
        `ui`.`zip`,
        `ui`.`first_name` AS 'recipient_name',
        `o`.`order_total`,
        `oc`.`order_occasion_name`,
        `o`.`ddate`,
        `o`.`customer_note`,
        `o`.`customer_comments`
    FROM  `jos_vm_order_user_info` AS `u`
    LEFT JOIN `jos_vm_orders` AS `o` 
        ON
        `o`.`order_id`=`u`.`order_id`
    LEFT JOIN `jos_vm_order_user_info` AS `ui` 
        ON 
        `ui`.`order_id`=`u`.`order_id`
    LEFT JOIN `jos_vm_order_occasion` AS `oc` 
        ON 
        `oc`.`order_occasion_code`=`o`.`customer_occasion`
    WHERE 
        `u`.`address_type`='BT' 
        AND 
        `ui`.`address_type`='ST' 
        AND 
        `u`.`order_id`=".$order_id."
    ";
    
    $result = $mysqli->query($query_bill);
    
    if (!$result) {
        die('Select error: ' . $mysqli->error);
    }

    if ($result->num_rows > 0) {
        $obj_bill = $result->fetch_object();
        $user_id = $obj_bill->user_id;
        
        $table_user = "<strong><h2>Information About Customer </h2></strong><table border='1' class='table table-hover'> 
        <tr>
            <th>First Name</th>
            <td>".$obj_bill->first_name."</td>
        </tr>
         <tr>
            <th>Last  Name</th>
            <td>".$obj_bill->last_name."</td>
        </tr>
        <tr>
             <th>Email</th>
             <td>".$obj_bill->user_email."</td>
        </tr>
        <tr>
            <th>Recipient Name</th>
            <td>".$obj_bill->recipient_name."</td>
        </tr>
        <tr>
            <th>Customer city</th>
            <td>".$obj_bill->city."</td>
        </tr>
        <tr>
            <th>Customer Address</th>
            <td>".$obj_bill->address_1."</td>
        </tr>
        <tr>
            <th>Zip</th>
            <td>".$obj_bill->zip."</td>
        </tr>
        <tr>
            <th>Order Id</th>
            <td>".$obj_bill->order_id."</td>
        </tr>
        <tr>
            <th>Order Total</th>
            <td>".$obj_bill->order_total."</td>
        </tr>
        <tr>
            <th>Order Ocassion</th>
            <td>".$obj_bill->order_occasion_name."</td>
        </tr>
        <tr>
            <th>Order Delivery Date</th>
            <td>".$obj_bill->ddate."</td>
        </tr>
        <tr>
            <th>Link</th>
            <td><a target='_blank' href=".$mosConfig_live_site . "/administrator/index2.php?option=com_phoneorder&order_call_type=Ocassion&user_id=".$user_id.">Link</a></td>
        </tr>    
        </tr></table><br><br>";
    };
    
    $result->close();

    $query_order_details = "SELECT 
        * 
        FROM `jos_vm_order_item` 
        WHERE `order_id`=".$order_id."
    ";
    
    $result = $mysqli->query($query_order_details);
    
    if (!$result) {
        die('Select error: ' . $mysqli->error);
    }

    $cart_products = '';
    if ($result->num_rows > 0) {
        $table_details = "<strong><h2>Order Details </h2></strong>
        <table class='table table-hover'>
           <tr class=\"active\">
                <th>Item Name</th>
                <th>Item Sku</th>
                <th>Item Quantity</th>
            </tr>";
        while ($obj_order = $result->fetch_object()) {
            $table_details .= "<tr>
                <td>".$obj_order->order_item_name."</td>
                <td>".$obj_order->order_item_sku."</td>
                <td>".$obj_order->product_quantity."</td>
            </tr>";
            $cart_products.=$obj_order->product_id.','.$obj_order->product_quantity.";";
        }
        $table_details .= "</table>";
    }
    
    $result->close();
    
    $cart_products = rtrim($cart_products,';');
    $send_email_div='<div class="send_email_div">
        <div class="form-group">
        <strong><h2>Send Email with 20% off coupon code and cart link</h2></strong>
          <input type="email" required class="form-control" id="user_email" value="'.$obj_bill->user_email.'">
        </div>
        <input type="hidden" id="cart_products" value="?cart_products='.$cart_products.'">
        <input type="hidden" id="first_name" value="'.$obj_bill->first_name.'">
        <input type="button" class="btn btn-success send_email_btn" onclick="send_email()" value="Send Email">
    </div>';

    return ($table_user . $table_details.$send_email_div);
}

function loadAbandonment($id_info, $mosConfig_live_site) {
    global $mysqli;
    
    $table_user = '';
    $table_abandonment = '';

    $query = "SELECT 
        * 
    FROM `tbl_cart_abandonment` 
    WHERE  
        `id`='".$id_info."'
    ";

    $result = $mysqli->query($query);
    
    if (!$result) {
        die('Select error: ' . $mysqli->error);
    }

    if ($result->num_rows > 0) {
        $obj = $result->fetch_object();
        $product_ies = '';
        $user_id = $obj->user_id;
        if ($obj->link != '') {
            $link = $obj->link;
            $link = substr(trim($link), 15);
            $products = explode(';', $link);
            $products = array_filter($products);

            $i = count($products);
            foreach ($products as $p) {
                $i--;
                $p = explode(',', $p);
                if ($i == 0)
                    $product_ies .= $p[0];
                else
                    $product_ies .= $p[0] . ',';
            }
        }

        $query_bill = "SELECT 
            * 
        FROM  `jos_vm_user_info` 
        WHERE  
            `user_id` ='".$user_id."'
            AND  
            `address_type` LIKE 'BT'
        ";
        
        $result_bill = $mysqli->query($query_bill);
    
        if (!$result_bill) {
            die('Select error: ' . $mysqli->error);
        }

        if ($result_bill->num_rows > 0) {
            $obj_bill = $result_bill->fetch_object();

            $table_user = "<strong><h2>Information About Customer </h2></strong><table border='1' class='table table-hover'>
            <tr>
                <th>First Name</th>
                <td>".$obj_bill->first_name."</td>
            </tr>
            <tr>
                <th>Last  Name</th>
                <td>".$obj_bill->last_name."</td>
            </tr>
            <tr>   
                <th>Customer city</th>
                <td>".$obj_bill->city."</td>
            </tr>
            <tr>
                <th>Customer Address</th>
                <td>".$obj_bill->address_1."</td>
            </tr>
            <tr>
                <th>Zip</th>
                <td>".$obj_bill->zip."</td>
            </tr>
            <tr>
                <th>Number</th>
                <td>".$obj_bill->number."</td>
            </tr>
            <tr>    
                <th>Card Message</th>
                <td>".$obj_bill->card_message."</td>
            </tr>
            <tr>    
                <th>Link</th>
                <td><a target='_blank' href=".$mosConfig_live_site . "/administrator/index2.php?option=com_phoneorder&order_call_type=Abandonment&user_id=".$user_id."&" . ltrim($obj->link, '?').">Link</a></td>
            </tr></table><br><br>";
        }
        
        $result_bill->close();

        if ($product_ies != '') {

            $query_abandonment_products = "SELECT 
                `product_name`,
                `product_sku` 
            FROM `jos_vm_product` 
            WHERE 
                `product_id` IN (".$product_ies.")
            ";
            
            $result_products = $mysqli->query($query_abandonment_products);
    
            if (!$result_products) {
                die('Select error: ' . $mysqli->error);
            }

            if ($result_products->num_rows > 0) {
                $table_abandonment = "<strong><h2>Product List </h2></strong><table border='1' class='table table-hover'>";
                while ($obj_product = $result_products->fetch_object()) {
                    $table_abandonment .= "<tr>
                        <td>".$obj_product->product_name."</td>
                        <td>".$obj_product->product_sku."</td>
                    </tr>";
                };
                $table_abandonment .= "</table>";
            }
        }


        // for api2 abandonments
        if ($obj->products != '' && $obj->project !='') {
            $table_user = "<strong><h2>Information About Customer </h2></strong>";
            $table_user .= "<strong><h5>Abandonment From Site ".$obj->project."</h5></strong>";
            $table_user .= "<table border='1' class='table table-hover'>
                                <tr>
                                    <th>Email</th>
                                    <td>".$obj->first_name."</td>
                                </tr>
                                <tr>
                                    <th>Number</th>
                                    <td>".$obj->number."</td>
                                </tr> 
                                <tr>
                                    <th>Products</th>
                                    <td>".$obj->products."</td>
                                </tr>
                                <tr>
                                    <th>Link</th>
                                    <td><a target='_blank' href='/administrator/index2.php?option=com_phoneorder&order_call_type=Abandonment'>Link</a></td>
                                </tr>
                            </table>
                            <br><br>";
        }
    }
    $result->close();
    
    return ($table_user . $table_abandonment);
}

?>