<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<?php

class Slider
{

    static $result = '';
    static $path = "/images/header_images/";
    static $names = array('Image', 'Alt', 'Queue', 'Thumb', 'SRC', 'Type', 'Start Date', 'End Date', 'Public', 'Delete',);

    function &createTemplate()
    {
        global $option, $mosConfig_absolute_path;
        require_once($mosConfig_absolute_path
            . '/includes/patTemplate/patTemplate.php');
        $tmpl = &patFactory::createTemplate($option, true, false);
        $tmpl->setRoot(dirname(__FILE__) . '/tmp');
        return $tmpl;
    }

    function RandomString()
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring = $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function checkPost()
    {
        global $database, $mosConfig_absolute_path;

        if (isset($_POST['src'])) {
            if ($_FILES["filename"]["size"] > 1024 * 3 * 1024) {
                self::$result = "The file size exceeds three megabytes.";
                return false;
            }

            $S3SliderImagePath = '';
            $filename = $_FILES["filename"]["name"];
            $temp_file = $_FILES["filename"]["tmp_name"];

            include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ImageConvertToWebp.php';

            if (is_uploaded_file($temp_file)) {
                $convertedImage = (new ImageConvertToWebp(realpath($temp_file)))->convert();
                move_uploaded_file($temp_file, $mosConfig_absolute_path . self::$path . $filename);
                self::$result = "Successfully loaded.";

                include_once $_SERVER['DOCUMENT_ROOT'] . '/core/php-image-master/ResizeImageAndSaveToS3.php';
                $ResizeAndSave = new ResizeImageAndSaveToS3();
                $S3SliderImagePath = $ResizeAndSave->resizeSliderImageAndSave($convertedImage ?: $mosConfig_absolute_path . self::$path . $filename,$filename);

            } else {
                self::$result = "Error loading file.";
                return false;
            }

            $public = (isset($_POST['public']) && (int)$_POST['public'] > 0) ? 1 : 0;
            $query = "INSERT INTO `jos_vm_slider`
            (
                `image`,
                `s3_image`,
                `src`,
                `public`, 
                `date_start`, 
                `date_end`, 
                `slider_type`, 
                `alt`
            ) 
            VALUES (
                '$filename',
                '$S3SliderImagePath',
                '" . $database->getEscaped($_POST['src']) . "',
                '$public',
                '" . $database->getEscaped($_POST['date_start']) . "',
                '" . $database->getEscaped($_POST['date_end']) . "',
                '" . $database->getEscaped($_POST['slider_type']) . "',
                '" . $database->getEscaped($_POST['alt']) . "'
            )";
            $database->setQuery($query);

            $database->query();
        }

        if (isset($_POST['url']) && isset($_POST['id']) && empty($_POST['update'])) {
            $query = "UPDATE jos_vm_slider set src='{$_POST['url']}' where id='{$_POST['id']}'";
            $database->setQuery($query);
            $database->query();
            self::$result = "updated successful.";
        } elseif (isset($_POST['date_start']) && isset($_POST['id']) && empty($_POST['update'])) {
            $query = "UPDATE jos_vm_slider set date_start='{$_POST['date_start']}' where id='{$_POST['id']}'";
            $database->setQuery($query);
            $database->query();
            self::$result = "updated successful.";
        } elseif (isset($_POST['date_end']) && isset($_POST['id']) && empty($_POST['update'])) {
            $query = "UPDATE jos_vm_slider set date_end='{$_POST['date_end']}' where id='{$_POST['id']}'";
            $database->setQuery($query);
            $database->query();
            self::$result = "updated successful.";
        } elseif (isset($_POST['id']) && empty($_POST['update'])) {
            $query = "DELETE FROM jos_vm_slider where id='{$_POST['id']}'";
            $filename = $_POST['filename'];
            $temp = explode('/', $filename);
            $dir = $mosConfig_absolute_path . self::$path . $temp[0];
            $file = $mosConfig_absolute_path . self::$path . $filename;


            if (file_exists($file)) {
                if (is_file($file))
                    unlink($file);
            }
            $database->setQuery($query);
            $database->query();
            self::$result = "Removel was successful.";
        } elseif (isset($_POST['id_public']) && empty($_POST['update'])) {
            $query = "UPDATE jos_vm_slider SET public='{$_POST['value']}' where id='{$_POST['id_public']}'";
            if ($_POST['value'] == 0) {
                $query = "UPDATE `jos_vm_slider`
                SET 
                    `public`='" . $database->getEscaped($_POST['value']) . "',
                    `queue`=" . (int)$_POST['value'] . ", 
                    `alt`='" . $database->getEscaped($_POST['alt']) . "'
                WHERE 
                    `id`=" . (int)$_POST['id_public'] . "
                ";
            }
            $database->setQuery($query);
            $database->query();
            self::$result = "Update was successful.";
        } elseif (isset($_POST['update'])) {
            $order_type = strtolower($_POST['order_type']);
            $query_type = "UPDATE tbl_options SET options = '$order_type' where type='slider' ";
            $database->setQuery($query_type);
            $database->query();

            $aQueue = $_POST['queue'];
            $query = "UPDATE jos_vm_slider SET queue = CASE ";
            foreach ($aQueue as $qID => $que) {
                $query .= "WHEN `id`=" . $qID . " THEN '" . $que . "' ";
            }
            $query .= " ELSE queue END";
            $database->setQuery($query);
            $database->query();
            unset($_POST['update']);
        }
        return true;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function view()
    {
        global $database, $mosConfig_absolute_path,$mosConfig_aws_s3_bucket_public_url;
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
		input,select{
			height:24px;
			border:1px solid #ddd;
			border-radius:4px;
			padding:0 2px;
		}
		/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

    /* Style the buttons inside the tab */
    .tab button {
      background-color: inherit;
      float: left;
      border: none;
      outline: none;
      cursor: pointer;
      padding: 14px 16px;
      transition: 0.3s;
      font-size: 17px;
    }
    
    /* Change background color of buttons on hover */
    .tab button:hover {
      background-color: #ddd;
    }
    
    /* Create an active/current tablink class */
    .tab button.active {
      background-color: #ccc;
    }
    
    /* Style the tab content */
    .tabcontent {
      display: none;
      padding: 6px 12px;
      border: 1px solid #ccc;
      border-top: none;
    }
	</style>
        	<script type="text/javascript" >
        	function openTab(evt, tabName) {
              var i, tabcontent, tablinks;
              tabcontent = document.getElementsByClassName("tabcontent");
              for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
              }
              tablinks = document.getElementsByClassName("tablinks");
              for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
              }
              document.getElementById(tabName).style.display = "block";
              evt.currentTarget.className += " active";
            }
		$(document).ready(function() {
			$(".val_minus").click(function () {
				var $input = $(this).parent().find("input");
				var count = parseInt($input.val()) - 1;
				count = count < 1 ? 1 : count;
				$input.val(count);
				$input.change();
				return false;
			});
			$(".val_plus").click(function () {
				var $input = $(this).parent().find("input");
				$input.val(parseInt($input.val()) + 1);
				$input.change();
				return false;
			});
	
            // Get the element with id="defaultOpen" and click on it
            document.getElementById("defaultOpen").click();
		});
		
	</script>';
        $html .= '<form action="/administrator/index2.php?option=com_slider" enctype="multipart/form-data" method="post">';
        $html .= '<div class="tab">
                  <button type="button" class="tablinks" onclick="openTab(event, \'Slider\')" id="defaultOpen">Slider</button>
                  <button type="button" class="tablinks" onclick="openTab(event, \'Banner\')">Banner</button>
                </div>';

        $htmlTable = '<table class="adminlist"><tr>';
        foreach (self::$names as $value) {
            if ($value == 'Delete') {
                $htmlTable .= "<th align='left'>$value</th>";
            } elseif ($value == 'Type') {
                continue;
            } else {
                if ($value == 'SRC')
                    $value = 'URL';
                if ($value == 'Public')
                    $value = 'Published';
                $htmlTable .= "<th width='13%' align='left'>$value</th>";
            }
        }
        $htmlTable .= '</tr>';

        $query = "SELECT * FROM jos_vm_slider ORDER BY public DESC ";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        $query_queue = "SELECT options FROM tbl_options where type = 'slider'";
        $database->setQuery($query_queue);
        $result_queue = $database->loadResult();
        $order_type = '<span>';
        $order_type .= '<select name="order_type">';
        $selected = '';
        if ($result_queue == "queue") {
            $selected = 'selected';
        }
        $order_type .= '<option value="queue" ' . $selected . '>Queue</option>';
        $selected = '';
        if ($result_queue == "random") {
            $selected = 'selected';
        }
        $order_type .= '<option value="random" ' . $selected . '>Random</option>';
        $order_type .= '</select></span>';

        if ($result) {

            foreach ($result as $item) {
                $type = ($item->slider_type == '1') ? 'Slider' : 'Banner';

                $$type .= "<tr>";
                foreach (self::$names as $value) {
                    if ($value == 'Type') {
                        continue;
                    }
                    switch ($value) {
                        case 'Delete':
                            $$type .= "<td align='left'>
                                        <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                            <input type='submit' value='$value' />
                                            <input type='hidden' name='id' value='{$item->id}' >
                                            <input type='hidden' name='filename' value='{$item->image}' >
                                        </form>
                                        </td>";
                            break;
                        case 'Public':
                            $publicValue = $item->{strtolower($value)};


                            $$type .= "<td align='left'>
                                            <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                                <input type='checkbox' onClick='this.form.submit()' " . (($publicValue == '1') ? "checked='checked' value=1 " : "") . "/>
                                                <input type='hidden' name='id_public' value='{$item->id}' >
                                                <input type='hidden' name='value' value='" . abs(1 - $publicValue) . "' >
                                            </form>
                                        </td>";
                            break;
                        case 'Queue':
                            $$type .= "<td align='left'>
                                            <span class='val_minus'>-</span>
                                            <input id='queue_id_" . $item->id . "' name='queue[" . $item->id . "]' type='text' size='2' value='" . $item->queue . "' />
                                            <span class='val_plus'>+</span>
                                         </td>";
                            break;
                        case 'Thumb':
                            $$type .= "<td align='left'>
                                <img src='" . $mosConfig_aws_s3_bucket_public_url . $item->s3_image . "' style='width:80px'>
                             </td>";
                            break;
                        case 'Start Date':
                            $$type .= "<td align='left'>
                                <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                            <input type='hidden' name='id' value='{$item->id}' >
                                            <input type='text' class='dateStartList' readonly name='date_start' value='" . $item->date_start . "'/>
                                        </form>
                             </td>";
                            break;
                        case 'End Date':
                            $$type .= "<td align='left'>
                                <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                            <input type='hidden' name='id' value='{$item->id}' >
                                            <input type='text'  readonly class='dateEndList' name='date_end' value='" . $item->date_end . "'/>
                                        </form>
                             </td>";
                            break;
                        case 'SRC':
                            $$type .= "<td align='left'>
                                        <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                            <input type='hidden' name='id' value='{$item->id}' >
                                            <input name='url' onblur=\"this.form.submit()\" type='text' value='{$item->{strtolower($value)}}' />
                                        </form>
                                         
                                         </td>";
                            break;
                        default:
                            $$type .= "<td width='13%' align='left'>{$item->{strtolower($value)}}</td>";
                            break;
                    }
                }
                $$type .= '</tr>';
            }
        }


        $html .= '<div id="Slider" class="tabcontent">' . $htmlTable . ($Slider ?? '') . '</table></div>
        
        <div id="Banner" class="tabcontent">' . $htmlTable . ($Banner ?? '') . '</table></div>
        ';

        $html .= "Order Type: " . $order_type;
        $html .= ' <span><input type="submit" name="update" value="Update"></span></form>';
        return $html;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function create()
    {
        self::checkPost();
        $tmpl = &self::createTemplate();
        $tmpl->setAttribute('body', 'src', 'slider.html');
        $tmpl->addVar('body', 'action', $_SERVER['REQUEST_URI']);
        $tmpl->addVar('body', 'result', self::$result);
        $tmpl->addVar('body', 'view', self::view());
        $tmpl->displayParsedTemplate('form');
    }

}

Slider::create();
?>