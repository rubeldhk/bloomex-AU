<?php defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); ?>
<?php

/**
 * Wraps HTML representation of the Joomap tree as an unordered list (ul)
 * @author Daniel Grothe
 * @see joomla.php
 * @package Joomap
 */

/** Wraps HTML output */
class JoomapHtml {

    function url($text) {
        global $mm_action_url, $page;


        $Itemid = '';


        switch ($text) {
            case SECUREURL:
                $text = SECUREURL . $_SERVER['PHP_SELF'] . "?option=com_virtuemart" . $Itemid;
                break;
            case URL:
                $text = URL . $_SERVER['PHP_SELF'] . "?option=com_virtuemart" . $Itemid;
                break;

            default:
                $limiter = strpos($text, '?');

                $appendix = "";
                // now append "&option=com_virtuemart&Itemid=XX"
                if (!strstr($text, "option=")) {
                    $appendix .= "&option=com_virtuemart";
                }
                $appendix .= $Itemid;

                if (!defined('_PSHOP_ADMIN')) {

                    // be sure that we have the correct PHP_SELF in front of the url
                    if (stristr($_SERVER['PHP_SELF'], "index2.php")) {
                        $prep = "index2.php";
                    } else {
                        $prep = "index.php";
                    }
                    if (stristr($text, "index2.php")) {
                        $prep = "index2.php";
                    }

                    $appendix = $prep . substr($text, $limiter, strlen($text) - 1) . $appendix;
                    $appendix = sefRelToAbs(str_replace($prep . '&', $prep . '?', $appendix));
                    if (!stristr($appendix, URL) && !stristr($appendix, SECUREURL)) {
                        $appendix = $mm_action_url . $appendix;
                    }
                } elseif ($_SERVER['SERVER_PORT'] == 443) {
                    $appendix = SECUREURL . "administrator/index2.php" . substr($text, $limiter, strlen($text) - 1) . $appendix;
                } else {
                    $appendix = URL . "administrator/index2.php" . substr($text, $limiter, strlen($text) - 1) . $appendix;
                }

                if (stristr($text, SECUREURL)) {
                    $appendix = str_replace(URL, SECUREURL, $appendix);
                } elseif (!@strstr($page, "checkout.") && !@strstr($page, "account.")) {
                    $appendix = str_replace(SECUREURL, URL, $appendix);
                }

                $text = $appendix;

                break;
        }

        return $text;
    }

    function &mimic_order($link) {
        global $mosConfig_lang;
        $lg = substr($mosConfig_lang, 0, 2);
        $url = parse_url($link);
        $get = array();
        parse_str(str_replace('&amp;','&',$url['query']), $get);
        if ($get['option'] == 'com_virtuemart' && $get['page'] == 'shop.browse') {
            $link = "/index.php?option=com_virtuemart&Itemid=" . $get['Itemid'] . "&category_id=" . $get['category_id'] . "&lang=" . $lg . "&page=shop.browse";
        }
        return $link;
    }


    function &getHtmlList( &$tree, &$exlink, $level = 0 ) {
        global $Itemid,$mosConfig_absolute_path;;

        if( !$tree ) {
            $result = '';
            return $result;
        }

        $out = '<ul class="list_sitemap level_'.$level.'">';
        foreach($tree as $node) {

            if ( $Itemid == $node->id )
                $out .= '<li class="list_li active">';
            else
                $out .= '<li class="list_li">';

            $link = JoomapHtml::mimic_order($node->link);
            require_once( $mosConfig_absolute_path . "/components/com_virtuemart/virtuemart_parser.php");
            $sess = new ps_session;
            if (isset($sess) && strpos($link, 'com_virtuemart')) {
                $link = $sess->url($link);
            } else {
                $link = sefRelToAbs($link);
            }

            switch( @$node->type ) {
                case 'separator':
                    break;
                case 'url':
                    if ( preg_match( "/index.php\?/i", $link ) ) {
                        if ( strpos( 'Itemid=', $link ) === FALSE ) {
                            $link .= '&amp;Itemid='.$node->id;
                        }
                    }
                    break;
                default:
                    $link .= '&amp;Itemid='.$node->id;
                    break;
            }

            if( strcasecmp( substr( $link, 0, 5), 'http:' ) )
                $link = sefRelToAbs($link);						// apply SEF transformation

            if( !isset($node->browserNav) )
                $node->browserNav = 0;

            if( isset($node->tree) ) {
                switch( $node->browserNav ) {
                    case 1:											// open url in new window
                        $ext_image = '';
                        if( $exlink[0] ){
                            $ext_image = '&nbsp;<img src="'. $GLOBALS['mosConfig_live_site'] .'/components/com_joomap/images/'. $exlink[1] .'" alt="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" title="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
                        }
                        $out .= '<div class="collapsible"><a href="'. $link .'" title="'. $node->name .'" target="_blank">'. $node->name . $ext_image .'</a><span></span></div><div>';
                        break;

                    case 2:											// open url in javascript popup window
                        $ext_image = '';
                        if( $exlink[0] ) {
                            $ext_image = '&nbsp;<img src="'. $GLOBALS['mosConfig_live_site'] .'/components/com_joomap/images/'. $exlink[1] .'" alt="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" title="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
                        }
                        $out .= '<div class="collapsible"><a href="'. $link .'" title="'. $node->name .'" target="_blank" '. "onClick=\"javascript: window.open('". $link ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false;\">". $node->name . $ext_image."</a><span></span></div><div>";
                        break;

                    case 3:											// no link
                        $out .= '<div class="collapsible"><a href="#">'. $node->name .'</a><span></span></div><div>';
                        break;

                    default:										// open url in parent window
                        $out .= '<div class="collapsible"><a href="'. $link .'" title="'. $node->name .'">'. $node->name .'</a><span></span></div><div>';
                        break;
                }

                $out .= JoomapHtml::getHtmlList( $node->tree, $exlink, $level + 1 );
                $out .="</div>";
            }else{
                switch( $node->browserNav ) {
                    case 1:											// open url in new window
                        $ext_image = '';
                        if( $exlink[0] ){
                            $ext_image = '&nbsp;<img src="'. $GLOBALS['mosConfig_live_site'] .'/components/com_joomap/images/'. $exlink[1] .'" alt="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" title="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
                        }
                        $out .= '<a href="'. $link .'" title="'. $node->name .'" target="_blank">'. $node->name . $ext_image .'</a>';
                        break;

                    case 2:											// open url in javascript popup window
                        $ext_image = '';
                        if( $exlink[0] ) {
                            $ext_image = '&nbsp;<img src="'. $GLOBALS['mosConfig_live_site'] .'/components/com_joomap/images/'. $exlink[1] .'" alt="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" title="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
                        }
                        $out .= '<a href="'. $link .'" title="'. $node->name .'" target="_blank" '. "onClick=\"javascript: window.open('". $link ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false;\">". $node->name . $ext_image."</a>";
                        break;

                    case 3:											// no link
                        $out .= '<span>'. $node->name .'</span>';
                        break;
                    case 4:
                        $out .= '<a href="'. $node->link.'" target="_blank" title="'. $node->name .'">'. $node->name .'</a>';
                        break;
                    default:										// open url in parent window
                        $out .= '<a href="'. $link .'" title="'. $node->name .'">'. $node->name .'</a>';
                        break;
                }

            }
            $out .= '</li>' . "\n";
        }
        $out .= '</ul>' . "\n";
        return $out;
    }

    /** Print component heading, etc. Then call getHtmlList() to print list */
    function printTree( &$joomap, &$root ) {
        global $database, $Itemid;

        ?>
        <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex7/js/jquery.cookie.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex7/js/jquery.collapsible.js"></script>
        <link rel="stylesheet" href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex7/css/collapsible.css" />
        <script type="text/javascript">
            $j(document).ready(function() {
                $j('.collapsible').collapsible({ });

                $j('#closeAll').click(function(event) {
                    $j('.collapsible').collapsible('closeAll');
                });
                $j('#openAll').click(function(event) {
                    $j('.collapsible').collapsible('openAll');
                });
            });
        </script>
        <p id="closeAll" title="Close all">Close All</p><p id="openAll" title="Open All">Open All</p>
        <?php

        $config = &$joomap->config;

        $menu = new mosMenu( $database );
        $menu->load( $Itemid );												// Load params for the Joomap menu-item
        $title = $menu->name;

        $exlink[0] = $config->exlinks;										// image to mark popup links
        $exlink[1] = $config->ext_image;

        if( $config->columns > 1 ) {										// calculate column widths
            $total = count($root);
            $columns = $total < $config->columns ? $total : $config->columns;
            $width	= 90;
        }

        echo '<div class="'. $config->classname .'">';
        echo '<h2 class="componentheading">'. $title .'</h2>';
        echo '<div class="contentpaneopen"'. ($config->columns > 1 ? ' style="margin-bottom: 10px;float:left;width:100%;"' : '') .'>';

        if( $config->show_menutitle || $config->columns > 1 ) {				// each menu gets a separate list
            foreach( $root as $menu ) {

                if( $config->columns > 1 )									// use columns
                    echo '<div style="float:left;width:'.$width.'%;padding-right:5px">';

                if( $config->show_menutitle )	{

//						echo '<h2 class="menutitle">'.$menu->name.'</h2>';
                    echo '<div class="collapsible">'.$menu->name.'<span></span></div><div>';
                    echo JoomapHtml::getHtmlList( $menu->tree, $exlink );
                    echo "</div>";
                }							// show menu titles
                if( $config->columns > 1 )
                    echo "</div>\n";
            }

            if( $config->columns > 1 )
                echo '<div style="clear:left"></div>';

        } else {															// don't show menu titles, all items in one big tree
            $tmp = array();
            foreach( $root as $menu ) {										// concatenate all menu-trees
                foreach( $menu->tree as $node ) {
                    $tmp[] = $node;
                }
            }
            echo JoomapHtml::getHtmlList( $tmp, $exlink );
        }

        //BEGIN: Advertisement
        if( $config->includelink ) {
            $keywords = array('Webdesign', 'Software Anpassung', 'Software Entwicklung', 'Programmierung');
            $location = array('Iserlohn', 'Hagen', 'Dortmund', 'Ruhrgebiet', 'NRW');
            $advert = $keywords[mt_rand() % count($keywords)].' '.$location[mt_rand() % count($location)];
            echo "<a href=\"http://www.ko-ca.com\" style=\"font-size:1px;display:none;\">$advert</a>";
        }
        //END: Advertisement

        echo "</div>";
        echo "</div>\n";
    }
};
?>