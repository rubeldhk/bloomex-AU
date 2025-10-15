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
class HTML_Resource_Manager_Settings {
    public static function show($rows, $pageNav, $option, $search = '')
    {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <div style="margin: 10px 0;">
                <label for="search"><strong>Search Title:</strong></label>
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" />
                <input type="submit" value="Search" class="button" />
                <input type="button" value="Reset" class="button" onclick="document.getElementById('search').value=''; document.adminForm.submit();" />
            </div>
            
            <table class="adminheading">
                <tr>
                    <th>Resource Manager Settings</th>
                </tr>
            </table>
            <table class="adminlist" style="text-align: center">
                <tr>
                    <th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th>Queue</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Alias</th>
                    <th>Created Date</th>
                    <th>Author Name</th>
                </tr>
                <?php
                $k = 0;
                foreach ($rows as $i => $iValue) {
                    $row = $iValue;
                    mosMakeHtmlSafe($row);

                    $link = 'index2.php?option=com_resource_manager&act=edit&id=' . $row->id;
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $checked; ?></td>
                        <td><?= $row->queue ?></td>
                        <td><a href="<?php echo $link; ?>" title="Sale settings"><b><?php echo $row->name ?></b></a></td>
                        <td><?= $row->description ?></td>
                        <td><?= $row->type ?></td>
                        <td><?= $row->status == 1 ? 'Enabled' : 'Disabled' ?></td>
                        <td><?= $row->alias ?></td>
                        <td><?= $row->created_at ?></td>
                        <td><?= $row->author_name ?></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="new"/>
            <input type="hidden" name="task" value="show"/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }

    public static function edit($row, $option)
    {
        mosCommonHTML::loadBootstrap(true);
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>
                        Resource Manager Settings:
                        <small><?php echo $row->id ? 'Edit' : 'New'; ?></small>
                    </th>
                </tr>
            </table>

            <script src="/administrator/templates/joomla_admin/js/jquery-2.2.4.min.js"></script>
            <table class="table table-condensed">
                <tr class="active">
                    <th colspan="2" class="text-center">Settings</th>
                </tr>
                <tr>
                    <td><b><label for="name" class="form-label">Name *</label></b></td>
                    <td>
                        <input type="text" name="name" class="form-control" id="name" value="<?= $row->name ?>" required>
                    </td>
                </tr>
                <tr>
                    <td><b><label for="description" class="form-label">Description</label></b></td>
                    <td>
                        <textarea class="form-control" name="description" id="description" rows="3"><?= $row->description ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><b><label for="type" class="form-label">Script Type *</label></b></td>
                    <td>
                        <div class="type">
                            <select class="form-control" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="JS" <?= $row->type == "JS" ? "selected" : ""?> >JavaScript</option>
                                <option value="CDN" <?= $row->type == "CDN" ? "selected" : ""?>>CDN</option>
                                <option value="API" <?= $row->type == "API" ? "selected" : ""?>>API</option>
                                <option value="CSS" <?= $row->type == "CSS" ? "selected" : ""?>>CSS</option>
                                <option value="Custom" <?= $row->type == "Custom" ? "selected" : ""?>>Custom</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><b><label for="headerContent" class="form-label">Header Content</label></b></td>
                    <td>
                        <textarea class="form-control" id="headerContent" name="headerContent" rows="3"><?= $row->header_content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><b><label for="bodyContent" class="form-label">Body Content</label></b></td>
                    <td>
                        <textarea class="form-control" id="bodyContent" name="bodyContent" rows="3"><?= $row->body_content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><b><label for="footerContent" class="form-label">Footer Content</label></b></td>
                    <td>
                        <textarea class="form-control" id="footerContent" name="footerContent" rows="3"><?= $row->footer_content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b><label for="alias" class="form-label">Page Aliases </label></b>
                        <p>(Separated by a comma. If empty, the code will spread to all pages)</p>
                    </td>
                    <td>
                        <input class="form-control" id="alias" name="alias" type="text" value="<?= $row->alias ?>">
                    </td>
                </tr>
                <tr>
                    <td><b><label for="queue" class="form-label">Queue</label></b></td>
                    <td>
                        <input class="form-control" id="queue" name="queue" type="number" min="1" value="<?= $row->queue ?>">
                    </td>
                </tr>
                <tr>
                    <td><b><label class="form-label">Status *</label></b></td>
                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="enabled" value="1" <?= $row->status == "1" ? "checked" : ""?>>
                            <label class="form-check-label" for="enabled">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="disabled" value="0" <?= $row->status == "0" ? "checked" : ""?>>
                            <label class="form-check-label" for="disabled">Disabled</label>
                        </div>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="save"/>
            <?php if($row->id) { ?>
                <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
            <?php } ?>
            <input type="hidden" name="task" value="save"/>
        </form>
        <?php
    }
}

