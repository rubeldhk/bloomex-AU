<?php
require_once "../configuration.php";
class RevealMenu {

    private $menuType = 'Bloomex_top';
    private $keys = array('id', 'name', 'link', 'parent', 'ordering', 'browserNav');
    private $parents = array();
    private $checkId = array();
    private $controlIdTop = 'w';
    private $controlIdMenu = 'ddmenu';
    private $classTopLi = 'top';

    function __construct($menuType = null, $keys = null, $controlIdTop = null, $controlIdMenu = null, $classTopLi = null) {
        if (!is_null($menuType))
            $this->menuType = $menuType;
        if (!is_null($keys))
            $this->keys = $keys;
        if (!is_null($controlIdTop))
            $this->controlIdTop = $controlIdTop;
        if (!is_null($controlIdMenu))
            $this->controlIdMenu = $controlIdMenu;
        if (!is_null($classTopLi))
            $this->classTopLi = $classTopLi;
    }

    function menu() {
        $menu = $this->selectMenu();
        return "<div id='$this->controlIdTop'><nav>" .
                $this->create(current($this->parents), 0, $menu) .
                "</nav></div>";
    }

    private function mimic_order($link) {
        global $mosConfig_lang;
        $lg = substr($mosConfig_lang, 0, 2);
        $url = parse_url($link);
        $get = array();
        parse_str($url['query'], $get);

        if ($get['option'] == 'com_virtuemart' && $get['page'] == 'shop.browse') {
            $link = "/index.php?option=com_virtuemart&Itemid=" . $get['Itemid'] . "&category_id=" . $get['category_id'] . "&lang=" . $lg . "&page=shop.browse";
        }
        return $link;
    }

    private function create($ids, $position, $menu) {
        global $sess, $database, $mosConfig_absolute_path;
        $result = '';
        if (is_array($ids)) {
            $result .= "<ul" . ( ( $position === 0 ) ? " id='$this->controlIdMenu'" : "" ) . ">";
            foreach ($ids as $value) {
                if (!in_array($value, $this->checkId)) {
                    $this->checkId[] = $value;
                    if ($menu[$value]['browserNav'] == 2) {
                        $link = '#';
                        $target = "onclick='javascript: window.open(\"" . $this->mimic_order($menu[$value]['link']) . "\", \"\", \"toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550\"); return false'";
                    } elseif ($menu[$value]['browserNav'] == 0) {
                        $link = $this->mimic_order($menu[$value]['link']);
                        $target = '';
                    }
                    require_once( $mosConfig_absolute_path . "/components/com_virtuemart/virtuemart_parser.php");
                    $sess = new ps_session;
                    if (isset($sess) && strpos($link, 'com_virtuemart')) {
                        $link = $sess->url($link);
                    } else {
                        $link = sefRelToAbs($link);
                    }
                    $result .= "<li" . ( ( $position === 0 ) ? " class='$this->classTopLi'" : "" ) . ">" .
                            "<a href='" . $link . "' " . $target . ">{$menu[$value]['name']}</a>" .
                            $this->create($this->parents[$value], $position + 1, $menu) .
                            "</li>";
                }
            }
            $result .= "</ul>";
        }
        return $result;
    }

    private function selectMenu() {
        global $database;
        $keys = implode(',', $this->keys);
        $query = "SELECT $keys FROM jos_menu
                WHERE published = '1' AND menutype = '$this->menuType'
                ORDER BY parent, ordering ASC";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        $menu = array();
        if ($result) {
            foreach ($result as $key) {
                foreach ($this->keys as $value) {
                    $menu[$key->id][$value] = $key->$value;
                }
                $this->parents[$key->parent][] = $key->id;
            }
        }
        return $menu;
    }

}

$RevealMenu = new RevealMenu();

echo $RevealMenu->menu();
?>
<script type="text/javascript">
    wMainMenu();
    function wMainMenu() {
        $j(document).ready(function() {
            $j('#new-product-menu #ddmenu li').hover(function() {
                $j('ul', this).slideDown(100);
            },
                    function() {
                        $j('ul', this).slideUp(100);
                    });
        });
    }
</script>

<?php

class RigthSlideBanners {

    static public $pages = array('com_landingpages', 'com_landingbasketpages', 'com_landingpages_funeral');

    function create() {
        global $mosConfig_live_site, $database, $mosConfig_lang;
        if (self::check()) {
            ?>
            <div id="rotatorLanding"><img alt="image" src="<?php echo $mosConfig_live_site; ?>/images/stories/headers<?php echo substr($mosConfig_lang, 0, 2); ?>/s1.jpg" /></div>
            <?php
            return true;
        }
        /*$sql = "SELECT COUNT(`order_id`) FROM jos_vm_orders";
        $database->setQuery($sql);
        $order_count = $database->loadResult();*/

        $database2 = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_temp_database);
        $q = "SELECT * FROM `order_count` ORDER BY id DESC LIMIT 1";
        $res = $database2->query($q);
        $order_count = $res->fetch_assoc();

//        echo '<div id="onumber" style="display:none">' . number_format(600000 + $order_count) . "</div>";
//        echo '<div id="onumber" style="display:none">' . number_format(1000000+$order_count) . "</div>";
        echo '<div id="onumber" style="display:none">' . number_format($order_count['order_count']) . "</div>";
        echo '<div id="onumber" style="display:none"></div>';
        mosLoadModules('user4', -1); //mosLoadModules('newbody', -1); 
       // echo '<div id="rotatorContainer"></div>';
    }

    function check() {
        return ( in_array($_REQUEST['option'], self::$pages) ) ? true : false;
    }

}

RigthSlideBanners::create();
?>
