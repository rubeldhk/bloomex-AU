<?php
/**
 * SEF module for Joomla!
 * Originally written for Mambo as 404SEF by W. H. Welch.
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF
 * @version     $Id: admin.sef.html.php 369 2008-04-09 21:12:53Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20}
 *
 * changed 2008.02.24 mic [ http://www.joomx.com ]
 */
 
// Security check to ensure this file is being included by a parent file.
if (!defined('_VALID_MOS')) die('Direct Access to this location is not allowed.');

class HTML_sef {

	/**
	 * building a input field
	 *
	 * @param int $x
	 * @param string $pTitle
	 * @param string $pToolTip
	 * @param string $pName
	 * @param string $pValue
	 * @param string $pSize
	 * @param string $pLength
	 * @param int $w1
	 * @param int $w2
	 *
	 * @return string
	 * @since 2008.02.25 (mic): $w1, $w2, check for tooltip text
	 */
	function shTextParamHTML( $x, $pTitle, $pToolTip, $pName, $pValue, $pSize, $pLength, $w1 = '200', $w2 = '150' ) {
		$output  = '<tr' . ( ( $x % 2 ) ? '' : ' class="row1"' ) . '>' . "\n"
		. '<td width="' . $w1 . '">' . $pTitle . '</td>' . "\n"
		. '<td width="' . $w2 . '"><input type="text" name="' . $pName . '" id="' . $pName . '" value="' . $pValue .'"'
			. ' size="' . $pSize . '" maxlength="' . $pLength . '" /></td>' . "\n"
		. '<td>' . ( ( $pToolTip || $pTitle ) ? mosToolTip( $pToolTip, $pTitle ) : '&nbsp;' ) . '</td>' . "\n"
		. '</tr>' . "\n"
		;
		echo $output;
	}

	/**
	 * building a yes/no field
	 *
	 * @param int $x
	 * @param string $pTitle
	 * @param string $pToolTip
	 * @param string $pName
	 * @param int $w1
	 * @param int $w2
	 *
	 * @return string
	 * @since 2008.02.25 (mic): $w1, $w2, check for tooltip text
	 */
	function shYesNoParamHTML( $x, $pTitle, $pToolTip, $pName, $w1 = '200', $w2 = '150' ) {
		$output  = '<tr'. ( ( $x % 2 ) ? '' : ' class="row1"' ).">\n"
		. '<td width="' . $w1 . '">' . $pTitle . '</td>' . "\n"
	    . '<td width="' . $w2 . '">' . $pName . '</td>' . "\n"
	    . '<td>' . ( ( $pToolTip || $pTitle ) ? mosToolTip( $pToolTip, $pTitle ) : '&nbsp;' ) . '</td>' . "\n"
	    . '</tr>' . "\n"
	    ;
	    echo $output;
	}

	/**
	 * Helper function to find correct cms version
	 *
	 * @return bool
	 * @author mic [ http://www.joomx.com ]
	 * @since 2008.02.24
	 */
	function shCheckCMSVersion() {
		global $_VERSION;

		if( $_VERSION->RELEASE >= '1.0' && $_VERSION->DEV_LEVEL >= '14' ) {
			return true;
		}else{
			return false;
		}
	}

	function configuration(&$lists, $txt404) {
		global $sefConfig, $sef_config_file;

		$tabs = new mosTabs( 1 );
		// new mic 2008.02.24
		mosCommonHTML::loadOverlib( HTML_sef::shCheckCMSVersion() ); ?>
		<table class="adminheading">
			<tr>
				<th>
					<?php
					echo _COM_SEF_TITLE_CONFIG
					. ( ( file_exists( $sef_config_file ) ) ? ( ( is_writable( $sef_config_file ) ) ? _COM_SEF_WRITEABLE : _COM_SEF_UNWRITEABLE ) : _COM_SEF_USING_DEFAULT ); ?>
					<br/>
					<span class="small"><a href="index2.php?option=com_sef" title="<?php echo _COM_SEF_BACK?>"><?php echo _COM_SEF_BACK?></a></span>
				</th>
			</tr>
		</table>
		<?php
		if( !$GLOBALS['mosConfig_sef'] ) { ?>
			<div style="margin: 10px 5px; padding: 5px 15px 5px 35px; min-height: 25px; border: 1px solid #cc0000; background: #FFFFEF; text-align: left; color: red; font-weight: bold; background-image: url(../includes/js/ThemeOffice/warning.png); background-repeat: no-repeat; background-position: 10px 50%;"><?php echo _COM_SEF_DISABLED; ?></div>
			<?php
		}
		$x = 0; ?>
		<script type="text/javascript">
        	/* <![CDATA[ */
        	function submitbutton(pressbutton) {
        		if (pressbutton == 'save') {
        			var eraseCache = confirm("<?php echo _COM_SEF_CONFIRM_ERASE_CACHE; ?>");
        			if (eraseCache) {
        				document.getElementById("eraseCache").value = "1";
        			}
        		}
				<?php getEditorContents( 'editor1', 'introtext' ) ; ?>
				submitform( pressbutton );
			};
			/* ]]> */
		</script>
        <form action="index2.php?option=com_sef&amp;task=saveconfig" method="post" name="adminForm" id="adminForm">
        	<?php
        	$tabs->startPane( 'sh404SEFConf' );
        	$tabs->startTab( _COM_SEF_SH_CONF_TAB_MAIN, 'basics' ); ?>
        	<table class="adminform">
        		<tr>
        			<th colspan="4"><?php echo _COM_SEF_TITLE_BASIC; ?></th>
        		</tr>
        		<?php
        		$x = 1;
        		echo HTML_sef::shYesNoParamHTML( $x,
        		_COM_SEF_ENABLED,
        		_COM_SEF_TT_ENABLED,
        		$lists['enabled'] );
        		$x++;
				echo HTML_sef::shTextParamHTML( $x,
				_COM_SEF_REPLACE_CHAR,
				_COM_SEF_TT_REPLACE_CHAR,
				'replacement',
				$sefConfig->replacement, 1, 1 );

	            if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
	            	$x++;
					echo HTML_sef::shTextParamHTML( $x,
					_COM_SEF_PAGEREP_CHAR,
					_COM_SEF_TT_PAGEREP_CHAR,
					'pagerep',
					$sefConfig->pagerep, 1, 1 );
	            }

	            $x++;
				echo HTML_sef::shTextParamHTML( $x,
				_COM_SEF_STRIP_CHAR,
				_COM_SEF_TT_STRIP_CHAR,
				'stripthese',
				$sefConfig->stripthese, 60, 255 ); ?>
	            <!-- shumisha 2007-04-01 new param for characters replacement table  -->
	            <tr<?php $x++; echo ( ( $x % 2) ? '' : ' class="row1"' ); ?>>
	            	<td valign="top"><?php echo _COM_SEF_SH_REPLACEMENTS; ?></td>
	            	<td><textarea name="shReplacements" cols="60" rows="5"><?php echo $sefConfig->shReplacements;?></textarea></td>
	            	<td><?php echo mosToolTip( _COM_SEF_TT_SH_REPLACEMENTS ); ?></td>
	            </tr>
	            <!-- shumisha 2007-04-01 end of new param for characters replacement table  -->
	            <?php
	            if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
	            	$x++;
					echo HTML_sef::shTextParamHTML( $x,
					_COM_SEF_FRIENDTRIM_CHAR,
					_COM_SEF_TT_FRIENDTRIM_CHAR,
					'friendlytrim',
					$sefConfig->friendlytrim, 60, 255 );
	            } ?>
	            <tr<?php $x++; echo( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
	            	<td><?php echo _COM_SEF_USE_ALIAS; ?></td>
	            	<td><?php echo $lists['usealias']; ?></td>
	            	<td><?php echo mosToolTip( _COM_SEF_TT_USE_ALIAS ); ?></td>
	            </tr>
	            <?php
	            $x++;
				echo HTML_sef::shTextParamHTML( $x,
				_COM_SEF_SUFFIX,
				_COM_SEF_TT_SUFFIX,
				'suffix',
				$sefConfig->suffix, 6, 6 );
	            if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
		            $x++;
					echo HTML_sef::shTextParamHTML( $x,
					_COM_SEF_ADDFILE,
					_COM_SEF_TT_ADDFILE,
					'addFile',
					$sefConfig->addFile, 60, 60 );
			        $x++;
			        echo HTML_sef::shYesNoParamHTML( $x,
			        _COM_SEF_LOWER,
			        _COM_SEF_TT_LOWER,
			        $lists['lowercase'] );
			        $x++;
			        echo HTML_sef::shYesNoParamHTML( $x,
			        _COM_SEF_404PAGE,
			        _COM_SEF_TT_404PAGE,
			        $lists['page404'] );
				} ?>
	        	<!-- shumisha 2007-04-01 new params for Numerical Id insert  -->
	        	<tr><th colspan="3"><?php echo _COM_SEF_SH_INSERT_NUMERICAL_ID_TITLE; ?></th></tr>
	        	<?php
	        	$x = 1;
	        	echo HTML_sef::shYesNoParamHTML( $x,
	        	_COM_SEF_SH_INSERT_NUMERICAL_ID_TITLE,
	        	_COM_SEF_TT_SH_INSERT_NUMERICAL_ID,
	        	$lists['shInsertNumericalId'] );
	        	$x++;
	        	echo HTML_sef::shYesNoParamHTML( $x,
	        	_COM_SEF_SH_INSERT_NUMERICAL_ID_CAT_LIST,
	        	_COM_SEF_TT_SH_INSERT_NUMERICAL_ID_CAT_LIST,
	        	$lists['shInsertNumericalIdCatList'] ); ?>
	        	<!-- shumisha 2007-04-01 end of new params for Numerical Id insert  -->
	        </table>
	        <?php
	        $tabs->endTab();
	        $tabs->startTab( _COM_SEF_SH_CONF_TAB_PLUGINS, 'plugins' ); ?>
	        <table class="adminform">
	        	<!-- shumisha 2007-06-30 new params for regular content  -->
	        	<tr>
	        		<th colspan="3"><?php echo _COM_SEF_SH_CONTENT_TITLE; ?></th>
	        	</tr>
	        	<?php
	        	$x = 1;
	        	echo HTML_sef::shYesNoParamHTML( $x,
	        	_COM_SEF_SHOW_SECT,
	        	_COM_SEF_TT_SHOW_SECT,
	        	$lists['showsection'] );
	        	$x++;
	        	echo HTML_sef::shYesNoParamHTML( $x,
	        	_COM_SEF_SHOW_CAT,
	        	_COM_SEF_TT_SHOW_CAT,
	        	$lists['showcat'] );

	        	if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_INSERT_CONTENT_TABLE_NAME,
	        		_COM_SEF_TT_SH_INSERT_CONTENT_TABLE_NAME,
	        		$lists['shInsertContentTableName'] );
	        		$x++;
    				echo HTML_sef::shTextParamHTML( $x,
    				_COM_SEF_SH_CONTENT_TABLE_NAME,
  					_COM_SEF_TT_SH_CONTENT_TABLE_NAME,
  					'shContentTableName',
  					$sefConfig->shContentTableName, 30, 30 );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_INSERT_CONTENT_BLOG_NAME,
	        		_COM_SEF_TT_SH_INSERT_CONTENT_BLOG_NAME,
	        		$lists['shInsertContentBlogName'] );
	        		$x++;
    				echo HTML_sef::shTextParamHTML( $x,
    				_COM_SEF_SH_CONTENT_BLOG_NAME,
  					_COM_SEF_TT_SH_CONTENT_BLOG_NAME,
  					'shContentBlogName',
  					$sefConfig->shContentBlogName, 30, 30 );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_INSERT_CONTENT_MULTIPAGES_TITLE,
	        		_COM_SEF_TT_SH_INSERT_CONTENT_MULTIPAGES_TITLE,
	        		$lists['shMultipagesTitle'] );
	        	}
	        	if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) { ?>
	        		<!-- shumisha 2007-04-01 new params for Virtuemart  -->
        			<tr>
        				<th colspan="3"><?php echo _COM_SEF_SH_VM_TITLE; ?></th>
        			</tr>
        			<?php
	        		$x = 1;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_VM_INSERT_SHOP_NAME,
	        		_COM_SEF_TT_SH_VM_INSERT_SHOP_NAME . _COM_SEF_TT_SH_NAME_BY_COMP,
	        		$lists['shVmInsertShopName'] );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_INSERT_PRODUCT_NAME,
	        		_COM_SEF_TT_SH_INSERT_PRODUCT_NAME,
	        		$lists['shVmInsertProductName'] );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_INSERT_PRODUCT_ID,
	        		_COM_SEF_TT_SH_INSERT_PRODUCT_ID,
	        		$lists['shInsertProductId'] );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_VM_USE_PRODUCT_SKU_124S,
	        		_COM_SEF_TT_SH_VM_USE_PRODUCT_SKU_124S,
	        		$lists['shVmUseProductSKU'] );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_VM_INSERT_MANUFACTURER_NAME,
	        		_COM_SEF_TT_SH_VM_INSERT_MANUFACTURER_NAME,
	        		$lists['shVmInsertManufacturerName'] );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_VM_INSERT_MANUFACTURER_ID,
	        		_COM_SEF_TT_SH_VM_INSERT_MANUFACTURER_ID,
	        		$lists['shInsertManufacturerId'] );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_VM_INSERT_CATEGORIES,
	        		_COM_SEF_TT_SH_VM_INSERT_CATEGORIES,
	        		$lists['shVMInsertCategories'] );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_VM_INSERT_CATEGORY_ID,
	        		_COM_SEF_TT_SH_VM_INSERT_CATEGORY_ID,
	        		$lists['shInsertCategoryId'] );
	        		$x++;
	        		echo HTML_sef::shYesNoParamHTML( $x,
	        		_COM_SEF_SH_VM_ADDITIONAL_TEXT,
	        		_COM_SEF_TT_SH_VM_ADDITIONAL_TEXT,
	        		$lists['shVmAdditionalText'] );
					$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_VM_INSERT_FLYPAGE,
                	_COM_SEF_TT_SH_VM_INSERT_FLYPAGE,
                	$lists['shVmInsertFlypage'] );

					$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_VM_USE_ITEMS_PER_PAGE,
                	_COM_SEF_TT_VM_USE_ITEMS_PER_PAGE,
                	$lists['shVmUsingItemsPerPage'] ); ?>
                	<!-- shumisha 2007-04-01 end of new params for Virtuemart  -->
                	<!-- shumisha 2007-04-25 new params for Community Builder  -->
                	<tr>
                		<th colspan="3"><?php echo _COM_SEF_SH_CB_TITLE; ?></th>
                	</tr>
                	<?php
                	$x = 1;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_CB_INSERT_NAME,
                	_COM_SEF_TT_SH_CB_INSERT_NAME . _COM_SEF_TT_SH_NAME_BY_COMP,
                	$lists['shInsertCBName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_CB_INSERT_USER_NAME,
                	_COM_SEF_TT_SH_CB_INSERT_USER_NAME,
                	$lists['shCBInsertUserName'] );
					$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_CB_INSERT_USER_ID,
                	_COM_SEF_TT_SH_CB_INSERT_USER_ID,
                	$lists['shCBInsertUserId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_CB_USE_USER_PSEUDO,
                	_COM_SEF_TT_SH_CB_USE_USER_PSEUDO,
                	$lists['shCBUseUserPseudo'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_CB_SHORT_USER_URL,
                	_COM_SEF_TT_SH_CB_SHORT_USER_URL,
                	$lists['shCBShortUserURL'] ); ?>
                	<!-- shumisha 2007-04-25 new params for Community Builder  -->
                	<!-- shumisha 2007-04-25 new params for Fireboard  -->
                	<tr>
                		<th colspan="3"><?php echo _COM_SEF_SH_FB_TITLE; ?></th>
                	</tr>
                	<?php
                	$x = 1;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_FB_INSERT_NAME,
                	_COM_SEF_TT_SH_FB_INSERT_NAME . _COM_SEF_TT_SH_NAME_BY_COMP,
                	$lists['shInsertFireboardName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_FB_INSERT_CATEGORY_NAME,
                	_COM_SEF_TT_SH_FB_INSERT_CATEGORY_NAME,
                	$lists['shFbInsertCategoryName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_FB_INSERT_CATEGORY_ID,
                	_COM_SEF_TT_SH_FB_INSERT_CATEGORY_ID,
                	$lists['shFbInsertCategoryId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_FB_INSERT_MESSAGE_SUBJECT,
                	_COM_SEF_TT_SH_FB_INSERT_MESSAGE_SUBJECT,
                	$lists['shFbInsertMessageSubject'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_FB_INSERT_MESSAGE_ID,
                	_COM_SEF_TT_SH_FB_INSERT_MESSAGE_ID,
                	$lists['shFbInsertMessageId'] ); ?>
	                <!-- shumisha 2007-04-25 new params for Fireboardr  -->
	                <!-- shumisha 2007-06-21 new params for Docman  -->
	                <tr>
	                	<th colspan="3"><?php echo _COM_SEF_SH_DOCMAN_TITLE; ?></th>
	                </tr>
	                <?php
	                $x = 1;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_NAME,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_NAME . _COM_SEF_TT_SH_NAME_BY_COMP,
                	$lists['shInsertDocmanName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_DOC_ID,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_DOC_ID,
                	$lists['shDocmanInsertDocId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_DOC_NAME,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_DOC_NAME,
                	$lists['shDocmanInsertDocName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_CAT_ID,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_CAT_ID,
                	$lists['shDMInsertCategoryId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_CATEGORIES,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_CATEGORIES,
                	$lists['shDMInsertCategories'] ); ?>
			        <!-- shumisha 2007-06-21 new params for Docman  -->
			        <!-- shumisha 2007-08-12 new params for Remository  -->
			        <tr>
			        	<th colspan="3"><?php echo _COM_SEF_SH_REMO_TITLE; ?></th>
			        </tr>
			        <?php
			        $x = 1;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_REMO_INSERT_NAME,
                	_COM_SEF_TT_SH_REMO_INSERT_NAME . _COM_SEF_TT_SH_NAME_BY_COMP,
                	$lists['shInsertRemoName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_DOC_ID,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_DOC_ID,
                	$lists['shRemoInsertDocId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_DOC_NAME,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_DOC_NAME,
                	$lists['shRemoInsertDocName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_CAT_ID,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_CAT_ID,
                	$lists['shRemoInsertCategoryId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_CATEGORIES,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_CATEGORIES,
                	$lists['shRemoInsertCategories'] ); ?>
			        <!-- shumisha 2007-08-12 new params for Remository  -->
			        <!-- shumisha 2007-04-01 new params for Letterman  -->
			        <tr>
			        	<th colspan="3"><?php echo _COM_SEF_SH_LETTERMAN_TITLE; ?></th>
			        </tr>
			        <?php
			        $x++;
    				echo HTML_sef::shTextParamHTML( $x,
    				_COM_SEF_SH_LETTERMAN_DEFAULT_ITEMID,
  					_COM_SEF_TT_SH_LETTERMAN_DEFAULT_ITEMID,
  					'shLMDefaultItemid',
  					$sefConfig->shLMDefaultItemid, 10, 10 ); ?>
			        <!-- shumisha 2007-04-01 end of new params for Letterman  -->
			        <!-- shumisha 2007-06-21 new params for MyBlog  -->
			        <tr>
			        	<th colspan="3"><?php echo _COM_SEF_SH_MYBLOG_TITLE;?></th>
			        </tr>
			        <?php
			        $x = 1;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_MYBLOG_INSERT_NAME,
                	_COM_SEF_TT_SH_MYBLOG_INSERT_NAME . _COM_SEF_TT_SH_NAME_BY_COMP,
                	$lists['shInsertMyBlogName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_MYBLOG_INSERT_POST_ID,
                	_COM_SEF_TT_SH_MYBLOG_INSERT_POST_ID,
                	$lists['shMyBlogInsertPostId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_MYBLOG_INSERT_TAG_ID,
                	_COM_SEF_TT_SH_MYBLOG_INSERT_TAG_ID,
                	$lists['shMyBlogInsertTagId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_MYBLOG_INSERT_BLOGGER_ID,
                	_COM_SEF_TT_SH_MYBLOG_INSERT_BLOGGER_ID,
                	$lists['shMyBlogInsertBloggerId'] ); ?>
			        <!-- shumisha 2007-06-21 new params for Myblog  -->
			        <!-- shumisha 2007-08-06 new params for Mosets Tree  -->
			        <tr>
			        	<th colspan="3"><?php echo _COM_SEF_SH_MTREE_TITLE; ?></th>
			        </tr>
			        <?php
			        $x = 1;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_MTREE_INSERT_NAME,
                	_COM_SEF_TT_SH_MTREE_INSERT_NAME . _COM_SEF_TT_SH_NAME_BY_COMP,
                	$lists['shInsertMTreeName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_MTREE_INSERT_LISTING_ID,
                	_COM_SEF_TT_SH_MTREE_INSERT_LISTING_ID,
                	$lists['shMTreeInsertListingId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_MTREE_PREPEND_LISTING_ID,
                	_COM_SEF_TT_SH_MTREE_PREPEND_LISTING_ID,
                	$lists['shMTreePrependListingId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_MTREE_INSERT_LISTING_NAME,
                	_COM_SEF_TT_SH_MTREE_INSERT_LISTING_NAME,
                	$lists['shMTreeInsertListingName'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_CAT_ID,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_CAT_ID,
                	$lists['shMTreeInsertCategoryId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_DOCMAN_INSERT_CATEGORIES,
                	_COM_SEF_TT_SH_DOCMAN_INSERT_CATEGORIES,
                	$lists['shMTreeInsertCategories'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_CB_INSERT_USER_ID,
                	_COM_SEF_TT_SH_CB_INSERT_USER_ID,
                	$lists['shMTreeInsertUserId'] );
                	$x++;
                	echo HTML_sef::shYesNoParamHTML( $x,
                	_COM_SEF_SH_CB_INSERT_USER_NAME,
                	_COM_SEF_TT_SH_CB_INSERT_USER_NAME,
                	$lists['shMTreeInsertUserName'] ); ?>
			       	<!-- shumisha 2007-11-27 new params for com_smf  -->
			       	<tr>
			       		<th colspan="3"><?php echo _COM_SEF_SH_COM_SMF_TITLE; ?></th>
			       	</tr>
			       	<?php
			       	$x = 1;
			       	echo HTML_sef::shYesNoParamHTML( $x,
			       	_COM_SEF_SH_INSERT_SMF_NAME,
			       	_COM_SEF_TT_SH_INSERT_SMF_NAME,
    				$lists['shInsertSMFName'] );
    				$x++;
    				echo HTML_sef::shTextParamHTML( $x,
    				_COM_SEF_SH_SMF_ITEMS_PER_PAGE,
  					_COM_SEF_TT_SH_SMF_ITEMS_PER_PAGE,
  					'shSMFItemsPerPage',
  					$sefConfig->shSMFItemsPerPage, 5, 5 );
  					$x++;
  					echo HTML_sef::shYesNoParamHTML( $x,
  					_COM_SEF_SH_INSERT_SMF_BOARD_ID,
  					_COM_SEF_TT_SH_INSERT_SMF_BOARD_ID,
    				$lists['shInsertSMFBoardId'] );
    				$x++;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_SMF_TOPIC_ID,
    				_COM_SEF_TT_SH_INSERT_SMF_TOPIC_ID,
    				$lists['shInsertSMFTopicId'] );
    				$x++;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_SMF_USER_NAME,
    				_COM_SEF_TT_SH_INSERT_SMF_USER_NAME,
    				$lists['shinsertSMFUserName'] );

    				$x++;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_SMF_USER_ID,
    				_COM_SEF_TT_SH_INSERT_SMF_USER_ID,
    				$lists['shInsertSMFUserId'] ); ?>
    				<!-- shumisha 2007-11-27 new params for com_smf  -->
    				<!-- shumisha 2007-04-25 new params for iJoomla magazine  -->
    				<tr>
    					<th colspan="3"><?php echo _COM_SEF_SH_IJOOMLA_MAG_TITLE; ?></th>
    				</tr>
    				<?php
    				$x = 1;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_ACTIVATE_IJOOMLA_MAG,
    				_COM_SEF_TT_SH_ACTIVATE_IJOOMLA_MAG,
    				$lists['shActivateIJoomlaMagInContent'] );
    				$x++;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_IJOOMLA_MAG_NAME,
    				_COM_SEF_TT_SH_INSERT_IJOOMLA_MAG_NAME . _COM_SEF_TT_SH_NAME_BY_COMP,
    				$lists['shInsertIJoomlaMagName'] );
    				$x++;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_IJOOMLA_MAG_MAGAZINE_ID,
    				_COM_SEF_TT_SH_INSERT_IJOOMLA_MAG_MAGAZINE_ID,
    				$lists['shInsertIJoomlaMagMagazineId'] );
    				$x++;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_IJOOMLA_MAG_ISSUE_ID,
    				_COM_SEF_TT_SH_INSERT_IJOOMLA_MAG_ISSUE_ID,
    				$lists['shInsertIJoomlaMagIssueId'] );
    				$x++;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_IJOOMLA_MAG_ARTICLE_ID,
    				_COM_SEF_TT_SH_INSERT_IJOOMLA_MAG_ARTICLE_ID,
    				$lists['shInsertIJoomlaMagArticleId'] ); ?>
    				<!-- shumisha 2007-04-25 new params for iJoomla magazine  -->
    				<!-- shumisha 2007-08-07 new params for iJoomla NewsPortal  -->
    				<tr>
    					<th colspan="3"><?php echo _COM_SEF_SH_IJOOMLA_NEWSP_TITLE;?></th>
    				</tr>
    				<?php
    				$x = 1;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_IJOOMLA_NEWSP_NAME,
    				_COM_SEF_TT_SH_INSERT_IJOOMLA_NEWSP_NAME . _COM_SEF_TT_SH_NAME_BY_COMP,
    				$lists['shInsertNewsPName'] );
    				$x++;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_IJOOMLA_NEWSP_CAT_ID,
    				_COM_SEF_TT_SH_INSERT_IJOOMLA_NEWSP_CAT_ID,
    				$lists['shNewsPInsertCatId'] );
    				$x++;
    				echo HTML_sef::shYesNoParamHTML( $x,
    				_COM_SEF_SH_INSERT_IJOOMLA_NEWSP_SECTION_ID,
    				_COM_SEF_TT_SH_INSERT_IJOOMLA_NEWSP_SECTION_ID,
    				$lists['shNewsPInsertSecId'] ); ?>
    				<!-- shumisha 2007-08-07 new params for iJoomla NewsPortal  -->
    				<?php
	        	} ?>
	        </table>
	        <?php
	        $tabs->endTab();
	        $tabs->startTab( _COM_SEF_SH_CONF_TAB_LANGUAGES, 'Languages' ); ?>
	        <table class="adminform">
	        	<!-- shumisha 27/09/2007 new params for languages management  -->
	        	<tr>
	        		<th colspan="3"><?php echo _COM_SEF_SH_TRANSLATION_TITLE; ?></th>
	        	</tr>
	        	<?php
	        	$x = 1;
				echo HTML_sef::shYesNoParamHTML( $x,
				_COM_SEF_SH_TRANSLATE_URL,
				_COM_SEF_TT_SH_TRANSLATE_URL_GEN,
				$lists['shTranslateURL'] );
				$x++;
				echo HTML_sef::shYesNoParamHTML( $x,
				_COM_SEF_SH_INSERT_LANGUAGE_CODE,
				_COM_SEF_TT_SH_INSERT_LANGUAGE_CODE_GEN,
				$lists['shInsertLanguageCode'] );

	        	if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
	        		foreach( $lists['activeLanguages'] as $currentLang ) { ?>
	        			<tr><th colspan="3"><?php echo ucfirst($currentLang); ?></th></tr>
	        			<?php
	        			$x++;
	    				echo HTML_sef::shTextParamHTML( $x,
	    				_COM_SEF_PAGETEXT,
	  					_COM_SEF_TT_PAGETEXT,
	  					'languages_'.$currentLang.'_pageText',
	  					$sefConfig->pageTexts[$currentLang], 10, 20 );
	        			$x++;
						echo HTML_sef::shYesNoParamHTML( $x,
						_COM_SEF_SH_TRANSLATE_URL,
						_COM_SEF_TT_SH_TRANSLATE_URL_PER_LANG,
						$lists['languages_' . $currentLang . '_translateURL'] );
						$x++;
						echo HTML_sef::shYesNoParamHTML( $x,
						_COM_SEF_SH_INSERT_LANGUAGE_CODE,
						_COM_SEF_TT_SH_INSERT_LANGUAGE_CODE_PER_LANG,
						$lists['languages_'.$currentLang.'_insertCode'] );
	        		}
	        	} ?>
	        	<!-- shumisha 27/09/2007 new params for languages management  -->
	        </table>
	        <?php
	        $tabs->endTab();
	        if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
	        	$tabs->startTab( _COM_SEF_SH_CONF_TAB_ADVANCED, 'Advanced' ); ?>
	        	<table class="adminform">
	        		<!-- shumisha 2007-04-01 new params for cache  -->
	        		<tr>
	        			<th colspan="3"><?php echo _COM_SEF_SH_CACHE_TITLE;?></th>
	        		</tr>
	        		<?php
	        		$x = 1;
					echo HTML_sef::shYesNoParamHTML( $x,
					_COM_SEF_SH_USE_URL_CACHE,
					_COM_SEF_TT_SH_USE_URL_CACHE,
					$lists['shUseURLCache'] );
					$x++;
    				echo HTML_sef::shTextParamHTML( $x,
    				_COM_SEF_SH_MAX_URL_IN_CACHE,
  					_COM_SEF_TT_SH_MAX_URL_IN_CACHE,
  					'shMaxURLInCache',
  					$sefConfig->shMaxURLInCache, 10, 10 );?>
	        		<!-- shumisha 2007-04-01 end of new params for cache  -->
	        		<tr>
	        			<th colspan="3"><?php echo _COM_SEF_TITLE_ADV;?></th>
	        		</tr>
	        		<!-- shumisha 2007-06-23 new param to select rewrite mode  -->
	        		<?php
	        		$x = 1;
					echo HTML_sef::shYesNoParamHTML( $x,
					_COM_SEF_SH_SELECT_REWRITE_MODE,
					_COM_SEF_TT_SH_SELECT_REWRITE_MODE,
					$lists['shRewriteMode'] );
	        		/* <!-- shumisha 2007-06-23 new param to select rewrite mode  -->
	        		<!-- shumisha 2007-04-13 new params for automatic 301 redirect of non-sef URL  --> */
	        		$x++;
					echo HTML_sef::shYesNoParamHTML( $x,
					_COM_SEF_SH_REDIRECT_NON_SEF_TO_SEF,
					_COM_SEF_TT_SH_REDIRECT_NON_SEF_TO_SEF,
					$lists['shRedirectNonSefToSef'] );
		        	/* <!-- shumisha 2007-05-4 new params for automatic 301 redirect of joomla-sef URL  --> */
	        		$x++;
					echo HTML_sef::shYesNoParamHTML( $x,
					_COM_SEF_SH_REDIRECT_JOOMLA_SEF_TO_SEF,
					_COM_SEF_TT_SH_REDIRECT_JOOMLA_SEF_TO_SEF,
					$lists['shRedirectJoomlaSefToSef'] );
		        	/* <!-- shumisha 2007-05-4 new params for automatic 301 redirect of joomla-sef URL  --> */
	        		$x++;
					echo HTML_sef::shYesNoParamHTML( $x,
					_COM_SEF_SH_REDIRECT_WWW,
					_COM_SEF_TT_SH_REDIRECT_WWW,
					$lists['shAutoRedirectWww'] );
		        	/* <!-- V 1.2.4.s new param to enable/disable duplicated URL logging to DB  --> */
	        		$x++;
					echo HTML_sef::shYesNoParamHTML( $x,
					_COM_SEF_SH_RECORD_DUPLICATES,
					_COM_SEF_TT_SH_RECORD_DUPLICATES,
					$lists['shRecordDuplicates'] );
		        	/* <!-- V 1.2.4.s new param to enable/disable duplicated URL logging to DB  -->
		        	<!-- V 1.2.4.k new param to enable/disable 404 errors logging to DB  --> */
	        		$x++;
					echo HTML_sef::shYesNoParamHTML( $x,
					_COM_SEF_SH_LOG_404_ERRORS,
					_COM_SEF_TT_SH_LOG_404_ERRORS,
					$lists['shLog404Errors'] );
		        	/* <!-- shumisha 2007-04-13 end of new params for enable/disable 404 errors logging to DB  -->
		        	<!-- shumisha 2007-04-13 new params for secure live site URL  --> */
		        	$x++;
    				echo HTML_sef::shTextParamHTML( $x,
    				_COM_SEF_SH_LIVE_SECURE_SITE,
  					_COM_SEF_TT_SH_LIVE_SECURE_SITE,
  					'shConfig_live_secure_site',
  					$sefConfig->shConfig_live_secure_site, 30, 60 );
		        	/* <!-- shumisha 2007-04-13 end of new params for secure live site  -->
		        	 <!-- shumisha 2007-04-13 new params for HTTPS force non sef  --> */
	        		$x++;
					echo HTML_sef::shYesNoParamHTML( $x,
					_COM_SEF_SH_FORCE_NON_SEF_HTTPS,
					_COM_SEF_TT_SH_FORCE_NON_SEF_HTTPS,
					$lists['shForceNonSefIfHttps'] );
		        	/* <!-- shumisha 2007-04-13 end of new params HTTPS force non sef  -->
		        	<!-- shumisha 2007-05-28 new params for URL encoding  --> */
	        		$x++;
					echo HTML_sef::shYesNoParamHTML( $x,
					_COM_SEF_SH_ENCODE_URL,
					_COM_SEF_TT_SH_ENCODE_URL,
					$lists['shEncodeUrl'] );
		        	/* <!-- shumisha 2007-04-13 end of new params for  URL encoding -->
		        	<!-- shumisha 2007-08-03 new params for forced homepage URL  --> */
		        	$x++;
    				echo HTML_sef::shTextParamHTML( $x,
    				_COM_SEF_SH_FORCED_HOMEPAGE,
  					_COM_SEF_TT_SH_FORCED_HOMEPAGE,
  					'shForcedHomePage',
  					$sefConfig->shForcedHomePage, 30, 60 );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_DEBUG_TO_LOG_FILE,
		        	_COM_SEF_TT_SH_DEBUG_TO_LOG_FILE,
		        	$lists['debugToLogFile'] ); ?>
		        	<!-- shumisha 2007-04-01 new params for Itemid handling and URL construction  -->
		        	<tr>
		        		<th colspan="3"><?php echo _COM_SEF_SH_ITEMID_TITLE; ?></th>
		        	</tr>
		        	<?php
		        	$x = 1;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_GUESS_HOMEPAGE_ITEMID,
		        	_COM_SEF_TT_SH_GUESS_HOMEPAGE_ITEMID,
		        	$lists['guessItemidOnHomepage'] );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_INSERT_GLOBAL_ITEMID_IF_NONE,
		        	_COM_SEF_TT_SH_INSERT_GLOBAL_ITEMID_IF_NONE,
		        	$lists['shInsertGlobalItemidIfNone'] );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_USE_DEFAULT_ITEMIDS,
		        	_COM_SEF_TT_SH_USE_DEFAULT_ITEMIDS,
		        	$lists['UseDefaultItemids'] );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_INSERT_TITLE_IF_NO_ITEMID,
		        	_COM_SEF_TT_SH_INSERT_TITLE_IF_NO_ITEMID,
		        	$lists['shInsertTitleIfNoItemid'] );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_ALWAYS_INSERT_MENU_TITLE,
		        	_COM_SEF_TT_SH_ALWAYS_INSERT_MENU_TITLE,
		        	$lists['shAlwaysInsertMenuTitle'] );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_ALWAYS_INSERT_ITEMID,
		        	_COM_SEF_TT_SH_ALWAYS_INSERT_ITEMID,
		        	$lists['shAlwaysInsertItemid'] );
		        	$x++;
    				echo HTML_sef::shTextParamHTML( $x,
    				_COM_SEF_SH_DEFAULT_MENU_ITEM_NAME,
  					_COM_SEF_TT_SH_DEFAULT_MENU_ITEM_NAME,
  					'shDefaultMenuItemName',
  					$sefConfig->shDefaultMenuItemName, 30, 30 ); ?>
		        	<!-- shumisha 2007-04-01 end of new params for Itemid handling and URL construction  -->
		        	<!-- shumisha 19/08/2007 16:29:22 new params for upgrade management  -->
		        	<tr>
		        		<th colspan="4"><?php echo _COM_SEF_SH_UPGRADE_TITLE; ?></th>
		        	</tr>
		        	<?php
		        	$x = 1;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_UPGRADE_KEEP_CONFIG,
		        	_COM_SEF_TT_SH_UPGRADE_KEEP_CONFIG,
		        	$lists['shKeepConfigOnUpgrade'] );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_UPGRADE_KEEP_URL,
		        	_COM_SEF_TT_SH_UPGRADE_KEEP_URL,
		        	$lists['shKeepStandardURLOnUpgrade'] );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_UPGRADE_KEEP_CUSTOM,
		        	_COM_SEF_TT_SH_UPGRADE_KEEP_CUSTOM,
		        	$lists['shKeepCustomURLOnUpgrade'] );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_UPGRADE_KEEP_META,
		        	_COM_SEF_TT_SH_UPGRADE_KEEP_META,
		        	$lists['shKeepMetaDataOnUpgrade'] );
		        	$x++;
		        	echo HTML_sef::shYesNoParamHTML( $x,
		        	_COM_SEF_SH_UPGRADE_KEEP_MODULES,
		        	_COM_SEF_TT_SH_UPGRADE_KEEP_MODULES,
		        	$lists['shKeepModulesSettingsOnUpgrade'] ); ?>
		        	<!-- shumisha 19/08/2007 16:29:22 new params for upgrade management  -->
		        </table>
		        <?php
		        $tabs->endTab();
	        }
	        $tabs->startTab( _COM_SEF_SH_CONF_TAB_BY_COMPONENT, 'ByComponent'); ?>
	        <table class="adminform">
	        	<tr align="left">
	        		<td width="140" align="left">&nbsp;</td>
	        		<td style="width:auto; text-align:left">&nbsp;<?php echo mosToolTip( _COM_SEF_TT_SH_ADV_MANAGE_URL, _COM_SEF_SH_ADV_MANAGE_URL ); ?></td>
	        		<?php
	        		if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) { ?>
	        			<td style="width:auto; text-align:left">
	        				&nbsp;
	        				<?php echo mosToolTip(_COM_SEF_TT_SH_ADV_TRANSLATE_URL, _COM_SEF_SH_ADV_TRANSLATE_URL ); ?>
	        			</td>
	        			<td style="width:auto; text-align:left">
	        				&nbsp;
	        				<?php echo mosToolTip( _COM_SEF_TT_SH_ADV_INSERT_ISO, _COM_SEF_SH_ADV_INSERT_ISO ); ?>
	        			</td>
	        			<td style="width:auto; text-align:left">
	        				&nbsp;
	        				<?php echo mosToolTip( _COM_SEF_TT_SH_ADV_OVERRIDE_SEF, _COM_SEF_SH_OVERRIDE_SEF_EXT ); ?>
	        			</td>
	        			<td style="width:auto; text-align:left">
	        				&nbsp;
	        				<?php echo mosToolTip( _COM_SEF_TT_SH_ADV_COMP_DEFAULT_STRING, _COM_SEF_SH_ADV_COMP_DEFAULT_STRING ); ?>
	        			</td>
	        			<td style="width:auto; text-align:left">
	        				&nbsp;
	        				<?php echo mosToolTip( _COM_SEF_TT_SH_ADV_COMP_DEFAULT_ITEMID, _COM_SEF_SH_ADV_COMP_DEFAULT_ITEMID ); ?>
	        			</td>
	        			<?php
	        		} ?>
	        		</tr>
	        		<?php
	        		foreach( $lists['adv_config'] as $key => $compList ) {
	        			$x++; ?>
	        			<tr<?php echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
	        				<td width="140"><?php echo $key; ?></td>
	        				<td style="width:auto"><?php echo $compList['manageURL']; ?></td>
	        				<?php
	        				if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) { ?>
	        					<td style="width:auto"><?php echo $compList['translateURL']; ?></td>
	        					<td style="width:auto"><?php echo $compList['insertIsoCode']; ?></td>
	        					<td style="width:auto"><?php echo $compList['shDoNotOverrideOwnSef']; ?></td>
	        					<?php
	        					echo $compList['defaultComponentString'];
	        					echo $compList['defaultComponentItemid'];
	        				} ?>
	        			</tr>
	        			<?php
	        		} ?>
	        	</table>
	        	<?php
	        	$tabs->endTab();
	        	$tabs->startTab( _COM_SEF_SH_CONF_TAB_META, 'TitleMeta' ); ?>
	        	<table class="adminform">
	        		<tr>
	        			<th colspan="3"><?php echo _COM_SEF_TITLE_META_MANAGEMENT; ?></th>
	        		</tr>
	        		<tr>
	        			<td colspan="3" align="left">
	        				<div style="border: 1px solid #FF0000; margin:5px; padding:5px; background-color:#FFEFEF">
	        					<?php echo _COM_SEF_SH_CONF_META_DOC; ?>
	        				</div>
	        			</td>
	        		</tr>
	        		<!-- shumisha 2007-07-01 Activate Meta management  -->
	        		<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
	        			<td width="200"><?php echo _COM_SEF_SH_META_MANAGEMENT_ACTIVATED; ?></td>
	        			<td width="150"><?php echo $lists['shMetaManagementActivated']; ?></td>
	        			<td><?php echo mosToolTip(_COM_SEF_TT_SH_META_MANAGEMENT_ACTIVATED ); ?></td>
	        		</tr>
	        		<?php
	        		if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) { ?>
	        			<!-- shumisha 2007-07-01 Remove Joomla Generator tag  -->
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
		        			<td width="200"><?php echo _COM_SEF_SH_REMOVE_JOOMLA_GENERATOR; ?></td>
		        			<td width="150"><?php echo $lists['shRemoveGeneratorTag']; ?></td>
		        			<td><?php echo mosToolTip( _COM_SEF_TT_SH_REMOVE_JOOMLA_GENERATOR ); ?></td>
	        			</tr>
	        			<!-- shumisha 2007-07-01 Put <h1>tags around content titles -->
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
	        				<td width="200"><?php echo _COM_SEF_SH_PUT_H1_TAG; ?></td>
	        				<td width="150"><?php echo $lists['shPutH1Tags']; ?></td>
	        				<td><?php echo mosToolTip(_COM_SEF_TT_SH_PUT_H1_TAG );?></td>
	        			</tr>
	        			<!-- shumisha 2007-11-09 shCustomTags new params V 1.3 RC  -->
	        			<?php
	        			$x++;
	        			echo HTML_sef::shYesNoParamHTML( $x,
	        			_COM_SEF_SH_MULTIPLE_H1_TO_H2,
	        			_COM_SEF_TT_SH_MULTIPLE_H1_TO_H2,
	        			$lists['shMultipleH1ToH2'] );
	        			$x++;
	        			echo HTML_sef::shYesNoParamHTML( $x,
	        			_COM_SEF_SH_INSERT_NOFOLLOW_PDF_PRINT,
	        			_COM_SEF_TT_SH_INSERT_NOFOLLOW_PDF_PRINT,
	        			$lists['shInsertNoFollowPDFPrint'] );
	        			$x++;
	        			echo HTML_sef::shYesNoParamHTML( $x,
	        			_COM_SEF_SH_INSERT_READMORE_PAGE_TITLE,
	        			_COM_SEF_TT_SH_INSERT_READMORE_PAGE_TITLE,
	        			$lists['shInsertReadMorePageTitle'] );
	        			// V 1.3.1
	        			$x++;
	        			echo HTML_sef::shYesNoParamHTML( $x,
	        			_COM_SEF_SH_INSERT_OUTBOUND_LINKS_IMAGE,
	        			_COM_SEF_TT_SH_INSERT_OUTBOUND_LINKS_IMAGE,
	        			$lists['shInsertOutboundLinksImage'] );
	        			$x++;
	        			echo HTML_sef::shYesNoParamHTML( $x,
	        			_COM_SEF_SH_OUTBOUND_LINKS_IMAGE,
	        			_COM_SEF_TT_SH_OUTBOUND_LINKS_IMAGE,
	        			$lists['shImageForOutboundLinks'] );
	        			$x++;
	        			HTML_sef::shTextParamHTML( $x,
	        			_COM_SEF_SH_PREPEND_TO_PAGE_TITLE,
	        			_COM_SEF_TT_SH_PREPEND_TO_PAGE_TITLE,
	        			'prependToPageTitle',
	        			$sefConfig->prependToPageTitle, 60, 250 );
	        			$x++;
	        			HTML_sef::shTextParamHTML( $x,
	        			_COM_SEF_SH_APPEND_TO_PAGE_TITLE,
	        			_COM_SEF_TT_SH_APPEND_TO_PAGE_TITLE,
	        			'appendToPageTitle',
	        			$sefConfig->appendToPageTitle, 60, 250 );
	        		} ?>
	        	</table>
	        	<?php
	        	$tabs->endTab();
	        	$tabs->startTab( _COM_SEF_SH_CONF_TAB_SECURITY, 'Title-Sec' ); ?>
	        	<table class="adminform">
	        		<tr>
	        			<th colspan="3"><?php echo _COM_SEF_SH_SECURITY_TITLE; ?></th>
	        		</tr>
	        		<!-- shumisha 2007-09-15 Activate Security  -->
	        		<?php
	        		$x = 1;
        			echo HTML_sef::shYesNoParamHTML( $x,
        			_COM_SEF_SH_ACTIVATE_SECURITY,
        			_COM_SEF_TT_SH_ACTIVATE_SECURITY,
        			$lists['shSecEnableSecurity'] );

	        		if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
	        			$x++;
		    			echo HTML_sef::shYesNoParamHTML( $x,
		    			_COM_SEF_SH_LOG_ATTACKS,
		    			_COM_SEF_TT_SH_LOG_ATTACKS,
		    			$lists['shSecLogAttacks'] );
		    			$x++;
	        			HTML_sef::shTextParamHTML( $x,
	        			_COM_SEF_SH_MONTHS_TO_KEEP_LOGS,
	        			_COM_SEF_TT_SH_MONTHS_TO_KEEP_LOGS,
	        			'monthsToKeepLogs',
	        			$sefConfig->monthsToKeepLogs, 5, 2 );
	        			$x++;
	        			echo HTML_sef::shYesNoParamHTML( $x,
	        			_COM_SEF_SH_CHECK_POST_DATA,
	        			_COM_SEF_TT_SH_CHECK_POST_DATA,
	        			$lists['shSecCheckPOSTData'] ); ?>
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
		        			<td width="200" valign="top"><?php echo _COM_SEF_SH_ONLY_NUM_VARS; ?></td>
		        			<td width="150">
		        				<textarea name="shSecOnlyNumVars" cols="20" rows="5"><?php echo $lists['shSecOnlyNumVars']; ?></textarea>
		        			</td>
		        			<td><?php echo mosToolTip( _COM_SEF_TT_SH_ONLY_NUM_VARS ); ?></td>
	        			</tr>
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
	        				<td width="200" valign="top"><?php echo _COM_SEF_SH_ONLY_ALPHA_NUM_VARS; ?></td>
	        				<td width="150">
	        					<textarea name="shSecAlphaNumVars" cols="20" rows="5"><?php echo $lists['shSecAlphaNumVars']; ?></textarea>
	        				</td>
	        				<td><?php echo mosToolTip( _COM_SEF_TT_SH_ONLY_ALPHA_NUM_VARS ); ?></td>
	        			</tr>
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
		        			<td width="200" valign="top"><?php echo _COM_SEF_SH_NO_PROTOCOL_VARS; ?></td>
		        			<td width="150">
		        				<textarea name="shSecNoProtocolVars" cols="20" rows="5"><?php echo $lists['shSecNoProtocolVars']; ?></textarea>
		        			</td>
		        			<td><?php echo mosToolTip( _COM_SEF_TT_SH_NO_PROTOCOL_VARS ); ?></td>
	        			</tr>
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
		        			<td width="200" valign="top"><?php echo _COM_SEF_SH_IP_WHITE_LIST; ?></td>
		        			<td width="150">
		        				<textarea name="ipWhiteList" cols="20" rows="5"><?php echo $lists['ipWhiteList']; ?></textarea>
		        			</td>
		        			<td><?php echo mosToolTip( _COM_SEF_TT_SH_IP_WHITE_LIST );?></td>
	        			</tr>
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
		        			<td width="200" valign="top"><?php echo _COM_SEF_SH_IP_BLACK_LIST; ?></td>
		        			<td width="150">
		        				<textarea name="ipBlackList" cols="20" rows="5"><?php echo $lists['ipBlackList']; ?></textarea>
		        			</td>
		        			<td><?php echo mosToolTip( _COM_SEF_TT_SH_IP_BLACK_LIST ); ?></td>
	        			</tr>
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
	        				<td width="200" valign="top"><?php echo _COM_SEF_SH_UAGENT_WHITE_LIST; ?></td>
	        				<td width="150">
	        					<textarea name="uAgentWhiteList" cols="60" rows="5"><?php echo $lists['uAgentWhiteList']; ?></textarea>
	        				</td>
	        				<td><?php echo mosToolTip( _COM_SEF_TT_SH_UAGENT_WHITE_LIST); ?></td>
	        			</tr>
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
	        				<td width="200" valign="top"><?php echo _COM_SEF_SH_UAGENT_BLACK_LIST; ?></td>
	        				<td width="150">
	        					<textarea name="uAgentBlackList" cols="60" rows="5"><?php echo $lists['uAgentBlackList']; ?></textarea>
	        				</td>
	        				<td><?php echo mosToolTip( _COM_SEF_TT_SH_UAGENT_BLACK_LIST );?></td>
	        			</tr>
	        			<tr>
	        				<th colspan="3"><?php echo _COM_SEF_SH_ANTIFLOOD_TITLE; ?></th>
	        			</tr>
	        			<?php
	        			$x++;
	        			echo HTML_sef::shYesNoParamHTML( $x,
	        			_COM_SEF_SH_ACTIVATE_ANTIFLOOD,
	        			_COM_SEF_TT_SH_ACTIVATE_ANTIFLOOD,
	        			$lists['shSecActivateAntiFlood'] );
	        			$x++;
	        			echo HTML_sef::shYesNoParamHTML( $x,
	        			_COM_SEF_SH_ANTIFLOOD_ONLY_ON_POST,
	        			_COM_SEF_TT_SH_ANTIFLOOD_ONLY_ON_POST,
	        			$lists['shSecAntiFloodOnlyOnPOST'] );
	        			$x++;
	        			HTML_sef::shTextParamHTML( $x,
	        			_COM_SEF_SH_ANTIFLOOD_PERIOD,
	        			_COM_SEF_TT_SH_ANTIFLOOD_PERIOD,
	        			'shSecAntiFloodPeriod',
	        			$sefConfig->shSecAntiFloodPeriod, 5, 5 );
	        			$x++;
	        			HTML_sef::shTextParamHTML( $x,
	        			_COM_SEF_SH_ANTIFLOOD_COUNT,
	        			_COM_SEF_SH_HONEYPOT_TITLE,
	        			'shSecAntiFloodCount',
	        			$sefConfig->shSecAntiFloodCount, 5, 5 ); ?>
	        			<tr>
	        				<th colspan="3"><?php echo _COM_SEF_SH_HONEYPOT_TITLE; ?></th>
	        			</tr>
	        			<tr>
	        				<td colspan="3" align="left">
	        					<div style="border: 1px solid #1D7D9F; margin:5px; padding:5px; background-color:#EFFBFF">
	        						<?php echo _COM_SEF_SH_CONF_HONEYPOT_DOC; ?>
	        					</div>
	        				</td>
	        			</tr>
	        			<?php
	        			$x = 1;
	        			echo HTML_sef::shYesNoParamHTML( $x,
	        			_COM_SEF_SH_CHECK_HONEY_POT,
	        			_COM_SEF_TT_SH_CHECK_HONEY_POT,
	        			$lists['shSecCheckHoneyPot'] );
	        			$x++;
	        			HTML_sef::shTextParamHTML( $x,
	        			_COM_SEF_SH_HONEYPOT_KEY,
	        			_COM_SEF_TT_SH_HONEYPOT_KEY,
	        			'shSecHoneyPotKey',
	        			$sefConfig->shSecHoneyPotKey, 30, 30 ); ?>
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
	        				<td width="200" valign="top"><?php echo _COM_SEF_SH_HONEYPOT_ENTRANCE_TEXT; ?></td>
	        				<td width="150">
	        					<textarea name="shSecEntranceText" id="shSecEntranceText" cols="60" rows="5"><?php echo $sefConfig->shSecEntranceText; ?></textarea>
	        				</td>
	        				<td><?php echo mosToolTip( _COM_SEF_TT_SH_HONEYPOT_ENTRANCE_TEXT ); ?></td>
	        			</tr>
	        			<tr<?php $x++; echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
	        				<td width="200" valign="top"><?php echo _COM_SEF_SH_SMELLYPOT_TEXT; ?></td>
	        				<td width="150">
	        					<textarea name="shSecSmellyPotText" id="shSecSmellyPotText" cols="60" rows="5"><?php echo $sefConfig->shSecSmellyPotText; ?></textarea>
	        				</td>
	        				<td><?php echo mosToolTip( _COM_SEF_TT_SH_SMELLYPOT_TEXT ); ?></td>
	        			</tr>
	        			<?php
	        		} ?>
	        	</table>
	        	<?php
	        	$tabs->endTab(); // new mic 2008.02.25
	        	
	        	$tabs->startTab( _COM_SEF_404PAGE, 'page_404' ); ?>
	        	<table class="adminform">
	        		<tr>
	        			<th colspan="4"><?php echo _COM_SEF_DEF_404_PAGE; ?></th>
	        		</tr>
	        		<tr>
	        			<td>
		        			<?php
		        			// parameters : areaname, content, hidden field, width, height, cols, rows
		        			editorArea( 'editor1',  $txt404, 'introtext','450','450','50','50' ); ?>
	        			</td>
	        		</tr>
	        	</table>
	        	<?php
	        	$tabs->endTab();
	        	
	        	if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
	        		$tabs->startTab( _COM_SEF_DEFAULT_PARAMS_TITLE, 'default_params' ); ?>
	        		<table class="adminform">
	        			<tr>
	        				<td colspan="3" align="left">
	        					<div style="border: 1px solid #FF0000; margin:5px; padding:5px; background-color:#FFEFEF">
	        						<?php echo _COM_SEF_DEFAULT_PARAMS_WARNING; ?>
		        				</div>
		        			</td>
	    	    		</tr>
	        			<tr>
	        				<td width="450">
		        				<textarea name="defaultParamList" cols="100" rows="30"><?php echo $lists['defaultParamList']; ?></textarea>
			        		</td>
		        		</tr>	
	    	    	</table>
	        		<?php
	        		$tabs->endTab();
				}
	        	
	        	$tabs->endPane(); ?>
        	<input type="hidden" name="id" value="" />
        	<input type="hidden" name="task" value="saveconfig" />
        	<input type="hidden" name="option" value="com_sef" />
        	<input type="hidden" name="section" value="config" />
        	<input type="hidden" name="eraseCache" id="eraseCache" value="0" />
        </form>
        <?php
	}

	// V 1.2.4.q added search
	function viewSEF( &$rows, &$lists, $pageNav, $option, $viewmode=0, $search = '' ) {
		global $my; ?>

		<script type="text/javascript">
			/* <![CDATA[ */
		    function submitbutton(pressbutton) {
		    	if (((pressbutton != 'viewDuplicates') && (pressbutton != 'newMetaFromSEF'))
		    	|| (pressbutton == 'newMetaFromSEF') && (document.adminForm.boxchecked.value == 1)
		    	|| (pressbutton == 'viewDuplicates') && (document.adminForm.boxchecked.value == 1)) {
		    		submitform( pressbutton );
		    	}else{
		    		alert("<?php echo _COM_SEF_SELECT_ONE_URL; ?>");
		    	}
			};
			/* ]]> */
			</script>
		<form action="index2.php" method="post" name="adminForm">
			<table class="adminheading">
				<tr>
					<th>
						<?php echo _COM_SEF_TITLE_MANAGER;?>
						<br/>
						<span class="small" style="text-align:left">
							<a href="index2.php?option=com_sef" title="<?php echo _COM_SEF_BACK; ?>"><?php echo _COM_SEF_BACK; ?></a>
						</span>
					</th>
					<td style="white-space:nowrap">
						<?php
						if( $viewmode == 2 ) { ?>
							<br />
							<a href="index2.php?option=<?php echo $option; ?>&amp;task=import_export&amp;ViewModeId=<?php echo $viewmode?>" title="<?php echo _COM_SEF_IMPORT_EXPORT; ?>"><?php echo _COM_SEF_IMPORT_EXPORT; ?></a>
							&nbsp;&nbsp;
							<?php
						}else{ ?>
							&nbsp;
							<?php
						} ?>
					</td>
					<td style="text-align:right">
						<?php echo _COM_SEF_VIEWMODE.$lists['viewmode'];?>
					</td>
					<td style="text-align:right">
						<?php echo _COM_SEF_SORTBY.$lists['sortby'];?>
					</td>
					<td style="text-align:left">
						<?php echo _COM_SEF_SH_FILTER.':'; ?>
						<br />
						<input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="text_area" onChange="document.adminForm.submit();" />
					</td>
				</tr>
			</table>
			<table class="adminlist">
				<tr>
					<th width="20">#</th>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
					</th>
					<th class="title" width="50"><?php echo _COM_SEF_HITS; ?></th>
					<th class="title"><?php echo ( ( $viewmode == 1 ) ? _COM_SEF_DATEADD: _COM_SEF_SEFURL ); ?></th>
					<th><?php echo ( ( $viewmode == 1 ) ? _COM_SEF_URL:_COM_SEF_REALURL ); ?></th>
				</tr>
				<?php
				$k = 0;
				//for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				if( !empty( $rows ) ) {
					foreach( array_keys( $rows ) as $i ) {
						$row = &$rows[$i]; ?>
						<tr class="row<?php echo $k; ?>">
							<td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo mosHTML::idBox( $i, $row->id, false ); ?></td>
							<td><?php echo $row->cpt; ?></td>
							<td style="text-align:left;">
								<?php
								if( $viewmode == 1 ) {
									echo $row->dateadd;
								}else{ ?>
									<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','edit')">
										<?php echo $row->oldurl;?>
									</a>
									<?php
								} ?>
							</td>
							<td style="text-align:left; width:80%">
								<?php
								if( $viewmode == 1 ) { ?>
									<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','edit')">
										<?php echo $row->oldurl; ?>
									</a>
									<?php
								}else{
									echo htmlspecialchars( $row->newurl );
								} ?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
				} ?>
			</table>
			<?php echo $pageNav->getListFooter(); ?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}

	function viewDuplicates( &$rows, &$lists, $pageNav, $option, $id ) { ?>
		<script type="text/javascript">
			/* <![CDATA[ */
			function submitbutton(pressbutton) {
				if ((pressbutton != 'makeMainUrl')
				|| (pressbutton == 'makeMainUrl')
				&& (document.adminForm.boxchecked.value == 1)) {
					submitform( pressbutton );
				}else{
					alert("<?php echo _COM_SEF_SELECT_ONE_URL; ?>");
				}
			};
			/* ]]> */
		</script>
		<form action="index2.php" method="post" name="adminForm">
			<table class="adminheading">
				<tr>
					<th>
						<?php echo _COM_SEF_MANAGE_DUPLICATES.$rows[0]->oldurl; ?>
						<br/>
						<span class="small" align="left">
							<a href="index2.php?option=com_sef"><?php echo _COM_SEF_BACK?></a>
						</span>
					</th>
					<td style="text-align:right"><?php echo _COM_SEF_SORTBY.$lists['sortby']; ?></td>
				</tr>
			</table>
			<table class="adminlist">
				<tr>
					<th width="20">#</th>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
					</th>
					<th class="title" width="50"><?php echo _COM_SEF_MANAGE_DUPLICATES_RANK; ?></th>
					<th><?php echo _COM_SEF_REALURL; ?></th>
				</tr>
				<?php
				$k = 0;
				//for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				foreach( array_keys( $rows ) as $i ) {
					$row = &$rows[$i]; ?>
					<tr class="row<?php echo $k; ?>">
						<td style="text-align:center"><?php echo $pageNav->rowNumber( $i ); ?></td>
						<td><?php echo mosHTML::idBox( $i, $row->id, false ); ?></td>
						<td><?php echo $row->rank; ?></td>
						<td style="text-align:left; width:80%">
							<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','makeMainUrl')">
								<?php echo htmlspecialchars($row->newurl); ?>
							</a>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				} ?>
			</table>
			<?php echo $pageNav->getListFooter(); ?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="task" value="viewDuplicates" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}

	function editSEF( &$_row, &$lists, $_option ) {

		// new mic 2008.02.24
		mosCommonHTML::loadOverlib( HTML_sef::shCheckCMSVersion() ); ?>

		<script type="text/javascript">
			/* <![CDATA[ */
			function changeDisplayImage() {
				if (document.adminForm.imageurl.value !='') {
					document.adminForm.imagelib.src='../images/404sef/' + document.adminForm.imageurl.value;
				} else {
					document.adminForm.imagelib.src='images/blank.png';
				}
			};
			function submitbutton(pressbutton) {
				var form = document.adminForm;
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}
				if (form.oldurl.value == "") {
					alert( "<?php echo _COM_SEF_EMPTYURL?>" );
		      	}
				<?php if ( (!$_row->id) || $_row->dateadd != "0000-00-00" )  { ?>
		        else {
					if (form.newurl.value.match(/^index.php/)) {
					  form.dateadd.value = "<?php echo date('Y-m-d'); ?>"  // V 1.2.4.s  always custom URL
						submitform( pressbutton );
					}else{
						alert( "<?php echo _COM_SEF_BADURL ?>" );
					}
				}
				<?php } else {?>
				else{
		  			submitform( pressbutton );
				}
				<?php } ?>
			};
			/* ]]> */
		</script>
		<table class="adminheading">
			<tr>
				<th><?php echo $_row->id ? _COM_SEF_EDIT : _COM_SEF_ADD;?> Url</th>
			</tr>
			</table>
			<form action="index2.php" method="post" name="adminForm">
				<table class="adminform">
					<tr>
						<td style="width:150px"><?php echo _COM_SEF_OLDURL; ?></td>
						<td>
							<input class="inputbox" type="text" size="100" name="oldurl" value="<?php echo $_row->oldurl; ?>" />
							<?php echo mosToolTip(_COM_SEF_TT_OLDURL); ?>
						</td>
					</tr>
					<tr>
						<td><?php echo _COM_SEF_NEWURL; ?></td>
						<td>
						<?php
						if( ($_row->id) && $_row->dateadd == '0000-00-00' ) {
							echo htmlspecialchars( $_row->newurl );
						}else{ ?>
							<input class="inputbox" type="text" size="100" name="newurl" value="<?php echo htmlspecialchars( $_row->newurl ); ?>" />
								<?php echo mosToolTip( _COM_SEF_TT_NEWURL );
						} ?>
						</td>
					</tr>
					<tr>
						<td>Won't be deleted from other scripts </td>
						<td>
							<input type="checkbox"  name="can_not_be_deleted"  <?php echo ($_row->can_not_be_deleted)? 'checked':''; ?>  id="payt3"/><label for="payt3"></label>
						</td>
					</tr>
				</table>
				<table class="adminform">
					<tr>
						<td style="width:150px; vertical-align:top"><?php echo _COM_SEF_ALIAS_LIST; ?></td>
						<td>
							<textarea name="shAliasList" cols="80" rows="20"><?php echo $lists['shAliasList']; ?></textarea>
							<span style="vertical-align:top;"><?php echo mosToolTip(_COM_SEF_TT_ALIAS_LIST); ?></span>
						</td>
					</tr>
				</table>
			<input type="hidden" name="option" value="<?php echo $_option; ?>" />
			<?php
			if( ( $_row->id ) && $_row->dateadd == '0000-00-00' ) { ?>
				<input type="hidden" name="newurl" value="<?php echo $_row->newurl; ?>" />
				<input type="hidden" name="dateadd" value="<?php echo date('Y-m-d'); ?>" />
				<?php
			}else{ ?>
				<input type="hidden" name="dateadd" value="<?php echo $_row->dateadd; ?>" />
				<?php
			} ?>
			<input type="hidden" name="id" value="<?php echo $_row->id; ?>" />
			<input type="hidden" name="returnTo" value="1" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="saveOldUrl" value="<?php echo $_row->oldurl; ?>" />
		</form>
		<style>
			#payt3 {display: none;}
			[for="payt3"] {
				position: relative;
				display: block;
				width: 30px;
				height: 30px;
				border-radius: 100%;
				background: #ddd linear-gradient(#ccc, #fff);
				box-shadow:
					inset 0 2px 1px rgba(0,0,0,.15),
					0 2px 5px rgba(200,200,200,.1);
				cursor: pointer;
			}
			[for="payt3"]:after {
				content: "";
				position: absolute;
				left: 40%; top: 40%;
				width: 20%;
				height: 20%;
				border-radius: 100%;
				background: #969696 radial-gradient(40% 35%, #ccc, #969696 60%);
				box-shadow:
					inset 0 2px 4px 1px rgba(0,0,0,.3),
					0 1px 0 rgba(255,255,255,1),
					inset 0 1px 0 white;
			}
			[for="payt3"]:before {
				content: "";
				position: absolute;
				top: 8%; right: 8%; bottom: 8%; left: 8%;
				border-radius: 100%;
				background: #eaeaea;
				box-shadow:
					0 3px 5px rgba(0,0,0,.25),
					inset 0 1px 0 rgba(255,255,255,.3),
					inset 0 -5px 5px rgba(100,100,100,.1),
					inset 0 5px 5px rgba(255,255,255,.3);
			}
			#payt3:checked ~ [for="payt3"]:before {
				background: #e5e5e5 linear-gradient(#dedede, #fdfdfd);
			}
			#payt3:checked ~ [for="payt3"]:after {
				background: #25d025 radial-gradient(40% 35%, #5aef5a, #25d025 60%);
				box-shadow:
					inset 0 3px 5px 1px rgba(0,0,0,.1),
					0 1px 0 rgba(255,255,255,.4),
					0 0 10px 2px rgba(0, 210, 0, .5);
			}
		</style>



		<?php
	}

	// edit homepage aliases
	function editHomeAlias( $lists ) {

		// new mic 2008.02.24
		mosCommonHTML::loadOverlib( HTML_sef::shCheckCMSVersion() ); ?>

		<table class="adminheading">
			<tr>
				<th><?php echo _COM_SEF_HOME_ALIAS; ?></th>
			</tr>
		</table>
		<form action="index2.php" method="post" name="adminForm">
			<table class="adminform">
				<tr>
					<td style="width:150px; vertical-align:top"><?php echo _COM_SEF_ALIAS_LIST; ?></td>
					<td>
						<textarea name="shAliasList" cols="80" rows="20"><?php echo $lists['shAliasList'];?></textarea>
						<span style="vertical-align:top;"><?php echo mosToolTip(_COM_SEF_TT_HOME_PAGE_ALIAS_LIST);?></span>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_sef" />
			<input type="hidden" name="section" value="homeAlias" />
			<input type="hidden" name="task" value="" />
		</form>
		<?php
	}

	// V 1.2.4.s
	function viewMeta( &$rows, &$lists, $pageNav, $option, $search = '' ) {
		global $my; ?>

		<form action="index2.php" method="post" name="adminForm">
			<table class="adminheading">
				<tr>
					<th>
						<?php echo _COM_SEF_TITLE_META_MANAGEMENT; ?>
						<br/>
						<span class="small" style="text-align:left">
							<a href="index2.php?option=com_sef"><?php echo _COM_SEF_BACK; ?></a>
						</span>
					</th>
					<td style="white-space:nowrap">
						<br/>
						<a href="index2.php?option=<?php echo $option; ?>&amp;task=import_export_meta"><?php echo _COM_SEF_IMPORT_EXPORT_META; ?></a>
						&nbsp;&nbsp;
					</td>
					<td></td>
					<td style="text-align:right"><?php echo _COM_SEF_SORTBY . $lists['sortby']; ?></td>
					<td style="text-align:left">
						<?php echo _COM_SEF_SH_FILTER.':'; ?>
						<br />
						<input type="text" name="searchMeta" value="<?php echo htmlspecialchars( $search ); ?>" class="text_area" onChange="document.adminForm.submit();" />
					</td>
				</tr>
			</table>
			<table class="adminlist">
				<tr>
					<th width="20">#</th>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
					</th>
					<th><?php echo _COM_SEF_REALURL; ?></th>
				</tr>
				<?php
				$k = 0;
				//for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				foreach( array_keys( $rows ) as $i ) {
					$row = &$rows[$i]; ?>
					<tr class="row<?php echo $k; ?>">
						<td style="text-align:center"><?php echo $pageNav->rowNumber( $i ); ?></td>
						<td><?php echo mosHTML::idBox( $i, $row->id, false ); ?></td>
						<td style="text-align:left;">
							<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')">
								<?php echo htmlspecialchars( $row->newurl ); ?>
							</a>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				} ?>
			</table>
			<?php echo $pageNav->getListFooter(); ?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="viewMeta" />
			<input type="hidden" name="section" value="meta" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="returnTo" value="0" />
		</form>
		<?php
	}

	function editMeta( &$_row, $_option, $returnTo, $editUrl, $oldUrl ) {

		// new mic 2008.02.24
		mosCommonHTML::loadOverlib( HTML_sef::shCheckCMSVersion() ); ?>

		<script type="text/javascript">
			/* <![CDATA[ */
			function changeDisplayImage() {
				if (document.adminForm.imageurl.value !='') {
					document.adminForm.imagelib.src='../images/404sef/' + document.adminForm.imageurl.value;
				} else {
					document.adminForm.imagelib.src='images/blank.png';
				}
			};
			function submitbutton(pressbutton) {
				var form = document.adminForm;
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}
				if (pressbutton == 'deleteHomeMeta' || pressbutton == 'deleteHomeMetaFromSEF') {
				  if (confirm("<?php echo _COM_SEF_CONF_ERASE_HOME_META?>" )) {
					  submitform( pressbutton );
					  return;
					} else return;
				}

				// do field validation
				if (form.newurl.value == "") {
					alert( "<?php echo _COM_SEF_EMPTYURL?>" );
				} else {
					if (  <?php echo empty($editUrl) ? 1 : 0; ?>
		         || form.newurl.value.match(/index.php/)) {
						submitform( pressbutton );
						return;
					}else{
						alert( "<?php echo _COM_SEF_BAD_META?>" );
					}
				}
			};
			/* ]]> */
			</script>
			<table class="adminheading">
				<tr>
					<th>
						<?php echo ($_row->id ? _COM_SEF_META_EDIT : _COM_SEF_META_ADD).(empty($oldUrl) ? '': ' : '.$oldUrl); ?>
					</th>
				</tr>
			</table>
			<form action="index2.php" method="post" name="adminForm">
				<table class="adminform">
					<?php
					// V 1.2.4.t
					if( !empty( $editUrl ) ) {  ?>
						<tr>
							<td style="width:150px"><?php echo _COM_SEF_NEWURL_META; ?></td>
							<td>
								<input class="inputbox" type="text" size="100" name="newurl" value="<?php echo htmlspecialchars($_row->newurl); ?>" />
								<?php echo mosToolTip(_COM_SEF_TT_NEWURL_META); ?>
							</td>
						</tr>
						<?php
					} else { ?>
		  				<input type="hidden" name="newurl" value="<?php echo htmlspecialchars($_row->newurl); ?>">
					<?php } ?>
					<tr>
						<td style="width:150px"><?php echo _COM_SEF_META_TITLE; ?></td>
						<td>
							<input class="inputbox" type="text" size="100" name="metatitle" value="<?php echo $_row->metatitle; ?>" />
							<?php echo mosToolTip(_COM_SEF_TT_META_TITLE); ?>
						</td>
					</tr>
					<tr>
						<td><?php echo _COM_SEF_META_DESC; ?></td>
						<td>
							<input class="inputbox" type="text" size="100" name="metadesc" value="<?php echo $_row->metadesc; ?>" />
							<?php echo mosToolTip(_COM_SEF_TT_META_DESC); ?>
						</td>
					</tr>
					<tr>
						<td><?php echo _COM_SEF_META_KEYWORDS; ?></td>
						<td>
							<input class="inputbox" type="text" size="100" name="metakey" value="<?php echo $_row->metakey; ?>" />
							<?php echo mosToolTip(_COM_SEF_TT_META_KEYWORDS); ?>
						</td>
					</tr>
					<tr>
						<td><?php echo _COM_SEF_META_ROBOTS; ?></td>
						<td>
							<input class="inputbox" type="text" size="30" name="metarobots" value="<?php echo $_row->metarobots; ?>" />
							<?php echo mosToolTip(_COM_SEF_TT_META_ROBOTS); ?>
						</td>
					</tr>
					<tr>
						<td><?php echo _COM_SEF_META_LANG; ?></td>
						<td>
							<input class="inputbox" type="text" size="30" name="metalang" value="<?php echo $_row->metalang; ?>" />
							<?php echo mosToolTip(_COM_SEF_TT_META_LANG); ?>
						</td>
					</tr>
				</table>
			<input type="hidden" name="option" value="<?php echo $_option; ?>" />
			<input type="hidden" name="id" value="<?php echo $_row->id; ?>" />
			<input type="hidden" name="section" value="meta" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="returnTo" value="<?php echo $returnTo; ?>" />
			<?php
			// V 1.2.4.t
			if( !empty( $editUrl ) ) {  ?>
				<input type="hidden" name="newurl" value="<?php echo htmlspecialchars( $_row->newurl ); ?>" />
				<?php
			} ?>
		</form>
		<?php
	}

	function help() {
		global $mosConfig_absolute_path; ?>

		 <table class="adminform">
	        <tr>
	            <th colspan="4"><?php echo _COM_SEF_TITLE_SUPPORT;?></th>
	        </tr>
	        <tr>
	        	<td>
	        		<?php include( $mosConfig_absolute_path . '/administrator/components/com_sef/readme.inc' ); ?>
	        	</td>
	        </tr>
		</table>
		<?php
	}

	function purge( $option, $message, $confirmed ) { ?>
		<form action="index2.php" method="post" name="adminForm">
			<table class="adminheading">
				<tr>
					<th>
						<?php echo _COM_SEF_TITLE_PURGE; ?>
						<br/>
						<span class="small" style="text-align:left">
							<a href="index2.php?option=com_sef" title="<?php echo _COM_SEF_BACK?>"><?php echo _COM_SEF_BACK?></a>
						</span>
					</th>
				</tr>
				<tr>
					<td>
						<p class="message">
							<?php echo $message; ?>
							<br/>
						</p>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<?php
			if( ( $message == _COM_SEF_SUCCESSPURGE ) || ( $message == _COM_SEF_NORECORDS ) ) { ?>
				<input type="hidden" name="task" value="" />
				<input type="submit" name="continue" value="<?php echo _COM_SEF_OK ?>" />
				<?php
			}else{ ?>
				<input type="hidden" name="task" value="purge" />
				<input type="submit" name="confirmed" value="<?php echo _COM_SEF_PROCEED; ?>" />
				<?php
			} ?>
		</form>
		<?php
	}

	function purgeMeta( $option, $message, $confirmed ) { ?>
		<form action="index2.php" method="post" name="adminForm">
			<table class="adminheading">
				<tr>
					<th>
						<?php echo _COM_SEF_META_TITLE_PURGE; ?>
						<br/>
						<span class="small" style="text-align:left">
							<a href="index2.php?option=com_sef"><?php echo _COM_SEF_BACK; ?></a>
						</span>
					</th>
				</tr>
				<tr>
					<td>
						<p class="message">
							<?php echo $message; ?>
							<br/>
						</p>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<?php
			if( ( $message == _COM_SEF_META_SUCCESS_PURGE ) || ( $message == _COM_SEF_NORECORDS ) ) { ?>
				<input type="hidden" name="task" value="" />
				<input type="submit" name="continue" value="<?php echo _COM_SEF_OK; ?>" />
				<?php
			}else{ ?>
				<input type="hidden" name="task" value="purgeMeta" />
				<input type="submit" name="confirmed" value="<?php echo _COM_SEF_PROCEED; ?>" />
				<?php
			} ?>
		</form>
		<?php
	}

	function import_export( $ViewModeId = 0 ) { ?>
		<script type="text/javascript">
			/* <![CDATA[ */
			function checkForm1(){
				if (document.backupform1.userfile.value == ""){
					alert('<?php echo _COM_SEF_SELECT_FILE; ?>');
					return false;
				}else{
					return true;
				}
			};
			function checkForm2(){
				if (document.backupform2.userfile.value == ""){
					alert('<?php echo _COM_SEF_SELECT_FILE; ?>');
					return false;
				}else{
					return true;
				}
			};

			function toggle_display(idname){
				obj = fetch_object(idname);
				if (obj){
					if (obj.style.display == "none"){
						obj.style.display = "";
					}else{
						obj.style.display = "none";
					}
				}
				return false;
			};
			/* ]]> */
		</script>
		<table class="adminheading">
			<tr>
				<th>
					<?php echo $ViewModeId == 2 ? _COM_SEF_IMPORT_EXPORT_CUSTOM:str_replace( '<br />',' ', _COM_SEF_IMPORT_EXPORT); ?>
					<br/>
					<span class="small" style="text-align:left">
						<a href="index2.php?option=com_sef"><?php echo _COM_SEF_BACK?></a>
					</span>
				</th>
			</tr>
		</table>
		<table class="adminform" width="100%" align="left" border="0" cellpadding="0" cellspacing="25">
			<tr>
				<td>
					<form method="post" action="index2.php?option=com_sef&amp;task=import&amp;ViewModeId=<?php echo $ViewModeId; ?>" enctype="multipart/form-data" onSubmit="return checkForm1();" name="backupform1">
						<input type="file" name="userfile" size="50" />
						<input type="submit" value="<?php echo ($ViewModeId == 2 ? _COM_SEF_IMPORT : _COM_SEF_IMPORT_ALL); ?>" />
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<form method="post" action="index2.php?option=com_sef&amp;task=importOpenSEF&amp;ViewModeId=<?php echo $ViewModeId; ?>" enctype="multipart/form-data" onSubmit="return checkForm2();" name="backupform2">
						<input type="file" name="userfile" size="50" />
						<input type="submit" value="<?php echo _COM_SEF_IMPORT_OPEN_SEF; ?>" />
					</form>
				</td>
			</tr>
			<tr><td><hr style="border-top: 1px solid #666666" /></td></tr>
			<tr>
				<td style="text-align:center">
					<input type="button" value="<?php echo ($ViewModeId == 2 ? _COM_SEF_EXPORT : _COM_SEF_EXPORT_ALL); ?>" onClick="javascript:location.href='index2.php?option=com_sef&amp;task=export&amp;ViewModeId=<?php echo $ViewModeId; ?>'" />
				</td>
			</tr>
		</table>
		<?php
	}

	function import_export_meta( ) { ?>
		<script type="text/javascript">
			/* <![CDATA[ */
			function checkForm(){
				if (document.backupform.userfile.value == ""){
					alert('<?php echo _COM_SEF_SELECT_FILE; ?>');
					return false;
				}else{
					return true;
				}
			};

			function toggle_display(idname){
				obj = fetch_object(idname);
				if (obj){
					if (obj.style.display == "none"){
						obj.style.display = "";
					}else{
						obj.style.display = "none";
					}
				}
				return false;
			};
			/* ]]> */
		</script>
		<div style="margin: 10px; padding; 10px">
			<form method="post" action="index2.php?option=com_sef&amp;task=import_meta" enctype="multipart/form-data" onSubmit="return checkForm();" name="backupform">
				<input type="file" name="userfile" size="60" />
				<input type="submit" value="<?php echo _COM_SEF_IMPORT_META; ?>" />
			</form>
			<br />
			<input type="button" value="<?php echo _COM_SEF_EXPORT_META; ?>" onClick="javascript:location.href='index2.php?option=com_sef&amp;task=export_meta'" />
		</div>
		<?php
	}
}
?>