<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<?php
/**
* @version $Id: admin.Category.html.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Category
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* @package Joomla
* @subpackage Category
*/
class HTML_PhpMagicMetaTag {

	//============================================= POSTAL CODE OPTION ===============================================
	function showMetaTagConfig( $option,$aConfig) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>MetaTag Configuration Manager</th>
			</tr>
		</table>
                    <div id="tabs">
  <ul>
    <li><a href="#tabs-1">Company Landings</a></li>
    <li><a href="#tabs-2">Basket Landings</a></li>
    <li><a href="#tabs-3">Flowers Landings</a></li>
    <li><a href="#tabs-4">Sympathy Landings</a></li>
    <li><a href="#tabs-5">Default</a></li>
    <li><a href="#tabs-6">Product</a></li>
    <li><a href="#tabs-7">Category</a></li>
  </ul>

                        <?php $i = 1; foreach($aConfig as $key=>$info){ ?>
                                
    <div id="tabs-<?php echo $i; ?>">
                <?php if($key == 'baskets' || $key == 'flowers' || $key == 'sympathy'){ ?>
                            <div align="left"><b><?php echo $key; ?> meta tag info (you can use short text {city} and {province} )</b></div>
                   <?php }elseif($key == 'company'){ ?>
                            <div align="left"><b><?php echo $key; ?> meta tag info (you can use short text {company})</b></div>
                   <?php }elseif($key == 'product'){ ?>
                            <div align="left"><b><?php echo $key; ?> meta tag info (you can use short text {product} and {category})</b></div>
                   <?php }elseif($key == 'category'){ ?>
                            <div align="left"><b><?php echo $key; ?> meta tag info (you can use short text {category})</b></div>
                  <?php }else{?>
                            <div align="left"><b><?php echo $key; ?> meta tag info</b></div>
                  <?php }

                           foreach($info as $inf){ $lang = $inf->lang ; $lang_title = ( $inf->lang == 1 ) ? 'English' : 'French';  ?>
                   <table style="width: 50%;float: left;">
                                     <tr>
                                            <td width="30%"><b><?php echo $lang_title  ;?>  version</b><br/></td>
                                    </tr>
                                    <tr>
                                            <td width="10%"><b>Page Title:</b></td>
                                            <td width="30%"><input style='width:371px' type="text" name='<?php echo $key; ?>_title_<?php echo $lang; ?>' value="<?php echo $inf->title ;?>" size="70" /></td>
                                    </tr>
                                    <tr>
                                            <td width="10%"><b>Meta Description:</b></td>
                                            <td width="30%"><textarea type="text" name='<?php echo $key; ?>_description_<?php echo $lang; ?>' rows="5" cols="50"><?php echo $inf->description ;?></textarea>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    </tr>
                                    <tr>
                                            <td width="10%"><b>Meta Keywords:</b></td>
                                            <td width="30%">
                                                <textarea type="text" name='<?php echo $key; ?>_keywords_<?php echo $lang; ?>' rows="5" cols="50"><?php echo $inf->keywords ;?></textarea><br/>
                                            </td>
                                    </tr>


                            </table>
                        <?php  } ?>
        <div style="clear: both;"></div>
   </div>
                        <?php $i++; } ?>

</div>

                    <script type="text/javascript" language="javascript">

                    window.onload = function() {
                    $(function() {
                      $( "#tabs" ).tabs();
                    });

                    }; </script>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}

}
?>