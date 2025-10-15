<?php defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); ?>
<?php

/**
 * Print "Google Sitemaps" list of the Joomap tree.
 * Does not use "priority" or "changefreq".
 * NOTE: When logged in, the tree will also contain private items!
 * @author Daniel Grothe
 * @see joomla.html.php
 * @see joomla.google.php
 * @package Joomap
 */

/** Wraps Google Sitemaps output */
class JoomapGoogle {

    /** Convert sitemap tree to a Google Sitemaps list */
    function &getList( &$tree ) {
        global $Itemid, $mosConfig_live_site;
        if( !$tree )
            return '';

        $out = '';

        $len_live_site = strlen( $mosConfig_live_site );
        foreach($tree as $node) {
            $link = $node->link;
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

            if( strcasecmp( substr($link, 0, 5), 'http:' ) != 0 ) {
                $link = sefRelToAbs($link);									// make path absolute and apply SEF transformation (if any)

                if( strcasecmp( substr($link,0,9), 'index.php' ) === 0 ){	// fix broken sefRelToAbs()
                    $link = $mosConfig_live_site. '/' .$link;
                }

                if( strncmp($link, '/', 1) === 0) {							// removes the dash again, when live_site URL is empty
                    $link = $mosConfig_live_site.$link;
                }
            }

            $is_extern = ( 0 != strcasecmp( substr($link, 0, $len_live_site), $mosConfig_live_site ) );

            if( !isset($node->browserNav) )
                $node->browserNav = 0;

            if( $node->browserNav != 3										// ignore "no link"
                && !$is_extern) {												// ignore external links

                $out .= "<url>\n";
                $out .= " <loc>". $link ."</loc>\n";						// http://complete-url
                if( isset($node->modified) && $node->modified != FALSE && $node->modified != -1) {
                    $modified = date('Y-m-d\TH:i:s', $node->modified);		// ISO 8601 yyyy-mm-ddThh:mm:ss.sTZD
                    $modified .= sprintf("%+03d:00", $GLOBALS['mosConfig_offset']);	//TimeZoneDesignator
                    $out .= " <lastmod>". $modified ."</lastmod>\n";
                }
                //$out .= " <changefreq>always</changefreq>";				// always, hourly, daily, weekly, monthly, yearly, never
                //$out .= " <priority>0.8</priority>";						// 0.0 - 1.0

                $out .= "</url>\n";
            }

            if( isset($node->tree) ) {
                $out .= JoomapGoogle::getList( $node->tree );
            }
        }
        return $out;
    }

    /** Print a Google Sitemaps representation of tree */
    function printTree( &$joomap, &$root ) {
        header('Content-type: text/xml;');
        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";

        $tmp = array();
        foreach( $root as $menu ) {											// concatenate all menu-trees
            foreach( $menu->tree as $node ) {
                $tmp[] = $node;
            }
        }
        echo JoomapGoogle::getList( $tmp );

        echo "</urlset>\n";
        die();
    }
};
?>