<?php

/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

/**
 * @package HelloWorld
 */
class EditEmailBanner {   


    // -----------------------------------------------------------------------------------------------------------------------------
    function show() { 
        global $database;
        $query = "SELECT * FROM jos_vm_edit_email_banner";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if( !$result )
        {
            for( $i = 0; $i < 3; $i++ )
            {
                $query  = "INSERT INTO jos_vm_edit_email_banner (id, href) VALUES ('".($i + 1)."', '' )";
                $database->setQuery($query);
                $database->query();
            }
        }
        $res = EditEmailBanner::result();
        $query = "SELECT * FROM jos_vm_edit_email_banner";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        $html = '<!--ERROR FORM--> </form>
            <form action="" method="post" enctype="multipart/form-data">
            <table class="adminlist">
                <thead>
                <tr style="text-align:left">
                   <th>Banner for updates</th>
                   <th>Form for update</th>  
                </tr>
                </thead>';
        $k = 0;
        $names = array( ' Top banner', 'Right banner top', 'Right banner bottom' );
        for( $i = 0; $i < 3; $i++ )
        {
            $k = ( $i == 1 ) ? 1 : 0;
            $html .= '
                <tr class="row'.$k.'">
                    <td>
                       '.$names[$i].'
                    </td>
                    <td>
                        Select file <input type="file" name="filename_'.$i.'">
                        <input type="text" name="href_'.$i.'" value="'.(isset($result[$i]) ? $result[$i]->href : '').'">
                    </td>
                </tr>';
        }
          $html .= '
            </table><div align="left">
            <input type="submit" name="upload" value="Update"> &nbsp;&nbsp;&nbsp; '.$res.'
                </div>
           </form>
           <!--ERROR FORM--> <form>';
        return $html;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function result()
    {
        global $database, $mosConfig_absolute_path;
        $html = '';
        if( isset($_POST['upload']) )
        {
            for( $i = 0; $i < 3; $i++ )
            {
                $second_name = '_'.$i;
                $image_name = 'email_banner_'.$i.'.jpg';
                if( $_FILES["filename".$second_name]["tmp_name"] != '' )
                {
                    if($_FILES["filename".$second_name]["size"] > 1024*3*1024)
                    {
                      $html = "<span style='color=#0000FF;'>File size is more than three megabytes.</span>";
                      $html = "<span style='color=#0000FF;'>Error loading.</span>";
                    }
                    else
                    {
                        if(is_uploaded_file($_FILES["filename".$second_name]["tmp_name"]))
                        {
                          $dir = $mosConfig_absolute_path."/images_upload/com_edit_email_banner/";
                          if ( !is_dir( $dir ) ) mkdir( $dir );
                          move_uploaded_file($_FILES["filename".$second_name]["tmp_name"], $dir.$image_name);                        
                          $html = "file has been updated.";
                        }
                        else
                        {
                           $html = "<span style='color=#0000FF;'>Error loading.</span>";
                        }
                    }
                }
                $query  = "UPDATE `jos_vm_edit_email_banner` SET href='".$_POST['href_'.$i]."' WHERE id='".($i+1)."'";
                $database->setQuery($query);
                if( $database->query() ) $html = "Updated.";
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
    function create($message='') {
         $tmpl = & EditEmailBanner::createTemplate();
         $tmpl->setAttribute('body', 'src', 'edit_email_banner_users_manager.html');
         $tmpl->addVar('body', 'domainlist', EditEmailBanner::show());
         $tmpl->displayParsedTemplate('form');
     }
}

?>