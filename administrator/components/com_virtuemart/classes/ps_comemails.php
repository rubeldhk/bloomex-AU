<?php

Class ps_comemails {

    var $bt_obj = false;
    var $st_obj = false;
    var $vendor_obj = false;
    var $driver_obj = false;
    var $change_obj = false;
    var $banner_obj = false;
    var $sefClass = null;


    public $isLastMinuteOrder = false;

    public $isLastMinuteOrderLabel = '';

    function __construct($type = '') {
        global $mosConfig_absolute_path;
        if (!class_exists('database')) {
            //we are using joomla libraries
            if (!(defined('_VALID_MOS'))) {
                define('_VALID_MOS', 1);
            }

            //have to declare mosconfig variables and database as global
            global $database, $mosConfig_mailfrom, $mosConfig_smtphost, $mosConfig_smtppass, $mosConfig_smtpuser, $mosConfig_smtpport, $mosConfig_smtpprotocol, $mosConfig_live_site, $mosConfig_mailfrom;
            global $mosConfig_user_adm, $mosConfig_user, $mosConfig_password_adm, $mosConfig_password, $mosConfig_host, $mosConfig_db, $mosConfig_dbprefix;
            if (!$mosConfig_live_site) {
                include dirname(__FILE__) . "/../../../../configuration.php";
            }
            $mosConfig_user = $mosConfig_user_adm ?? $mosConfig_user;
            $mosConfig_password = $mosConfig_password_adm ?? $mosConfig_password;
            include_once dirname(__FILE__) . "/../../../../includes/joomla.php";
        }
        $GLOBALS["sefLib"] = true;
        global $sef;
        require_once $mosConfig_absolute_path . '/includes/router.php';
    }

    function getPartnerInfo($order_id) {
        global $database;
        $query = "SELECT * FROM `jos_vm_api2_orders` ao "
                . "where ao.order_id = $order_id";
        $order = false;
        $database->setQuery($query);
        $database->loadObject($order);
        return $order;
    }

    /**
     * Sends email from correct email account (bloomex/parthners)
     * @param string $email_subject
     * @param string $email_text
     * @param int $order_id 
     * @param string $to 
     */
    function send($email_subject, $email_html, $order_id, $to) {
        global $database;
        if (!class_exists('MAIL5')) {
            require_once dirname(__FILE__) . "/../../../../includes/MAIL5/MAIL5.php";
        }

        $api2partner = false;

        $query = "SELECT * FROM `jos_vm_api2_orders` ao  where ao.order_id like {$order_id}";
        $database->setQuery($query);
        $database->loadObject($api2partner);

        $m = new MAIL5;
        if ($m->AddTo($to)) {
            $m->Subject($email_subject);
            $m->Html($email_html, 'utf-8');

//            if ($api2partner) {
//                $from = $api2partner->email;
//                $host = $api2partner->smtp_host;
//                $port = intval($api2partner->smtp_port); //fucking crash othervise
//                $login = $api2partner->smtp_login;
//                $pass = $api2partner->smtp_password;
//                $protocol = 'ssl';
//            } else {
                global $mosConfig_mailfrom_noreply, $mosConfig_smtphost, $mosConfig_smtppass, $mosConfig_smtpuser, $mosConfig_smtpport, $mosConfig_smtpprotocol;
                $from = $mosConfig_mailfrom_noreply;
                $host = $mosConfig_smtphost;
                $port = intval($mosConfig_smtpport);
                $login = $mosConfig_smtpuser;
                $pass = $mosConfig_smtppass;
                $protocol = $mosConfig_smtpprotocol;
            //}
            $m->from($from);
            $c = $m->Connect($host, $port, $login, $pass, $protocol, 20);
            $return = $m->Send($c);
            return $return;
        } else {
            return false;
        }
    }

    /**

     * Gets correct email text based type

     * @param int $emailType

     */

    function getEmailTextByType(int $emailType, $recipientType)
    {
        global $database;

        $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='$emailType' AND `recipient_type`='$recipientType'";
        $emailObj = false;
        $database->setQuery($query);
        $database->loadObject($emailObj);

        if(!$emailObj){
            throw new Exception('Email template not found');
        }

        return $emailObj;
    }

    /**
     * Gets correct email text based on order origin
     * @param int $order_id
     * @param string $order_status_code default updated
     * @return boolean
     */
    function get_email_text($order_id, $order_status_code = '') {
        global $database;

        $query = "SELECT `order_id` FROM `jos_vm_api2_orders` WHERE `order_id`='{$order_id}'";
        $order_from_api2 = false;
        $database->setQuery($query);
        $database->loadObject($order_from_api2);

        $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='2' AND `recipient_type`='1' AND `order_status_code`='" . $order_status_code . "'";
        $query.=($order_from_api2 == true) ? " AND for_foreign_orders='1'":" AND for_foreign_orders='0'";
        $confirmation_obj = false;
        $database->setQuery($query);
        $database->loadObject($confirmation_obj);

        if (!$confirmation_obj) {
            $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='2' AND `recipient_type`='1'";
            $query.=($order_from_api2 == true) ? " AND for_foreign_orders='1'":" AND for_foreign_orders='0'";
            $database->setQuery($query);
            $database->loadObject($confirmation_obj);
        }
        return $confirmation_obj;
    }

    function createCoupon($length, $prefix) {
        global $database;

        $letters_array = range('A', 'Z');
        $numbers_array = range(0, 9);

        $all_array = array_merge($letters_array, $numbers_array);

        $coupon_code = $prefix . '';

        for ($i = 1; $i <= $length; $i++) {
            $coupon_code .= $all_array[array_rand($all_array)];
        }

        $database->setQuery($query);
        $database->loadObject($this->driver_obj);

        $query = "SELECT `coupon_id` FROM `jos_vm_coupons` WHERE `coupon_code`='" . $coupon_code . "'";
        $coupon_obj = false;
        $database->setQuery($query);
        $database->loadObject($coupon_obj);

        if ($coupon_obj == false) {
            return $coupon_code;
        } else {
            unset($coupon_obj);
            create_coupon($length, $prefix);
        }
    }

    private function getDriverInfo($order_id) {
        global $database, $global_return;

        $return = array();
        $return['result'] = false;
        $return['driver_obj'] = false;

        if (!$this->driver_obj) {
            $this->driver_obj = false;

            $query = "SELECT 
                `d`.`driver_name`,
                `d`.`description`
            FROM `jos_vm_routes_orders` AS `ro`
            INNER JOIN `jos_vm_routes` AS `r` ON `r`.`id`=`ro`.`route_id`
            INNER JOIN `tbl_driver_option` AS `d` ON `d`.`id`=`r`.`driver_id`
            WHERE `ro`.`order_id`=" . $order_id . "";

            $database->setQuery($query);
            $database->loadObject($this->driver_obj);

            if ($this->driver_obj) {
                $return['result'] = true;
                $return['driver_obj'] = $this->driver_obj;
            } else {
                $global_return['errors'][] = 'Driver not found.';
            }
        } else {
            $return['result'] = true;
            $return['driver_obj'] = $this->driver_obj;
        }

        return $return;
    }

    private function getDateChangeOrderStatus($order_id, $order_status_code) {
        global $database, $global_return;

        $return = array();
        $return['result'] = false;
        $return['change_obj'] = false;

        $this->change_obj = false;

        $query = "SELECT 
            `date_added`
        FROM `jos_vm_order_history` 
        WHERE `order_id`=" . $order_id . " AND `order_status_code`='" . $order_status_code . "'
        ORDER BY `order_status_history_id` ASC LIMIT 1";

        $database->setQuery($query);
        $database->loadObject($this->change_obj);

        if ($this->change_obj) {
            $return['result'] = true;
            $return['change_obj'] = $this->change_obj;
        } else {
            $global_return['errors'][] = 'Not found.';
        }

        return $return;
    }

    private function getVendorInfo($vendor_id) {
        global $database, $global_return;

        $return = array();
        $return['result'] = false;
        $return['vendor_obj'] = false;

        if (!$this->vendor_obj) {
            $this->vendor_obj = false;

            $query = "SELECT * FROM `jos_vm_vendor`
            WHERE `vendor_id`=" . $vendor_id . "";

            $database->setQuery($query);
            $database->loadObject($this->vendor_obj);

            if ($this->vendor_obj) {
                $return['result'] = true;
                $return['vendor_obj'] = $this->vendor_obj;
            } else {
                $global_return['errors'][] = 'Vendor not found.';
            }
        } else {
            $return['result'] = true;
            $return['vendor_obj'] = $this->vendor_obj;
        }

        return $return;
    }

    private function getBTInfo($order_id) {
        global $database;
        $query = "SELECT * FROM `jos_vm_order_user_info`
            WHERE `order_id`=" . $order_id . " AND `address_type`='BT'";
        $database->setQuery($query);
        $database->loadObject($this->bt_obj);
    }

    private function getSTInfo($order_id) {
        global $database;
        $query = "SELECT * FROM `jos_vm_order_user_info`
            WHERE `order_id`=" . $order_id . " AND `address_type`='ST'";
        $database->setQuery($query);
        $database->loadObject($this->st_obj);
    }

    function getCanonicalProduct($alias, $relative = false) {
        global $database;
        $query = "SELECT
            `c`.`alias`
        FROM `jos_vm_product` AS `p`
        INNER JOIN `jos_vm_product_options` AS `po`
            ON `po`.`product_id`=`p`.`product_id`
        INNER JOIN `jos_vm_category` AS `c`
            ON `c`.`category_id`=`po`.`canonical_category_id`
            AND
            `c`.`category_publish`='Y'
        WHERE
            `p`.`alias`='" . $alias . "'
            AND
            `p`.`product_publish`='Y'
        ";



        $database->setQuery($query);

        $category_obj = false;

        $canonical = '';

        if ($database->loadObject($category_obj)) {
            $canonical = $this->getCanonicalCategory($category_obj->alias, $relative);
        } else {
            $query = "SELECT
                `c`.`alias`
            FROM `jos_vm_product` AS `p`
            INNER JOIN `jos_vm_product_category_xref` AS `pc_x`
                ON
                `pc_x`.`product_id`=`p`.`product_id`
            INNER JOIN `jos_vm_category` AS `c`
                ON
                `c`.`category_id`=`pc_x`.`category_id`
                AND
                `c`.`category_publish`='Y'
            WHERE 
                `p`.`alias`='" . $alias . "'
                AND
                `p`.`product_publish`='Y'
            GROUP BY `c`.`alias`
            ORDER BY `c`.`category_id` DESC LIMIT 1";

            $database->setQuery($query);

            $category_obj = false;

            $canonical = '';
            $database->loadObject($category_obj);
            if ($category_obj) {
                $canonical = $this->getCanonicalCategory($category_obj->alias, $relative);
            }
        }
        if ($canonical) {
            return $canonical . $alias . '/';
        } else {
            return '';
        }
    }

    function getCanonicalCategory($alias, $relative = false) {

        global $mosConfig_live_site, $database;

        $aliases = [];

        $category_parent_id = 1;

        $i = 1;
        while ($category_parent_id > 0) {
            $query = "SELECT
                `c`.`category_id`,
                `c`.`alias`,
                `c2`.`alias` AS `parent_alias`,
                `c_x`.`category_parent_id`
            FROM `jos_vm_category` AS `c`
            LEFT JOIN `jos_vm_category_xref` AS `c_x`
                ON `c_x`.`category_child_id`=`c`.`category_id`
            LEFT JOIN `jos_vm_category` AS `c2`
                ON `c2`.`category_id`=`c_x`.`category_parent_id`
            WHERE
                `c`.`alias`='" . $alias . "'
                AND  
                `c`.`category_publish`='Y'
            ";

            $database->setQuery($query);

            $category_obj = false;
            if ($database->loadObject($category_obj)) {

                $alias = $category_obj->parent_alias;
                $aliases[] = $category_obj->alias;
                $category_parent_id = $category_obj->category_parent_id;
            } else {
                $category_parent_id = 0;
            }

            $i++;
        }

        return $mosConfig_live_site . (sizeof($aliases) > 0 ? '/' . implode('/', array_reverse($aliases)) . '/' : '');
    }

    private function getItemsInfo($order_id) {
        global $database, $global_return, $mosConfig_live_site,$mosConfig_aws_s3_bucket_public_url;

        $return = array();
        $return['result'] = false;
        $return['order_items'] = false;

        $query = "SELECT `oi`.*, `s`.`medium_image_link_webp`,p.alias
        FROM `jos_vm_order_item` AS `oi`
        LEFT JOIN `jos_vm_product` AS `p` ON `p`.`product_id`=`oi`.`product_id`
        LEFT JOIN `jos_vm_product_s3_images` AS `s` ON `s`.`product_id`=`p`.`product_id`
        WHERE `oi`.`order_id`=" . $order_id . "";

        $database->setQuery($query);
        $items_obj = $database->loadObjectList();

        if (sizeof($items_obj) > 0) {

            $return['result'] = true;

            $return['order_items'] = '<table style="width: 500px" cellspacing="0" cellpadding="0" align="center">';
            $return['order_items'] .= '<tbody>';

            $it = 1;
            foreach ($items_obj as $item_obj) {
                $url = $this->getCanonicalProduct($item_obj->alias, true);
                $return['order_items'] .= '<tr>
                    <td style="padding: 10px; width: 99px; text-align: center; vertical-align: top;" height="160px">';
                if($item_obj->medium_image_link_webp) {
                    $return['order_items'] .= '<a target="_blank" href="' . $url . '?utm_source=email&utm_medium=email-confirmation&utm_campaign=product">
                                                <img src="' . $mosConfig_aws_s3_bucket_public_url . $item_obj->medium_image_link_webp . '" width="140" height="163" style="border-width: 0px" alt=" ">
                                               </a> ';
                } else {
                    $return['order_items'] .= '<img src="https://bloomex.com.au/components/com_virtuemart/shop_image/product/nophoto.jpg" width="140" height="163" style="border-width: 0px" alt=" ">';
                }
                $return['order_items'] .= '</td><td style="padding: 10px; font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #333333; vertical-align: top; line-height: 20px; text-align: left" height="160px">
                    <a target="_blank" href="' . $url . '?utm_source=email&utm_medium=email-confirmation&utm_campaign=product" style="text-decoration: none; color: #9467CB; border-bottom-style: dotted; border-bottom-width: 1px; border-bottom-color: #7B54AF">' . $item_obj->order_item_name . '</a><br>
                    <strong>SKU:</strong> ' . $item_obj->order_item_sku . '<br>
                    <strong>Quantity:</strong> ' . $item_obj->product_quantity . '<br>
                    <strong>Product Price:</strong> $' . number_format($item_obj->product_item_price, 2, '.', ' ') . '<br>
                    <strong>Total:</strong> $' . number_format($item_obj->product_item_price * intval($item_obj->product_quantity), 2, '.', ' ') . '</td>
                </tr>';

                $it++;
            }
            $return['order_items'] .= '</tbody></table>';
            /*
              $return['order_items'] = '<table width="100%">';
              $return['order_items'] .= '<tr style="background-color: #E0E0E0;color: #000;font-weight: bold;">
              <td width="5%" style="border-top: 5px solid red;border: 1px solid black;">#</td>
              <td width="50%" style="border-top: 5px solid red;border: 1px solid black;">Product Name</td>
              <td width="15%" style="border-top: 5px solid red;border: 1px solid black;">SKU</td>
              <td width="5%" style="border-top: 5px solid red;border: 1px solid black;">Quantity</td>
              <td width="15%" style="border-top: 5px solid red;border: 1px solid black;">Product Price</td>
              <td width="15%" style="border-top: 5px solid red;border: 1px solid black;">Total</td>
              </tr>';

              $it = 1;
              foreach ($items_obj as $item_obj) {
              $return['order_items'] .= '<tr style="background-color: #DFE7EF">
              <td style="border: 1px solid #ccc;">'.$it.'. </td>
              <td style="border: 1px solid #ccc;">'.$item_obj->order_item_name.'<br/></td>
              <td style="border: 1px solid #ccc;">'.$item_obj->order_item_sku.'</td>
              <td style="border: 1px solid #ccc;">'.$item_obj->product_quantity.'</td>
              <td style="border: 1px solid #ccc;">$'.number_format($item_obj->product_item_price, 2, '.', ' ').'</td>
              <td style="border: 1px solid #ccc;">$'.number_format($item_obj->product_item_price * intval($item_obj->product_quantity), 2, '.', ' ').'</td>
              </tr>';

              $it++;
              }

              $return['order_items'] .= '</table>'; */
        } else {
            $global_return['errors'][] = 'Order Items not found.';
        }

        return $return;
    }

    private function getBannerInfo() {
        global $database, $global_return;

        $return = array();
        $return['result'] = false;
        $return['banner_obj'] = false;

        if (!$this->banner_obj) {
            $this->banner_obj = false;

            $query = "SELECT * FROM `jos_vm_edit_email_banner`";

            $database->setQuery($query);
            $database->loadObject($this->banner_obj);

            if ($this->banner_obj) {
                $return['result'] = true;
                $return['banner_obj'] = $this->banner_obj;
            } else {
                $global_return['errors'][] = 'Banner not found.';
            }
        } else {
            $return['result'] = true;
            $return['banner_obj'] = $this->banner_obj;
        }

        return $return;
    }

    private function getBucksInfo($order_id) {
        global $database, $global_return;

        $return = array();
        $return['result'] = false;
        $bucks_obj = $return['bucks_obj'] = false;

        $query = "SELECT `used_bucks` FROM `tbl_bucks_history`
        WHERE `order_id`=" . $order_id . "";

        $database->setQuery($query);
        $database->loadObject($bucks_obj);

        if ($bucks_obj) {
            $return['result'] = true;
            $return['bucks_obj'] = $bucks_obj;
        } else {
            $global_return['errors'][] = 'Bucks not found.';
        }

        return $return;
    }

    private function getDonateInfo($order_id) {
        global $database, $global_return;

        $return = array();
        $return['result'] = false;
        $bucks_obj = $return['donate_obj'] = false;

        $query = "SELECT `donation_price` FROM `tbl_used_donation`
        WHERE `order_id`=" . $order_id . "";

        $database->setQuery($query);
        $database->loadObject($donate_obj);

        if ($donate_obj) {
            $return['result'] = true;
            $return['donate_obj'] = $donate_obj;
        } else {
            $global_return['errors'][] = 'Donate not found.';
        }

        return $return;
    }

    private function getShopperDiscountInfo($order_id) {
        global $database, $global_return;

        $return = array();
        $return['result'] = false;
        $shopper_discount_obj = $return['shopper_discount_obj'] = false;

        $query = "SELECT `shopper_discount_value` FROM `jos_vm_orders_extra`
        WHERE `order_id`=" . $order_id . "";

        $database->setQuery($query);
        $database->loadObject($shopper_discount_obj);

        if ($shopper_discount_obj) {
            $return['result'] = true;
            $return['shopper_discount_obj'] = $shopper_discount_obj;
        } else {
            $global_return['errors'][] = 'Donate not found.';
        }

        return $return;
    }

    private function getCreditsInfo($order_id) {
        global $database, $global_return;

        $return = array();
        $return['result'] = false;
        $credits_obj = $return['credits_obj'] = false;

        $query = "SELECT 
            `credits`
        FROM `jos_vm_users_credits_uses`
        WHERE `order_id`=" . $order_id . "";

        $database->setQuery($query);
        $database->loadObject($credits_obj);

        if ($credits_obj) {
            $return['result'] = true;
            $return['credits_obj'] = $credits_obj;
        } else {
            $global_return['errors'][] = 'Credits used not found.';
        }

        return $return;
    }

    public function setVariables($order_id, $value) {
        global $database, $mosConfig_live_site, $mosConfig_mailfrom;

        $this->getBTInfo($order_id);
        $this->getSTInfo($order_id);

        $global_return = array();
        $global_return['result'] = false;
        $global_return['errors'] = array();

        $order_obj = false;

        $query = "SELECT `o`.*, `s`.`order_status_name` 
        FROM `jos_vm_orders` AS `o`
            INNER JOIN `jos_vm_order_status` AS `s` ON `s`.`order_status_code`=`o`.`order_status`
        WHERE `order_id`=" . $order_id . "";

        $database->setQuery($query);
        $database->loadObject($order_obj);

        if ($order_obj) {

            //1
            $variable = '{phpShopVendorName}';

            if (strpos($value, $variable) !== false) {
                $vendor_fnc = $this->getVendorInfo($order_obj->vendor_id);
                $vendor_obj = $vendor_fnc['vendor_obj'];

                if ($vendor_obj) {
                    $value = str_replace($variable, $vendor_obj->vendor_name, $value);
                }
            }

            //2
            $variable = '{phpShopVendorStreet1}';

            if (strpos($value, $variable) !== false) {
                $vendor_fnc = $this->getVendorInfo($order_obj->vendor_id);
                $vendor_obj = $vendor_fnc['vendor_obj'];

                if ($vendor_obj) {
                    $value = str_replace($variable, $vendor_obj->vendor_phone, $value);
                }
            }

            //3
            $variable = '{phpShopVendorStreet2}';

            if (strpos($value, $variable) !== false) {
                $vendor_fnc = $this->getVendorInfo($order_obj->vendor_id);
                $vendor_obj = $vendor_fnc['vendor_obj'];

                if ($vendor_obj) {
                    $value = str_replace($variable, $vendor_obj->vendor_address_1, $value);
                }
            }

            //4
            $variable = '{phpShopVendorZip}';

            if (strpos($value, $variable) !== false) {
                $vendor_fnc = $this->getVendorInfo($order_obj->vendor_id);
                $vendor_obj = $vendor_fnc['vendor_obj'];

                if ($vendor_obj) {
                    $value = str_replace($variable, $vendor_obj->vendor_zip, $value);
                }
            }

            //5
            $variable = '{phpShopVendorCity}';

            if (strpos($value, $variable) !== false) {
                $vendor_fnc = $this->getVendorInfo($order_obj->vendor_id);
                $vendor_obj = $vendor_fnc['vendor_obj'];

                if ($vendor_obj) {
                    $value = str_replace($variable, $vendor_obj->vendor_city, $value);
                }
            }

            //6
            $variable = '{phpShopVendorState}';

            if (strpos($value, $variable) !== false) {
                $vendor_fnc = $this->getVendorInfo($order_obj->vendor_id);
                $vendor_obj = $vendor_fnc['vendor_obj'];

                if ($vendor_obj) {
                    $value = str_replace($variable, $vendor_obj->vendor_state, $value);
                }
            }

            //7
            $variable = '{phpShopVendorImage}';

            if (strpos($value, $variable) !== false) {
                $vendor_fnc = $this->getVendorInfo($order_obj->vendor_id);
                $vendor_obj = $vendor_fnc['vendor_obj'];

                if ($vendor_obj) {
                    $value = str_replace($variable, '<img src="' . $mosConfig_live_site . '/components/com_virtuemart/shop_image/vendor/' . $vendor_obj->vendor_full_image . '" alt="vendor_image" border="0" />', $value);
                }
            }

            //8
            $variable = '{phpShopOrderHeader}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, 'Purchase Order', $value);
            }

            //9
            $variable = '{phpShopOrderNumber}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $order_obj->order_id, $value);
            }

            //10
            $variable = '{phpShopOrderDate}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, date('M d, Y', $order_obj->cdate), $value);
            }

            //11
            $variable = '{phpShopDeliveryDate}';

            if (strpos($value, $variable) !== false) {

                if ($this->isLastMinuteOrder) {
                    $order_obj->ddate .= " - $this->isLastMinuteOrderLabel";
                }
                $value = str_replace($variable, $order_obj->ddate, $value);
            }



            //12
            $variable = '{phpShopOrderStatus}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $order_obj->order_status_name, $value);
            }

            //13
            $variable = '{phpShopBTCompany}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->company, $value);
            }

            //14
            $variable = '{phpShopBTName}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->first_name . ' ' . $this->bt_obj->last_name, $value);
            }

            //15
            $variable = '{phpShopBTStreet1}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->suite . ' ' . $this->bt_obj->street_number . ' ' . $this->bt_obj->street_name, $value);
            }

            //16
            $variable = '{phpShopBTStreet2}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->address_2, $value);
            }

            //17
            $variable = '{phpShopBTCity}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->city, $value);
            }
            //17
            $variable = '{phpShopBTDistrict}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->district, $value);
            }
            //18
            $variable = '{phpShopBTState}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->state, $value);
            }

            //19
            $variable = '{phpShopBTZip}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->zip, $value);
            }

            //19
            $variable = '{phpShopBTCountry}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->country, $value);
            }

            //19
            $variable = '{phpShopBTCountry}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->country, $value);
            }

            //20
            $variable = '{phpShopBTPhone}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->phone_1, $value);
            }

            //21
            $variable = '{phpShopBTEmail}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->bt_obj->user_email, $value);
            }

            //22
            $variable = '{phpShopSTCompany}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->company, $value);
            }

            //23
            $variable = '{phpShopSTName}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->first_name . ' ' . $this->st_obj->last_name, $value);
            }

            //24
            $variable = '{phpShopSTStreet1}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->suite . ' ' . $this->st_obj->street_number . ' ' . $this->st_obj->street_name, $value);
            }

            //25
            $variable = '{phpShopSTStreet2}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->address_2, $value);
            }

            //26
            $variable = '{phpShopSTCity}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->city, $value);
            }

            //26
            $variable = '{phpShopSTDistrict}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->district, $value);
            }

            //27
            $variable = '{phpShopSTState}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->state, $value);
            }

            //28
            $variable = '{phpShopSTZip}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->zip, $value);
            }

            //29
            $variable = '{phpShopSTCountry}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->country, $value);
            }

            //30
            $variable = '{phpShopSTPhone}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->phone_1, $value);
            }

            //31
            $variable = '{phpShopSTEmail}';
            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $this->st_obj->user_email, $value);
            }

            //32
            $variable = '{phpShopOrderItems}';

            if (strpos($value, $variable) !== false) {
                $order_items_fnc = $this->getItemsInfo($order_obj->order_id);
                $order_items = $order_items_fnc['order_items'];

                if ($order_items) {
                    $value = str_replace($variable, $order_items, $value);
                }
            }

            //33
            $variable = '{as1}';

            if (strpos($value, $variable) !== false) {
                $banner_fnc = $this->getBannerInfo();
                $banner_obj = $banner_fnc['banner_obj'];

                if ($banner_obj) {
                    $value = str_replace($variable, $banner_obj->href, $value);
                }
            }

            //34
            $variable = '{as2}';

            if (strpos($value, $variable) !== false) {
                $banner_fnc = $this->getBannerInfo();
                $banner_obj = $banner_fnc['banner_obj'];

                if ($banner_obj) {
                    $value = str_replace($variable, $banner_obj->href, $value);
                }
            }

            //35
            $variable = '{as3}';

            if (strpos($value, $variable) !== false) {
                $banner_fnc = $this->getBannerInfo();
                $banner_obj = $banner_fnc['banner_obj'];

                if ($banner_obj) {
                    $value = str_replace($variable, $banner_obj->href, $value);
                }
            }

            //35
            $variable = '{phpShopOrderSubtotal}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, '$' . number_format($order_obj->order_subtotal, 2, '.', ' '), $value);
            }

            //36
            $variable = '{phpShopOrderShipping}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, '$' . number_format($order_obj->order_shipping, 2, '.', ' '), $value);
            }

            //37
            $variable = '{phpShopOrderTax}';

            if (strpos($value, $variable) !== false) {
                $order_tax = round($order_obj->order_total - ($order_obj->order_total / 1.1), 2);
                $value = str_replace($variable, '$' . number_format($order_tax, 2, '.', ' ') . ' (included in total)', $value);
            }


            //38
            $variable = '{phpUsedBucks}';

            if (strpos($value, $variable) !== false) {
                $bucks_fnc = $this->getBucksInfo($order_obj->order_id);
                $bucks_obj = $bucks_fnc['bucks_obj'];

                if ($bucks_obj) {
                    $value = str_replace($variable, '$' . number_format($bucks_obj->used_bucks, 2, '.', ' '), $value);
                } else {
                    $value = str_replace($variable, '$0.00', $value);
                }
            }

            //39
            $variable = '{phpDonatedPrice}';


            if (strpos($value, $variable) !== false) {
                $donate_fnc = $this->getDonateInfo($order_obj->order_id);
                $donate_obj = $donate_fnc['donate_obj'];

                if ($donate_obj) {
                    $order_obj->order_total = $order_obj->order_total + $donate_obj->donation_price;
                    $value = str_replace($variable, '$' . number_format($donate_obj->donation_price, 2, '.', ' '), $value);
                } else {
                    $value = str_replace('<strong>Donation:</strong> ' . $variable, '', $value);
                }
            }
            //40
            $variable = '{phpShopOrderTotal}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, '$' . number_format($order_obj->order_total, 2, '.', ' '), $value);
            }
            //41
            $variable = '{phpShopCouponDiscount}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, '$' . number_format($order_obj->coupon_discount, 2, '.', ' '), $value);
            }

            //42
            $variable = '{phpShopShippingDiscount}';

            /*
              if (strpos($value, $variable) !== false) {
              $value = str_replace($variable, number_format($order_obj->coupon_discount, 2, '.', ' '), $value);
              } */

            //43, 44, 45

            $variables = array(
                '{phpShopOrderDisc1}',
                '{phpShopOrderDisc2}',
                '{phpShopOrderDisc3}'
            );

            foreach ($variables as $variable) {
                if (strpos($value, $variable) !== false) {
                    $value = str_replace($variable, '', $value);
                }
            }

            //46
            $variable = '{phpShopCustomerNote}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $order_obj->customer_note, $value);
            }

            //47
            $variable = '{phpShopCustomerSignature}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $order_obj->customer_signature, $value);
            }

            //48
            $variable = '{phpShopCustomerInstructions}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, $order_obj->customer_comments, $value);
            }

            //49
            $variable = '{PAYMENT_INFO_LBL}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, 'Payment Information', $value);
            }

            //50
            $variable = '{PAYMENT_INFO_DETAILS}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, 'Payment Information', $value);
            }

            //51
            $variable = '{SHIPPING_INFO_LBL}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, 'Delivery Information', $value);
            }

            //52
            $variable = '{SHIPPING_INFO_DETAILS}';

            if (strpos($value, $variable) !== false) {
                $shipping_info = explode('|', $order_obj->ship_method_id);

                $value = str_replace($variable, $shipping_info[2], $value);
            }

            //53
            $variable = '{phpPickUpVoucher}';

            if (strpos($value, $variable) !== false) {
                $shipping_info = explode('|', $order_obj->ship_method_id);

                $value = str_replace($variable, '', $value);
            }

            //54
            $variable = '{phpShopOrderHeaderMsg}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, 'Thank you for shopping with us.  Your order information follows.', $value);
            }

            //55
            $variable = '{phpShopOrderClosingMsg}';

            if (strpos($value, $variable) !== false) {
                $closing_msg = '<br/><br/>Thank you for your patronage.<br/><br/><a title="View the order by following the link below." href="' . $mosConfig_live_site . '/order-details/?order_id=' . $order_obj->order_id . '">View the order by following the link below.</a><br /><br />Questions? Problems?<br />E-mail: <a href="mailto:' . $mosConfig_mailfrom . '">' . $mosConfig_mailfrom . '</a>';

                $value = str_replace($variable, $closing_msg, $value);
            }

            //56
            $variable = '{sDiscount}';

            if (strpos($value, $variable) !== false) {
                $shopper_discount_fnc = $this->getShopperDiscountInfo($order_obj->order_id);
                $shopper_discount_obj = $shopper_discount_fnc['shopper_discount_obj'];

                if ($shopper_discount_obj) {
                    $value = str_replace($variable, '$' . number_format($shopper_discount_obj->shopper_discount_value, 2, '.', ' '), $value);
                } else {
                    $value = str_replace($variable, '$0.00', $value);
                }
            }

            //57
            $variable = '{OrderStatusChangeDateOrdered}';

            if (strpos($value, $variable) !== false) {
                $value = str_replace($variable, date('M d', $order_obj->cdate), $value);
            }

            //58
            $variable = '{OrderStatusChangeDateConfirmed}';

            if (strpos($value, $variable) !== false) {
                $change_date_fnc = $this->getDateChangeOrderStatus($order_obj->order_id, 'C');
                $change_date_obj = $change_date_fnc['change_obj'];
                $value = str_replace($variable, (!empty($change_date_obj->date_added) ? date('M d', strtotime($change_date_obj->date_added)) : 'N/A'), $value);
            }

            //59
            $variable = '{OrderStatusChangeDateInTransit}';

            if (strpos($value, $variable) !== false) {
                $change_date_fnc = $this->getDateChangeOrderStatus($order_obj->order_id, 'Z');
                $change_date_obj = $change_date_fnc['change_obj'];

                $value = str_replace($variable, (!empty($change_date_obj->date_added) ? date('M d', strtotime($change_date_obj->date_added)) : 'N/A'), $value);
            }

            //60
            $variable = '{OrderStatusChangeDateDelivered}';

            if (strpos($value, $variable) !== false) {
                $change_date_fnc = $this->getDateChangeOrderStatus($order_obj->order_id, 'D');
                $change_date_obj = $change_date_fnc['change_obj'];

                $value = str_replace($variable, (!empty($change_date_obj->date_added) ? date('M d', strtotime($change_date_obj->date_added)) : 'N/A'), $value);
            }

            //61
            $variable = '{driverName}';

            if (strpos($value, $variable) !== false) {
                $driver_fnc = $this->getDriverInfo($order_obj->order_id);
                $driver_obj = $driver_fnc['driver_obj'];

                if ($driver_obj) {
                    $value = str_replace($variable, $driver_obj->driver_name, $value);
                }
            }

            //62
            $variable = '{driverPhone}';

            if (strpos($value, $variable) !== false) {
                $driver_fnc = $this->getDriverInfo($order_obj->order_id);
                $driver_obj = $driver_fnc['driver_obj'];

                if ($driver_obj) {
                    $driver_info_a = explode('[--1--]', $driver_obj->description);

                    $value = str_replace($variable, $driver_info_a[1], $value);
                }
            }
            //63
            $variable = '{phpUsedCredits}';

            if (strpos($value, $variable) !== false) {
                $credits_fnc = $this->getCreditsInfo($order_obj->order_id);
                $credits_obj = $credits_fnc['credits_obj'];

                if ($credits_obj) {
                    $value = str_replace($variable, '$' . number_format($credits_obj->credits, 2, '.', ' '), $value);
                } else {
                    $value = str_replace($variable, '$0.00', $value);
                }
            }

            //64
            $variable = '{phpShopOrderTrackingurl}';

            if (strpos($value, $variable) !== false) {
                $cache = base64_encode('order_id=' . $order_obj->order_id);
                $trackingUrl = $mosConfig_live_site . '/order-tracking/?data=' . $cache;
                if ($trackingUrl) {
                    $value = str_replace($variable, '<br>Track <strong><a href="' . $trackingUrl . '" target="_blank">here</a></strong> your order', $value);
                } else {
                    $value = str_replace($variable, '', $value);
                }
            }

            //65
            $variable = '{phpPartnerOrderNumber}';

            if (strpos($value, $variable) !== false) {
                $partner_obj = $this->getPartnerInfo($order_obj->order_id);

                if ($partner_obj) {
                    $value = str_replace($variable, $partner_obj->api_order_id, $value);
                } else {
                    $value = str_replace($variable, '', $value);
                }
            }

            return $value;
        }
    }

}
