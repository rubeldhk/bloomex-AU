<?php

/**
 * @version $Id: admin.Category.php 10002 2008-02-08 10:56:57Z willebil $
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

include_once $_SERVER['DOCUMENT_ROOT'] . '/core/aws/S3ClientAdapter.php';

require_once($mainframe->getPath('admin_html'));


$act = mosGetParam($_REQUEST, "act", "");
$id = mosGetParam($_REQUEST, "id", "");
$limitstart = mosGetParam($_REQUEST, "limitstart", null);
$limit = mosGetParam($_REQUEST, "limit", null);
$task = mosGetParam($_REQUEST, "task", null);
$step = 0;

switch ($act) {
    case 'new':
        if ($task == 'remove') {
            remove($option);
        } else if ($task == 'show') {
            show($option);
        } else {
            edit('0', $option);
        }
        break;

    case 'edit':
        edit($id, $option);
        break;

    case 'save':
        save($option);
        break;

    default:
        show($option);
        break;
}

function show($option)
{
    global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mainframe, $mosConfig_list_limit;

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $mysqli->set_charset('utf8');

    if ($mysqli->connect_errno) {
        die("Mysql connection error: " . $mysqli->connect_error);
    }

    $limit = (int)$mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = (int)$mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    $search = trim(mosGetParam($_REQUEST, 'search', ''));

    $where = "";
    if (!empty($search)) {
        $safeSearch = $mysqli->real_escape_string($search);
        $where = "WHERE bp.slug LIKE '%" . $safeSearch . "%'";
    }

    $countQuery = "SELECT COUNT(DISTINCT bp.id) AS total
                    FROM jos_vm_blog_posts AS bp
                   $where";
    $countResult = $mysqli->query($countQuery);
    $row = $countResult->fetch_assoc();
    $total = (int)$row['total'];
    $countResult->free();

    require_once($GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    $query = "SELECT 
            bp.id,
            bt.slug,
            bt.title,
            bt.description,
            bt.short_description,
            bp.is_published,
            u.username
        FROM 
            jos_vm_blog_posts AS bp
        LEFT JOIN jos_vm_blog_post_data AS bt ON bt.post_id = bp.id 
        LEFT JOIN jos_users u ON u.id = bp.creator_id
        $where
        ORDER BY 
            bp.id DESC
        LIMIT {$limitstart}, {$limit}";

    $result = $mysqli->query($query);
    if (!$result) {
        die("SyntaxError: " . $mysqli->error);
    }

    $rows = [];
    while ($obj = $result->fetch_object()) {
        $rows[] = $obj;
    }

    $result->free();
    $mysqli->close();

    HTML_Blog_Post_Settings::show($rows, $pageNav, $option, $search);
}

function edit($id, $option)
{
    global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db;

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $mysqli->set_charset('utf8');
    $id = (int)$id;

    if ($mysqli->connect_errno) {
        die("Mysql connection error: " . $mysqli->connect_error);
    }

    $query = "SELECT 
            bp.id,
            bt.slug,
            bt.title,
            bt.description,
            bt.short_description,
            bp.is_published,
            bi.image_link,
            bi.thumb_link,
            u.username
        FROM 
            jos_vm_blog_posts AS bp
        LEFT JOIN jos_vm_blog_post_data AS bt ON bt.post_id = bp.id 
        LEFT JOIN jos_vm_blog_post_images AS bi ON bi.blog_post_data_id = bt.id 
        LEFT JOIN jos_users u ON u.id = bp.creator_id
        WHERE bp.id = $id";
    $result = $mysqli->query($query);
    if (!$result) {
        die("SyntaxError jos_vm_product: " . $mysqli->error);
    }

    $row = $result->fetch_object();
    $mysqli->close();

    HTML_Blog_Post_Settings::edit($row, $option);
}

function save($option)
{
    global $database, $my;

    $title = trim(mosGetParam($_POST, "title"));
    $slug = trim(mosGetParam($_POST, "slug"));
    $shortDescription = $_POST["short_description"];
    $description = $_POST["description"];

    $id = (int)mosGetParam($_POST, "id", 0);
    $isPublished = mosGetParam($_POST, "is_published", '');
    $isPublished = $isPublished == 'on';
    $creatorId = (int)$my->id;

    if ($title === '') {
        echo "<script>alert('Title field is required'); window.history.go(-1);</script>\n";
        exit();
    } elseif ($slug === '') {
        echo "<script>alert('Slug field is required.'); window.history.go(-1);</script>\n";
        exit();
    } elseif (strlen($title) >= 120) {
        echo "<script>alert('The title field max 120.'); window.history.go(-1);</script>\n";
        exit();
    } elseif (strlen($shortDescription) >= 255) {
        echo "<script>alert('The short description field max 255.'); window.history.go(-1);</script>\n";
        exit();
    } elseif (strlen($title) < 3) {
        echo "<script>alert('The title  must be more than 3'); window.history.go(-1);</script>\n";
        exit();
    }


    $s3 = new S3ClientAdapter();


    $query = "SELECT id FROM jos_vm_blog_posts WHERE slug = $slug";
    $database->setQuery($query);
    $blogsExists = (int)$database->loadResult();
    if ($blogsExists) {
        echo "<script>alert('This blog already exists, the name and slug must be unique.'); window.history.go(-1);</script>\n";
        exit();
    }

    if ($id > 0) {
        $now = date('Y-m-d H:i:s');
        $query = "UPDATE jos_vm_blog_posts 
                  SET is_published = $isPublished, 
                      published_at = " . ($isPublished ? "'$now'" : "NULL") . ", 
                      updated_at = NOW() 
                  WHERE id = $id";
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script>alert('Failed to update blog_posts: " . $database->getErrorMsg() . "'); window.history.go(-1);</script>\n";
            exit();
        }

        $query = "SELECT id FROM jos_vm_blog_post_data WHERE post_id = $id ";
        $database->setQuery($query);
        $dataId = (int)$database->loadResult();

        if ($dataId) {
            $query = "UPDATE jos_vm_blog_post_data SET 
                        title = " . $database->Quote($title) . ",
                        short_description = '" . $shortDescription . "',
                        description = " . $database->Quote($description) . ",
                        slug = " . $database->Quote($slug) . "
                      WHERE id = $dataId";
        } else {
            $query = "INSERT INTO jos_vm_blog_post_data 
                      (title, short_description, description, post_id, slug)
                      VALUES (" . $database->Quote($title) . ", '" . $shortDescription . "', " . $database->Quote($description) . ",  $id, " . $database->Quote($slug) . ")";
        }
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script>alert('Failed to update/insert English data: " . $database->getErrorMsg() . "'); window.history.go(-1);</script>";
            exit();
        }
        if (!$dataId) $dataId = $database->insertid();

        $images = [
            'en' => ['image' => 'image_link', 'thumb' => 'thumb_link', 'data_id' => $dataId]
        ];

        foreach ($images as $lang => $data) {
            $imageLink = '';
            $thumbLink = '';

            $query = "SELECT id,image_link,thumb_link FROM jos_vm_blog_post_images WHERE blog_post_data_id = " . $data['data_id'];
            $database->setQuery($query);
            $imageId = false;
            $database->loadObject($imageId);

            if (!empty($_FILES[$data['image']]['tmp_name'])) {

                if(isset($imageId->image_link) && $imageId->image_link)
                    $s3->delete($imageId->image_link);

                $key = time() . '_' . basename($_FILES[$data['image']]['name']);
                $imageLink = $s3->upload($_FILES[$data['image']]['tmp_name'], $key,'blog');
            }
            if (!empty($_FILES[$data['thumb']]['tmp_name'])) {

                if(isset($imageId->thumb_link) && $imageId->thumb_link)
                    $s3->delete($imageId->thumb_link);

                $key = time() . '_' . basename($_FILES[$data['thumb']]['name']);
                $thumbLink = $s3->upload($_FILES[$data['thumb']]['tmp_name'], $key,'blog');
            }

            if ($imageLink || $thumbLink) {

                if ($imageId) {
                    $query = "UPDATE jos_vm_blog_post_images SET 
                                image_link = " . $database->Quote($imageLink) . ", 
                                thumb_link = " . $database->Quote($thumbLink) . "
                              WHERE blog_post_data_id = " . $data['data_id'];
                } else {
                    $query = "INSERT INTO jos_vm_blog_post_images 
                              (image_link, thumb_link, blog_post_data_id, post_id)
                              VALUES (" . $database->Quote($imageLink) . ", " . $database->Quote($thumbLink) . ", " . $data['data_id'] . ", $id)";
                }

                $database->setQuery($query);
                if (!$database->query()) {
                    echo "<script>alert('Failed to update/insert {$lang} images: " . $database->getErrorMsg() . "'); window.history.go(-1);</script>";
                    exit();
                }
            }
        }

    } else {
        $query = "SELECT COUNT(*) FROM jos_vm_blog_post_data WHERE slug = " . $database->Quote($slug);
        $database->setQuery($query);
        if ($database->loadResult() > 0) {
            mosRedirect("index2.php?option=$option", "Slug  $slug already exists.");
            exit();
        }


        $now = date('Y-m-d H:i:s');
        $query = "INSERT INTO jos_vm_blog_posts (is_published, published_at, creator_id, created_at, updated_at)
              VALUES ($isPublished, " . ($isPublished ? "'$now'" : "NULL") . ", $creatorId, NOW(), NOW())";
        $database->setQuery($query);

        if (!$database->query()) {
            echo "<script>alert('Failed to insert into blog_posts: " . $database->getErrorMsg() . "'); window.history.go(-1);</script>\n";
            exit();
        }

        $postId = $database->insertid();

        $query = "INSERT INTO jos_vm_blog_post_data (
                   title, 
                   short_description,
                   description, 
                   post_id,
                   slug
               ) VALUES (
                    " . $database->Quote($title) . ", 
                    '" . $shortDescription . "', 
                    " . $database->Quote($description) . ",
                    $postId, " .
            $database->Quote($slug) .
            ")";
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script>alert('Failed to insert post data: " . $database->getErrorMsg() . "'); window.history.go(-1);</script>\n";
            exit();
        }
        $dataId = $database->insertid();

        $imageLink = '';
        $thumbLink = '';
        if (!empty($_FILES['image_link']['tmp_name'])) {
            $key = time() . '_' . basename($_FILES['image_link']['name']);
            $imageLink = $s3->upload($_FILES['image_link']['tmp_name'], $key,'blog');
        }
        if (!empty($_FILES['thumb_link']['tmp_name'])) {
            $key = time() . '_' . basename($_FILES['thumb_link']['name']);
            $thumbLink = $s3->upload($_FILES['thumb_link']['tmp_name'], $key,'blog');
        }

        $query = "INSERT INTO jos_vm_blog_post_images (image_link, thumb_link, blog_post_data_id, post_id)
              VALUES (" . $database->Quote($imageLink) . ", " . $database->Quote($thumbLink) . ", $dataId, " . $postId . ")";
        $database->setQuery($query);
        if (!$database->query()) {
            echo "<script>alert('Failed to insert images: " . $database->getErrorMsg() . "'); window.history.go(-1);</script>\n";
            exit();
        }

    }
    mosRedirect("index2.php?option=$option", "Blog post saved successfully.");
}

function remove($option)
{
    global $database;
    $s3 = new S3ClientAdapter();
    $cid = $_POST['cid'];
    if (count($cid)) {
        foreach ($cid as $value) {

            $query = "SELECT id,image_link,thumb_link FROM jos_vm_blog_post_images WHERE blog_post_data_id = " . $value;
            $database->setQuery($query);
            $imageId = false;
            $database->loadObject($imageId);


            $query = "DELETE FROM jos_vm_blog_post_images WHERE post_id = $value";
            $database->setQuery($query);
            if (!$database->query()) {
                echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            }


            if(isset($imageId->image_link) && $imageId->image_link)
                $s3->delete($imageId->image_link);

            if(isset($imageId->thumb_link) && $imageId->thumb_link)
                $s3->delete($imageId->thumb_link);

            $query = "DELETE FROM jos_vm_blog_posts WHERE id = $value";
            $database->setQuery($query);
            if (!$database->query()) {
                echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            }
        }
    }

    mosRedirect("index2.php?option=$option&act=show", "Remove Blog Post(s) Successfully");
}
