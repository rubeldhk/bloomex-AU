<?php
/*
 * The class creates a field for uploading pictures and links to them
 */
class LoadImageWithReferenceTemplate
{
    private static $table = '';
    private static $folder = '';
    private static $titles = array();
    private static $names = array();
    private static $header = array();
    private static $image_name;
    
    // public __construct
    function LoadImageWithReferenceTemplate( $database_table, $folder_for_images, $names_array, $languages_array, $header, $image_name )
    {
        $this->table = $database_table;
        $this->folder = $folder_for_images;
        $this->titles = $languages_array;
        $this->names = $names_array;
        $this->header = $header;
        $this->image_name = $image_name;
    }
    
    // -----------------------------------------------------------------------------------------------------------------------------
    function show() { 
        global $database;
        
        $this->result(); 
        
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
        $count = count($this->titles);
        $line = 0;
        $query = "SELECT * FROM ".$this->table." ORDER BY id ASC";
        $database->setQuery($query);
        $result = $database->loadObjectList();  
        for( $i = 0; $i < $count; $i++ )
        {
            $line = ( $i == (((int)($i/2))*2) ) ? 0 : 1;
            $html .= '<tr class="row'.$line.'">
                    <td>
                        '.$this->titles[$i].'
                    </td>
                    <td>
                        Select file <input type="file" name="filename'.$this->names[$i].'">
                    </td>
                    <td>
                       <input type="text" name="href'.$this->names[$i].'" value="'.$result[$i]->href.'"> &nbsp;&nbsp;&nbsp;
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
            $this->isset_database_line();
            
            $count = count($this->names);
            for( $i = 0; $i < $count; $i++ )
            {
                $second_name = $this->names[$i];
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
                          $dir = $mosConfig_absolute_path.$this->folder;
                          if ( !is_dir( $dir ) ) mkdir( $dir );
                          $dir .= $second_name."/";
                          if ( !is_dir( $dir ) ) mkdir( $dir );
                          move_uploaded_file($_FILES["filename".$second_name]["tmp_name"], $dir.$this->image_name);
                          $html = "file has been updated.";
                        }
                        else
                        {
                           $html = "<span style='color=#0000FF;'>Error loading.</span>";
                           return $html;
                        }
                    }
                }
                $query  = "UPDATE `".$this->table."` SET href='".$_POST['href'.$second_name]."' WHERE id='".($i+1)."'";
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
        $query = "SELECT * FROM ".$this->table."";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if( !$result )
        {
            for( $i = 0; $i < 2; $i++ )
            {
                $l = ( $l == $langs[0] ) ? $langs[1] : $langs[0];
                $query  = "INSERT INTO ".$this->table." ( id, href) VALUES ('".($i+1)."', '' )";
                $database->setQuery($query);
                $database->query();
            }
        }
    }
    
    // functions for folder in administrator/compotents/
    // But places in administrator/ 

    // -----------------------------------------------------------------------------------------------------------------------------
    function &createTemplate()
    {
        global $option, $mosConfig_absolute_path;
        require_once( $mosConfig_absolute_path
                . '/includes/patTemplate/patTemplate.php' );

        $tmpl = & patFactory::createTemplate($option, true, false);
        $tmpl->setRoot(dirname(__FILE__) . '/tmpl');
        return $tmpl;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function create($message='')
    {
         $tmpl = & $this->createTemplate();
         $tmpl->setAttribute('body', 'src', 'tmp_load_image_with_reference_template.html');
         $tmpl->addVar('body', 'header', $this->header);
         $tmpl->addVar('body', 'domainlist', $this->show()); 
         $tmpl->displayParsedTemplate('form');
    }  
    
}
?>
