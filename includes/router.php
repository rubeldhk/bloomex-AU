<?php

class newSef {

    private $database = null;

    private $parent = null;
    private $parents = array();
    public $h1 = '';
    public $canonical = '';
    public $landing_type = 0; //0 - default, 1 - flowers, 2 baskets
    public $landing_old = false; // city last on old landing
    public $description_text = '';
    public $footer_links = '';
    public $city = false;
    public $nofollow_category = false;
    public $nofollow = false;
    public $noindex_follow = false;
    public $alternate = "";
    public $aliases = array();
    public $homepage = false;
    public $real_uri = "";
    public $savedmenuvars = false; // used to store query vars
    public $meta_source = false; //used to store meta object for when we are using saved vars
    public $dropfr = false; // for old landing canonical/alternate
    public $fullmode = false;

    public function __construct() { // this just allow usage of translateURI
        global $database,$mosConfig_disable_robots_indexing;
        $this->nofollow = $mosConfig_disable_robots_indexing ?? false;
        $this->database = $database; // mldatabase
    }

    public function run($request_uri) { // main workhorse of SEF object
        global $mainframe, $mosConfig_lang, $iso_client_lang, $mosConfig_live_site;
        $this->fullmode = true;
        $request_uri = preg_replace('~/+~', '/', $request_uri);

        //we want to catch up 301 right away
        $getAlias = $this->getAlias($request_uri);

        //now trim slashes
        $request_uri = preg_replace('/^\/|\/$/', '', $request_uri);



        //fleuriste - old landings
        $request_uri_a = explode("/", $request_uri);
        $first = $request_uri_a[0] ?? "";
        $second = $request_uri_a[1] ?? "";
        $old_landings = array("florist-online", "gift-hamper-basketa", "sympathy-flowers", "flower-delivery");

        if($first == 'gift-hamper-basket') {
            header('HTTP/1.1 301 Moved Permanently');
            $where = $mosConfig_live_site . '/gift-hampers/';
            if (isset($_SERVER['REQUEST_QUERY'])) {
                $where .= "?" . $_SERVER['REQUEST_QUERY'];
            }
            header('Location: ' . $where);
            exit();
        }
        if($first == 'florist') {
            header('HTTP/1.1 301 Moved Permanently');
            $where = $mosConfig_live_site . '/flower-delivery/'.$second;
            if (isset($_SERVER['REQUEST_QUERY'])) {
                $where .= "?" . $_SERVER['REQUEST_QUERY'];
            }
            header('Location: ' . $where);
            exit();
        }
        if($first == 'sympathy-flowers') {
            header('HTTP/1.1 301 Moved Permanently');
            $where = $mosConfig_live_site . '/occasions/sympathy-funeral-flowers/';
            if (isset($_SERVER['REQUEST_QUERY'])) {
                $where .= "?" . $_SERVER['REQUEST_QUERY'];
            }
            header('Location: ' . $where);
            exit();
        }

        //florist-online - old landing - needed for alternate
        if (in_array($first, $old_landings)) {
            $this->dropfr = true;
        }

        $this->real_uri = $request_uri;

        $GLOBALS['real_uri'] = $request_uri;

        if ($getAlias->result) {
            if (!empty($getAlias->to)) {
                $request_uri = $getAlias->to;
            }
        }

//check for abandoment
        $this->getAbandonment($request_uri);

        //check for no indexing
        $this->checkIfUrlExecutedFromSeo($request_uri);

        $this->aliases = explode('/', $request_uri);

        if ($this->aliases[0] == "") {
            $this->aliases[0] = "";
        }

        if (sizeof($this->aliases) > 0) {
                $_GLOBALS['mosConfig_lang'] = 'english';
                $mosConfig_lang = 'english';
                $iso_client_lang = 'english';
                //$_COOKIE['mbfcookie']['lang'] = 'en';

            $GLOBALS['aliases'] = $this->aliases;

            if (sizeof($this->aliases) > 1) {
                $this->city = $this->getCity($this->aliases[0]);
                if ($this->city) {
                    $this->city->url = $this->aliases[0];
                    array_shift($this->aliases);
                    $_SERVER['REQUEST_URI'] = '/' . implode('/', $this->aliases);
                    $GLOBALS['real_uri'] = implode('/', $this->aliases) . '/';

                    $this->noindex_follow = true;

                    //we want to catch up 301 right away if there is a location
                    $this->getAlias($GLOBALS['real_uri']);
                } else {
                    //old landing?

                    $this->city = $this->getCity(end($this->aliases));

                    if ($this->city) {
                        $this->landing_old = true;
                        $this->city->url = end($this->aliases);
                    }
                }
            }
            $this->parent = false;
        }

        if ($this->aliases[0] == '') {
            $this->homepage = true;
        }

        foreach ($this->aliases as $alias) {
            $parent = $this->getAliasInfo($alias, $this->parent);

            $this->parent = $parent;
            $this->parents[] = $parent;
        }

        $GLOBALS['footer_links'] = $this->getFooterLinks($request_uri);
    }

    public function getNoFollow($request_uri) {
        $return = false;

        $need_array = [
            'account-details/',
            'login/',
            'my-orders/',
            'your-bloomex-buck-history/',
            'lost-password/',
            'cart/',
            'registration/',
            'checkout-specials/'
        ];

        if (in_array($request_uri, $need_array)) {
            $return = true;
        }


        return $return;
    }

    public function setMetaTags() {
        global $mainframe, $mosConfig_lang;
        if (is_object($this->parent)) {
            $meta_tags = $this->getMetaTags();
            //     f($meta_tags);
            if ($meta_tags->result) {
                $this->h1 = $meta_tags->meta->h1;
                $this->canonical = $meta_tags->meta->canonical;
                $this->description_text = $meta_tags->meta->description_text;
                $this->description_text_footer = isset($meta_tags->meta->description_text_footer) ? $meta_tags->meta->description_text_footer : '';
                if ($this->city) {
                    $this->city->description = $meta_tags->meta->description_text;
                    $this->canonical = $this->add_city($meta_tags->meta->canonical);
                }

                if (preg_match('/page=(\d+)/i', $_SERVER['QUERY_STRING'], $matches)) {
                    $page = (int) $matches[1];
                    if($page > 1){
                        $this->canonical .= "?page=".$page;
                        $this->h1 .= " – Page $page | Bloomex";
                        $this->description_text = ' ';
                        $this->description_text_footer = '';
                        $meta_tags->meta->title .= " – Page $page";
                        $meta_tags->meta->description = $meta_tags->meta->title .". ". $meta_tags->meta->description;
                    }
                }

                $mainframe->setPageTitle2($meta_tags->meta->title);
                $mainframe->appendMetaTag('description', $meta_tags->meta->description);
                $mainframe->appendMetaTag('keywords', $meta_tags->meta->keywords);

                if (($this->nofollow == true) OR ( $this->nofollow_category == true)) {
                    $mainframe->appendMetaTag('robots', 'noindex, nofollow');
                }elseif ($this->noindex_follow == true) {
                    $mainframe->appendMetaTag('robots', 'noindex, follow');
                }
                return $mainframe->getHead();
            }
        }
    }

    public function add_city($url) {
        if ($this->landing_old) {
            //bypass it
            return $url;
        }
        global $mosConfig_live_site;
        $host = (strpos($url, $mosConfig_live_site) === 0) ? $mosConfig_live_site : "";
        //  f($url);
        $url2 = str_replace($mosConfig_live_site, "", $url);
        $lead = ($url2[0] == "/") ? "/" : "";
        $url_trimmed = ltrim($url2, "/");
        $result = $host . $lead  . $this->city->url . "/" . $url_trimmed;
        //  f($url, $url2, $lead, $url_trimmed, $fr, $url_nolang, $this->city->url, $result);
        return $result;
    }

    public function __destruct() {
        unset($this->database);
    }

    private function getAlias($request_uri) {
        global $mosConfig_live_site;
        $return = (object) ['result' => false];
        $request_uri = urldecode($request_uri);
        $request_uri = preg_replace('/^\/|\/$/', '', $request_uri);
        $return->to = '';

        $query = "SELECT 
            `a`.`id`,
            `a`.`to`,
            `a`.`type`,
            `a`.`status`
        FROM `jos_aliases` AS `a`
        WHERE 
        (
            `a`.`from`='" . $this->database->getEscaped(strtolower($request_uri)) . "'
        )
        ";

        f($request_uri, $query);

        $alias = false;
        $this->database->setQuery($query);

        if ($this->database->loadObject($alias)) {
            if ($alias->status == '1') { // 301
                header('HTTP/1.1 301 Moved Permanently');
                if($this->city->url && $alias->type == '3'){
                    $where = $mosConfig_live_site . '/'.$this->city->url. '/'.$alias->to;
                }else{
                    $where = $mosConfig_live_site . '/'.$alias->to;
                }
                if (isset($_SERVER['REQUEST_QUERY'])) {
                    $where .= "?" . $_SERVER['REQUEST_QUERY'];
                }
                header('Location: ' . $where);
                exit();
            } else {
                $return->result = true;
                $return->to = $alias->to;
            }
        }
        return $return;
    }

    private function getAliasInfo($alias, $parent = false) {
        //   d($alias, $parent, $this->savedmenuvars);
        $return = new stdClass();
        $return->alias = $alias;
        $return->result = false;
        $return->type = '404';

        if ($parent) {
            switch ($parent->type) {
                case 'menu':
                    $menu = $this->getMenu($alias, $parent);

                    if ($menu->result) {
                        $return->type = 'menu';
                        $return->data['menu'] = $menu->menu; //for metatags
                        $this->meta_source = $return;
                    } elseif ($this->savedmenuvars) {//we have component with query string in parents
                        $this->setVariables($this->savedmenuvars);
                        $return->type = 'menu';

                    } // will go 404 by default
                    break;
                case 'category':
                    $category = $this->getCategory($alias, $parent);
                    if(!$category->result){
                        $this->checkCategoryAliasInHistory($alias, $parent);
                    }
                    if ($category->result) {
                        $return->type = 'category';
                        $return->data['category'] = $category->category; //for metatags
                        $this->meta_source = $return;
                    } else {
                        $product = $this->getProduct($alias, $parent);
                        if(!$product->result){
                            $this->checkProductAliasInHistory($alias, $parent);
                        }
                        if ($product->result) {

                            if($this->city && $product->product->cdate > strtotime('2025-09-15')){
                                $this->removeCityAliasFromUrl();
                            }

                            $return->type = 'product';
                            $return->data['product'] = $product->product;
                            $this->meta_source = $return;
                        }
                    }
                    break;
            }
        } else {
            $menu = $this->getMenu($alias, $parent);

            if ($menu->result) {
                $return->type = 'menu';
                $return->data['menu'] = $menu->menu; //for metatags
                $this->meta_source = $return;
            } else {
                $category = $this->getCategory($alias, $parent);
                if(!$category->result){
                    $this->checkCategoryAliasInHistory($alias, $parent);
                }
                if ($category->result) {

                    $return->type = 'category';
                    $return->data['category'] = $category->category; //for metatags
                    $this->meta_source = $return;
                }else{
                    global $mosConfig_live_site;
                    array_pop($this->aliases);
                    header('HTTP/1.1 301 Moved Permanently');
                    $where = $mosConfig_live_site . '/';
                    if (isset($_SERVER['REQUEST_QUERY'])) {
                        $where .= "?" . $_SERVER['REQUEST_QUERY'];
                    }
                    header('Location: ' .$where);
                    exit();
                }
            }
        }
        if ($return->type == '404') {
            $this->set404();
        } else {
            $return->result = true; //TODO : REMOVE
        }
        return $return;
    }
    private function removeCityAliasFromUrl(){
        global $mosConfig_live_site;
            header('HTTP/1.1 301 Moved Permanently');
            $where = $mosConfig_live_site . '/'.implode('/', $this->aliases) . '/';
            if (isset($_SERVER['REQUEST_QUERY'])) {
                $where .= "?" . $_SERVER['REQUEST_QUERY'];
            }
            header('Location: ' .$where);
            exit();
    }

    private function checkProductAliasInHistory($alias, $parent){
        global $mosConfig_live_site;
        $queryNewAlias = "select h.new as alias from jos_vm_product as p
                                left join ((SELECT product_id,new,old,date FROM  `jos_vm_product_history_stage` 
                                WHERE  name='alias' ORDER BY `date` DESC limit 1)
                                UNION
                                (SELECT product_id,new,old,date AS `where` FROM `jos_vm_product_history_live` 
                                WHERE  name='alias' ORDER BY `date` DESC)
                                ORDER BY `date` DESC limit 1) as h on h.product_id = p.product_id 
                                where h.old = '".$this->database->getEscaped($alias)."'";
        $productNewAlias = false;
        $this->database->setQuery($queryNewAlias);
        if ($this->database->loadObject($productNewAlias)) {
            $product = $this->getProduct($productNewAlias->alias, $parent);
            if($product->result){
                $searchValue = $alias;
                $replaceValue = $productNewAlias->alias;

                $this->aliases = array_map(function($value) use ($searchValue, $replaceValue) {
                    return ($value === $searchValue) ? $replaceValue : $value;
                }, $this->aliases);

                header('HTTP/1.1 301 Moved Permanently');
                $where = $mosConfig_live_site . '/'.implode('/', $this->aliases) . '/';
                if (isset($_SERVER['REQUEST_QUERY'])) {
                    $where .= "?" . $_SERVER['REQUEST_QUERY'];
                }
                header('Location: ' .$where);
                exit();
            }
        }else{
            array_pop($this->aliases);
            header('HTTP/1.1 301 Moved Permanently');
            $where = $mosConfig_live_site . '/'.implode('/', $this->aliases) . '/';
            if (isset($_SERVER['REQUEST_QUERY'])) {
                $where .= "?" . $_SERVER['REQUEST_QUERY'];
            }
            header('Location: ' .$where);
            exit();
        }
    }
    private function checkCategoryAliasInHistory($alias, $parent)
    {
        global $mosConfig_live_site;
        $queryNewAlias = "select h.new as alias from jos_vm_category as p
                                    left join ((SELECT category_id,new,old,date FROM  `jos_vm_category_history_stage` 
                                    WHERE  name='alias' ORDER BY `date` DESC)
                                    UNION
                                    (SELECT category_id,new,old,date AS `where` FROM `jos_vm_category_history_live` 
                                    WHERE  name='alias' ORDER BY `date` DESC)
                                    ORDER BY `date` DESC) as h on h.category_id = p.category_id 
                                    where h.old = '".$this->database->getEscaped($alias)."'";

        $categoryNewAlias = false;
        $this->database->setQuery($queryNewAlias);
        if ($this->database->loadObject($categoryNewAlias)) {

            $category = $this->getCategory($categoryNewAlias->alias, $parent);

            if($category->result){
                $searchValue = $alias;
                $replaceValue = $categoryNewAlias->alias;

                $this->aliases = array_map(function($value) use ($searchValue, $replaceValue) {
                    return ($value === $searchValue) ? $replaceValue : $value;
                }, $this->aliases);

                header('HTTP/1.1 301 Moved Permanently');
                $where = $mosConfig_live_site . '/'.implode('/', $this->aliases) . '/';
                if (isset($_SERVER['REQUEST_QUERY'])) {
                    $where .= "?" . $_SERVER['REQUEST_QUERY'];
                }
                header('Location: ' .$where);
                exit();
            }
        }
    }

    private function getMetaTags() {
        // f($this);
        global $mosConfig_live_site, $mosConfig_lang;
        $return = new stdClass();
        $return->result = false;
        $return->meta = new stdClass();
        if ($this->parent->type == '404') { // we're on 404 go fuck - is it normal thou?
            return $return;
        }
        $obj = $this->meta_source;
        $data = $obj->data[key($obj->data)];

        Switch (key($obj->data)) {
            case 'product':
                $page_type = 2;
                break;

            case 'category':
                $page_type = 1;
                break;

            case 'menu':
            default:
                $page_type = 0;
                break;
        }

        $city = '0';
        if ($this->city) {
            $city = '1';
        }
        $metatags_obj = (object) [];

        if (isset($data->meta_city) OR isset($data->meta)) {
            $metatags_obj = ($city == '1' AND is_object($data->meta_city)) ? $data->meta_city : $data->meta;
        }

        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri_a = explode('/', $request_uri);

        foreach ($request_uri_a as $request_uri_one) {
            $request_uri_link = implode('/', $request_uri_a);

            if (!preg_match('/^\//u', $request_uri_link)) {
                $request_uri_link = '/' . $request_uri_link;
            }

            $query = "SELECT
                *
            FROM `jos_metatags` 
            WHERE 
                (`url`='" . $this->database->getEscaped($request_uri.'/') . "' or 
                `url`='" . $this->database->getEscaped($request_uri_link) . "')
                AND
                `page_type`='" . $page_type . "'
                AND
                `city`='" . $city . "'
            ";

            $url_obj = false;
            $this->database->setQuery($query);


            if ($this->database->loadObject($url_obj)) {

                $this->landing_type = $url_obj->landing_type;
            }
                $return->result = true;

                $return->meta->title = (!empty($metatags_obj->title) ?  $metatags_obj->title : ($url_obj->title ?? ''));
                $return->meta->description = (!empty($metatags_obj->description) ? $metatags_obj->description : ($url_obj->description??''));
                $return->meta->keywords = (!empty($metatags_obj->keywords) ? $metatags_obj->keywords : ($url_obj->keywords??''));
                $return->meta->h1 = (!empty($metatags_obj->h1) ? $metatags_obj->h1 : ($url_obj->h1??''));
                $return->meta->canonical = (isset($metatags_obj->canonical) AND!empty($metatags_obj->canonical)) ? $metatags_obj->canonical : $mosConfig_live_site . "/" . $this->real_uri . (($this->real_uri) ? "/" : "");
                $return->meta->description_text = (!empty($metatags_obj->description_text) ? $metatags_obj->description_text : ($url_obj->description_text??''));
                //NB! different names for columns in meta_tags and vm_category!
                $return->meta->description_text_footer = !empty($metatags_obj->description_text_footer) ? $metatags_obj->description_text_footer : ($url_obj->description_text_footer??'');
                $return_mt = $this->returnMetaTags($data, $return->meta, $city);
                if ($return_mt->result && $return_mt->meta->title!='') {
                    return $return_mt;
                }


            array_pop($request_uri_a);
        }

        return $return;
    }

    private function returnMetaTags($obj, $metatags_obj, $city) {
        $return = new stdClass();
        $return->result = false;
        $return->meta = new stdClass();

        if (sizeof((array) $metatags_obj) > 0) {
            $array_from = array(
                '{category_name}',
                '{product_name}',
                '{product_discounted_price}',
            );

            $array_to = [];

            if (isset($obj->name)) {
                $array_to = array(
                    $obj->name,
                    $obj->name,
                    '$' . number_format(isset($obj->product_discounted_price) ? $obj->product_discounted_price : 0, 2, '.', '')
                );
            }

            if ($city == '1') {
                $array_from[] = '{city_name}';
                $array_from[] = '{state_name}';
                $array_from[] = '{state_short}';
                $array_from[] = '{city_url}';

                $array_to[] = $this->city->city;
                $array_to[] = $this->city->state;
                $array_to[] = $this->city->state_short;
                $array_to[] = $this->city->url;
            }

            foreach ($metatags_obj AS $k => $v) {
                $metatags_obj->$k = str_replace($array_from, $array_to, $v);
            }

            $return->result = true;
            $return->meta->title = $metatags_obj->title;
            $return->meta->description = $metatags_obj->description;
            $return->meta->keywords = $metatags_obj->keywords;
            $return->meta->h1 = $metatags_obj->h1;
            $return->meta->canonical = $metatags_obj->canonical;
            $return->meta->description_text = $metatags_obj->description_text;
            $return->meta->description_text_footer = $metatags_obj->description_text_footer;

            return $return;
        }

        return $return;
    }

    private function getComponent($aliases, $alias, $parent = false) {
        $return = new stdClass();
        $return->result = false;
        $return->landing = false;
        $return->last = false;


        //TODO: fix partnership if needed
        $array = array(
            'Partnership-Account' => array(
                'option' => 'com_corporateapp',
                'url' => isset($aliases[array_search($alias, $aliases) + 1]) ? $aliases[array_search($alias, $aliases) + 1] : '',
                'last' => true
            )
        );


        if($alias=='login' && $aliases[array_search($alias, $aliases) + 1]=='twitter'){
            $twitter_social_login['login'] = [
                'option' => 'com_new_social_login',
                'social' => 'Twitter',
                'task' => 'auth',
                'oauth_token' => isset($_GET['oauth_token']) ? $_GET['oauth_token'] : '',
                'oauth_verifier' => isset($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : '',
                'last' => true
            ];
            $array = array_merge($array, $twitter_social_login);
        }
        if (array_key_exists($alias, $array)) {
            $return->result = true;
            if (isset($array[$alias]['last'])) {
                $return->last = true;
            }
            $this->setVariables($array[$alias]);
        }

        return $return;
    }

    function getMenu($alias, $parent = false) {
        $return = new stdClass();
        $return->result = false;

        //menu should have menu only as parent
        if (($parent) && ($parent->type != "menu")) {
            return $return;
        }


        $query = "SELECT 
            `jos_menu`.`id`,
            `jos_menu`.`parent`,
            `jos_menu`.`nofollow`,
            `jos_menu`.`new_type` as 'type',
            `jos_menu`.`link`,
            `jos_menu`.`name`,
            `jos_menu`.`page_title`,
            `jos_menu`.`metadesc`,
            `jos_menu`.`metakey`,
            `jos_menu`.`page_title_fr`,
            `jos_menu`.`metadesc_fr`,
            `jos_menu`.`metakey_fr`,
             `jos_menu`.`alias`           
        FROM `jos_menu` 
        LEFT JOIN `jos_menu` AS `mp` ON
            `mp`.`id`=`jos_menu`.`parent`
        WHERE 
            `jos_menu`.`alias`='" . $this->database->getEscaped($alias) . "'
            AND 
                `jos_menu`.`published`='1'
        ";

        if ($parent && isset($parent->data['menu']->id)) {
            $query .= " AND `jos_menu`.`parent`=" . (int) $parent->data['menu']->id . "";
        } else {
            $query .= " AND ((`jos_menu`.`parent` = 0) OR (`jos_menu`.`new_type` like 'blog_item') OR (`jos_menu`.`new_type` like 'component'))";
        }

        $menu = false;
        $this->database->setQuery($query);

        /*        f($query); */

        if ($this->database->loadObject($menu)) {
            /* f($menu); */
            $menu->meta = (object) [];
            $menu->meta_city = (object) [];


            if ($menu->nofollow == '1') {
                $this->nofollow = true;
            }

            if (!empty($menu->page_title) OR!empty($menu->metadesc) OR!empty($menu->metakey)) {
                $menu->meta->title = $menu->page_title;
                $menu->meta->description = $menu->metadesc;
                $menu->meta->keywords = $menu->metakey;
            }

            if (!empty($menu->page_title_fr) OR!empty($menu->metadesc_fr) OR!empty($menu->metakey_fr)) {
                $menu->meta_city->title = $menu->page_title_fr;
                $menu->meta_city->description = $menu->metadesc_fr;
                $menu->meta_city->keywords = $menu->metakey_fr;
            }

            Switch ($menu->type) {
                case 'component':
                    $return->result = true;

                    parse_str($menu->link, $variables);

                    $url_variable_a = array(); //we parse variables which should be set in url to this one
                    foreach ($variables as $k => $v) {
                        if (is_int($k)) {
                            $url_variable_a[$k] = $v;
                            unset($variables[$k]);
                        }
                    }
//                    f($this->aliases,$this->parents,$url_variable_a,$variables);

                    $variables['Itemid'] = $menu->id;
//                    if ((count($this->aliases) - count($this->parents) - 1) == count($url_variable_a)) {
                        // we have same amount of left url segments as variables to fill
                        foreach ($url_variable_a as $k => $v) {
                            if(isset($this->aliases[$k + count($this->parents) + 1]))
                                $url_variable_a[$v] = $this->aliases[$k + count($this->parents) + 1]; //this is the position of currently parsed var
                            unset($url_variable_a[$k]); // unset processed var
                        }

                        $this->savedmenuvars = array_merge($url_variable_a, $variables);
//                    } else {
//                        $this->savedmenuvars = false; //clear previously saved vars
//                    }

                    //    f($menu->alias, $menu->link, $variables, $this->savedmenuvars, $url_variable_a, count($this->aliases), count($this->parents) - 1, count($url_variable_a));
                    $this->setVariables($variables);
                    break;

                case 'blog_category':
                    $return->result = true;

                    $variables = array(
                        'option' => 'com_content',
                        'Itemid' => $menu->id,
                        'task' => 'category',
                        'id' => $menu->link
                    );

                    $this->setVariables($variables);
                    break;

                case 'blog_section':
                    $return->result = true;

                    $variables = array(
                        'option' => 'com_content',
                        'Itemid' => $menu->id,
                        'task' => 'section',
                        'id' => $menu->link
                    );

                    $this->setVariables($variables);
                    break;

                case 'blog_item':
                    $query = "SELECT 
                        `title` AS 'name',
                        `page_title`,
                        `metadesc`,
                        `metakey`
                    FROM  `jos_content` 
                    WHERE `id`=" . (int) $menu->link . "
                    ";

                    $this->database->setQuery($query);

                    if ($this->database->loadObject($content)) {
                        $return->result = true;

                        $menu->name = $content->name;

                        if (!empty($content->page_title) OR!empty($content->metadesc) OR!empty($content->metakey)) {
                            $menu->meta->title = $content->page_title;
                            $menu->meta->description = $content->metadesc;
                            $menu->meta->keywords = $content->metakey;
                        }

                        if ($this->city) {
                            $return->result = false;
                        }
                    }

                    $variables = array(
                        'option' => 'com_content',
                        'Itemid' => $menu->id,
                        'task' => 'view',
                        'id' => $menu->link
                    );

                    $this->setVariables($variables);
                    break;

                case 'vm_category':
                    /* TODO: fix old categories to be present on cp */
                    $query = "SELECT
                        `c`.`category_name` AS 'name',
                        `c`.`meta_info`,
                        `c`.`meta_info_fr`,
                        `c`.`category_description`,
                        `c`.`category_description_city`,
                        `cp`.`category_type` AS 'product_type',
                        `cp`.`h1`,
                        `cp`.`h1_city`,
                        `cp`.`sitemap_publish`
                    FROM `jos_vm_category` AS `c`
                    LEFT JOIN `jos_vm_category_options` AS `cp`
                        ON `cp`.`category_id`=`c`.`category_id`";

                    $query .= "WHERE 
                        `c`.`category_id`=" . $menu->link . "
                        AND
                        `c`.`category_publish`='Y'
                    ";

                    $this->database->setQuery($query);

                    if ($this->database->loadObject($category)) {

                        $menu->name = $category->name;
                        $menu->product_type = $category->product_type;

                        $category_meta = explode('[--2010--]', trim($category->meta_info));
                        
                                                   
                        if ($category->sitemap_publish == '0') {
                            $this->nofollow_category = true;
                        }

                        if (!empty($category_meta[0]) OR ! empty($category_meta[1]) OR ! empty($category_meta[2]) OR ! empty($category->h1)) {
                            $menu->meta->title = $category_meta[0];
                            $menu->meta->description = $category_meta[1];
                            $menu->meta->keywords = $category_meta[2];
                            $menu->meta->h1 = $category->h1;
                            $menu->meta->description_text = $category->category_description;
                        }

                        $category_meta = explode('[--2010--]', trim($category->meta_info_fr));

                        if (!empty($category_meta[0]) OR ! empty($category_meta[1]) OR ! empty($category_meta[2]) OR ! empty($category->h1_city)) {
                            $menu->meta_city->title = $category_meta[0];
                            $menu->meta_city->description = $category_meta[1];
                            $menu->meta_city->keywords = $category_meta[2];
                            $menu->meta_city->h1 = $category->h1_city;
                            $menu->meta_city->description_text = $category->category_description_city;
                        }
                        $alias = $this->getCategoryAlias($menu->link);
                        $menu->meta->canonical = $menu->meta_city->canonical = $this->getCanonicalCategory($alias);
                    }
                    $variables = array(
                        'option' => 'com_virtuemart',
                        'Itemid' => $menu->id,
                        'category_id' => $menu->link,
                        'category_name' => $category->name??'',
                        'lang' => 'en',
                        'page' => 'shop.browse'
                    );

                    $this->setVariables($variables);
                    //$_SERVER['REQUEST_URI'] = '/index.php?'.http_build_query($variables);
                    break;

                default:
                    //$return->result = true;
                    break;
            }
            $return->menu = $menu;
        }

        return $return;
    }
    private function getCategory($alias, $parent = false) {
        $return = new stdClass();
        $return->result = false;

        $query = "SELECT 
            `c`.`category_id`,
            `c`.`category_name` AS 'name',
            `c`.`meta_info`,
            `c`.`meta_info_fr`,
            `c`.`category_description_city`,
            `c`.`category_description`,
            `cp`.`description_footer_city`,
            `cp`.`description_footer`,
            `cp`.`category_type` AS 'product_type',
            `cp`.`h1`,
            `cp`.`h1_city`,
            `cc`.`alias` as canonical_alias,
            `cp`.`sitemap_publish`
        FROM `jos_vm_category` AS `c`
        LEFT JOIN `jos_jf_content` AS `j` ON
            `j`.`reference_id`=`c`.`category_id`
            AND 
            `j`.`reference_table`='vm_category' 
            AND 
            `j`.`reference_field`='alias' 
        INNER JOIN `jos_vm_category_options` AS `cp`
            ON `cp`.`category_id`=`c`.`category_id`
        LEFT JOIN `jos_vm_category` AS `cc`
            ON `cc`.`category_id`=`cp`.`canonical_category_id`
            ";

        if ($parent AND ( (isset($parent->data['menu']) AND $parent->data['menu']->type == 'vm_category') OR isset($parent->data['category']))) {
            if (isset($parent->data['menu']) AND $parent->data['menu']->type == 'vm_category') {
                $category_parent_id = (int) $parent->data['menu']->link;
            } elseif (isset($parent->data['category'])) {
                $category_parent_id = (int) $parent->data['category']->category_id;
            }

            $query .= " INNER JOIN `jos_vm_category_xref` AS `c_x`
            ON `c_x`.`category_child_id`=`c`.`category_id`
                AND
                `c_x`.`category_parent_id`=" . $category_parent_id . "
            ";
        } else {
            $query .= " INNER JOIN `jos_vm_category_xref` AS `c_x`
            ON `c_x`.`category_child_id`=`c`.`category_id`
                AND
                `c_x`.`category_parent_id`=0
            ";
        }

        $query .= " WHERE 
        (
            `c`.`alias`='" . $this->database->getEscaped($alias) . "'
            OR 
            `j`.`value`='" . $this->database->getEscaped($alias) . "'
        )
        AND `c`.`category_publish`='Y'
        ";

        $category = false;
        $this->database->setQuery($query);

        if ($this->database->loadObject($category)) {
            $category_meta = explode('[--2010--]', trim($category->meta_info));

            if ($category->sitemap_publish == '0') {
                $this->nofollow_category = true;
            }

            $category->meta = (object) [];
            $category->meta_city = (object) [];

            if (!empty($category_meta[0]) OR ! empty($category_meta[1]) OR ! empty($category_meta[2]) OR ! empty($category->h1)) {
                $category->meta->title = $category_meta[0];
                $category->meta->description = $category_meta[1];
                $category->meta->keywords = $category_meta[2];
                $category->meta->h1 = $category->h1;
                $category->meta->description_text = $category->category_description;
                $category->meta->description_text_footer = $category->description_footer;
            }

            $category_meta = explode('[--2010--]', trim($category->meta_info_fr));

            if (!empty($category_meta[0]) OR ! empty($category_meta[1]) OR ! empty($category_meta[2]) OR ! empty($category->h1_city)) {
                $category->meta_city->title = $category_meta[0];
                $category->meta_city->description = $category_meta[1];
                $category->meta_city->keywords = $category_meta[2];
                $category->meta_city->h1 = $category->h1_city;
                $category->meta_city->description_text = $category->category_description_city;
                $category->meta_city->description_text_footer = $category->description_footer_city;
            }

            $category->meta->canonical = $category->meta_city->canonical = $this->getCanonicalCategory($this->database->getEscaped($category->canonical_alias??$alias));

            $variables = array(
                'option' => 'com_virtuemart',
                'category_id' => $category->category_id,
                'lang' => 'en',
                'page' => 'shop.browse'
            );


            $this->setVariables($variables);
            //$_SERVER['REQUEST_URI'] = '/index.php?'.http_build_query($variables);

            $return->result = true;
            $return->category = $category;
        }
        return $return;
    }


    private function getProduct($alias, $parent) {
        $return = new stdClass();
        $return->result = false;
        //product should have category as parent
        if ($parent->type != "category") {
            return $return;
        }

        $query = "SELECT 
            `p`.`product_id`,
            `p`.`cdate`,
            `p`.`product_name` AS 'name',
            `p`.`meta_info`,
            `p`.`meta_info_fr`,
            `po`.`product_type`,
            (`pp`.`product_price`-`pp`.`saving_price`) AS `product_discounted_price`
        FROM `jos_vm_product` AS `p`
        INNER JOIN `jos_vm_product_price` AS `pp` 
            ON 
            `pp`.`product_id`=`p`.`product_id`
        INNER JOIN `jos_vm_product_options` AS `po`
            ON 
            `po`.`product_id`=`p`.`product_id`";
        if ($parent AND ( (isset($parent->data['menu']) AND $parent->data['menu']->type == 'vm_category') OR isset($parent->data['category']))) {
            if (isset($parent->data['menu']) AND $parent->data['menu']->type == 'vm_category') {
                $category_parent_id = (int) $parent->data['menu']->link;
            } elseif (isset($parent->data['category'])) {
                $category_parent_id = (int) $parent->data['category']->category_id;
            }

            $query .= " INNER JOIN `jos_vm_product_category_xref` AS `pc_x`
            ON `pc_x`.`product_id`=`p`.`product_id`
                AND
                `pc_x`.`category_id`=" . $category_parent_id . "
            ";
        } else {
            return $return;
        }

        $query .= " WHERE 
        (
            `p`.`alias`='" . $this->database->getEscaped($alias) . "'

        )
        AND `p`.`product_publish`='Y'
        ";

        $product = false;
        //f($query);
        $this->database->setQuery($query);
        if ($this->database->loadObject($product)) {
            $product_meta = explode('[--2010--]', trim($product->meta_info));

            $product->meta = (object) [];
            $product->meta_city = (object) [];

            if (!empty($product_meta[0]) OR!empty($product_meta[1]) OR!empty($product_meta[2])) {
                $product->meta->title = $product_meta[0];
                $product->meta->description = $product_meta[1];
                $product->meta->keywords = $product_meta[2];
            }

            $product_meta = explode('[--2010--]', trim($product->meta_info_fr));

            if (!empty($product_meta[0]) OR!empty($product_meta[1]) OR!empty($product_meta[2])) {
                $product->meta_city->title = $product_meta[0];
                $product->meta_city->description = $product_meta[1];
                $product->meta_city->keywords = $product_meta[2];
            }

            $product->meta->canonical = $product->meta_city->canonical = $this->getCanonicalProduct($this->database->getEscaped($alias));

            $variables = array(
                'option' => 'com_virtuemart',
                'product_id' => $product->product_id,
                'lang' => 'en',
                'page' => 'shop.product_details'
            );

            $this->setVariables($variables);
            //$_SERVER['REQUEST_URI'] = '/index.php?'.http_build_query($variables);

            $return->result = true;
            $return->product = $product;
        }
        return $return;
    }

    private function getAbandonment($request_uri) {

        global $mosConfig_live_site;
        $query = "SELECT 
            `hash`,
            `get_parameters`
        FROM `jos_vm_carts`
        WHERE 
        (
            hash like '" . $this->database->getEscaped(strtolower($request_uri)) . "'
        )
        ";
        $alias = false;
        $this->database->setQuery($query);
        if ($this->database->loadObject($alias)) {
            $where = $mosConfig_live_site . "/cart/" . strtolower($alias->hash);
            if ($alias->get_parameters) {
                $where .= "?" . $alias->get_parameters;
            }
            http_response_code(301);
            header('Location: ' . $where);
            die();
        }
        return false;
    }

    private function checkIfUrlExecutedFromSeo($request_uri) {

        $query = "SELECT `id`
        FROM `tbl_disable_indexing`
        WHERE 
        (
            url = '" . $this->database->getEscaped(strtolower($request_uri)) . "'
        )";

        $this->database->setQuery($query);
        if ($this->database->loadResult()) {
            $this->noindex_follow = true;
        }
    }

    /**
     * sets variables for 404
     */
    public function set404() {
        $variables = array(
            'option' => 'com_page_not_found'
        );

        $this->setVariables($variables);

        header('HTTP/1.0 404 Not Found');
        $_SERVER['REQUEST_URI'] = '/index.php?' . http_build_query($variables);
    }

    /**
     * forces rending of 404 right here
     */
    public function run404() {
        global $mainframe, $database;
        $this->set404();

        $option = strval(strtolower(mosGetParam($_REQUEST, 'option')));
        $mainframe = new mosMainFrame($database, $option, '.');
    }

    private function setVariables($variables) {
        foreach ($variables as $k => $v) {
            $_REQUEST[$k] = $v;
            $_GET[$k] = $v;
        }

    }

    function getCategoryAlias($category_id) {
        $query = "SELECT `alias` FROM `jos_vm_category`"
            . " WHERE `category_id`= '$category_id' "
            . " AND `category_publish`='Y'  ";
        $this->database->setQuery($query);

        $alias = false;
        if ($this->database->loadObject($alias)) {
            return $alias->alias;
        } else {
            return false;
        }
    }

    function getCanonicalCategory($alias, $relative = false, $test = false) {
        global $mosConfig_live_site, $mosConfig_lang;
        $alias2 = $alias;

        $aliases = [];
        while ($alias2) {

            $query = "SELECT
                `jos_vm_category`.`category_id`,
                `jos_vm_category`.`alias`,
                `c2`.`alias` AS `parent_alias`
            FROM `jos_vm_category` AS `jos_vm_category`
            LEFT JOIN `jos_vm_category_xref` AS `c_x`
                ON `c_x`.`category_child_id`=`jos_vm_category`.`category_id`
            LEFT JOIN `jos_vm_category` AS `c2`
                ON `c2`.`category_id`=`c_x`.`category_parent_id`
            WHERE
                `jos_vm_category`.`alias`='" . $alias2 . "'
                AND  
                `jos_vm_category`.`category_publish`='Y'
            ";

            $this->database->setQuery($query);

            $category_obj = false;
            if ($this->database->loadObject($category_obj)) {

                $alias =  $category_obj->alias;
                $alias2 = $category_obj->parent_alias;

                $aliases[] = $alias;
                if ($test) {
                    f($category_obj, $alias, $alias2, $aliases);
                }
            } else {
                $alias2 = false;
            }
        }
        $return = ($relative ? '' : $mosConfig_live_site)  . (sizeof($aliases) > 0 ? '/' . implode('/', array_reverse($aliases)) . '/' : '');
        return $return;
    }

    function getCategoryURIById($id) {
        global $mosConfig_lang, $microtimelog;
        $url = '';
        $query = "SELECT c1.`alias`, xr.`category_parent_id` AS `parent_id` 
            FROM `jos_vm_category` c1
            LEFT JOIN `jos_vm_category_xref` xr ON xr.`category_child_id`=c1.`category_id` 
            left join `jos_vm_category` c2 on c2.`category_id`=xr.`category_parent_id` 
            WHERE c1.`category_id`='$id' and ( c2.category_id IS NULL or c2.category_publish like 'Y')
            ";
        $this->database->setQuery($query);

        $category_obj = false;
        $parents = false;
        if ($this->database->loadObject($category_obj)) {

            if ($category_obj->parent_id != '0') {
                $parents = $this->getCategoryURIById($category_obj->parent_id);
                $url = $parents . $category_obj->alias . "/";
            } else {
                $url = $category_obj->alias . "/";
            }
            //     f($query, $category_obj, $parents, $url);
        } else {
            return "";
        }
            return $url;
    }





    function getCity($alias) {
        $return = false;
        //country_id =38 - CANADA
        $query = "SELECT 
            `lp`.`id`,
            `lp`.`city`,
            `lp`.`url`,
            `lp`.`lat`,
            `lp`.`lng`,
            `lp`.`province`,
            `lp`.`nearby_cities`,
            `lp`.`telephone` AS 'phone',
            `s`.`state_name` AS 'state',
            `s`.`state_3_code` AS 'state_short'
        FROM `tbl_landing_pages` AS `lp`
        INNER JOIN `jos_vm_state` AS `s`
            ON 
                `s`.`state_3_code`=`lp`.`province` 
                AND
                `s`.`country_id`=13
        WHERE `lp`.`url`='" . strtolower($this->database->getEscaped($alias)) . "'
        ";
        $this->database->setQuery($query);

        $landing_obj = false;
        if ($this->database->loadObject($landing_obj)) {
            $return = $landing_obj;
        }
        return $return;
    }

    public function getCanonicalProductById($product_id) {
        $query = "select alias from jos_vm_product where product_id like '" . $product_id . "'";
        $this->database->setQuery($query);
        $product = false;
        if ($this->database->loadObject($product)) {
            return $this->getCanonicalProduct($product->alias);
        } else {
            return false;
        }
    }

    function getCanonicalProduct($alias, $relative = false) {
        global $mosConfig_live_site, $mosConfig_lang;
        //we want to search by english alias
        $alias2 = $alias;

        $query = "SELECT
            `jos_vm_category`.`alias`,
              `jos_vm_category`.`category_id`
        FROM `jos_vm_product` AS `p`
        INNER JOIN `jos_vm_product_options` AS `po`
            ON `po`.`product_id`=`p`.`product_id`
        INNER JOIN `jos_vm_category` AS `jos_vm_category`
            ON `jos_vm_category`.`category_id`=`po`.`canonical_category_id`
            AND
            `jos_vm_category`.`category_publish`='Y'
        WHERE
            `p`.`alias`='" . $alias2 . "'
            AND
            `p`.`product_publish`='Y'
        ";

        $this->database->setQuery($query);
        $category_obj = false;
        $canonical = '';

        if ($this->database->loadObject($category_obj)) {
            $canonical = $this->getCanonicalCategory($category_obj->alias, $relative);
        } else {
            $query2 = "SELECT
                `jos_vm_category`.`alias`,
                  `jos_vm_category`.`category_id`
            FROM `jos_vm_product` AS `p`
            INNER JOIN `jos_vm_product_category_xref` AS `pc_x`
                ON
                `pc_x`.`product_id`=`p`.`product_id`
            INNER JOIN `jos_vm_category` AS `jos_vm_category`
                ON
                `jos_vm_category`.`category_id`=`pc_x`.`category_id`
                AND
                `jos_vm_category`.`category_publish`='Y'
            WHERE 
                `p`.`alias`='" . $alias2 . "'
                AND
                `p`.`product_publish`='Y'
            GROUP BY `jos_vm_category`.`alias`
            ORDER BY `jos_vm_category`.`category_id` DESC LIMIT 1";

            $this->database->setQuery($query2);

            $category_obj = false;

            $canonical = '';
            $this->database->loadObject($category_obj);
            if (!$category_obj) {
                // d($alias, $alias2, $query, $query2);
            } else {
                $canonical = $this->getCanonicalCategory($category_obj->alias, $relative);
            }
        }

        return $canonical . $alias . '/';
    }

    private
    function getFooterLinks($real_uri) {
        global $mosConfig_lang;
        $links_obj = '';
        if (isset($_GET['category_id']) AND ( isset($_GET['page']) AND $_GET['page'] == 'shop.browse')) {
            $query = "SELECT
                `l`.`html`,`l`.`ref`
                 FROM `tbl_footer_links_default` AS `l`
                  WHERE `l`.`type`='category'";

            $this->database->setQuery($query);
            $html_obj = $this->database->loadObjectList();

            if ($html_obj) {
                foreach ($html_obj as $h) {
                    if (unserialize($h->ref)) {
                        $ref = unserialize($h->ref);
                        if (in_array($_GET['category_id'], $ref)) {
                            $links_obj = $h->html;
                        }
                    }
                }
            }
        }


        if (!$links_obj) {
            $query = "SELECT
            `tbl_footer_links_default`.`html`
        FROM `tbl_footer_links_default`  
          
            WHERE `tbl_footer_links_default`.`type`='default'";

            $this->database->setQuery($query);
            $html_obj = false;
            $this->database->loadObject($html_obj);
                $links_obj = $html_obj->html;
        }

        //$_GET['category_id'] = 369;

        if (isset($_GET['category_id']) AND ( isset($_GET['page']) AND $_GET['page'] == 'shop.browse')) {
            if ($this->city) {
                $links = array();
                if ($this->city->lat && $this->city->lng) {

                    $lat = (float) $this->city->lat;
                    $lng = (float) $this->city->lng;

                    $category_name = '';
                    if (
                        isset($this->meta_source->data['category'])
                        && is_object($this->meta_source->data['category'])
                        && isset($this->meta_source->data['category']->name)
                    ) {
                        $category_name = (string) $this->meta_source->data['category']->name;
                    }
                    $radius_km = 25.0; // float radius. wtf is a kilometer???
                    $latDelta  = $radius_km / 110.57; // radius / length of 0 degree lat
                    $cosLat    = cos(deg2rad($lat));
                    $lngDelta  = $radius_km / (111.32 * max($cosLat, 0.0001)); // radius / length of 0 degree long

                    // for excluding current city from results
                    $currentId  = isset($this->city->id) ? (int) $this->city->id : null;
                    $currentUrl = isset($this->city->url) ? $this->city->url : null;

                    $sql = "SELECT page.id, page.url, page.city, page.lat, page.lng
                            FROM tbl_landing_pages AS page
                            WHERE page.lat BETWEEN " . ($lat - $latDelta) . " AND " . ($lat + $latDelta) . "
                            AND page.lng BETWEEN " . ($lng - $lngDelta) . " AND " . ($lng + $lngDelta);

                    // excluding current city
                    if ($currentId) {
                        $sql .= " AND page.id <> " . $currentId;
                    } elseif ($currentUrl) {
                        $sql .= " AND page.url <> " . $this->database->Quote($currentUrl);
                    }

                    // limit just to be safe ;)
                    $sql .= " LIMIT 800";

                    $this->database->setQuery($sql);
                    $rows = (array) $this->database->loadObjectList();

                    // Haversine formula
                    $R = 6371.0; // Approximate Earth Radius

                    if (isset($this->meta_source->data['real_uri'])) {
                        $real_uri = (string) $this->meta_source->data['real_uri'];
                    } elseif (isset($this->real_uri)) {
                        $real_uri = (string) $this->real_uri;
                    } else {
                        $real_uri = '';
                    }

                    $uriParts = explode('/', strtolower(trim($real_uri, '/')), 2);
                    $rest     = isset($uriParts[1]) ? $uriParts[1] : '';

                    $lat1    = deg2rad($lat);
                    $lng1    = deg2rad($lng);
                    $cosLat1 = cos($lat1);

                    foreach ($rows as $row) {
                        $lat2 = deg2rad((float) $row->lat);
                        $lng2 = deg2rad((float) $row->lng);

                        $dLat = $lat2 - $lat1;
                        $dLng = $lng2 - $lng1;

                        $sin_dlat = sin($dLat / 2);
                        $sin_dlng = sin($dLng / 2);
                        $a        = $sin_dlat * $sin_dlat + $cosLat1 * cos($lat2) * $sin_dlng * $sin_dlng;

                        $distance = $R * 2 * asin(min(1, sqrt($a)));

                        if ($distance <= $radius_km) {
                            $name = trim($row->city . ' ' . $category_name);
                            $link = '/' . strtolower($row->url) . ($rest !== '' ? '/' . $rest : '') . '/';

                            $links[] = (object) array(
                                'url'       => $row->url,
                                'name'      => $name,
                                'show_hide' => 1,
                                'distance'  => $distance,
                                'link'      => $link,
                            );
                        }
                    }

                    usort($links, function ($a, $b) {
                        if ($a->distance == $b->distance) {
                            return 0;
                        }
                        return ($a->distance < $b->distance) ? -1 : 1;
                    });

                    $links = array_slice($links, 0, 20);
                }
                if (count($links)) {
                    $links_obj = $links;

                    shuffle($links_obj);
                } elseif (!empty($this->city->nearby_cities)) {
                    $cities = array_map('trim', explode(',', $this->city->nearby_cities));
                    $links_obj = [];
                    foreach ($cities as $city) {
                        $query = "SELECT 
                            `lp`.`city`,
                            `lp`.`url`,
                            `jos_vm_category`.`category_name`
                        FROM `tbl_landing_pages` AS `lp`
                        LEFT JOIN `jos_vm_category` AS `jos_vm_category`
                            ON
                                `jos_vm_category`.`category_id`=" . (int) $_GET['category_id'] . "
                        WHERE 
                            `lp`.`city`='" . $this->database->getEscaped($city) . "'
                        ";

                        $this->database->setQuery($query);
                        $city_obj = false;
                        $this->database->loadObject($city_obj);

                        if (!is_null($city_obj)) {
                            $links_obj[] = (object) [
                                'link' => '/' . str_replace($this->city->url, $city_obj->url, $real_uri),
                                'name' => $city_obj->category_name . ' in ' . $city_obj->city
                            ];
                        }
                    }

                    shuffle($links_obj);
                }
            }
        }

        return $links_obj;
    }

}
