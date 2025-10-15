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
class HTML_Blog_Post_Settings {
    public static function show($rows, $pageNav, $option, $search = '')
    {
        mosCommonHTML::loadOverlib();
        ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>Blog Post Settings</th>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th width="5%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th>#</th>
                    <th align="left">Title</th>
                    <th align="left">Short Description</th>
                    <th align="left">Slug</th>
                    <th align="left">Status</th>
                    <th align="left">Creator</th>
                </tr>
                <?php
                $k = 0;
                foreach ($rows as $i => $iValue) {
                    $row = $iValue;
                    mosMakeHtmlSafe($row);

                    $link = 'index2.php?option=com_blog_post&act=edit&id=' . $row->id;
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $checked; ?></td>
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><a href="<?php echo $link; ?>" title="settings"><b><?php echo html_entity_decode($row->title); ?></b></a></td>
                        <td><a href="<?php echo $link; ?>" title="settings"><b><?php echo html_entity_decode($row->short_description); ?></b></a></td>
                        <td>
                            <?= $row->slug ?>
                        </td>
                        <td><?= $row->is_published == 1 ? 'Yes' : 'No' ?></td>
                        <td><?= $row->username ?></td>
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
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Blog Post Settings:
                        <small><?= $row->id ? 'Edit' : 'New' ?></small>
                    </th>
                </tr>
            </table>

            <!-- Scripts and Styles -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
            <script src="/administrator/templates/joomla_admin/js/jquery-2.2.4.min.js"></script>
            <script src="/ckeditor/ckeditor.js"></script>
            <script>
                function generateSlug(text) {
                    return text
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/--+/g, '-');
                }

                document.addEventListener('DOMContentLoaded', function () {
                    const title = document.querySelector('[name="title"]');
                    const slug = document.querySelector('[name="slug"]');

                    if (title && slug) {
                        title.addEventListener('input', function () {
                            slug.value = generateSlug(title.value);
                        });
                    }

                    slug && slug.addEventListener('input', () => {
                        slug.value = generateSlug(slug.value);
                    });

                });
            </script>

            <table class="table table-condensed">
                <tr class="active">
                    <th class="text-center">Settings</th>
                </tr>

                <tr>
                    <td >

                        <div class="tab-content" style="margin-top: 15px;">
                            <div class="tab-pane active">
                                <div class="form-group">
                                    <label><b>Title</b></label>
                                    <input type="text" class="form-control input-width" name="title" value="<?= html_entity_decode($row->title, ENT_QUOTES, 'UTF-8') ?>" required/>
                                </div>
                                <div class="form-group">
                                    <label><b>Alias</b></label>
                                    <input type="text" class="form-control input-width" name="slug" value="<?= $row->slug ?>" required/>
                                </div>
                                <div class="form-group" style="width: 700px">
                                    <label><b>Short Description</b></label>
                                    <textarea class="text_area form-control input-width" name="short_description"><?= html_entity_decode($row->short_description, ENT_QUOTES) ?></textarea>
                                </div>
                                <div class="form-group" style="width: 700px">
                                    <label><b>Description</b></label>
                                    <textarea id="description" rows="4" cols="50" class="text_area form-control form-control-textarea" name="description" required><?= html_entity_decode($row->description, ENT_QUOTES) ?></textarea>
                                </div>
                                <script>CKEDITOR.replace("description");</script>
                                <div class="form-group">
                                    <label><b>Image Original</b></label>
                                    <input type="file" class="form-control image-width" name="image_link"/>
                                </div>
                                <div class="form-group">
                                    <label><b>Image Thumb</b></label>
                                    <input type="file" class="form-control image-width" name="thumb_link"/>
                                </div>
                                <?php if ($row->image_link) { ?>
                                    <label><b>Image Original</b></label>
                                    <img src="<?= html_entity_decode($row->image_link) ?>" alt="blog-image-origin" style="margin:5px;width:350px">
                                <?php } if ($row->thumb_link) { ?>
                                    <label><b>Image Thumb</b></label>
                                    <img src="<?= html_entity_decode($row->thumb_link) ?>" alt="blog-image-thumb" style="margin:5px;width:300px">
                                <?php } ?>
                            </div>

                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <b>Status</b><input type="checkbox" class="form-control" style="width: 50px !important;" name="is_published" <?= $row->is_published == 1 ? 'checked' : '' ?>/>
                    </td>
                </tr>
            </table>

            <!-- Hidden fields -->
            <input type="hidden" name="option" value="<?= $option ?>"/>
            <input type="hidden" name="act" value="save"/>
            <?php if ($row->id) { ?>
                <input type="hidden" name="id" value="<?= $row->id ?>"/>
            <?php } ?>
            <input type="hidden" name="task" value="save"/>
        </form>

        <!-- Styling -->
        <style>
            .table tr td:first-child, a[data-toggle="tab"] {
                font-size: 18px;
            }

            #languageTabs > li > a {
                padding: 6px 20px;
                font-size: 16px;
                font-weight: 500;
            }

            #languageTabs > li.active > a {
                background-color: #5bc0de;
                color: white;
                border-radius: 4px 4px 0 0;
            }

            .form-control-textarea {
                width: 700px !important;
                padding: 17px;
                font-size: 17px;
            }

            .input-width {
                width: 700px !important;
                padding: 17px;
                font-size: 17px;
            }
            
            .image-width{
                width: 700px !important;
                height: 48px;
                padding: 13px;
            }
        </style>
        <?php
    }

}

