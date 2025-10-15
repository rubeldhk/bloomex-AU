<?php

/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

/**
 * @package HelloWorld
 */
class EditBanner {

    private static $table = 'jos_vm_edit_banner_href';

    public function __construct() {
        // NULL
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function show() {
        global $database;
        $error_message = EditBanner::result();
        $html = '<!--ERROR FORM--> </form>
            <div style="text-align:left">
            <span class="admin-file-supported-info" >Notice: all characters except english letters, digits, "_" and "-" will be deleted from uploaded file\'s name, spaces will be replaced with "_"</span><br/>
            <span class="admin-file-supported-info" >Used image sizes are 400x150px for Top Banner (Not strict but keep aspect ratio as close as possible)</span>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
            <table class="adminlist">
                <thead>
                <tr style="text-align:left">
                   <th>Banner for updates</th>
                   <th>Title</th>
                   <th>Href</th>
                   <th>Image File</th>
                   <th>Upload New Image</th>
                </tr>
                </thead>';

        $line = 0;
        $query = "SELECT * FROM " . EditBanner::$table . " ORDER BY id ASC";
        $database->setQuery($query);
        $result = $database->loadObjectList();

        foreach ($result as $res) {
$img = "<img style='width: 300px' src='/images/banners/".$res->image."'>";
            $html .= '<tr class="row' . $line . '">
                    <td>
                        ' . $res->title . '
                    </td>
                     <td>
                       <input type="text" name="title_' . $res->id . '" value="' . $res->title . '"> 
                    </td>
                     <td>
                       <input type="text" name="href_' . $res->id . '" value="' . $res->href . '">
                    </td>
                      <td>
                      <span>'.$img.'</span>
                    </td>
                    <td>
                        Select file <input type="file" name="filename_' . $res->id . '">
                    </td>
                   
                </tr>';
        }

        $html .= '</table>
               <span style="align:left"><input type="submit" name="upload" value="Update"></span>
               </form>
           <!--ERROR FORM-->  ' . $error_message . '<form>';

        return $html;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function result() {
        global $mosConfig_absolute_path, $database;
        $query = "SELECT * FROM " . EditBanner::$table . " ORDER BY id ASC";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        $html = '<div class="admin-log-screen" style="text-align:left">';
        if (isset($_POST['upload'])) {
            EditBanner::isset_database_line();
            foreach ($result as $res) {
                $image_name = '';
                if ($_FILES["filename_".$res->id]["tmp_name"] != '') {
                        $image_name = preg_replace('/[() ]/', '_', $_FILES["filename_".$res->id]['name']);
                        $image_name = preg_replace('/[^A-Za-z0-9_\.\-]+/', '', $image_name);
                        $image_name = str_replace("_", " ", $image_name);
                        $image_name = preg_replace('/\s+/', ' ', $image_name); //remove double whitespaces
                        $image_name = strtolower($image_name);
                        $image_name = ucfirst($image_name);
                        $image_name = str_replace(" ", "_", $image_name);
                    if ($_FILES["filename_".$res->id]["size"] > 1024 * 150) {
                        $html .= "<span style='color=#0000FF;'>Error uploading: $image_name (File size is more than 150 kilobytes)</span> ||";
                        return $html;
                    } elseif($_FILES["filename_".$res->id]['type']!='image/jpeg') {
                        $html .= "<span style='color=#0000FF;'>Error uploading: $image_name (File type must be jpeg)</span> ||";
                        return $html;
                    } else {
                        if (is_uploaded_file($_FILES["filename_".$res->id]["tmp_name"])) {
                            $dir = $mosConfig_absolute_path . "/images/banners/";
                            if (!is_dir($dir))
                                mkdir($dir);
                            move_uploaded_file($_FILES["filename_".$res->id]["tmp_name"], $dir . $image_name);
                            $html .= "Success:  $image_name has been uploaded ||";
                        }
                        else {
                            $html .= "<span style='color=#0000FF;'>Error uploading: $image_name (error while moving uploaded file)</span> ||";
                            return $html;
                        }
                    }
                }
                $query = "UPDATE `" . EditBanner::$table . "` SET  title='" . $_POST['title_' . $res->id] . "',   href='" . $_POST['href_' . $res->id] . "' ";
                $query.=($image_name) ? " ,  image='" . $image_name . "' " : '';
                $query.= "    WHERE id='" . $res->id . "'";
                $database->setQuery($query);
                if (!$database->query()) {
                    echo "<pre>";
                    var_dump($database);
                    echo "</pre>";
                }
            }
        }
        $html.="</div>";
        return $html;
    }

    function isset_database_line() {
        global $database;
        $langs = array(0 => 'en', 1 => 'fr');
        $l = $langs[1];
        $query = "SELECT * FROM " . EditBanner::$table . "";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if (!$result) {
            for ($i = 0; $i < 2; $i++) {
                $l = ( $l == $langs[0] ) ? $langs[1] : $langs[0];
                $query = "INSERT INTO " . EditBanner::$table . " ( id, href) VALUES ('" . ($i + 1) . "', '' )";
                $database->setQuery($query);
                $database->query();
            }
        }
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function &createTemplate() {
        global $option, $mosConfig_absolute_path;
        require_once( $mosConfig_absolute_path
                . '/includes/patTemplate/patTemplate.php' );

        $tmpl = & patFactory::createTemplate($option, true, false);
        $tmpl->setRoot(dirname(__FILE__) . '/tmpl');
        return $tmpl;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function create($message = '') {
        $tmpl = & EditBanner::createTemplate();
        $tmpl->setAttribute('body', 'src', 'edit_banner_users_manager.html');
        $tmpl->addVar('body', 'domainlist', EditBanner::show());
        $tmpl->displayParsedTemplate('form');
    }

}

?>