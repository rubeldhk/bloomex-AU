<?php
/**
* @version $Id: components.menu.html.php 266 2005-09-30 04:44:59Z Levis $
* @package Joomla
* @subpackage Menus
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* Writes the edit form for new and existing content item
*
* A new record is defined when <var>$row</var> is passed with the <var>id</var>
* property set to 0.
* @package Joomla
* @subpackage Menus
*/
class view_menu_html {


	function edit( &$menu, &$components, &$lists, &$params, $option,$changes_history ) {
		global $mosConfig_live_site;

		if ( $menu->id ) {
			$title = '[ '. $lists['componentname'] .' ]';
		} else {
			$title = '';
		}
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			var comp_links = new Array;
			<?php
			foreach ($components as $row) {
				?>
				comp_links[ <?php echo $row->value;?> ] = 'index.php?<?php echo addslashes( $row->link );?>';
				<?php
			}
			?>
                        /*
			if ( form.id.value == 0 ) {
				var comp_id = getSelectedValue( 'adminForm', 'componentid' );
				form.link.value = comp_links[comp_id];
			} else {
				form.link.value = comp_links[form.componentid.value];
			}
                        */
			if ( trim( form.name.value ) == "" ){
				alert( "Item must have a name" );
			} else {
				submitform( pressbutton );
			}
		}
                
                jQuery( document ).ready(function() {
                    jQuery('#new_type').change();
                    
                    jQuery('#new_type').change(function() {
                        jQuery('#type_value').text(jQuery('#new_type option:selected').attr('id'));
                    });

                 jQuery('.value_input').keyup(function() {
                     if(jQuery('#new_type').val()=='vm_category'){
                          get_category_alias(jQuery(this).val());
                     }
                     if(jQuery('#new_type').val()=='blog_item'){
                          get_content_alias(jQuery(this).val());
                     }
                });
                 jQuery('.seo_alias_offer_button').click(function(){
                     jQuery('.seo_alias').val(jQuery('.seo_alias_offer').text())
                 })

                });
        function get_content_alias(content_id){
            $.ajax({
                type: 'POST',
                url: "index2.php",
                data:({
                    option:"com_menus",
                    task:"get_content_alias",
                    content_id: content_id
                }),
                async:false,
                success: function( data ){
                    if(data){
                        jQuery('.seo_alias_offer_div').show();
                        jQuery('.seo_alias_offer').text(data);
                    }else{
                        jQuery('.seo_alias_offer_div').hide();
                        jQuery('.seo_alias_offer').text('');
                    }
                }
            });

        }
		function get_category_alias(category_id){
            $.ajax({
            type: 'POST',
                url: "index2.php",
                data:({
                option:"com_menus",
                task:"get_category_alias",
                category_id: category_id
            }),
                async:false,
                success: function( data ){
                    if(data){
                        jQuery('.seo_alias_offer_div').show();
                        jQuery('.seo_alias_offer').text(data);
                    }else{
                        jQuery('.seo_alias_offer_div').hide();
                        jQuery('.seo_alias_offer').text('');
                    }
            }
        });

        }

		</script>

		<form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th>
			<?php echo $menu->id ? 'Edit' : 'Add';?> Menu Item :: Component <small><small><?php echo $title; ?></small></small>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr valign="top">
			<td width="60%">
				<table class="adminform">
				<tr>
					<th colspan="2">
					Details
					</th>
				</tr>
				<tr>
					<td width="10%" align="right">Name:</td>
					<td width="80%">
					<input class="inputbox seo_name" type="text" name="name" size="50" maxlength="100" value="<?php echo htmlspecialchars( $menu->name, ENT_QUOTES ); ?>" />
					</td>
				</tr>
                                <tr>
					<td width="10%" align="right">
					Alias:
					</td>
					<td width="80%">
					<input class="inputbox seo_alias" type="text" name="alias" size="50" maxlength="100" value="<?php echo $menu->alias; ?>" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">Component:</td>
					<td>
					<?php echo $lists['componentid']; ?>
					</td>
				</tr>
				<tr>
					<td width="10%" align="right" id="type_value">Value:</td>
					<td width="80%">
                       <input class="inputbox value_input" type="text" name="link" size="50" maxlength="100" value="<?php echo $menu->link; ?>" />
                        <div class="seo_alias_offer_div" style="display: none;float: right;width: 50%;">
                            <div class="seo_alias_offer" style="display:inline-block"></div>
                            <button type="button" style="cursor:pointer" class="btn btn-success seo_alias_offer_button">Set As Alias</button>
                        </div>

                    </td>
				</tr>
                                <tr>
					<td valign="top" align="right">
					On Click, Open in:
					</td>
					<td>
					<?php echo $lists['target']; ?>
					</td>
				</tr>
				<tr>
					<td align="right">Parent Item:</td>
					<td>
					<?php echo $lists['parent'];?>
					</td>
				</tr>

				<tr>
					<td valign="top" align="right">Ordering:</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">Access Level:</td>
					<td>
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">Published:</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
                                <tr>
					<td valign="top" align="right">Show:</td>
					<td>
					<?php echo $lists['show']; ?>
					</td>
				</tr>
                                <tr>
					<td valign="top" align="right">Nofollow:</td>
					<td>
					<?php echo $lists['nofollow']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>
			</td>
			<td style="display: inherit;">
                <table class="adminform" border="1" style="display: block;max-height: 270px;overflow-y: scroll;padding: 0;width: fit-content;margin: 0px;">
                    <tr>
                        <th colspan="6">
                            Changes History
                        </th>
                    </tr>
                    <tr>
                        <td><h2>Name</h2></td>
                        <td><h2>Old value</h2></td>
                        <td><h2>New value</h2></td>
                        <td><h2>Username</h2></td>
                        <td><h2>Date</h2></td>
                        <td><h2>Where</h2></td>
                    </tr>
                    <?php echo $changes_history;?>
                </table>
                <br/>
				<table class="adminform">
				<tr>
					<th>
					Parameters
					</th>
				</tr>
				<tr>
					<td>
					<?php
					if ($menu->id) {
						echo $params->render();
					} else {
						?>
						<strong>Parameter list will be available once you save this New menu item</strong>
						<?php
					}
					?>
					</td>
				</tr>
				</table>
				<br/>
				<table class="adminform">
				<tr>
					<th colspan="2">
					Meta Information
					</th>
				</tr>
				<tr>
					<td>
						<b>English Version:</b>
					</td>
				</tr>
				<tr>
					<td>
					Page Title:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="page_title"><?php echo str_replace('&','&amp;', (isset($menu->page_title) ? $menu->page_title : "")); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
					Description:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="metadesc"><?php echo str_replace('&','&amp;',(isset($menu->metadesc) ? $menu->metadesc : "")); ?></textarea>
					</td>
				</tr>
					<tr>
					<td>
					Keywords:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="metakey"><?php echo str_replace('&','&amp;',(isset($menu->metakey) ? $menu->metakey : "" )); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<b>Frech Version:</b>
					</td>
				</tr>
				<tr>
					<td>
					Page Title:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="page_title_fr"><?php echo str_replace('&','&amp;',(isset($menu->page_title_fr) ? $menu->page_title_fr : "")); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
					Description:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="metadesc_fr"><?php echo str_replace('&','&amp;',(isset($menu->metadesc_fr) ? $menu->metadesc_fr : "")); ?></textarea>
					</td>
				</tr>
					<tr>
					<td>
					Keywords:
					<br />
					<textarea class="text_area" cols="30" rows="3" style="width: 350px; height: 50px" name="metakey_fr"><?php echo str_replace('&','&amp;',(isset($menu->metakey_fr) ? $menu->metakey_fr : "")); ?></textarea>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="id" value="<?php echo $menu->id; ?>" />
		<input type="hidden" name="menutype" value="<?php echo $menu->menutype; ?>" />
		<input type="hidden" name="type" value="<?php echo $menu->type; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
		<?php
	}
}
?>