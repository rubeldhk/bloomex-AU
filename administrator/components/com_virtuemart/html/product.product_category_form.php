<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: product.product_category_form.php,v 1.7.2.1 2006/02/18 09:20:11 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage html
 * @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
mm_showMyFileName(__FILE__);
global $ps_product_category, $ps_product,$mosConfig_aws_s3_bucket_public_url;

$category_id = mosgetparam($_REQUEST, 'category_id', 0);
$option = empty($option) ? mosgetparam($_REQUEST, 'option', 'com_virtuemart') : $option;

//First create the object and let it print a form heading
$formObj = new formFactory($VM_LANG->_PHPSHOP_CATEGORY_FORM_LBL);
//Then Start the form
$formObj->startForm('adminForm', 'enctype="multipart/form-data"');

$french_database = array();
$searchable_obj = false;

if ($category_id) {
    $q = "SELECT * FROM #__{vm}_category_data_fr ";
    $q .= "WHERE category_id='$category_id' ";
    $db->query($q);
    $db->next_record();
    $french_database[] = $db->f("id");
    $french_database[] = $db->f("category_id");
    $french_database[] = $db->f("category_thumb_image");
    $french_database[] = $db->f("category_full_image");
    
    $query = "SELECT `category_id` FROM `jos_vm_category_unsearchable` WHERE `category_id`=".$category_id."";
    $database->setQuery($query);
    $database->loadObject($searchable_obj);

    $q = "SELECT c.*,s.full_image_link_webp FROM #__{vm}_category as c
          join #__{vm}_category_xref as x on x.category_child_id = c.category_id
          left join #__{vm}_category_s3_images as s on s.category_id = c.category_id ";
    $q .= " WHERE c.category_id='$category_id' ";

    $db->query($q);
    $db->next_record();
} elseif (empty($vars["error"])) {
    $default["category_publish"] = "Y";
    $default["category_flypage"] = FLYPAGE;
    $default["category_browsepage"] = CATEGORY_TEMPLATE;
    $default["products_per_row"] = PRODUCTS_PER_ROW;
}

$tabs = new mShopTabs(0, 1, "_main");
$tabs->startPane("category-pane");
$tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/edit.png\" align=\"center\" width=\"16\" height=\"16\" border=\"0\" />&nbsp;" . $VM_LANG->_PHPSHOP_CATEGORY_FORM_LBL, "info-page");

global $database;

$sql = "SELECT * FROM #__vm_category_options WHERE category_id = $category_id";
$database->setQuery($sql);
$database->loadObject($category_options);

$sqlProducts = "SELECT o.product_id,p.product_name,p.product_sku
     from  jos_vm_product_options as o 
     left join jos_vm_product as p on p.product_id=o.product_id
     WHERE o.canonical_category_id = $category_id";
$database->setQuery($sqlProducts);
$productsList = $database->loadObjectList();

mosCommonHTML::loadCKeditor();


$footer_description_default='';
$footer_description_city_default='';
$sql = "SELECT `description_text_footer` FROM `jos_metatags` WHERE `url` LIKE '/' AND `page_type` LIKE '1'  order by city ";
$database->setQuery($sql);
$footer_description_default_obj = $database->loadObjectList();
if($footer_description_default_obj){
    $footer_description_default = ($footer_description_default_obj[0]->description_text_footer)?$footer_description_default_obj[0]->description_text_footer:'';
    $footer_description_city_default = ($footer_description_default_obj[1]->description_text_footer)?$footer_description_default_obj[1]->description_text_footer:'';
}
?> 
<table class="adminform">
    <tr> 
        <td width="21%" nowrap><div align="right"><?php echo $VM_LANG->_PHPSHOP_CATEGORY_FORM_PUBLISH ?>:</div></td>
        <td width="79%"><?php
            if ($db->sf("category_publish") == "Y") {
                echo "<input type=\"checkbox\" name=\"category_publish\" value=\"Y\" checked=\"checked\" />";
            } else {
                echo "<input type=\"checkbox\" name=\"category_publish\" value=\"Y\" />";
            }
            ?> 
        </td>
    </tr>
    <tr> 
        <td width="21%" nowrap><div align="right">UnSearchable:</div></td>
        <td width="79%"><?php
            if ($searchable_obj != false) {
                echo "<input type=\"checkbox\" name=\"searchable\"  value=\"Y\"  checked=\"checked\" />";
            } else {
                echo "<input type=\"checkbox\" name=\"searchable\"  value=\"Y\"  />";
            }
            ?> 
        </td>
    </tr>
    <tr> 
        <td width="21%" nowrap><div align="right">Sitemap Publish?:</div></td>
        <td width="79%">
            <input type="checkbox" name="sitemap_publish" value="1" <?php echo (isset($category_options->sitemap_publish) AND $category_options->sitemap_publish == '1') ? 'checked="checked"' : ''; ?>/>
        </td>
    </tr>
    <tr>
        <td width="21%" nowrap><div align="right">Show in parent page?:</div></td>
        <td width="79%">
            <input type="checkbox" name="child_list_publish" value="1" <?php echo (isset($category_options->child_list_publish) AND $category_options->child_list_publish == '1') ? 'checked' : ''; ?>/>
        </td>
    </tr>
    <tr>
        <td width="50%" >
            <div style="text-align:right;font-weight:bold;">Flowers <input name="category_type_option" type="radio" value="1" <?php echo (isset($category_options->category_type) AND $category_options->category_type == '1') ? 'checked' : ''; ?>></div>
        </td>
        <td width="50%" >
            <div style="font-weight:bold;">Gift Basket <input name="category_type_option" type="radio" value="2" <?php echo (isset($category_options->category_type) AND $category_options->category_type == '2') ? 'checked' : ''; ?>></div>
        </td>
    </tr>

    <tr> 
        <td width="21%" nowrap><div align="right"><?php echo $VM_LANG->_PHPSHOP_CATEGORY_FORM_NAME ?>:</div></td>
        <td width="79%"> 
            <input type="text" class="inputbox seo_name" name="category_name" size="60" value="<?php echo shopMakeHtmlSafe($db->sf('category_name')) ?>" />
        </td>
    </tr>
    <tr> 
        <td width="21%">
            <div style="text-align:right;">Alias:</div>
        </td>
        <td width="79%"> 
            <input type="text" class="inputbox seo_alias"  name="alias" value="<?php echo shopMakeHtmlSafe($db->sf("alias")); ?>" size="32" maxlength="255" />
        </td>
    </tr>
    <tr>
        <td >
            <div align="right">Products list with canonical by this Category:</div>
        </td>
        <td valign="top">
            <?php
            foreach($productsList as $p){
                echo "<a href='/administrator/index2.php?page=product.product_form&product_id=".$p->product_id."&option=com_virtuemart' target='_blank'>".$p->product_name."  (".$p->product_sku.")</a><br>";
            }
            ?>
        </td>
    </tr>
    <tr> 
        <td width="21%" valign="top" nowrap><div  align="right"><?php echo $VM_LANG->_PHPSHOP_CATEGORY_FORM_DESCRIPTION ?>:</div></td>
        
        <td width="79%" valign="top"><textarea id="category_description" name="category_description" cols="60" rows="4"><?php echo $db->f("category_description"); ?></textarea>
            <?php //editorArea('editor1', $db->f("category_description"), 'category_description', '500', '200', '100', '20') ?>
        </td>
    </tr>
    <tr>
        <td width="21%" valign="top" nowrap><div  align="right"><?php echo $VM_LANG->_PHPSHOP_CATEGORY_FORM_DESCRIPTION ?> For City {city_name} {city_url} {state_name} {state_short}:</div></td>
        <td width="79%" valign="top"><textarea id="category_description_city" name="category_description_city" cols="60" rows="4"><?php echo $db->f("category_description_city"); ?></textarea>
            <?php //editorArea('editor2', $db->f("category_description_city"), 'category_description_city', '500', '200', '100', '20') ?>
        </td>
    </tr>
    <tr>
        <td width="21%" valign="top" nowrap><div  align="right">Category Description Footer {readmore}:</div></td>
        <td width="79%" valign="top">
            <textarea id="description_footer" name="description_footer" cols="60" rows="4"><?php echo ($category_options->description_footer) ? $category_options->description_footer : $footer_description_default; ?></textarea>
            <?php //editorArea('editor2', $db->f("category_description_city"), 'category_description_city', '500', '200', '100', '20') ?>
        </td>
    </tr>
    <tr>
        <td width="21%" valign="top" nowrap><div  align="right">Category Description Footer For City {city_name} {city_url} {state_name} {state_short} {readmore}:</div></td>
        <td width="79%" valign="top">
            <textarea id="description_footer_city" name="description_footer_city" cols="60" rows="4"><?php echo ($category_options->description_footer_city) ? $category_options->description_footer_city : $footer_description_city_default; ?></textarea>
            <?php //editorArea('editor2', $db->f("category_description_city"), 'category_description_city', '500', '200', '100', '20') ?>
        </td>
    </tr>
    <tr>
        <td ><div align="right"><?php echo $VM_LANG->_PHPSHOP_MODULE_LIST_ORDER ?>: </div></td>
        <td valign="top"><?php
            echo $ps_product_category->list_level($db->f("category_parent_id"), $db->f("category_id"), $db->f("list_order"));
            echo "<input type=\"hidden\" name=\"currentpos\" value=\"" . $db->f("list_order") . "\" />";
            ?>
        </td>
    </tr>
    <tr> 
        <td width="21%" valign="top" nowrap><div  align="right"><?php echo $VM_LANG->_PHPSHOP_CATEGORY_FORM_PARENT ?>:</div></td>
        <td width="79%" valign="top"> <?php
            if (!$category_id) {
                $ps_product_category->list_all("parent_category_id", $category_id);
            } else {
                $ps_product_category->list_all("category_parent_id", $category_id);
            }
            echo "<input type=\"hidden\" name=\"current_parent_id\" value=\"" . $db->f("category_parent_id") . "\" />";
            ?>
        </td>
    </tr>

    <tr>
        <td colspan="2"><br /></td>
    </tr>
    <tr>
        <td><div align="right">Category Browse Page: </div></td>
        <td valign="top">
            <input type="text" class="inputbox" name="category_browsepage" value="<?php $db->sp("category_browsepage"); ?>" />
        </td>
    </tr>
    <tr>
        <td ><div align="right">Show x products per row: </div></td>
        <td valign="top">
            <input type="text" class="inputbox" size="3" name="products_per_row" value="<?php $db->sp("products_per_row"); ?>" />
        </td>
    </tr>
    <tr>
        <td colspan="2"><br /></td>
    </tr>
    <tr>
        <td ><div align="right">
                <?php echo $VM_LANG->_PHPSHOP_CATEGORY_FORM_FLYPAGE . " " . $VM_LANG->_PHPSHOP_LEAVE_BLANK ?>:</div>
        </td>
        <td valign="top">
            <input type="text" class="inputbox" name="category_flypage" value="<?php $db->sp("category_flypage"); ?>" />
        </td>
    </tr>
    <tr>
        <td width="29%" valign="top">
            <div style="text-align:right;font-weight:bold;">
                Canonical category:
            </div>
        </td>
        <td width="71%">
            <?php $ps_product_category->list_all("canonical_category_id", "", [($category_options->canonical_category_id??$category_id)=>1], 10, false, false); ?>
        </td>
    </tr>
</table>
<script type="text/javascript">
    CKEDITOR.replace('category_description');
    CKEDITOR.replace('category_description_city');
    CKEDITOR.replace('description_footer', {
        enterMode : CKEDITOR.ENTER_BR
    });
    CKEDITOR.replace('description_footer_city', {
        enterMode : CKEDITOR.ENTER_BR
    });
</script>
<?php
$tabs->endTab();
$tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/image.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;" . _E_IMAGES, "status-page");

if (!stristr($db->f("category_thumb_image"), "http")) {
    echo "<input type=\"hidden\" name=\"category_thumb_image_curr\" value=\"" . $db->f("category_thumb_image") . "\" />";
    echo "<input type=\"hidden\" name=\"category_thumb_image_curr_fr\" value=\"".(isset($french_database[2]) ? $french_database[2] : ''). "\" />";
}


if (!stristr($db->f("category_full_image"), "http")) {
    echo "<input type=\"hidden\" name=\"category_full_image_curr\" value=\"" . $db->f("category_full_image") . "\" />";
    echo "<input type=\"hidden\" name=\"category_full_image_curr_fr\" value=\"" .(isset($french_database[3]) ? $french_database[3] : ''). "\" />";
}


$ps_html->writableIndicator(array(IMAGEPATH . "category"));
$lang_names = array('English: ', 'French: ');
$lang_suffix = array('', '_fr');
$lang_count = count($lang_suffix);
?>

<table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
        <td valign="top" width="50%" style="border-right: 1px solid black;">
            <h2><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_FULL_IMAGE ?></h2>
            <table>
                <tr>
                    <td colspan="2" >
                        <?php
                        if ($category_id) {
                            echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_UPDATE_LBL . "<br />";
                        }
                        for ($i = 0; $i < $lang_count; $i++) {
                            $cti = 'category_full_image' . $lang_suffix[$i];
                            echo $lang_names[$i] . '<input type="file" class="inputbox" name="' . $cti . '" id="' . $cti . '" size="50" maxlength="255" onchange="upload_new_image(\'' . $cti . '\',\'' . $cti . '_new_name\',\'' . $cti . '_new_name_result\');" />
                    <input type="text" name="' . $cti . '_new_name" id="' . $cti . '_new_name" onKeyUp="isset_name(\'' . $cti . '_new_name\',\'' . $cti . '_new_name_result\', \'' . $cti . '\');">
                    <div id="' . $cti . '_new_name_result"></div>
                    <input type="hidden" name="' . $cti . '_new_name_hidden" id="' . $cti . '_new_name_hidden" value="">  
                    <br/>';
                        }
                        ?> 
                    </td>
                </tr>
                <tr> 
                    <td colspan="2" ><strong><?php echo $VM_LANG->_PHPSHOP_IMAGE_ACTION ?>:</strong><br/>
                        <input type="radio" class="inputbox" name="category_full_image_action" id="category_full_image_action0" checked="checked" value="none" onchange="toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image, true);
                                toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_fr, true);
                                toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_url, true);
                                toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_url_fr, true);"/>
                        <label for="category_full_image_action0"><?php echo $VM_LANG->_PHPSHOP_NONE ?></label><br/>
                        <?php
                        if (function_exists('imagecreatefromjpeg')) {
                            ?>
                            <input type="radio" class="inputbox" name="category_full_image_action" id="category_full_image_action1" value="auto_resize" onchange="toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image, true);
                                    toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_fr, true);
                                    toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_url, true);
                                    toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_url_fr, true);"/>
                            <label for="category_full_image_action1"><?php
                                echo $VM_LANG->_PHPSHOP_FILES_FORM_AUTO_THUMBNAIL ."</label><br />";
                            }

                            if ($category_id and ( $db->f("category_full_image") || $french_database[3])) {
                                ?>
                                <input type="radio" class="inputbox" name="category_full_image_action" id="category_full_image_action2" value="delete" onchange="toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image, true);
                                        toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_fr, true);
                                        toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_url, true);
                                        toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_url_fr, true);"/>
                                <label for="category_full_image_action2"><?php
                                    echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_DELETE_LBL . "</label><br />";
                                }
                                ?> 
                                </td>
                                </tr>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr> 
                                    <td width="21%">
                                        <?php
                                        for ($i = 0; $i < $lang_count; $i++) {
                                            echo $lang_names[$i] . _URL . " (" . _CMN_OPTIONAL . "!)&nbsp;<br/>";
                                        }
                                        ?>
                                    </td>
                                    <td width="79%" >
                                        <?php
                                        if (stristr($db->f("category_full_image"), "http")) {
                                            $category_full_image_url = $db->f("category_full_image");
                                            $category_full_image_url_fr = $french_database[3];
                                        } else {
                                            if (!empty($_REQUEST['category_full_image_url'])) {
                                                $category_full_image_url = $_REQUEST['category_full_image_url'];
                                                $category_full_image_url_fr = $_REQUEST['category_full_image_url_fr'];
                                            } else {
                                                $category_full_image_url = "";
                                                $category_full_image_url_fr = "";
                                            }
                                        }
                                        ?>
                                        <?php echo $lang_names[0]; ?><input type="text" class="inputbox" size="50" name="category_full_image_url<?php echo $lang_suffix[0]; ?>" value="<?php echo $category_full_image_url ?>" onchange="if (this.value.length > 0)
                                                    document.adminForm.auto_resize.checked = false;
                                                else
                                                    document.adminForm.auto_resize.checked = true;
                                                toggleDisable(document.adminForm.auto_resize, document.adminForm.category_thumb_image_url, true);
                                                toggleDisable(document.adminForm.auto_resize, document.adminForm.category_thumb_image, true);" /><br/>
                                        <?php echo $lang_names[1]; ?><input type="text" class="inputbox" size="50" name="category_full_image<?php echo $lang_suffix[1]; ?>_url" value="<?php echo $category_full_image_url_fr ?>" onchange="if (this.value.length > 0)
                                                    document.adminForm.auto_resize.checked = false;
                                                else
                                                    document.adminForm.auto_resize.checked = true;
                                                toggleDisable(document.adminForm.auto_resize, document.adminForm.category_thumb_image_fr_url, true);
                                                toggleDisable(document.adminForm.auto_resize, document.adminForm.category_thumb_image_fr, true);" />
                                    </td> 
                                </tr>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr> 
                                    <td colspan="2" >
                                        <div style="overflow:auto;">
                                            <?php
                                            echo $lang_names[0];
                                            echo $ps_product->image_tag($mosConfig_aws_s3_bucket_public_url . $db->f("full_image_link_webp"), "", 1, "category");
                                            ?>
                                            <br/>

                                            <?php
                                            echo $lang_names[1];
                                            echo $ps_product->image_tag((isset($french_database[3]) ? $french_database[3] : ''), "", 0, "category")
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                </table>
                                </td>

                                <td valign="top" width="50%">
                                    <h2><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_THUMB_IMAGE ?></h2>
                                    <table>
                                        <tr> 
                                            <td colspan="2" ><?php
                                                if ($category_id) {
                                                    echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_UPDATE_LBL . "<br/>";
                                                }
                                                ?> 

                                                <?php
                                                $script_data = array(
                                                    "if(document.adminForm.category_thumb_image.value!='') document.adminForm.category_thumb_image_url.value='';",
                                                    "if(document.adminForm.category_thumb_image_fr.value!='') document.adminForm.category_thumb_image_url_fr.value='';"
                                                );
                                                for ($i = 0; $i < $lang_count; $i++) {
                                                    $cti = 'category_thumb_image' . $lang_suffix[$i];
                                                    echo $lang_names[$i] . '<input type="file" class="inputbox" name="' . $cti . '" id="' . $cti . '" size="50" maxlength="255" onchange="upload_new_image(\'' . $cti . '\',\'' . $cti . '_new_name\',\'' . $cti . '_new_name_result\');' . $script_data[$i] . '" />
                    <input type="text" name="' . $cti . '_new_name" id="' . $cti . '_new_name" onKeyUp="isset_name(\'' . $cti . '_new_name\',\'' . $cti . '_new_name_result\', \'' . $cti . '\');">
                    <div id="' . $cti . '_new_name_result"></div>
                    <input type="hidden" name="' . $cti . '_new_name_hidden" id="' . $cti . '_new_name_hidden" value="">  
                    <br/>';
                                                }
                                                ?>

                                                <!--  
                                                <?php echo $lang_names[0]; ?><input type="file" class="inputbox" name="category_thumb_image<?php echo $lang_suffix[0]; ?>" size="50" maxlength="255" onchange="if(document.adminForm.category_thumb_image.value!='') document.adminForm.category_thumb_image_url.value='';" /><br/>
                                                <?php echo $lang_names[1]; ?><input type="file" class="inputbox" name="category_thumb_image<?php echo $lang_suffix[1]; ?>" size="50" maxlength="255" onchange="if(document.adminForm.category_thumb_image_fr.value!='') document.adminForm.category_thumb_image_url_fr.value='';" />
                                                -->
                                            </td>
                                        </tr>
                                        <tr> 
                                            <td colspan="2" ><strong><?php echo $VM_LANG->_PHPSHOP_IMAGE_ACTION ?>:</strong><br/>
                                                <input type="radio" class="inputbox" id="category_thumb_image_action0" name="category_thumb_image_action" checked="checked" value="none" onchange="toggleDisable(document.adminForm.image_action[1], document.adminForm.category_thumb_image, true);
                                                        toggleDisable(document.adminForm.image_action[1], document.adminForm.category_thumb_image_url, true);"/>
                                                <label for="category_thumb_image_action0"><?php echo $VM_LANG->_PHPSHOP_NONE ?></label><br/>
                                                <?php if ($category_id and ( $db->f("category_thumb_image") || $french_database[2])) { ?>
                                                    <input type="radio" class="inputbox" id="category_thumb_image_action1" name="category_thumb_image_action" value="delete" onchange="toggleDisable(document.adminForm.image_action[1], document.adminForm.category_thumb_image, true);
                                                            toggleDisable(document.adminForm.image_action[1], document.adminForm.category_thumb_image_url, true);"/>
                                                    <label for="category_thumb_image_action1"><?php
                                                        echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_DELETE_LBL . "</label><br />";
                                                    }
                                                    ?> 
                                            </td>
                                        </tr>
                                        <tr><td colspan="2">&nbsp;</td></tr>
                                        <tr> 
                                            <td width="21%" >
                                                <?php
                                                for ($i = 0; $i < $lang_count; $i++) {
                                                    echo $lang_names[$i] . _URL . " (" . _CMN_OPTIONAL . "!)&nbsp;<br/>";
                                                }
                                                ?></td>
                                            <td width="79%" >
                                                <?php
                                                if (stristr($db->f("category_thumb_image"), "http")) {
                                                    $category_thumb_image_url = $db->f("category_thumb_image");
                                                    $category_thumb_image_url_fr = $french_database[2];
                                                } else {
                                                    if (!empty($_REQUEST['category_thumb_image_url'])) {
                                                        $category_thumb_image_url = $_REQUEST['category_thumb_image_url'];
                                                        $category_thumb_image_url_fr = $_REQUEST['category_thumb_image_url'];
                                                    } else {
                                                        $category_thumb_image_url = "";
                                                        $category_thumb_image_url_fr = "";
                                                    }
                                                }
                                                ?>
                                                <input type="text" class="inputbox" size="50" name="category_thumb_image_url<?php echo $lang_suffix[0]; ?>" value="<?php echo $category_thumb_image_url ?>" /><br/>
                                                <input type="text" class="inputbox" size="50" name="category_thumb_image_url<?php echo $lang_suffix[1]; ?>" value="<?php echo $category_thumb_image_url_fr ?>" />
                                            </td>
                                        </tr>
                                        <tr><td colspan="2">&nbsp;</td></tr>
                                        <tr>
                                            <td colspan="2" >
                                                <div style="overflow:auto;">
                                                    <?php
                                                    echo $lang_names[0];
                                                    echo $ps_product->image_tag($db->f("category_thumb_image"), "", 1, "category")
                                                    ?>
                                                    <br/>
                                                    <?php
                                                    echo $lang_names[1];
                                                    echo $ps_product->image_tag((isset($french_database[2]) ? $french_database[2] : ''), "", 0, "category")
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <br/>
                                        <hr  size="1" width="100%"/>
                                        <h2>Header Image</h2>
                                        <b>Dimensions: 735px × 193px<br/> Max size: 1 MB <br/>Supported formats are PNG, JPG, GIF and BMP. <br/></b>
                                        <?php
                                        global $database, $mosConfig_live_site, $mosConfig_absolute_path;

                                        $sql = " SELECT * FROM #__vm_category_header_img	WHERE category_id = $category_id";
                                        $database->setQuery($sql);
                                        $rows = $database->loadObjectList();

                                        $sImage = "";
                                        if (!empty($rows[0]->header_image) && is_file($mosConfig_absolute_path . "/images/header_images/" . $rows[0]->header_image)) {
                                            $sImage = $mosConfig_live_site . "/images/header_images/" . $rows[0]->header_image;
                                        }
                                        ?>
                                        To update actual image, please type in path to the new image<br/>
                                        <input  type="file" size="40" name="header_image" value=""/><br/>
                                        <?php if (!empty($sImage)) { ?>
                                            <img src="<?php echo $sImage; ?>" /><br/>
                                            <input type="checkbox" name="remove_header_image" value="1" /> Please tick here to remove this image.
                                        <?php } ?><br/><br/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <br/>
                                        <hr  size="1" width="100%"/>
                                        <h2>French Header Image</h2>
                                        <b>Dimensions: 735px × 193px<br/> Max size: 1 MB <br/>Supported formats are PNG, JPG, GIF and BMP. <br/></b>
                                        <?php
                                        $sImageFr = "";
                                        if (!empty($rows[0]->header_image_fr) && is_file($mosConfig_absolute_path . "/images/header_images/" . $rows[0]->header_image_fr)) {
                                            $sImageFr = $mosConfig_live_site . "/images/header_images/" . $rows[0]->header_image_fr;
                                        }
                                        ?>
                                        To update actual image, please type in path to the new image<br/>
                                        <input  type="file" size="40" name="header_image_fr" value=""/><br/>
                                        <?php if (!empty($sImageFr)) { ?>
                                            <img src="<?php echo $sImageFr; ?>" /><br/>
                                            <input type="checkbox" name="remove_header_image_fr" value="1" /> Please tick here to remove this image.
                                        <?php } ?><br/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <br/>
                                        <hr  size="1" width="100%"/>
                                        <h2>Category Title Background</h2>
                                        <?php
                                        global $database, $mosConfig_live_site, $mosConfig_absolute_path;

                                        $sql = " SELECT background FROM #__vm_category_header_img WHERE category_id = $category_id";
                                        $database->setQuery($sql);
                                        $rows = $database->loadObjectList();
                                        
                                        if ($rows && !empty($rows[0]->background) && is_file($mosConfig_absolute_path . "/images/header_images/" . $rows[0]->background)) {
                                            $sImage = $mosConfig_live_site . "/images/header_images/" . $rows[0]->background;
                                        } else {
                                            $sImage = null;
                                        }
                                        ?>
                                        To update actual image, please type in path to the new image<br/>
                                        <input  type="file" size="40" name="background" value=""/><br/>
                                        <?php if (!is_null($sImage)) { ?>
                                            <img src="<?php echo $sImage; ?>" /><br/>
                                            <input type="checkbox" name="remove_background" value="1" /> Please tick here to remove this image.
                                        <?php } ?><br/><br/>
                                    </td>
                                </tr>
                                </table>

                                <?php
                                $tabs->endTab();

                                $tabs->startTab("<img src=\"" . IMAGEURL . "ps_image/info.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;Meta Information", "images-page");

                                $aMetaInfo = explode("[--2010--]", trim($db->f("meta_info")));
                                $aMetaInfoFr = explode("[--2010--]", trim($db->f("meta_info_fr")));
                                
                                ?>
                                <table class="adminform">
                                    <tr>
                                        <td colspan="2"><h2>Meta Information(English Version)</h2></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"><b>H1:</b></td>
                                        <td width="90%" align="left"><input type="text" name="h1" value="<?php echo (isset($category_options->h1) ? $category_options->h1 : ''); ?>" size="70" /></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"><b>Page Title:</b></td>
                                        <td width="90%" align="left"><input type="text" name="page_title" value="<?php echo (isset($aMetaInfo[0]) ? $aMetaInfo[0] : ''); ?>" size="70" /></td>
                                    </tr>
                                    <tr>
                                        <td><b>Meta Description:</b></td>
                                        <td align="left"><textarea type="text" name="meta_description" rows="5" cols="71"><?php echo (isset($aMetaInfo[1]) ? $aMetaInfo[1] : ''); ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td><b>Meta Keywords:</b></td>
                                        <td align="left">	
                                            <textarea type="text" name="meta_keywords" rows="5" cols="71"><?php echo (isset($aMetaInfo[2]) ? $aMetaInfo[2] : ''); ?></textarea><br/>
                                            System will replace all space by commas
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><h2>Meta Information For City {city_name} {state_name}</h2></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"><b>H1:</b></td>
                                        <td width="90%" align="left"><input type="text" name="h1_city" value="<?php echo (isset($category_options->h1_city) ? $category_options->h1_city : ''); ?>" size="70" /></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"><b>Page Title:</b></td>
                                        <td width="90%" align="left"><input type="text" name="page_title_fr" value="<?php echo (isset($aMetaInfoFr[0]) ? $aMetaInfoFr[0] : ''); ?>" size="70" /></td>
                                    </tr>
                                    <tr>
                                        <td><b>Meta Description:</b></td>
                                        <td align="left"><textarea type="text" name="meta_description_fr" rows="5" cols="71"><?php echo (isset($aMetaInfoFr[1]) ? $aMetaInfoFr[1] : ''); ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td><b>Meta Keywords:</b></td>
                                        <td align="left">	
                                            <textarea type="text" name="meta_keywords_fr" rows="5" cols="71"><?php echo (isset($aMetaInfoFr[2]) ? $aMetaInfoFr[2] : ''); ?></textarea><br/>
                                            System will replace all space by commas
                                        </td>
                                    </tr>
                                </table>
                                <!-- Changed Product Type - Begin -->
                                <?php
                                $tabs->endTab();
                                $tabs->startTab("History", "images-page");
                                $dbh = new ps_DB;
                                $sql = "(SELECT *,  'stage' AS `where` FROM  `jos_vm_category_history_stage` 
                                WHERE `category_id`=".$category_id.")
                                UNION
                                (SELECT *,  'live' AS `where` FROM `jos_vm_category_history_live` 
                                WHERE `category_id`=".$category_id.")
                                ORDER BY `date` DESC";

                                $dbh->query($sql);
                                $product_histories = $dbh->loadObjectList();

                                if (sizeof($product_histories) > 0)
                                {
                                    ?>
                                    <table class="adminform" border="1">
                                        <tr>
                                            <td><h2>Name</h2></td>
                                            <td><h2>Old value</h2></td>
                                            <td><h2>New value</h2></td>
                                            <td><h2>Username</h2></td>
                                            <td><h2>Date</h2></td>
                                            <td><h2>Where</h2></td>
                                        </tr>
                                        <?php
                                        foreach ($product_histories as $product_history)
                                        {
                                            ?>
                                            <tr>
                                                <td><?php echo $product_history->name; ?></td>
                                                <td><?php echo $product_history->old; ?></td>
                                                <td><?php echo $product_history->new; ?></td>
                                                <td><?php echo $product_history->username; ?></td>
                                                <td><?php echo $product_history->date; ?></td>
                                                <td><?php echo $product_history->where; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>

                                    </table>
                                    <?php
                                }
                                $tabs->endTab();
                                $tabs->endPane();

// Add necessary hidden fields
                                $formObj->hiddenField('category_id', $category_id);

                                $funcname = !empty($category_id) ? "productCategoryUpdate" : "productCategoryAdd";

//finally close the form:
                                $formObj->finishForm($funcname, $modulename . '.product_category_list', $option);
                                ?>
                                <script src="/administrator/templates/joomla_admin/js/jquery1-7-2.js"></script> 
                                <script src="/administrator/templates/joomla_admin/js/jquery.form.js"></script>
                                <?php
                                /*
                                <script type="text/javascript" src="https://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
                                */
                                ?>
                                <script language="javascript">
                                                    function toggleDisable(elementOnChecked, elementDisable, disableOnChecked) {
                                                        if (!disableOnChecked) {
                                                            if (elementOnChecked.checked == true) {
                                                                elementDisable.disabled = false;
                                                            }
                                                            else {
                                                                elementDisable.disabled = true;
                                                            }
                                                        }
                                                        else {
                                                            if (elementOnChecked.checked == true) {
                                                                elementDisable.disabled = true;
                                                            }
                                                            else {
                                                                elementDisable.disabled = false;
                                                            }
                                                        }
                                                    }

                                                    toggleDisable(document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image, true);

                                                    // ============================================================================
                                                    // BEGIN Treatment names of pictures

                                                    function get_image_name(name)
                                                    {
                                                        name = name.replace(/\\/g, '/');
                                                        name = name.substr(name.lastIndexOf('/') + 1);
                                                        return name.substr(0, name.lastIndexOf('.'));
                                                    }

                                                    function get_file_extension(name)
                                                    {
                                                        name = name.replace(/\\/g, '/');
                                                        name = name.substr(name.lastIndexOf('/') + 1);
                                                        return name.substr(name.lastIndexOf('.') + 1);
                                                    }

                                                    function upload_new_image(id_upload, id_isset_name, id_report)
                                                    {
                                                        var upload = document.getElementById(id_upload);
                                                        var elem_isset_name = document.getElementById(id_isset_name);
                                                        elem_isset_name.value = get_image_name(upload.value);
                                                        isset_name(id_isset_name, id_report, id_upload);
                                                    }

                                                    function isset_name(id_image, id_report, id_upload)
                                                    {
                                                        var file_extension = get_file_extension(document.getElementById(id_upload).value);
                                                        var elem = document.getElementById(id_image);
                                                        var elem_hidden = document.getElementById(id_image + '_hidden');
                                                        var report = document.getElementById(id_report);
                                                        var $j = jQuery.noConflict();
                                                        $j.post("/administrator/components/com_virtuemart/classes/phpInputFilter/image_name.php", {name: elem.value, extension: file_extension, folder: 'category'},
                                                        function(data) {
                                                            if (data == 'true') {
                                                                elem_hidden.value = (check_for_matching_names()) ? '' : elem.value;
                                                                report.innerHTML = (check_for_matching_names()) ? style_text('The file name is used.', false) : style_text('Permitted file name.', true);
                                                            }
                                                            else {
                                                                report.innerHTML = style_text(data, false);
                                                                elem_hidden.value = '';
                                                            }

                                                        });

                                                    }

                                                    function check_for_matching_names()
                                                    {
                                                        var val_1 = document.getElementById('category_full_image_new_name').value;
                                                        var val_2 = document.getElementById('category_full_image_fr_new_name').value;
                                                        var val_3 = document.getElementById('category_thumb_image_new_name').value;
                                                        var val_4 = document.getElementById('category_thumb_image_fr_new_name').value;
                                                        var full_data = new Array(val_1, val_2, val_3, val_4);

                                                        var count = full_data.length;
                                                        for (var i = 0; i < count; i++)
                                                        {
                                                            for (var i2 = 0; i2 < count; i2++)
                                                            {
                                                                if (i2 != i && full_data[i2].length > 0)
                                                                {
                                                                    if (full_data[i2] == full_data[i])
                                                                        return true;
                                                                }
                                                            }
                                                        }
                                                        return false;
                                                    }

                                                    function style_text(text, flag)
                                                    {
                                                        var color = (flag) ? '56BA15' : 'FF0000';
                                                        return '<span style="color:#' + color + ';">' + text + '</span>';
                                                    }

                                                    // END Treatment names of pictures
                                                    // ============================================================================
                                </script>
