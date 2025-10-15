<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_slider
{
    function show()
    {
        global $my, $acl, $database, $mosConfig_offset;
        
       ?>
        <form action="index2.php?option=com_slider&task=add" enctype="multipart/form-data" method="post" name="insert-form">
        <table class="adminlist">
            <tr>
                <th width="13%" align='left'>
                    Image
                </th>
                <th align='left'>
                    Alt
                </th>
                <th width="13%" align='left'>
                    URL
                </th>
                <th width="13%" align='left'>
                    Lang
                </th>
                <th width="13%" align='left'>
                    Published
                </th>
                <th align="left">
                    Insert
                </th>
            </tr>
            <tr>
                <td align="left">
                    <input type="file" name="filename">
                </td>
                <td align="left">
                    <input type="text" name="alt" value="" />
                </td>
                <td align="left">
                    <input type="text" name="src" value="" />
                </td>
                <td align="left">
                    <select name="lang">
                        <option value="en">English</option>
                        <option value="fr">French</option>
                    </select>
                </td>
                <td align="left">
                    <input type="checkbox" name="public" value="1">
                </td>
                <td align="left">
                    <input type="submit" name="insert" value="Insert" >
                </td>
            </tr>
        </table>
        </form>
        <table class="adminlist">
        <tr>
        <th width="13%" align='left'>
            Image
        </th>
        <th align='left'>
            Alt
        </th>
        <th width="13%" align='left'>
            URL
        </th>
        <th colspan="2" align="center" width="5%">
            Reorder
        </th>
        <th width="13%" align='left'>
            Lang
        </th>
        <th width="13%" align='left'>
            Published
        </th>
        <th align="left">
            Delete
        </th>
        
        
        <?php
        $query = "SELECT * FROM jos_vm_slider ORDER BY `position` DESC";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        
        if ($result)
        {
            $total = sizeof($result);
            $i = $total;
            foreach ($result as $item) 
            {
                echo '</tr><tr>';
                echo '<td><a href="index2.php?option=com_slider&task=edit&id='.$item->id.'">'.$item->image.'</a></td><td>'.$item->alt.'</td><td>'.$item->src.'</td>';

                if ($i < $total) echo '<td align="right"><a href="#reorder" onClick="change_position('.$item->id.', '.$i.', '.($i+1).');" title="Move Up"><img src="images/uparrow.png" width="12" height="12" border="0" alt="Move Up"></a></td>';
                else echo '<td align="right"></td>';
                
                if ($i > 1) echo '<td align="left"><a href="#reorder" onClick="change_position('.$item->id.', '.$i.', '.($i-1).');" title="Move Down"><img src="images/downarrow.png" width="12" height="12" border="0" alt="Move Down"></a></td>';
                else echo '<td align="left"></td>';
                
                echo '<td>'.$item->lang.'</td>
                    <td align="left">
                        <!--
                        <form action="" method="post">            
                           <input type="submit" value="'.( ( $item->public != 1 ) ? 'Yes' : 'No' ).'" />
                           <input type="hidden" name="id_public" value="'.$item->id.'">
                           <input type="hidden" name="value" value="'.abs(1-$publicValue).'">
                        </form>-->
                        '.( ( $item->public == 1 ) ? 'Yes' : 'No' ).'
                    </td>
                    <td align="left">
                        <form action="index2.php?option=com_slider&task=delete" method="post">
                            <input type="submit" value="Delete" />
                            <input type="hidden" name="id" value="'.$item->id.'" >
                            <input type="hidden" name="filename" value="'.$item->image.'" >
                        </form>
                    </td>';
                $i--;
            }
            echo '</tr></table>';
        }
    }
    
    function save($id)
    {
        global $database, $mosConfig_absolute_path;
        
        if (isset($_POST['update']))
        {
            if ($_FILES["filename"]["size"] > 0)
            {
                $filename = "slider_" . $_FILES["filename"]["name"];
                if (file_exists($mosConfig_absolute_path.'/images/header_images/'.$filename))
                {
                    return false;
                    exit;
                }  
                elseif ($_FILES["filename"]["size"] > 1024*3*1024)
                {
                  return false;
                  exit;
                }
                elseif(is_uploaded_file($_FILES["filename"]["tmp_name"]))
                {
                  move_uploaded_file($_FILES["filename"]["tmp_name"], $mosConfig_absolute_path.'/images/header_images/'.$filename);  
                } 
                
                $query = "UPDATE jos_vm_slider SET `image`='$filename' WHERE `id`='$id' LIMIT 1";
                $database->setQuery($query);
                $database->query();
            }     
            
            $public = ( isset($_POST['public']) && (int)$_POST['public'] > 0 ) ? 1 : 0;
            
            $query = "UPDATE jos_vm_slider SET `src`='".$_POST['src']."', `lang`='".$_POST['lang']."', `public`='$public' WHERE `id`='$id' LIMIT 1";
            $database->setQuery($query);
            $database->query();
            
        }
        
        return true;
    }
    
    function edit($id)
    {
        global $database, $mosConfig_absolute_path;

        $query = "SELECT * FROM jos_vm_slider WHERE `id`='$id' LIMIT 1";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        $result = $result[0];

        if ($result)
        {
        ?>
        <form action="index2.php?option=com_slider&task=save&id=<?php echo $id; ?>" enctype="multipart/form-data" method="post" name="insert-form">
        <table class="adminlist">
            <tr>
                <th width="13%" align='left'>
                    Image
                </th>
                <th width="13%" align='left'>
                    URL
                </th>
                <th width="13%" align='left'>
                    Lang
                </th>
                <th width="13%" align='left'>
                    Published
                </th>
                <th align="left">
                    Update
                </th>
            </tr>
            <tr>
                <td align="left">
                    <img src="/images/header_images/<?php echo $result->image;?>" width="150px"><br/>
                    <?php echo $result->image;?><br/>
                    <input type="file" name="filename">
                </td>
                <td align="left">
                    <input type="text" name="src" value="<?php echo $result->src;?>" />
                </td>
                <td align="left">
                    <select name="lang">
                        <option value="en" <?php echo ($result->lang == 'en') ? 'selected' : ''; ?>>English</option>
                        <option value="fr" <?php echo ($result->lang == 'fr') ? 'selected' : ''; ?>>French</option>
                    </select>
                </td>
                <td align="left">
                    <input type="checkbox" name="public" value="1" <?php echo ($result->public == '1') ? 'checked' : ''; ?>>
                </td>
                <td align="left">
                    <input type="submit" name="update" value="Update" >
                </td>
            </tr>
        </table>
        </form>
        <?php
        }
    }
    
    function add()
    {
        global $database, $mosConfig_absolute_path;
        
        if (isset($_POST['insert']))
        {
                $filename = "slider_" . $_FILES["filename"]["name"];
                if(file_exists($mosConfig_absolute_path.'/images/header_images/'.$filename) ){
                   echo $result = "File exists.";
                }
                if($_FILES["filename"]["size"] > 1024*3*1024){
                  echo "The file size exceeds three megabytes.";
                }
                if(is_uploaded_file($_FILES["filename"]["tmp_name"])){
                  move_uploaded_file($_FILES["filename"]["tmp_name"], $mosConfig_absolute_path.'/images/header_images/'.$filename);
                  echo "Slide successfully loaded.";
                } else {
                  echo "Error loading file.";
                }
                $public = ( isset($_POST['public']) && (int)$_POST['public'] > 0 ) ? 1 : 0;
                $query = "INSERT INTO jos_vm_slider (image,src,lang,public) VALUES ('$filename','{$_POST['src']}','{$_POST['lang']}','$public')";
                $database->setQuery($query);
                $database->query();
                
                show();
        }
    }
    
    function delete()
    {
        global $database, $mosConfig_absolute_path;
        
        $query = "DELETE FROM jos_vm_slider where id='{$_POST['id']}'";
        $filename = $_POST['filename'];
        if (file_exists($mosConfig_absolute_path.'/images/header_images/'.$filename) )
        {
             unlink($mosConfig_absolute_path.'/images/header_images/'.$filename);
        }
        $database->setQuery($query);
        $database->query();
        
        $query = "SELECT * FROM jos_vm_slider ORDER BY `position` DESC";
        $database->setQuery($query);
        $result = $database->loadObjectList();

        foreach ($result as $item)
        {
            $query = "UPDATE jos_vm_slider SET `position`=$i WHERE `id`=$item->id";
            $database->setQuery($query);
            $database->query();
            
            $i++;
        }
        
        echo "Removal was successful.";
        
        show();
    }
    
    function change_position()
    {
        global $database;
        
        $id = (int)$_POST['id'];
        $now = (int)$_POST['now'];
        $want = (int)$_POST['want'];
        
        $query = "UPDATE jos_vm_slider SET `position`=$now WHERE `position`=$want";
        $database->setQuery($query);
        $database->query();
        
        $query = "UPDATE jos_vm_slider SET `position`=$want WHERE `id`=$id";
        $database->setQuery($query);
        $database->query();
    }
}

?>