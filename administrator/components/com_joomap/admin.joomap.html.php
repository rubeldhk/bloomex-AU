<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php

/** HTML class for all JooMap administration output */
class JoomapAdminHtml {
	/* Show the configuration options and menu ordering */
	function show ( &$config, &$menus, &$pageNav, &$lists ) {

		$tabs = new mosTabs(1);			// uses cookie to save last used tab
		mosCommonHTML::loadOverlib();
		?>
		
		<style type="text/css">
		.adminForm label {
			white-space: nowrap;
		}
		.adminForm {
			table-layout: auto;
			text-align: left;
		}
		.adminForm td, .adminForm tr {
			vertical-align: middle;
		}
		</style>
		
		<script type="text/javascript">
        function menu_listItemTask( id, task, option ) {
            var f = document.adminForm;
            cb = eval( 'f.' + id );
            if (cb) {
                cb.checked = true;
                submitbutton(task);
            }
            return false;
        }

        function changeDisplayImage() {
            if (document.adminForm.imageurl.value !='') {
                document.adminForm.imagelib.src='../components/com_joomap/images/' + document.adminForm.imageurl.value;
            } else {
                document.adminForm.imagelib.src='../images/blank.png';
            }
        }

		function addExclude() {
			var exclude = document.adminForm.exclmenus.value.split(',');
			exclude.push( document.adminForm.excl_menus.value );

			//remove duplicates;
			var tmp = new Object();
			for(var i = 0; i < exclude.length; i++) {
				var id = parseInt(exclude[i]);
				if( isNaN(id))
					continue;

				tmp[ id ] = id;
			}
			exclude = new Array();
			for(var k in tmp) {
				exclude.push( tmp[k] );
			}
			
			document.adminForm.exclmenus.value = exclude.join(',');
		}
		</script>

		<form action="index2.php" method="post" name="adminForm">
		
		<table class="adminheading">
		<tr>
		  <th class="menus">
            <small style="margin-left:50px;">
            <?php echo $lists['msg_success']; ?>
            </small>
          </th>
        </tr>
		</table>

		<?php
	        $tabs->startPane( 'joomap' );
	        $tabs->startTab( _JOOMAP_TAB_MENUS, 'menus' );
        ?>

        <span class="componentheading"><?php echo _JOOMAP_CFG_SET_ORDER; ?>:</span>

        <table width="100%" border="0" cellpadding="2" cellspacing="0" class="adminlist">
        <tr style="white-space:nowrap">
          <th width="1%">&nbsp;#</th>
          <th width="1%" style="display:none"> Select </th>
          <th width="1%"><?php echo _JOOMAP_CFG_MENU_SHOW; ?></th>
          <th width="1%"><?php echo _JOOMAP_CFG_MENU_REORDER; ?></th>
          <th width="1%"><?php echo _JOOMAP_CFG_MENU_ORDER; ?></th>
          <th class="title" width="95%"><?php echo _JOOMAP_CFG_MENU_NAME; ?></th>
        </tr>
		<?php
			/** Print list of the website's menus */
	        if ( isset($pageNav->limitstart) ) {								// Obey nav start
				$start = $pageNav->limitstart;
	        } else {
	            $start = 0;
			}
	        $limit = count($menus) - $start;
	        if ( isset($pageNav->limit) && $limit > $pageNav->limit) {			// Obey nav limit
	            $limit = $pageNav->limit;
			}
		
		    $alternate = 0;
		    $keys = array_keys( $menus );                               		// associative array offsets
		
		    for ($i = $start; $i < $start+$limit; ++$i) {
		        $menu = $menus[ $keys[$i] ];                            		// get array element at offset i
		
		        $menu->checked_out = 0;                                 		// get the selection boxes needed for move up/down
		        $checked = mosCommonHTML::CheckedOutProcessing( $menu, $i );
		
		        if ( $menu->show ) {                                    		// Menu is included in sitemap
		            $img = 'tick.png';
		            $alt = _JOOMAP_SHOW;
		            $title = _JOOMAP_CFG_DISABLE;
		        } else {                                                		// Menu not included in sitemap
		            $img = 'publish_x.png';
		            $alt = _JOOMAP_NO_SHOW;
		            $title = _JOOMAP_CFG_ENABLE;
		        }
		        // START: row output
		?>
		<tr class="row<?php echo $alternate; ?>">
		  <td align="right">
		    <?php echo $pageNav->rowNumber( $i ); ?>
		  </td>
		  <td style="display:none">
		    <?php echo $checked; ?>
		  </td>
		  <td align="center">
		    <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $menu->show ? 'unpublish' : 'publish';?>')">
		    <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" title="<?php echo $title; ?>"/>
		    </a>
		  </td>
		  <td align="center">
		    <?php echo $pageNav->orderUpIcon( $i, true ); ?>
		    <?php echo $pageNav->orderDownIcon( $i, $limit, true ); ?>
		  </td>
		  <td align="center">
		    <input type="text" name="order[<?php echo $menu->id; ?>]" size="5" value="<?php echo $menu->ordering; ?>" class="text_area" style="text-align:center" />
		  </td>
		  <td align="left">
		    <?php echo $menu->type; ?>
		  </td>
		</tr>
		<?php
				// END: row output
				$alternate = 1 - $alternate;
			}
		?>
        </table>
        <?php
			echo $pageNav->getListFooter();
			
			$tabs->endTab();
			$tabs->startTab( _JOOMAP_TAB_DISPLAY, 'display' );

			// currently selected external link marker image
			if( preg_match( '/gif|jpg|jpeg|png/i', @$config->ext_image )) {
				$ext_imgurl = $GLOBALS['mosConfig_live_site'].'/components/com_joomap/images/'.$config->ext_image;
			} else {
				$ext_imgurl = $GLOBALS['mosConfig_live_site'].'/images/blank.png';
			}
		?>

        <table width="100%" border="0" cellpadding="2" cellspacing="0" class="adminForm" style="table-layout: auto; white-space: nowrap;">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo _JOOMAP_CFG_OPTIONS; ?></legend>
              <table>
              <tr>                                            
                <td style="width:1%">
                  <label for="classname"><?php echo _JOOMAP_CFG_CSS_CLASSNAME; ?></label>:
                </td>
                <td style="width:32%">
                  <input type="text" name="classname" id="classname" value="<?php echo @$config->classname; ?>"/>
                </td>
                
                <td style="width:1%">
                  <label for="show_menutitle"><?php echo _JOOMAP_CFG_SHOW_MENU_TITLES; ?></label>:
                </td>
                <td style="width:32%">
                  <input type="checkbox" name="show_menutitle" id="show_menutitle" value="1"<?php echo @$config->show_menutitle ? ' checked="checked"' : ''; ?> />
                </td>

                <td style="width:1%">
                   <label for="columns"><?php echo _JOOMAP_CFG_NUMBER_COLUMNS; ?></label>:
                </td style="width:32%">
                <td>
                  <?php echo $lists['columns']; ?>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="expand_category"><?php echo _JOOMAP_CFG_EXPAND_CATEGORIES; ?></label>:
                </td>
                <td>
                  <input type="checkbox" name="expand_category" id="expand_category" value="1"<?php echo @$config->expand_category ? ' checked="checked"' : ''; ?> />
                </td>

                <td>
                  <label for="expand_section"><?php echo _JOOMAP_CFG_EXPAND_SECTIONS; ?></label>:
                </td>
                <td>
                  <input type="checkbox" name="expand_section" id="expand_section" value="1"<?php echo @$config->expand_section ? ' checked="checked"' : ''; ?> />
                </td>

                <td>
                  <label for="include_link"><?php echo _JOOMAP_CFG_INCLUDE_LINK; ?></label>:
                </td>
                <td>
                  <input type="checkbox" name="includelink" id="include_link" value="1"<?php echo @$config->includelink ? ' checked="checked"' : ''; ?> />
                </td>
              </tr>
              <tr>

                <td>
                  <label for="exlinks"><?php echo _JOOMAP_EX_LINK; ?></label>:
                </td>
                <td colspan="4">
                  <input type="checkbox" name="exlinks" id="exlinks" value="1"<?php echo @$config->exlinks ? ' checked="checked"' : ''; ?> />
                  &nbsp;
                  <?php echo $lists['imageurl']; ?>
                  &nbsp;
                  <img src="<?php echo $ext_imgurl; ?>" name="imagelib" alt="" />
                </td>
                
              </tr>
              </table>
            </fieldset>
          </td>
        </tr>
        <tr>
          <td>
            <fieldset>
              <legend><?php echo _JOOMAP_CFG_GOOGLE_MAP; ?></legend>
              <table>
              <tr>
                <td>
                  <?php echo _JOOMAP_CFG_GOOGLE_MAP; ?>:
                </td>
                <td>
                  <a href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/index.php?option=com_joomap&view=google" target="_blank" title="Google Sitemap">
                  <?php echo _JOOMAP_CFG_CLICK_HERE; ?>
                  </a>
                  &nbsp;
		<?php
			$tip = _JOOMAP_CFG_GOOGLE_MAP_TIP;
			echo mosToolTip( $tip );
		?>
                </td>
              </tr>
              <tr>
                <td>
                  <?php echo _JOOMAP_GOOGLE_LINK; ?>:
                </td>
                <td>
		<?php
			$link = $GLOBALS['mosConfig_live_site'] . '/index.php?option=com_joomap&view=google';
		?>
                  <span id="googlelink" style="background:#FFFFCC; padding:1px; border:1px inset;">
                    <?php echo $link; ?>
                  </span>
                  &nbsp;
		<?php
			$tip = _JOOMAP_GOOGLE_LINK_TIP;
			echo mosToolTip( $tip );
		?>
                </td>
              </tr>
              </table>
            </fieldset>
          </td>
        </tr>
        <tr>
          <td>
            <fieldset>
              <legend><?php echo _JOOMAP_EXCLUDE_MENU; ?></legend>
              <table>
              <tr>
                <td>
                  <?php echo _JOOMAP_EXCLUDE_MENU; ?>:
                </td>
                <td>
                  <input type="text" name="exclmenus" id="exclmenus" size="40" value="<?php echo $config->exclmenus; ?>" />
                  &nbsp;
                  <button onclick="addExclude(); return false;">&larr;</button>&nbsp;
                </td>
                <td>
                  <?php echo $lists['exclmenus']; ?>
                  &nbsp;
		<?php
			$tip = _JOOMAP_EXCLUDE_MENU_TIP;
			echo mosToolTip( $tip );
		?>
                </td>
              </tr>
              </table>
            </fieldset>
          </td>
        </tr>
        </table>
		<?php
			$tabs->endTab();
			$tabs->startTab( 'CSS', 'css' );

			$template_path = $GLOBALS['mosConfig_absolute_path'] . '/components/com_joomap/css/joomap.css';

			if ( $fp = @fopen( $template_path, 'r' )) {
				$csscontent = @fread( $fp, @filesize( $template_path ));
				$csscontent = htmlspecialchars( $csscontent );
			}
		?>
        <table cellpadding="1" cellspacing="1" border="0" width="100%">
        <tr>
          <td width="290">
            <table class="adminheading">
            <tr>
              <th class="templates">
                <?php echo _JOOMAP_CSS_EDIT; ?>
              </th>
            </tr>
            </table>
          </td>
          <td width="220">
            <span class="componentheading"><?php echo _JOOMAP_CSS; ?>:
            <strong><?php echo is_writable($template_path) ? '<font color="green">' .  _JOOMAP_CFG_WRITEABLE . '</font>' : '<font color="red">' . _JOOMAP_CFG_UNWRITEABLE . '</font>'; ?></strong>
            </span>
          </td>
		<?php
			if ( mosIsChmodable( $template_path )) {
				if ( is_writable( $template_path )) {
		?>
          <td>
            <input type="checkbox" id="disable_write" name="disable_write" value="1" />
            <label for="disable_write"><?php echo _JOOMAP_MSG_MAKE_UNWRITEABLE; ?></label>
          </td>
		<?php
    	    	} else {
		?>
		  <td>
		    <input type="checkbox" id="enable_write" name="enable_write" value="1" />
		    <label for="enable_write"><?php echo _JOOMAP_MSG_OVERRIDE_WRITE_PROTECTION; ?></label>
		  </td>
		<?php
				}
			}
		?>
        </tr>
        </table>
        
        <table class="adminform">
        <tr>
          <th><?php echo $template_path; ?></th>
        </tr>
        <tr>
          <td>
            <textarea style="width:100%;height:500px" cols="80" rows="25" name="csscontent" class="inputbox"><?php echo $csscontent; ?></textarea>
          </td>
        </tr>
        </table>
		<?php
			$tabs->endTab();
			$tabs->endPane();
		?>

		<input type="hidden" name="option" value="com_joomap" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}
}
?>