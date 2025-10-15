<?php
class Slider{
    static $result = '';
    static $path = "/images/header_images/";
    static $names = array('Image', 'SRC', 'Lang', 'Public', 'Delete' );
    
    
    function &createTemplate() {
            global $option, $mosConfig_absolute_path;
            require_once( $mosConfig_absolute_path
                    . '/includes/patTemplate/patTemplate.php' );
            $tmpl = & patFactory::createTemplate($option, true, false);
            $tmpl->setRoot(dirname(__FILE__) . '/tmp');
            return $tmpl;
        }
    
        // -----------------------------------------------------------------------------------------------------------------------------
        function checkPost(){
            global $database, $mosConfig_absolute_path;
            if(isset($_POST['src'])){
                $filename = "slider_" . $_FILES["filename"]["name"];
                if(file_exists($mosConfig_absolute_path.self::$path.$filename) ){
                    self::$result = "File exists.";
                    return false;
                }
                if($_FILES["filename"]["size"] > 1024*3*1024){
                  self::$result = "The file size exceeds three megabytes.";
                  return false;
                }
                if(is_uploaded_file($_FILES["filename"]["tmp_name"])){
                  move_uploaded_file($_FILES["filename"]["tmp_name"], $mosConfig_absolute_path.self::$path.$filename);
                  self::$result = "Slide successfully loaded.";
                } else {
                  self::$result = "Error loading file.";
                  return false;
                }
                $public = ( isset($_POST['public']) && (int)$_POST['public'] > 0 ) ? 1 : 0;
                $query = "INSERT INTO jos_vm_slider (image,src,lang,public) VALUES ('$filename','{$_POST['src']}','{$_POST['lang']}','$public')";
                $database->setQuery($query);
                $database->query();
            }     
            if(isset($_POST['id'])){
                $query = "DELETE FROM jos_vm_slider where id='{$_POST['id']}'";
                $filename = $_POST['filename'];
                if(file_exists($mosConfig_absolute_path.self::$path.$filename) ){
                    unlink($mosConfig_absolute_path.self::$path.$filename);
                }
                $database->setQuery($query);
                $database->query();
                self::$result = "Removal was successful.";
            }
            if(isset($_POST['id_public'])){
                $query = "UPDATE jos_vm_slider SET public='{$_POST['value']}' where id='{$_POST['id_public']}'";                
                $database->setQuery($query);
                $database->query();
                self::$result = "Update was successful.";
            }
            return true;
        }
        
        // -----------------------------------------------------------------------------------------------------------------------------
        function view(){
            global $database, $mosConfig_absolute_path;
            $html = '<table class="adminlist"><tr>';
            foreach (self::$names as $value) {
                if( $value == 'Delete' ){
                    $html .= "<th align='left'>$value</th>";
                }
                else{
                    if( $value == 'SRC' ) $value = 'URL';
                    if( $value == 'Public' ) $value = 'Published';
                    $html .= "<th width='13%' align='left'>$value</th>";
                }
            }
            $query = "SELECT * FROM jos_vm_slider";
            $database->setQuery($query);
            $result = $database->loadObjectList();
            if( $result ){
                foreach ($result as $item) {
                    $html .= "</tr><tr>";
                    foreach (self::$names as $value) {
                        switch ($value) {
                            case 'Delete':
                                 $html .= "<td align='left'>
                                        <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                            <input type='submit' value='$value' />
                                            <input type='hidden' name='id' value='{$item->id}' >
                                            <input type='hidden' name='filename' value='{$item->image}' >
                                        </form>
                                        </td>";
                                break;
                            case 'Public':
                                $publicValue = $item->{strtolower($value)};
                                $html .= "<td align='left'>
                                        <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                            <input type='submit' value='".( ( $publicValue == '1' ) ? "Yes" : "No" )."' />
                                            <input type='hidden' name='id_public' value='{$item->id}' >
                                            <input type='hidden' name='value' value='".abs(1-$publicValue)."' >
                                        </form>
                                        </td>";
                                break;
                            default:
                                $html .= "<td width='13%' align='left'>{$item->{strtolower($value)}}</td>";
                                break;
                        }                        
                    }
                }
            }
            $html .= '</tr></table>';
            return $html;
        }

        // -----------------------------------------------------------------------------------------------------------------------------
        function create() {
             self::checkPost();
             $tmpl = & self::createTemplate();
             $tmpl->setAttribute('body', 'src', 'slider.html');
             $tmpl->addVar('body', 'action', $_SERVER['REQUEST_URI'] );
             $tmpl->addVar('body', 'result', self::$result );
             $tmpl->addVar('body', 'view', self::view() );
             $tmpl->displayParsedTemplate('form');
         }

}

Slider::create();
?>