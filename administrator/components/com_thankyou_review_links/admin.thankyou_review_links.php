<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<?php

class Thankyou_review_links {

    static $result = '';
    static $item = '';
    static $default_type = 'google';
    static $path = "/images/thankyou_images/";
    static $names = array('Name','Image', 'URL','Percent', 'Published', 'Delete');

    function &createTemplate() {
        global $option, $mosConfig_absolute_path;
        require_once( $mosConfig_absolute_path
                . '/includes/patTemplate/patTemplate.php' );
        $tmpl = & patFactory::createTemplate($option, true, false);
        $tmpl->setRoot(dirname(__FILE__) . '/tmp');
        return $tmpl;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function checkPost() {
        global $database, $mosConfig_absolute_path;
        if (isset($_POST['url'])) {
            if ($_FILES["filename"]["size"] > 1024 * 3 * 1024) {
                self::$result = "The file size exceeds three megabytes.";
                return false;
            }

            if($_REQUEST['item_id'] && $_REQUEST['item_id']!='' && $_REQUEST['update']){
                $published = ( isset($_POST['published']) && (int) $_POST['published'] > 0 ) ? 1 : 0;
                $query = "UPDATE tbl_thankyou_review_links SET `name`='{$_POST['name']}',url='{$_POST['url']}',percent='{$_POST['percent']}',published='$published' WHERE id=".$_REQUEST['item_id'];
                $database->setQuery($query);
                $database->query();
                self::$result = "Item Updated successfully.";
            }else{
                $filename =  $_FILES["filename"]["name"];
                if (is_uploaded_file($_FILES["filename"]["tmp_name"])) {
                    move_uploaded_file($_FILES["filename"]["tmp_name"], $mosConfig_absolute_path . self::$path . $filename);
                    self::$result = "Image successfully loaded.";
                } else {
                    self::$result = "Error loading file.";
                }

                $published = ( isset($_POST['published']) && (int) $_POST['published'] > 0 ) ? 1 : 0;
                $query = "INSERT INTO tbl_thankyou_review_links (name,image,url,percent,published,type) VALUES ('{$_POST['name']}','$filename','{$_POST['url']}','{$_POST['percent']}','$published','{$_POST['type']}')";
                $database->setQuery($query);
                $database->query();
            }
        }
        if (isset($_POST['id'])) {
            $query = "DELETE FROM tbl_thankyou_review_links where id='{$_POST['id']}'";
            $filename = $_POST['filename'];
            $temp = explode('/', $filename);
            $dir = $mosConfig_absolute_path . self::$path . $temp[0];
            $file = $mosConfig_absolute_path . self::$path . $filename;


            if (file_exists($file)) {
                if (is_file($file))
                    unlink($file);
            }
            rmdir($dir);
            $database->setQuery($query);
            $database->query();
            self::$result = "Removal was successful.";
        }
        if (isset($_POST['id_public'])) {
            $query = "UPDATE tbl_thankyou_review_links SET published='{$_POST['value']}' where id='{$_POST['id_public']}'";
            if ($_POST['value'] == 0) {
                $query = "UPDATE tbl_thankyou_review_links SET published='{$_POST['value']}' where id='{$_POST['id_public']}'";
            }
            $database->setQuery($query);
            $database->query();
            self::$result = "Update was successful.";
        }

        return true;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function view() {
        global $database, $mosConfig_absolute_path,$option;
        $type=($_REQUEST['type'])?$_REQUEST['type']:self::$default_type;
        $html = '
                <style type="text/css">
		span {cursor:pointer; }
		.number{
			margin:100px 30%;
		}
		.val_minus, .val_plus{
			width:10px;
			height:10px;
			background:#f2f2f2;
			border-radius:4px;
			padding:3px 5px 3px 5px;
			border:1px solid #ddd;
		}
		input{
			height:24px;
			border:1px solid #ddd;
			border-radius:4px;
			padding:0 2px;
		}
	</style>
 
                <table class="adminlist"><tr>';
        foreach (self::$names as $value) {
            if ($value == 'Image') {
                $html .= "<th width='25%'>$value</th>";
            }
            elseif($value == 'Delete') {
                $html .= "<th align='left'>$value</th>";
            } else {
                $html .= "<th width='15%' align='left'>$value</th>";
            }
        }
        $query = "SELECT * FROM tbl_thankyou_review_links where `type` = '$type' ORDER BY published DESC ";
        $database->setQuery($query);
        $result = $database->loadObjectList();

        if ($result) {

            foreach ($result as $item) {
                if($_REQUEST['item_id'] && $_REQUEST['item_id']!='' && $_REQUEST['item_id']==$item->id){
                    self::$item=$item;
                }
                $html .= "</tr><tr>";
                foreach (self::$names as $value) {
                    switch ($value) {
                        case 'Delete':
                            $html .= "<td align='center'>
                                        <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                            <input type='submit' value='$value' />
                                            <input type='hidden' name='id' value='{$item->id}' >
                                            <input type='hidden' name='filename' value='{$item->image}' >
                                        </form>
                                        </td>";
                            break;
                        case 'Published':
                            $publicValue = $item->{strtolower($value)};
                            $html .= "<td align='center'>
                                            <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                                <input type='checkbox' onClick='this.form.submit()' " . ( ( $publicValue == '1' ) ? "checked='checked' value=1 " : "" ) . "/>
                                                <input type='hidden' name='id_public' value='{$item->id}' >
                                                <input type='hidden' name='value' value='" . abs(1 - $publicValue) . "' >
                                            </form>
                                        </td>";
                            break;
                        case 'Image':
                            $html.= "<td align='center'>
                                            <img src='"  . self::$path. $item->image . "' style='width:80px'>
                                         </td>";
                            break;

                        case 'Name':
                            $html.= "<td align='center'>
                                            <a href='index2.php?option=". $option . "&type=". $item->type . "&item_id=". $item->id . "' style='width:80px'>". $item->name . "</a>
                                         </td>";
                            break;
                        default:
                            $html .= "<td width='13%' align='center'>{$item->{strtolower($value)}}</td>";
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
        $tmpl->setAttribute('body', 'src', 'thankyou_review_links.html');
        $tmpl->addVar('body', 'action', $_SERVER['REQUEST_URI']);
        $tmpl->addVar('body', 'result', self::$result);
        $tmpl->addVar('body', 'view', self::view());
        $tmpl->addVar('body', 'type', ($_REQUEST['type']?$_REQUEST['type']:self::$default_type));
        $tmpl->addVar('body', 'google', ($_REQUEST['type']=='google'?'selected':''));
        $tmpl->addVar('body', 'company', ($_REQUEST['type']=='company'?'selected':''));
        $item_details = self::$item;
        if($item_details){
            $tmpl->addVar('body', 'item_id', $item_details->id?'<input type="hidden" name="item_id" value="'.$item_details->id.'">':'');
            $tmpl->addVar('body', 'item_url', $item_details->url?$item_details->url:'');
            $tmpl->addVar('body', 'item_percent', $item_details->percent?$item_details->percent:0);
            $tmpl->addVar('body', 'item_name', $item_details->name?$item_details->name:'');
            $tmpl->addVar('body', 'item_published', $item_details->published?' value="1" checked':' value="1"');
            $tmpl->addVar('body', 'item_action', 'update');
        }else{
            $tmpl->addVar('body', 'item_action', 'insert');
            $tmpl->addVar('body', 'item_published', 'value="1"');
        }
        $tmpl->displayParsedTemplate('form');
    }

}

Thankyou_review_links::create();
?>