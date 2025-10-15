<?php
/**
* @version $Id: mod_sections.php 4500 2006-08-13 22:45:33Z eddiea $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
global $mosConfig_offset;

/// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

$count 		= intval( $params->get( 'count', 20 ) );
$access 	= !$mainframe->getCfg( 'shownoauth' );
$now 		= _CURRENT_SERVER_TIME;
$nullDate 	= $database->getNullDate();

$query = "SELECT a.id AS id, a.title AS title, COUNT(b.id) as cnt"
. "\n FROM #__sections as a"
. "\n LEFT JOIN #__content as b ON a.id = b.sectionid"
. ( $access ? "\n AND b.access <= $my->gid" : '' )
. "\n AND ( b.publish_up = '$nullDate' OR b.publish_up <= '$now' )"
. "\n AND ( b.publish_down = '$nullDate' OR b.publish_down >= '$now' )"
. "\n WHERE a.scope = 'content'"
. "\n AND a.published = 1"
. ( $access ? "\n AND a.access <= $my->gid" : '' )
. "\n GROUP BY a.id"
. "\n HAVING COUNT( b.id ) > 0"
. "\n ORDER BY a.ordering"
;
$database->setQuery( $query, 0, $count );
$rows = $database->loadObjectList();

$bs 	= $mainframe->getBlogSectionCount();
$bc 	= $mainframe->getBlogCategoryCount();
$gbs 	= $mainframe->getGlobalBlogSectionCount();

if ( $rows ) {
	?>
	<ul>
	<?php
		foreach ($rows as $row) {
			$_Itemid 	= $mainframe->getItemid( $row->id, 0, 0, $bs, $bc, $gbs );
			if ( $Itemid == $_Itemid ) {
				$link 		= sefRelToAbs( "index.php?option=com_content&task=blogsection&id=". $row->id );
			} else {
				$link 		= sefRelToAbs( "index.php?option=com_content&task=blogsection&id=". $row->id ."&Itemid=". $_Itemid );
			}
			?>
			<li>
				<a href="<?php echo $link;?>">
					<?php echo $row->title;?></a>
			</li>
			<?php
		}
		?>
	</ul>
	<?php
}
?>