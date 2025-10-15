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
defined('_VALID_MOS') or die('Restricted access');

/**
 * @package Joomla
 * @subpackage Category
 */
class HTML_LandingPages_Default {

    static function LandingPages_Default(&$row, $option, &$info, &$categories, $changes = array()) {


        ?>
        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Location Manager Default:
                    </th>
                </tr>
            </table>

            <div id="tabs">
                <ul>
                    <li><a href="#tabs-0">Changes history</a></li>
                    <?php
                    foreach ($info as $k=>$i) {
                        echo '<li><a href="#tabs-'.($k+1).'">'.$i['type'].'</a></li>';
                    }
                    ?>


                </ul>
                <p>you can use short text {city} and {province} and  {countdown:X} (X is countdown minute) and {readmore}</p>
                <div id="tabs-0">
                    <table class="adminlist" style="text-align: left;">
                        <tr>
                            <th>Type</th>
                            <th>Url</th>
                            <th>Changes</th>
                            <th>Username</th>
                            <th>Date</th>
                        </tr>
                        <?php
                        foreach ($changes as $change) {
                            $changes_a = unserialize($change->changes);
                            ?>
                            <tr>
                                <td>
                                    <?php echo $change->type; ?>
                                </td>
                                <td>
                                    <?php echo $change->landing_url; ?>
                                </td>
                                <td>
                                    <?php foreach ($changes_a as $key => $value) {
                                        ?>
                                        <span style="font-weight: bold;"><?php echo $key; ?> changed from</span><p><?php echo $value['old']; ?></p>
                                        <span style="font-weight: bold;">to</span> <p><?php echo $value['new']; ?></p>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $change->username; ?>
                                </td>
                                <td>
                                    <?php echo $change->datetime; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <?php
                foreach ($info as $k=>$i) {

                    $categoryOption = '';
                    $selected_cat = unserialize($i['category']);
                    foreach ($categories as $cat) {
                        if (is_array($selected_cat) && in_array($cat['category_id'], $selected_cat)) {
                            $categoryOption .= '<option selected="selected" value="' . $cat['category_id'] . '">' . $cat['category_name'] . '  ---------------  ' . $cat['product_count'] . '</option>';
                        } else {
                            $categoryOption .= '<option value="' . $cat['category_id'] . '">' . $cat['category_name'] . '  ---------------  ' . $cat['product_count'] . '</option>';
                        }
                    }

                    echo '<div id="tabs-'.($k+1).'">
                    <table class="adminlist" style="text-align: left;">
                        <tr>
                            <td><b>English Content :</b></td>
                            <td><textarea type="text" name="en_content[]" rows="5" cols="50">'.$i['en_content'].'</textarea></td>
                        </tr>
                        <tr>
                            <td><b>Pop Left English Content :</b></td>
                            <td><input type="text" name="en_left_pop[]" value="'.$i['en_left_pop'].'" size="30" /></td>
                        </tr>
                        <tr>
                            <td><b>Pop Center English Content :</b></td>
                            <td><input type="text" name="en_center_pop[]" value="'.$i['en_center_pop'].'" size="30" /></td>
                        </tr>
                        <tr>
                            <td><b>Pop Right English Content :</b></td>
                            <td><input type="text" name="en_right_pop[]" value="'.$i['en_right_pop'].'" size="30" />
                                <input type="checkbox" class="publish" name="publish['.$k.']"  value="1" '.(($i['right_pop_publish']) > 0 ? 'checked="checked"' : '').'/>
                                <label for="publish[]"><b> Publish</b></label>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Category :</b></td>
                            <td>
                                <select class="" name="category['.$k.'][]">
                                '.$categoryOption.'
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>';
                }
                ?>

            </div>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="task" value="" />
        </form>

        <script type="text/javascript" language="javascript">

            window.onload = function () {
                $(function () {
                    $("#tabs").tabs();
                });

            };
            


        </script>
        <style>
            .category{
                width: 100%;
                height: 150px;
            }
            
            .publish {
                transform: scale(1.3);
                margin: 0px 2px 0px 70px ;
            }
        </style>
        <?php
    }

}
?>
