<?php
/****************************************************************************************************
* Package : Brightcode Reporter
* Author : Theo van der Sluijs
* Llink : http://www.brightcode.eu
* Copyright (C) : 2007 Brightcode.eu
* Email : info@brightcode.eu
* Date : October 2007
* Package Code License :  Commercial License / http://www.brightcode.eu
* Joomla! API Code License : http://www.gnu.org/copyleft/gpl.html GNU/GPL 
* JavaScript Code & CSS : Commercial License / http://www.brightcode.eu
****************************************************************************************************
 * Copyrights (c) 2007
 * All rights reserved. Brightcode.eu
 *
 * This program is Commercial software.
 * Unauthorized reproduction is not allowed.
 * Read the complete license model on our site before using this product
 * http://www.brightcode.eu
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.
 *
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *****************************************************************************************************/

// no direct access to file
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_BrightReporter {

   function displayReports(&$rows, $option, $Itemid){
        global $my, $mosConfig_live_site, $mosConfig_absolute_path;
        
        require($mosConfig_absolute_path."/administrator/components/".$option."/config.brightreporter.php");
        mosCommonHTML::loadOverlib();

        if($user!=0)
        {
          if($user > $my->gid){
            mosNotAuth();
            return;
          }        
        }
        ?>
        <form action="index.php" method="post" name="adminForm">
        <table class="adminheading">
          <tr>
              <th>
              Report List
              </th>
          </tr>
         </table>

        <table class="adminlist">
          <tr>
              <th align="left" nowrap="nowrap">
              Title
              </th>
              <th align="left" nowrap="nowrap">
              Description
              </th>
              <th align="left" nowrap="nowrap">
              Created on
              </th>
          </tr>
        <?php
        $k = 0;
        for ($i=0, $n=count( $rows ); $i < $n; $i++) {
            $row = &$rows[$i];
            mosMakeHtmlSafe($row);
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td align="left"><a href="index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=PrepReport&cid=<?php echo $row->id;?>">
                <?php echo $row->title;?></a>
                </td>
                <td align="left" nowrap="nowrap">
                  <?php echo $row->memo;?>
                </td>                
                <td align="left" nowrap="nowrap">
                  <?php echo $row->createdon;?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </table>
<?php
        echo '<input type="hidden" name="option" value="'.$option.'" />';
        echo '<input type="hidden" name="task" value="" />';
        echo '</form>';
      }  
  
  function PrepReport($cid, $option, $title, $jquery, $rows){
          global $mosConfig_live_site, $mainframe, $database, $my, $mosConfig_absolute_path;

          require($mosConfig_absolute_path."/administrator/components/".$option."/config.brightreporter.php");

          echo "<script type=\"text/javascript\" src=\"$mosConfig_live_site/includes/js/joomla.javascript.js\"></script>";
          mosCommonHTML::loadOverlib();
          mosCommonHTML::loadCalendar();

          if($user!=0)
          {
            if($user > $my->gid){
              mosNotAuth();
              return;
            }        
          }          

?>
            <script type="text/javascript">
              function checkform (form)
              {
                // ** START **
<?php
                foreach($rows as $row){ 
?>
                if (document.adminForm.<?php echo $row->fieldname;?>.value == "") {
                  alert( "Please enter a <?php echo $row->fieldname;?>." );
                  document.adminForm.<?php echo $row->fieldname;?>.focus();
                  return false ;
                }
<?php
                } 
?>
                // ** END **
                return true ;
              }
            </script>
            <form action="index.php" method="post" name="adminForm">
            <table class="adminheading">
              <tr>
                  <th>
                  <?php echo $title;?> - Report Variables
                  </th>
              </tr>
             </table>
             <input type="hidden" name="cid" value="<?php echo $cid;?>" />
             <table class="adminlist">
            <?php   
              foreach($rows as $row){ 
                echo "<tr>";
                  echo "<td>".str_replace('_', ' ', $row->fieldname)."</td>";
                  echo "<td>";
                  if($row->fieldkindid == 1){ //DateBox
                      echo "<input class=\"text_area\" type=\"text\" name=\"".$row->fieldname."\" id=\"".$row->fieldname."\" size=\"10\" maxlength=\"10\" value=\"".$fromdate."\" />
                      <input name=\"reset\" type=\"reset\" class=\"button\" onclick=\"return showCalendar('".$row->fieldname."', 'y-mm-dd');\" value=\"...\" />";
                  }elseif($row->fieldkindid == 2 || $row->fieldkindid == 3){//SelectBox && //MultiSelectBox
                     if($row->fieldkindid == 3){$list='size="4" class="inputbox" multiple="true"';}else{$list='class="inputbox"';}
                        if(stristr($row->fieldcode, "select"))
                        {
                          $options = array();
                          $database->setQuery( $row->fieldcode );
                          $options = array_merge( $options, $database->loadObjectList() );
                          echo mosHTML::selectList( $options, $row->fieldname, $list, 'value', 'text');
                          }
                        elseif(is_array(explode ("|", $row->fieldcode))){
                         $fields = array();
                         $fields = explode ("|", $row->fieldcode);

                         $options = array();
                         foreach($fields as $field)
                          {
                            $options[] = mosHTML::makeOption( $field, $field);
                          }
                         echo mosHTML::selectList( $options, $row->fieldname, $list, 'value', 'text');
                       }

                  }elseif($row->fieldkindid == 4){//StringField
                      echo "<input class=\"text_area\" type=\"text\" name=\"".$row->fieldname."\" id=\"date1\" size=\"20\" maxlength=\"20\" value=\"".$fromdate."\" />";              
                  }
                  echo "</td>";
                echo " </tr>";
              } 
?>
             </table>
<?php
            if($excelorlist==2 || $excelorlist ==0){
              echo '<input name="imageField" alt="Show Report on Screen" title="Show Report on Screen" type="image" src="'.$mosConfig_live_site.'/components/'.$option.'/images/jfile.gif" border="0" OnClick="if(checkform(this))submitbutton(\'showreport\'); return false" /> ';
            }
            
            if($excelorlist==1 || $excelorlist ==0) {
              echo '<input name="imageField" alt="Export Report to MS Excel" title="Export Report to MS Excel" type="image" src="'.$mosConfig_live_site.'/components/'.$option.'/images/jexcel.gif" border="0" OnClick="if(checkform(this))submitbutton(\'showexcell\'); return false" /> ';
            }
            
            echo '<input type="hidden" name="option" value="'.$option.'" />';
            echo '<input type="hidden" name="task" value="" />';
            echo '</form>';
}
  
  function showReport($rows, $title, $option, $cid){
      global $mosConfig_live_site, $mainframe, $my, $mosConfig_absolute_path;
      require($mosConfig_absolute_path."/administrator/components/".$option."/config.brightreporter.php");
      
      require_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/HTML_toolbar.php' );

      if($user!=0)
      {
        if($user > $my->gid){
          mosNotAuth();
          return;
        }        
      }      
      
      // Toolbar Bottom
      mosToolBar::startTable();
      mosToolBar::back( 'back' );
      mosToolBar::endtable();

      echo "<form action=\"index.php\" method=\"post\" name=\"adminForm\">";  
    ?>

            <table class="adminheading">
              <tr>
                  <th>
                    <?php echo $title;?>
                  </th>
              </tr>
             </table>
    <?php
         if(count($rows)>0)
          {

           echo ' <table class="adminlist">';
            echo '  <tr>';

                $fields = (array_keys($rows[0]));

                $columns = count($fields);
                    // Put the name of all fields to $out.
                for ($i = 0; $i < $columns; $i++) {
                echo '<th align="left">';
                echo $fields[$i];
                echo '</th>';
                }

            echo '</tr>';

            for($k=0; $k < count( $rows ); $k++) {
                    $row = $rows[$k];

                    echo '<tr class="row'.$k.'">';

                    foreach ($row as $value) {
                    echo '<td align="left">';
                          echo $value;
                    echo '</td>';
                        }

                }
          echo '</table>';

          }
        else
          {
            echo "Sorry no rows to display !";
          }
          
        echo '<input type="hidden" name="option" value="'.$option.'" />';
        echo '<input type="hidden" name="task" value="" />';
        echo '<input type="hidden" name="cid" value="'.$cid.'" />';
        echo '</form>';
  }

  function showExcell($rows, $title, $option, $cid){
      global $my, $mosConfig_absolute_path;
      require($mosConfig_absolute_path."/administrator/components/".$option."/config.brightreporter.php");
      if($user!=0)
      {
        if($user > $my->gid){
          mosNotAuth();
          return;
        }        
      }        
  
          $data = "" ;
          $sep = "\t"; //tabbed character
     if(count($rows)>0)      {
          //firstline is title + date
          
          $data .= "Report: \t ".$title."\t \t Date :\t" . date("d/m/Y"). "\t \t \n \n";
          
          $fields = (array_keys($rows[0]));

          $columns = count($fields);
              // Put the name of all fields to $out.
          for ($i = 0; $i < $columns; $i++) {
          $data .= $fields[$i].$sep;
          }
          $data .= "\n";

          for($k=0; $k < count( $rows ); $k++) {
                $row = $rows[$k];
                $line = '';

                foreach ($row as $value) {
                  $value = str_replace('"', '""', $value);
                  $line .= '"' . $value . '"' . "\t";
                }
                $data .= trim($line)."\n";
            }

          $data = str_replace("\r","",$data);

          if (count( $rows ) == 0) {
              $data .= "\n(0) Records Found!\n";
          }
          
          $data .= "\n \n \n Jeports \t Copyrights 2007 \t by Vandersluijs.nl \t Open Source License.\t Contact: theo@vandersluijs.nl \n";
      }
      else
      {$data = "\n(0) Records Found!\n";}
      
          header("Content-type: application/octet-stream");
          header("Content-Disposition: attachment; filename=jeports.xls");
          header("Pragma: no-cache");
          header("Expires: 0");
          header("Lacation: excel.htm?id=yes");
          print $data ;
          die();
      }
}
?>