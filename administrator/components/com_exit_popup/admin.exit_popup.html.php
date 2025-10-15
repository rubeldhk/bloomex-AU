<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

class ExitPagePopUp
{
    private static $table = 'jos_vm_exit_popup';
    private static $path = "/images/exit_popup/";
    protected static $result = '';
    protected static $options = 'com_exit_popup';

    private static function &createTemplate()
    {
        global $option, $mosConfig_absolute_path;
        require_once($mosConfig_absolute_path
            . '/includes/patTemplate/patTemplate.php');

        $tmpl = &patFactory::createTemplate($option, true, false);
        $tmpl->setRoot(dirname(__FILE__) . '/tmpl');
        return $tmpl;
    }

    public static function create()
    {
        $tmpl = self::createTemplate();
        $tmpl->setAttribute('body', 'src', 'exit_popup_manager.html');
        $tmpl->addVar('body', 'domainlist', self::show());
        $tmpl->addVar('body', 'action', "/administrator/index2.php?option=com_exit_popup&task=add");
        $tmpl->addVar('body', 'message', self::$result);
        $tmpl->displayParsedTemplate('form');
    }

    public static function add()
    {
        global $database, $mosConfig_absolute_path, $mosConfig_live_site;

        if (!isset($_POST)) {
            self::$result = "Error loading.";
        }

        if (isset($_POST['btn_title'])) {
            if ($_FILES["filename"]["size"] > 1024 * 3 * 1024) {
                self::$result = "The file size exceeds three megabytes.";
                return false;
            }

            $filename = time() . "_" . $_FILES["filename"]["name"];
            if (is_uploaded_file($_FILES["filename"]["tmp_name"])) {
                if (!is_dir($mosConfig_absolute_path . self::$path)) {
                    if (!mkdir($concurrentDirectory = $mosConfig_absolute_path . self::$path) && !is_dir($concurrentDirectory)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                    }
                }
                move_uploaded_file($_FILES["filename"]["tmp_name"], $mosConfig_absolute_path . self::$path . $filename);
                self::$result = "Successfully loaded.";
            } else {
                self::$result = "Error loading file.";
            }

            $public = (isset($_POST['is_active']) && (int)$_POST['is_active'] > 0) ? 1 : 0;

            $query = sprintf("
                    INSERT INTO `%s` (
                        `image`,
                        `product_id`,
                        `btn_title`,
                        `btn_color`,
                        `btn_style`,
                        `position_x`,
                        `position_y`,
                        `is_active`
                    ) VALUES (
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                    )
                ",
                self::$table,
                $filename,
                $database->getEscaped($_POST['product_id']),
                $database->getEscaped($_POST['btn_title']),
                $database->getEscaped($_POST['btn_color']),
                $database->getEscaped($_POST['btn_style']),
                $database->getEscaped($_POST['position_x']),
                $database->getEscaped($_POST['position_y']),
                $public
            );
            $database->setQuery($query);
            $database->query();
        }

        header("Location: " . $mosConfig_live_site . "/administrator/index2.php?option=" . self::$options);
        die();
    }

    private static function show(): string
    {
        global $database, $mosConfig_live_site_front;
        $html = '</form>
            <div style="text-align:left">
                <span class="admin-file-supported-info" >Notice: all characters except english letters, digits, "_" and "-" will be deleted from uploaded file\'s name, spaces will be replaced with "_"</span><br/>
            </div>
            <br>
            <table class="adminlist">
            <thead>
                <tr style="text-align:left">
                   <th>Btn Title</th>
                   <th>Btn Color</th>
                   <th>Btn Style</th>
                   <th>Btn PositionX</th>
                   <th>Btn PositionY</th>
                   <th>Product ID</th>
                   <th>Published</th>
                   <th>Image File</th>
                   <th>Upload New Image</th>
                   <th>Update</th>
                   <th>Delete</th>
                </tr>
            </thead>';
        $query = "SELECT * FROM " . self::$table . " ORDER BY id DESC";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        $url = '/administrator/index2.php?option=' . self::$options;
        foreach ($result as $res) {
            $img = "<img style='width: 150px' src='" . $mosConfig_live_site_front . self::$path . $res->image . "'>";
            $checked = $res->is_active ? 'checked' : null;
            $html .= '<tr class="row' . $res->id . '">
                     <form action="' . $url . '&task=update" method="post" enctype="multipart/form-data">
                     <td>
                       <input type="text" name="btn_title" value="' . $res->btn_title . '"> 
                    </td>
                    <td>
                        <select name="btn_color">
                            <option value="red" ' . ($res->btn_color == "red" ? "selected" : null) . '>Red</option>
                            <option value="green" ' . ($res->btn_color == "green" ? "selected" : null) . '>Green</option>
                            <option value="blue" ' . ($res->btn_color == "blue" ? "selected" : null) . '>Blue</option>
                            <option value="yellow" ' . ($res->btn_color == "yellow" ? "selected" : null) . '>Yellow</option>
                            <option value="orange" ' . ($res->btn_color == "orange" ? "selected" : null) . '>Orange</option>
                            <option value="purple" ' . ($res->btn_color == "purple" ? "selected" : null) . '>Purple</option>
                            <option value="pink" ' . ($res->btn_color == "pink" ? "selected" : null) . '>Pink</option>
                            <option value="brown" ' . ($res->btn_color == "brown" ? "selected" : null) . '>Brown</option>
                            <option value="gray" ' . ($res->btn_color == "gray" ? "selected" : null) . '>Gray</option>
                            <option value="black" ' . ($res->btn_color == "black" ? "selected" : null) . '>Black</option>
                            <option value="white" ' . ($res->btn_color == "white" ? "selected" : null) . '>White</option>
                            <option value="cyan" ' . ($res->btn_color == "cyan" ? "selected" : null) . '>Cyan</option>
                            <option value="magenta" ' . ($res->btn_color == "magenta" ? "selected" : null) . '>Magenta</option>
                        </select>
                    </td>
                    <td>
                        <textarea name="btn_style" type="number" rows="4" cols="30">' . $res->btn_style . '</textarea>
                    </td>
                    <td>
                        <input type="number" name="position_x" value="' . $res->position_x . '" min="10" style="width: 40px"/>
                    </td>
                    <td>
                        <input type="number" name="position_y" value="' . $res->position_y . '" min="10" style="width: 40px"/>
                    </td>
                    <td>
                        <input type="text" name="product_id" value="' . $res->product_id . '"/>
                    </td>
                    <td>
                        <input type="checkbox" name="is_active" value="1" ' . $checked . '/>
                    </td>
                    <td><span>' . $img . '</span></td>
                    <td>Select file <input type="file" name="filename"></td>
                    <td>
                        <button type="submit" style="color:green"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                        <input type="hidden" name="id" value="' . $res->id . '">
                    </form>
                    </td>
                    <td style="text-align: center">
                        <form action="' . $url . '&task=delete" method="post">
                            <button type="submit" style="color:red"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            <input type="hidden" name="id" value="' . $res->id . '">
                            <input type="hidden" name="filename" value="' . $res->image . '">
                        </form>
                    </td>
                </tr>';
        }

        $html .= '</table>';

        return $html;
    }

    public static function update($id): bool
    {
        global $database, $mosConfig_absolute_path, $mosConfig_live_site;

        if (isset($_FILES['filename'])) {
            if ($_FILES["filename"]["size"] > 0) {
                $query = sprintf("SELECT image FROM %s WHERE `id`='$id' LIMIT 1", self::$table, $id);
                $database->setQuery($query);
                $result = false;
                $database->loadObject($result);
                if ($result) {
                    if (file_exists($mosConfig_absolute_path . self::$path . $result->image)) {
                        unlink($mosConfig_absolute_path . self::$path . $result->image);
                    }
                }

                $filename = time() . "_" . $_FILES["filename"]["name"];
                if (file_exists($mosConfig_absolute_path . self::$path . $filename)) {
                    self::$result = "The file already exists.";
                    return false;
                }

                if ($_FILES["filename"]["size"] > 1024 * 3 * 1024) {
                    self::$result = "The file size exceeds three megabytes.";
                }

                if (is_uploaded_file($_FILES["filename"]["tmp_name"])) {
                    if (!is_dir($mosConfig_absolute_path . self::$path)) {
                        if (!mkdir($concurrentDirectory = $mosConfig_absolute_path . self::$path) && !is_dir($concurrentDirectory)) {
                            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                        }
                    }
                    move_uploaded_file($_FILES["filename"]["tmp_name"], $mosConfig_absolute_path . self::$path . $filename);
                }

                $query = sprintf("UPDATE %s SET `image`='%s' WHERE `id`='$id' LIMIT 1", self::$table, $filename);
                $database->setQuery($query);
                $database->query();
            }
            $public = ( isset($_POST['is_active']) && (int)$_POST['is_active'] > 0 ) ? 1 : 0;

            $query = sprintf("UPDATE %s SET 
                `product_id`='%s', 
                `btn_title`='%s', 
                `is_active`='%s', 
                `btn_color` = '%s',
                `btn_style` = '%s',
                `position_x` = '%s',
                `position_y` = '%s'
            WHERE `id`='%s'",
                self::$table,
                $database->getEscaped($_POST['product_id']),
                $database->getEscaped($_POST['btn_title']),
                $public,
                $database->getEscaped($_POST['btn_color']),
                $database->getEscaped($_POST['btn_style']),
                $database->getEscaped($_POST['position_x']),
                $database->getEscaped($_POST['position_y']),
                $id
            );
            $database->setQuery($query);
            $database->query();

        }

        header("Location: " . $mosConfig_live_site . "/administrator/index2.php?option=" . self::$options);
        die();
    }

    public static function delete()
    {
        global $database, $mosConfig_absolute_path, $mosConfig_live_site;

        $query = sprintf("DELETE FROM %s where id='%s'", self::$table, $_POST['id']);
        $filename = $_POST['filename'];
        if (file_exists($mosConfig_absolute_path . self::$path . $filename)) {
            unlink($mosConfig_absolute_path . self::$path . $filename);
        }
        $database->setQuery($query);
        $database->query();

        self::$result = "Removal was successful.";
        header("Location: " . $mosConfig_live_site . "/administrator/index2.php?option=" . self::$options);
        die();
    }
}