<?php

ini_set('max_file_uploads', '30');
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

/**
 * @package HelloWorld
 */
class EditTitleCategory_funeral {

    // -----------------------------------------------------------------------------------------------------------------------------
    function show() {
        global $database;
        $langs = array(0 => 'en', 1 => 'fr');
        $l = $langs[1];
        $query = "SELECT * FROM jos_vm_landing_page_funeral_banner ";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if (!$result) {
            for ($i = 0; $i < 9; $i++) {
                for ($i2 = 0; $i2 < 2; $i2++) {
                    $l = ( $l == $langs[0] ) ? $langs[1] : $langs[0];
                    $query = "INSERT INTO jos_vm_landing_page_funeral_banner (num, lang, href) VALUES ('" . ($i + 1) . "', '" . $l . "', '' )";
                    $database->setQuery($query);
                    $database->query();
                }
            }
        }
        $res = EditTitleCategory_funeral::result();

        $html = '<!--ERROR FORM--> </form>' . $res . '
            <form action="" method="post" enctype="multipart/form-data">
            <h3> Edit main banners</h3>
     
            <table class="adminlist">
                <thead>
                <tr style="text-align:left">
                   <th width="25%">Banner for updates </th>
                   <th>File for update</th>  
                   <th>Link</th>  
                </tr>
                </thead>';

        $html .= EditTitleCategory_funeral::top_content('English top banner', $langs[0]);
               $html .= '
            </table>
        <table class="adminlist">
                <thead>
                <tr style="text-align:left">
                   <th width="25%">Banner for updates </th>
                   <th>File for update</th>  
                   <th>Link</th>  
                </tr>
                </thead>';
        
        
          $html .= EditTitleCategory_funeral::top_content('French top banner', $langs[1]);
          
          $html .='</table><div align="left">
               <input type="submit" name="upload" value="Update"> &nbsp;&nbsp;&nbsp; 
               <input type="hidden" name="type" value="top"/>
                </div>
           </form>
           <br>
            <form action="" method="post" enctype="multipart/form-data">
             <h3> Edit mini banners</h3>
            <div style="display:none">
              <input type="checkbox" name="upper_banner_en" value="upper" '.EditTitleCategory_funeral::selectContentUpper($langs[0]).'>Do not use main page banners<br></div>
            <table class="adminlist">
                <thead>
                <tr style="text-align:left">
                   <th width="25%">Banner for updates </th>
                   <th>File for update</th>  
                   <th>Link</th>  
                </tr>
                </thead>';
          
        $html .= EditTitleCategory_funeral::upper_content('English mini banner', $langs[0]);
        $html .= '
            </table>
            <div style="display:none">
             <input type="checkbox" name="upper_banner_fr" value="upper" '.EditTitleCategory_funeral::selectContentUpper($langs[1]).'>Do not use main page banners<br></div>
        <table class="adminlist">
                <thead>
                <tr style="text-align:left">
                   <th width="25%">Banner for updates </th>
                   <th>File for update</th>  
                   <th>Link</th>  
                </tr>
                </thead>';
        $html .= EditTitleCategory_funeral::upper_content('French mini banner', $langs[1]);

                

        $html .= '
            </table><div align="left">
               <input type="submit" name="upload" value="Update"> &nbsp;&nbsp;&nbsp; 
               <input type="hidden" name="type" value="upper"/>
                </div>
           </form>
             <form style="display:none;" action="" method="post" enctype="multipart/form-data">
             <br>
             <h3> Edit content page </h3>
             <div style="display:none">
              <input type="checkbox" name="categ_land_en" value="categ_land" '.EditTitleCategory_funeral::selectContentBodyCateg($langs[0]).'>Do not use caterory from landing manager<br></div>
              <input type="radio" name="categ_product_en" value="category" '.EditTitleCategory_funeral::selectContentBody($langs[0],'categ').'>Category<br>
        <table class="adminlist">
                <thead>

                <tr style="text-align:left">
                   <th width="25%">Banner for updates</th>
                   <th>File for update</th>  
                   <th>Link</th>  
                </tr>
                </thead>';


        $html .= EditTitleCategory_funeral::center_content_categ('English Category', $langs[0]);
        $html .= '
            </table>
            
 <input type="radio" name="categ_product_en" value="product" '.EditTitleCategory_funeral::selectContentBody($langs[0],'product').'>Products<br>';
       $html .= ' <table class="adminlist">
                <thead>

                <tr style="text-align:left">
                        <th width="15">
                            #
                        </th>
                        <th>
                            SKU
                        </th>
                        <th width="90%">
                        </th>
                    </tr>';
         $html .= EditTitleCategory_funeral::center_content_product('English Products', $langs[0]);       
  $html .= '
            </table>
            <div style="display:none">
            <input type="checkbox" name="categ_land_fr" value="categ_land" '.EditTitleCategory_funeral::selectContentBodyCateg($langs[1]).'>Do not use caterory from landing manager<br></div>
            <input type="radio" name="categ_product_fr" value="category" '.EditTitleCategory_funeral::selectContentBody($langs[1],'categ').'>Category<br>
        <table class="adminlist">
                <thead>
               
                
              
                <tr style="text-align:left">
                   <th width="25%">Banner for updates </th>
                   <th>File for update</th>  
                   <th>Link</th>  
                </tr>
                </thead>';
        $html .= EditTitleCategory_funeral::center_content_categ('French Category', $langs[1]);
        $html .= '
            </table>
           
            <input type="radio" name="categ_product_fr" value="product" '.EditTitleCategory_funeral::selectContentBody($langs[1],'product').'>Products<br>';
       $html .= ' <table class="adminlist">
                <thead>

                <tr style="text-align:left">
                        <th width="15">
                            #
                        </th>
                        <th>
                            SKU
                        </th>
                        <th width="90%">
                        </th>
                    </tr>';
         $html .= EditTitleCategory_funeral::center_content_product('French Products', $langs[1]); 
        $html .= ' 
            </table>
            
            <div align="left">
            <input type="submit" name="upload" value="Update"> &nbsp;&nbsp;&nbsp;
            <input type="hidden" name="type" value="center"/>
                </div>
           </form>
           <!--ERROR FORM--><form>';
        return $html;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
 function center_content_product($name, $l){
     global $database;
     $html .= '
                <tr class="row0">
                    <td>
                      ' . $name . '
                    </td>
                    <td>
                    </td>
                </tr>';
      $q = "SELECT sku,num FROM jos_vm_landing_products_funeral where lang='" . $l . "' ORDER BY num ASC";
        $database->setQuery($q);
        $result = $database->loadObjectList();
       
        for ($index = 0; $index < count($result); $index++) {
            $html .= '<tr>
                        <td align="center">
                            '.( $index + 1 ).'
                        </td>
                        <td align="center">
                            <input type="text" size="10" name="sku_'. $l .( $index + 1 ).'" value="'.( ( $result && $result[$index] ) ? $result[$index]->sku : '' ).'">
                        </td>
                        <td>
                        </td>
                    </tr>';
        }
        
       return $html;
 }
    function selectContentBody($l,$type){
      global $database;
      $ret='';
   $query = "SELECT * FROM jos_vm_landing_page_funeral_banner WHERE lang='" . $l . "' AND position='categ_product'";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if(($result[0]->href=='0')&&($type=='categ')){
            $ret='checked';
        }
        if(($result[0]->href=='1')&&($type=='product')){
            $ret='checked';
        }
      return $ret;
  }
   function selectContentUpper($l){
      global $database;
      $ret='';
   $query = "SELECT * FROM jos_vm_landing_page_funeral_banner WHERE lang='" . $l . "' AND position='check_upper_banner'";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if($result[0]->href=='1'){
            $ret='checked';
        }
        if($result[0]->href=='0'){
            $ret='';
        }
      return $ret;
  }
  function selectContentBodyCateg($l){
      global $database;
      $ret='';
   $query = "SELECT * FROM jos_vm_landing_page_funeral_banner WHERE lang='" . $l . "' AND position='categ_land'";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if($result[0]->href=='0'){
            $ret='checked';
        }
        if($result[0]->href=='1'){
            $ret='';
        }
      return $ret;
  }
      function top_content($name, $l) {
        global $database;
        $html .= '';
        $query = "SELECT * FROM jos_vm_landing_page_funeral_banner WHERE lang='" . $l . "' AND position='top' ORDER BY num ASC";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        $k = 0;
        for ($i = 0; $i < 1; $i++) {
            $k = 0;
            $html .= '
                <tr class="row' . $k . '">
                    <td>
                      ' . $name . '
                    </td>
                    <td>
                        Select file <input type="file" name="tfilename_' . $l . $i . '"> </td>
                          <td>  
                        <input type="text" name="thref_' . $l . $i . '" value="' . $result[$i]->href . '">
                    </td>
                </tr>';
        }
        return $html;
    }

    function upper_content($name, $l) {
        global $database;
		$posit=array('left','right');
        $html .= '';
        $query = "SELECT * FROM jos_vm_landing_page_funeral_banner WHERE lang='" . $l . "' AND position='upper' ORDER BY num ASC";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        $k = 0;
        for ($i = 0; $i < 2; $i++) {
            $k = ( $i == ((int) ($i / 2) * 2) ) ? 1 : 0;
            $html .= '
                <tr class="row' . $k . '">
                    <td>
                      ' . $name . ' '.$posit[$i].'
                    </td>
                    <td>
                        Select file <input type="file" name="ufilename_' . $l . $i . '"> </td>
                    <td>
                        <input type="text" name="uhref_' . $l . $i . '" value="' . $result[$i]->href . '">
                    </td>
                </tr>';
        }
        return $html;
    }

    function center_content_categ($name, $l) {
        global $database;
        $html .= '';
        $query = "SELECT * FROM jos_vm_landing_page_funeral_banner WHERE lang='" . $l . "' AND position='body' ORDER BY num ASC";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        $k = 0;
        for ($i = 0; $i < 9; $i++) {
            $k = ( $i == ((int) ($i / 2) * 2) ) ? 1 : 0;
            $html .= '
                <tr class="row' . $k . '">
                    <td>
                      ' . $name . '
                    </td>
                    <td>
                        Select file <input type="file" name="filename_' . $l . $i . '"></td>
                    <td>
                        <input type="text" name="href_' . $l . $i . '" value="' . $result[$i]->href . '">
                    </td>
                </tr>';
        }
        return $html;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function result() {
        global $database, $mosConfig_absolute_path;
        $html = '';
        $allowed = array('jpg', 'JPG','png','PNG', 'gif','GIF');
        //var_dump($_POST);
        if (isset($_POST['upload'])) {
            $langs = array(0 => 'en', 1 => 'fr');
            $l = $langs[1];
            
            switch ($_POST['type']) {
                case "center":
                    $categ_product_en=$_POST['categ_product_en'];
                    $categ_product_fr=$_POST['categ_product_fr'];
                    $checked_en=($categ_product_en=='category')?'0':'1';
                    $checked_fr=($categ_product_fr=='category')?'0':'1';
                     $query = "UPDATE `jos_vm_landing_page_funeral_banner` SET href='" .$checked_fr . "' WHERE num='0' AND lang='fr' AND position='categ_product'";
                            $database->setQuery($query);
                            $database->query();
                                   
                     $query = "UPDATE `jos_vm_landing_page_funeral_banner` SET href='" .$checked_en . "' WHERE num='0' AND lang='en' AND position='categ_product'";
                            $database->setQuery($query);
                            $database->query();
                                
                    $categ_land_en=$_POST['categ_land_en'];
                    $categ_land_fr=$_POST['categ_land_fr'];
                   
                    $checked_en=($categ_land_en=='categ_land')?'0':'1';
                    $checked_fr=($categ_land_fr=='categ_land')?'0':'1';
                     $query = "UPDATE `jos_vm_landing_page_funeral_banner` SET href='" .$checked_fr . "' WHERE num='0' AND lang='fr' AND position='categ_land'";
                            $database->setQuery($query);
                            $database->query();
                                   
                             
                     $query = "UPDATE `jos_vm_landing_page_funeral_banner` SET href='" .$checked_en . "' WHERE num='0' AND lang='en' AND position='categ_land'";
                            $database->setQuery($query);
                            $database->query();
                                   
                             
                    for ($i = 0; $i < 9; $i++) {
                        for ($i2 = 0; $i2 < 2; $i2++) {
                            $l = ( $l == $langs[0] ) ? $langs[1] : $langs[0];
                            $second_name = '_' . $l . $i;
                            $second_name_p = '_' . $l . ($i+1);
                            
                            $image_name = 'landing_mid_funeral_' . $l . $i . '.png';
                            if ($_FILES["filename" . $second_name]["tmp_name"] != '') {
                                $filename = $_FILES["filename" . $second_name]["name"];
                                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                                if ($_FILES["filename" . $second_name]["size"] > 1024 * 3 * 1024) {
                                    $html = "<span style='color=#0000FF;'>Title category error (" . $filename . ")File size is more than three megabytes.</span><br/>";
                                    $html .= "<span style='color=#0000FF;'>Error update.</span>";
                                    return $html;
                                } elseif (!(in_array($ext, $allowed))) {
                                    $html = "<span style='color=#0000FF;'>Title category error (" . $filename . ") Allowed image types: JPG </span><br/>";
                                    $html .= "<span style='color=#0000FF;'>Error update.</span>";
                                    return $html;
                                } else {
                                    if (is_uploaded_file($_FILES["filename" . $second_name]["tmp_name"])) {
                                        $dir = $mosConfig_absolute_path . "/images/" . $l . "/";
                                        if (!is_dir($dir))
                                            mkdir($dir);
                                        move_uploaded_file($_FILES["filename" . $second_name]["tmp_name"], $dir . $image_name);
                                        $html = "file has been updated.";
                                    }
                                    else {
                                        $html = "<span style='color=#0000FF;'>Error loading.</span>";
                                        return $html;
                                    }
                                }
                            }
                            $query = "UPDATE `jos_vm_landing_page_funeral_banner` SET href='" . $_POST['href' . $second_name] . "' WHERE num='" . ($i + 1) . "' AND lang='" . $l . "' AND position='body'";
                            $database->setQuery($query);
                            if ($database->query())
                                $html .= "Updated. categ";
                            $q = "UPDATE jos_vm_landing_products_funeral SET sku='{$_POST['sku'. $second_name_p]}' WHERE num='".( $i + 1 )."' AND lang='" . $l . "'";
                            $database->setQuery($q);
                             if ($database->query())
                                $html .= "Updated. product";
                        }
                    }
                    
                    break;
                case "upper":
                    // code fo upper part
                    $allowed = array('png', 'PNG','jpg', 'JPG', 'gif','GIF');
                    $upper_banner_en=$_POST['upper_banner_en'];
                    $upper_banner_fr=$_POST['upper_banner_fr'];
                    $checked_en=($upper_banner_en=='upper')?'1':'0';
                    $checked_fr=($upper_banner_fr=='upper')?'1':'0';
                     $query = "UPDATE `jos_vm_landing_page_funeral_banner` SET href='" .$checked_fr . "' WHERE num='0' AND lang='fr' AND position='check_upper_banner'";
                            $database->setQuery($query);
                            if ($database->query())
                                    $html = "Updated. type fr ";
                     $query = "UPDATE `jos_vm_landing_page_funeral_banner` SET href='" .$checked_en . "' WHERE num='0' AND lang='en' AND position='check_upper_banner'";
                            $database->setQuery($query);
                            if ($database->query())
                                    $html = "Updated. type en ".$query;
                    for ($i = 0; $i < 2; $i++) {
                        for ($i2 = 0; $i2 < 2; $i2++) {
                            $l = ( $l == $langs[0] ) ? $langs[1] : $langs[0];
                            $second_name = '_' . $l . $i;
                            $image_name = 'landing_top_funeral_' . $l . $i . '.png';
                            if ($_FILES["ufilename" . $second_name]["tmp_name"] != '') {
                                $filename = $_FILES["ufilename" . $second_name]["name"];
                                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                                if ($_FILES["ufilename" . $second_name]["size"] > 1024 * 3 * 1024) {
                                    $html = "<span style='color=#0000FF;'>Top banner upload error (" . $filename . ")File size is more than three megabytes.</span><br/>";
                                    $html .= "<span style='color=#0000FF;'>Error update.</span>";
                                    return $html;
                                } elseif (!(in_array($ext, $allowed))) {
                                    $html = "<span style='color=#0000FF;'>Top banner upload error(" . $filename . ") Allowed image types: PNG </span><br/>";
                                    $html .= "<span style='color=#0000FF;'>Error update.</span>";
                                    return $html;
                                } else {
                                    if (is_uploaded_file($_FILES["ufilename" . $second_name]["tmp_name"])) {
                                        $dir = $mosConfig_absolute_path . "/images/" . $l . "/";
                                        if (!is_dir($dir))
                                            mkdir($dir);
                                        move_uploaded_file($_FILES["ufilename" . $second_name]["tmp_name"], $dir . $image_name);
                                        $html = "file has been updated.";
                                    }
                                    else {
                                        $html = "<span style='color=#0000FF;'>Error loading.</span>";
                                    }
                                }
                            }
                            $query = "UPDATE `jos_vm_landing_page_funeral_banner` SET href='" . $_POST['uhref' . $second_name] . "' WHERE num='" . ($i + 1) . "' AND lang='" . $l . "' AND position='upper'";
                            $database->setQuery($query);
                            if ($database->query())
                                $html = "Updated.";
                        }
                    }
                    break;
                    case "top":
                    // code fo top part
                    $allowed = array('png', 'PNG','jpg', 'JPG', 'gif','GIF');
                    for ($i = 0; $i < 1; $i++) {
                        for ($i2 = 0; $i2 < 2; $i2++) {
                            $l = ( $l == $langs[0] ) ? $langs[1] : $langs[0];
                            $second_name = '_' . $l . $i;
                            $image_name = 'landing_main_funeral_' . $l . $i . '.png';
                            if ($_FILES["tfilename" . $second_name]["tmp_name"] != '') {
                                $filename = $_FILES["tfilename" . $second_name]["name"];
                                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                                if ($_FILES["tfilename" . $second_name]["size"] > 1024 * 3 * 1024) {
                                    $html = "<span style='color=#0000FF;'>Top banner upload error (" . $filename . ")File size is more than three megabytes.</span><br/>";
                                    $html .= "<span style='color=#0000FF;'>Error update.</span>";
                                    return $html;
                                } elseif (!(in_array($ext, $allowed))) {
                                    $html = "<span style='color=#0000FF;'>Top banner upload error(" . $filename . ") Allowed image types: PNG </span><br/>";
                                    $html .= "<span style='color=#0000FF;'>Error update.</span>";
                                    return $html;
                                } else {
                                    if (is_uploaded_file($_FILES["tfilename" . $second_name]["tmp_name"])) {
                                        $dir = $mosConfig_absolute_path . "/images/" . $l . "/";
                                        if (!is_dir($dir))
                                            mkdir($dir);
                                        move_uploaded_file($_FILES["tfilename" . $second_name]["tmp_name"], $dir . $image_name);
                                        $html = "file has been updated.";
                                    }
                                    else {
                                        $html = "<span style='color=#0000FF;'>Error loading.</span>";
                                    }
                                }
                            }
                            $query = "UPDATE `jos_vm_landing_page_funeral_banner` SET href='" . $_POST['thref' . $second_name] . "' WHERE num='" . ($i + 1) . "' AND lang='" . $l . "' AND position='top'";
                            $database->setQuery($query);
                            if ($database->query())
                                $html = "Updated.";
                        }
                    }
                    break;
            }
        }


        return $html;
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
        $tmpl = & EditTitleCategory_funeral::createTemplate();
        $tmpl->setAttribute('body', 'src', 'edit_funeral_banner_users_manager.html');
        $tmpl->addVar('body', 'domainlist', EditTitleCategory_funeral::show());
        $tmpl->displayParsedTemplate('form');
    }

}

?>