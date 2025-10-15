<?php

/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

/**
 * @package HelloWorld
 */
class EditCorners {   
    
    private static $table = 'jos_vm_edit_corners_href';
    private static $titles = array( 'Left Corner English', 'Right Corner English', 'Left Corner French', 'Right Corner French' );
    private static $names = array( 'corner_en_left', 'corner_en_right', 'corner_fr_left', 'corner_fr_right' );
    
    public function __construct()
    {
        // NULL
    }
    
    // -----------------------------------------------------------------------------------------------------------------------------
    function show() { 
        global $database;
        
        EditCorners::result();
        
        $html = '<!--ERROR FORM--> </form>
            <form action="" method="post" enctype="multipart/form-data">
            <table class="adminlist">
                <thead>
                <tr style="text-align:left">
                   <th>Banner for updates</th>
                   <th>Upload image</th>
                   <th>Href</th>
                </tr>
                </thead>';
        $count = count(EditCorners::$titles);
        $line = 0;
        $query = "SELECT * FROM ".EditCorners::$table." ORDER BY id ASC";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        for( $i = 0; $i < $count; $i++ )
        {
            $line = ( $i == (((int)($i/2))*2) ) ? 0 : 1;
            $html .= '<tr class="row'.$line.'">
                    <td>
                        '.EditCorners::$titles[$i].'
                    </td>
                    <td>
                        Select file <input type="file" name="filename'.EditCorners::$names[$i].'">
                    </td>
                    <td>
                       <input type="text" name="href'.EditCorners::$names[$i].'" value="'.$result[$i]->href.'"> &nbsp;&nbsp;&nbsp;
                    </td>
                </tr>';
        }
 
           $html .=  '</table>
               <span style="align:left"><input type="submit" name="upload" value="Update"> &nbsp;&nbsp;&nbsp;</span>
               </form>
           <!--ERROR FORM--> <form>';
                
        return $html;
    }
    
    // -----------------------------------------------------------------------------------------------------------------------------
    function result()
    {
        global $mosConfig_absolute_path, $database;
        $html = '';
        if( isset($_POST['upload']) )
        {
            EditCorners::isset_database_line();
            $count = count(EditCorners::$names);
            for( $i = 0; $i < $count; $i++ )
            {
                $image_name = EditCorners::$names[$i].'.png';
                $second_name = EditCorners::$names[$i];
                if( $_FILES["filename".$second_name]["tmp_name"] != '' )
                {
                    if($_FILES["filename".$second_name]["size"] > 1024*3*1024)
                    {
                      $html = "<span style='color=#0000FF;'>File size is more than three megabytes.</span>";
                      return $html;
                    }
                    else
                    {
                        if(is_uploaded_file($_FILES["filename".$second_name]["tmp_name"]))
                        {
                          $dir = $mosConfig_absolute_path."/images_upload/com_edit_corners/";
                          if ( !is_dir( $dir ) ) mkdir( $dir );
                          move_uploaded_file($_FILES["filename".$second_name]["tmp_name"], $dir.$image_name);
                          $html = "file has been updated.";
                        }
                        else
                        {
                           $html = "<span style='color=#0000FF;'>Error loading.</span>";
                           return $html;
                        }
                    }
                }
                $query  = "UPDATE `".EditCorners::$table."` SET href='".$_POST['href'.$second_name]."' WHERE id='".($i+1)."'";
                $database->setQuery($query);
                if( $database->query() ) $html = "Updated.";
            }
        }
        return $html;
    }
    
    function isset_database_line()
    {
        global $database;
        $langs = array( 0 => 'en', 1 => 'fr' );
        $l = $langs[1];
        $query = "SELECT * FROM ".EditCorners::$table."";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if( !$result )
        {
            $count = count(EditCorners::$names);
            for( $i = 0; $i < $count; $i++ )
            {
                $l = ( $l == $langs[0] ) ? $langs[1] : $langs[0];
                $query  = "INSERT INTO ".EditCorners::$table." ( id, href ) VALUES ('".($i+1)."', '' )";
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
    function create($message='') {
         $tmpl = & EditCorners::createTemplate();
         $tmpl->setAttribute('body', 'src', 'edit_corners.html');
         $tmpl->addVar('body', 'domainlist', EditCorners::show());
         $tmpl->displayParsedTemplate('form');
     }
}

?>