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

// ensure this file is being included by a parent file
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') || $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_dailymessage'))) {
    mosRedirect('index2.php', _NOT_AUTH);
}

class HTML_BrightReporter
  {

 function displayConfigForm( $option  )
    {
      global $mosConfig_live_site, $mosConfig_cachepath, $my, $mosConfig_absolute_path, $acl;
      require($mosConfig_absolute_path."/administrator/components/".$option."/config.brightreporter.php");

      ?>
      <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
      <form action="index2.php" method="post" name="adminForm">
      <table class="adminheading">
      <tr>
        <th>
       Brightcode Reporter Settings
        </th>
      </tr>
      </table>

    <table class="adminform">
      <tr valign="top">
        <td>User rights</td>
        <td><?php 
        $showacl[] = mosHTML::makeOption( '0', 'Guest' );
        $showacl[] = mosHTML::makeOption( '1', 'Registered' );
        $showacl[] = mosHTML::makeOption( '2', 'Special' );
        echo mosHTML::selectList( $showacl, 'user', 'class="inputbox" size="3"', 'value', 'text', $user );
        ?></td>
        <td>Set the rights to see the reports in the frontend</td>
      </tr>

      <tr valign="top">
        <td>Excel Or List Or Both</td>
        <td>
         <?php
        $acopeningmode[] = mosHTML::makeOption( '0', 'Both' );
        $acopeningmode[] = mosHTML::makeOption( '1', 'Excel' );
        $acopeningmode[] = mosHTML::makeOption( '2', 'List' );
        echo mosHTML::selectList( $acopeningmode, 'excelorlist', 'class="inputbox" size="3"', 'value', 'text', $excelorlist );
      ?>
        </td>
        <td>Choose if you want users to only get an Excel file or a screen displayed list or both.</td>
      </tr>

    </table>

  		<input type="hidden" name="id" value="<?php echo $id; ?>" />
  		<input type="hidden" name="option" value="<?php echo $option; ?>" />
  		<input type="hidden" name="task" value="" />
  		<input type="hidden" name="act" value="config" />
  		<input type="hidden" name="boxchecked" value="0" />
  		</form>      
    <?php
    }
    function replace_carriage_return( $string)
    {
        return str_replace(array("\n\r", "\n", "\r"), ' ', str_replace("\"", "'", $string));
    }

  function showReport($jquery, $rows, $title, $option, $act, $cid,$time_diff_secs){
    ?>
    <style>
    .explain_query{
        display: none;
    }
    .explain_query>table.adminlist th{
        width: auto;
    }
    .explain_query_trigger{
        cursor: pointer;
    }
</style>
    <script>
        $( document ).ready(function() {
            $('.get_query_explain').click(function(){
                $('.get_query_explain').val('Please Wait...')
                $.post("./index2.php", {option: "<?php echo $option;?>",task: "show_explain",query:"<?php echo HTML_BrightReporter::replace_carriage_return($jquery);?>"}, function(result){
                    if(result){
                        var  obj = JSON.parse(result);
                        if(obj.result){
                            $('.explain_query').show().html(obj.res)
                        }else{
                            $('.explain_query').show().html('explain error')
                        }
                    }
                    $('.get_query_explain').val('Get Query Explain')
                });
            })
        });
    </script>
      <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                        <tr>
                  <th>
                    <?php echo $title;?>
                      <input type="button" style="cursor: pointer" class="get_query_explain" value="Get Query Explain">
                  </th>
              </tr>
              <tr>
                  <td>
                        Execution time sec ( <?php echo $time_diff_secs; ?> )
                    <div class="explain_query">
                    </div>
                  </td>
              </tr>
             </table>
            <table class="adminheading">
              <tr>
                  <td style="padding: 20px; background-color: #E5E5E5; line-height: 25px;">
                        <?php echo $jquery; ?>
                  </td>
              </tr>
            </table>
    <?php
                  echo HTML_BrightReporter::parse_rows($rows);

            echo '<input type="hidden" name="option" value="'.$option.'" />';

            // Act is also available as global variable
            echo '<input type="hidden" name="act" value="'.$act.'" />';

            // The value of task will be set by Mambo upon submit,
            // depending on which button is clicked
            echo '<input type="hidden" name="task" value="" />';
            echo '<input type="hidden" name="cid" value="'.$cid.'" />';
            echo '<input type="hidden" name="boxchecked" value="0" />';
            echo '<input type="hidden" name="hidemainmenu" value="0" />';
            echo '</form>';

      }
function parse_rows($rows){
     $table = '';
     if(count($rows)>0)
          {

            $table.='<table class="adminlist">';
            $table.= '  <tr>';

                $fields = (array_keys($rows[0]));
                array_unshift($fields,'#');
                $columns = count($fields);

                for ($i = 0; $i < $columns; $i++) {
                $table.= '<th align="left">';
                $table.= $fields[$i];
                $table.= '</th>';
                }

                $table.= '</tr>';

              for($k=0; $k < count( $rows ); $k++) {
                $row = $rows[$k];

                $table.= '<tr class="row'.$k.'">';
                $table.= '<td align="left">'.($k+1).'</td>';
                foreach ($row as $value) {
                $table.= '<td align="left">';
                      $table.= $value;
                $table.= '</td>';
                    }
                $table.= '</tr>';

            }

             $table.= '</table>';

          }
          else
          {
            $table= "Sorry no rows to display !";
          }
          return $table;
}
function showExcell($rows, $title, $option, $act, $cid,$variables){

          $data = "" ;
          $sep = "\t"; //tabbed character
     if(count($rows)>0)      {
          //firstline is title + date
          if($title == 'order emails' or $title == 'user_numbers' ){

                    $data = serialize($rows);

            }else{
                    $data .= "Report: \t ".$title."\t \t Date :\t" . date("d/m/Y"). "\n \n";
                    if($variables){
                      $data .= "Variables: \t ".$variables. "\n \n";
                    }

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
                            $value = strip_tags(str_replace('"', '""', $value));
                            $line .= '"' . $value . '"' . "\t";
                          }
                          $data .= trim($line)."\n";
                      }

                    $data = str_replace("\r","",$data);

                    if (count( $rows ) == 0) {
                        $data .= "\n(0) Records Found!\n";
                    }
            }
      }
      else
      {$data = "\n(0) Records Found!\n";}
          $fileTitle = $cid.'_'.strip_tags(str_replace(' ', '_', str_replace('  ', ' ', $title))).'.xls';
          header("Content-type: application/octet-stream");
          header("Content-Disposition: attachment; filename=$fileTitle");
          header("Pragma: no-cache");
          header("Expires: 0");
          header("Lacation: excel.htm?id=yes");
          print $data ;
          die();
      }


    function showGoogleSheets($jquery, $rows, $title, $option, $act, $cid){

        if(count($rows)>0)
        {
            $output_string_array = array();

            $output_string_array['values'][] = array_keys($rows[0]);

            foreach ($rows as $key_rows => $val_rows)
            {
                $output_string_array['values'][] = array_values($val_rows);
            }

            ?>
            <!--Add buttons to initiate auth sequence and sign out-->

            <button id="authorize-button" style="display: none;">Authorize</button>
            <button id="signout-button" style="display: none;">Sign Out</button>

            <pre id="content"></pre>

            <script type="text/javascript">

              var spreadsheetId = '';
              var spreadsheetUrl = '';
              var sheetId = '';

              // Client ID and API key from the Developer Console
              var CLIENT_ID = '420686801673-jena616s39s621tjh01em2ff5kvv3j9t.apps.googleusercontent.com';

              // Array of API discovery doc URLs for APIs used by the quickstart
              var DISCOVERY_DOCS = ["https://sheets.googleapis.com/$discovery/rest?version=v4"];

              // Authorization scopes required by the API; multiple scopes can be
              // included, separated by spaces.
              var SCOPES = 'https://www.googleapis.com/auth/analytics.readonly https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/spreadsheets';

              var authorizeButton = document.getElementById('authorize-button');
              var signoutButton = document.getElementById('signout-button');

              /**
               *  On load, called to load the auth2 library and API client library.
               */
              function handleClientLoad() {
                gapi.load('client:auth2', initClient);
              }

              /**
               *  Initializes the API client library and sets up sign-in state
               *  listeners.
               */
              function initClient() {
                gapi.client.init({
                  discoveryDocs: DISCOVERY_DOCS,
                  clientId: CLIENT_ID,
                  scope: SCOPES
                }).then(function () {
                  // Listen for sign-in state changes.
                  gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);

                  // Handle the initial sign-in state.
                  updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
                  authorizeButton.onclick = handleAuthClick;
                  signoutButton.onclick = handleSignoutClick;
                });
              }

              /**
               *  Called when the signed in status changes, to update the UI
               *  appropriately. After a sign-in, the API is called.
               */
              function updateSigninStatus(isSignedIn) {
                if (isSignedIn) {
                  authorizeButton.style.display = 'none';
                  signoutButton.style.display = 'block';

                  createSheet();
                } else {
                  authorizeButton.style.display = 'block';
                  signoutButton.style.display = 'none';
                }
              }

              /**
               *  Sign in the user upon button click.
               */
              function handleAuthClick(event) {
                gapi.auth2.getAuthInstance().signIn();
              }

              /**
               *  Sign out the user upon button click.
               */
              function handleSignoutClick(event) {
                gapi.auth2.getAuthInstance().signOut();
              }

              /**
               * Append a pre element to the body containing the given message
               * as its text node. Used to display the results of the API call.
               *
               * @param {string} message Text to be placed in pre element.
               */
              function appendPre(message) {
                var pre = document.getElementById('content');
                var textContent = document.createTextNode(message + '\n');
                pre.appendChild(textContent);
              }

                function clearSheet() {
                    gapi.client.sheets.spreadsheets.values.clear({
                      spreadsheetId: spreadsheetId,
                      range: 'A2:J'
                    }).then(function (response) {
                      appendPre(response);
                    }, function (response) {
                      appendPre('Error: ' + response.result.error.message);
                    });
                }

                function appendSheet() {
                    var responseJson = '<?php echo json_encode($output_string_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>';
                    gapi.client.sheets.spreadsheets.values.append({
                      spreadsheetId: spreadsheetId,
                      range: 'A1',
                      resource: responseJson,
                      valueInputOption: 'USER_ENTERED',
                    }).then(function (response) {
                        updateSheet();
                    }, function (response) {
                      appendPre('Error: ' + response.result.error.message);
                    });
                }

                function updateSheet() {
                gapi.client.sheets.spreadsheets.batchUpdate({
                    spreadsheetId: spreadsheetId,
                    "requests": [
                    {
                        "autoResizeDimensions": {
                            "dimensions": {
                              "sheetId": sheetId,
                              "dimension": "COLUMNS",
                              "startIndex": 0,
                              "endIndex": 20
                            }
                        }
                    }]
                }).then(function (response) {
                    document.getElementById('report_loader').style.display='none';
                    document.getElementById('content').innerHTML = 'The report will be opened in new window. If You don\'t see it yet - click here: <a target="_blank" href="'+spreadsheetUrl+'">'+spreadsheetUrl+'</a>';
                    window.open(spreadsheetUrl);
                }, function (response) {
                  appendPre('Error: ' + response.result.error.message);
                });
                }

                function createSheet() {
                document.getElementById('report_loader').style.display='block';
                gapi.client.sheets.spreadsheets.create({
                    "properties": {
                        "title": 'Bloomex report'
                    },
                    "sheets": [
                        {
                            "properties": {
                                "title": 'Data'
                            },
                        }
                    ],
                }).then(function (response) {

                    spreadsheetId = response.result.spreadsheetId;
                    spreadsheetUrl = response.result.spreadsheetUrl;
                    sheetId = response.result.sheets[0].properties.sheetId;

                    appendSheet();
                }, function (response) {
                  appendPre('Error: ' + response.result.error.message);
                });
                }

            </script>

            <script async defer src="https://apis.google.com/js/api.js"
              onload="this.onload=function(){};handleClientLoad()"
              onreadystatechange="if (this.readyState === 'complete') this.onload()">
            </script>
            <?php
          }
          else
          {
            echo "Sorry no rows to display !";
          }

        /*
      <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
              <tr>
                  <th>
                    <?php echo $title;?>
                  </th>
              </tr>
             </table>
            <table class="adminheading">
              <tr>
                  <td style="padding: 20px; background-color: #E5E5E5; line-height: 25px;">
                        <?php echo $jquery; ?>
                  </td>
              </tr>
            </table>
    <?php
        if(count($rows)>0)
         {

           echo '<table class="adminlist">';
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

           // Act is also available as global variable
           echo '<input type="hidden" name="act" value="'.$act.'" />';

           // The value of task will be set by Mambo upon submit,
           // depending on which button is clicked
           echo '<input type="hidden" name="task" value="" />';
           echo '<input type="hidden" name="cid" value="'.$cid.'" />';
           echo '<input type="hidden" name="boxchecked" value="0" />';
           echo '<input type="hidden" name="hidemainmenu" value="0" />';
           echo '</form>';
           */
     }

  function showAbout($option) {
    include("components/".$option."/about.brightreporter.php");
    }

  function displayReports(&$rows, &$pageNav, $option, $act,$search ){
            global $my, $mosConfig_live_site;

            mosCommonHTML::loadOverlib();
            ?>

            <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
              <tr>
                  <th>
                  Report List <input class="show_hide" type="button" value="Show Queries">
                  </th>

              </tr>

            <tr>
                <td align="right">
                     Filter:
                </td>
                <td>
                    <input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
                </td>
            </tr>

             </table>


                 <script>
                    jQuery(function() {
                        jQuery('.link_query').click(function( ){
                                var id = jQuery(this).attr('cb')
                                var  task = 'showlist'
                               var ob =  listItemTaskNew(id, task );
                               var href = "index2.php?option=<?php echo $option;?>&cid="+ob;
                                 jQuery(this).attr('href',href)
                                return true;

                        })
                        jQuery('.show_hide').click(function( ){
                            if(jQuery(this).hasClass('shown')){
                                jQuery('.query_column').hide()
                                jQuery(this).removeClass('shown').val('Show Queries')
                            }else{
                                jQuery('.query_column').show()
                                jQuery(this).addClass('shown').val('Hide Queries')
                            }
                        })
                    });
                    function listItemTaskNew( id, task ) {
                            var f = document.adminForm;
                            cb = eval( 'f.' + id );
                            if (cb) {
                                for (i = 0; true; i++) {
                                    cbx = eval('f.cb'+i);
                                    if (!cbx) break;
                                    cbx.checked = false;
                                } // for
                                cb.checked = true;
                                f.boxchecked.value = 1;
                                 return cb.value
                            }
                            return false;
                        }
                </script>
            <table class="adminlist">
              <tr>
                  <th width="50">
                  #
                  </th>
                  <th width="50">
                  <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
                  </th>
                  <th align="left" nowrap="nowrap">
                  Title
                  </th>
                  <th align="left" nowrap="nowrap">
                     Description
                  </th>
                  <th align="left" style="display: none" class="query_column" nowrap="nowrap">
                  Query
                  </th>
              </tr>
            <?php
            $k = 0;
            for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                $row = &$rows[$i];
                mosMakeHtmlSafe($row);

                $img  = $row->block ? 'publish_x.png' : 'tick.png';
                $task   = $row->block ? 'unblock' : 'block';
                $alt  = $row->block ? 'Enabled' : 'Blocked';

                $checked    = mosHTML::idBox( $i, $row->id );
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td align="center">
                    <?php echo $row->id; ?>
                    </td>
                    <td align="center">
                    <?php echo $checked; ?>
                    </td>
                   <!-- <a href="index2.php?option=<?php echo $option;?>&act=<?php echo $act;?>&task=showexcell&cid=<?php echo $row->id;?>"><img src="<?php echo $mosConfig_live_site;?>/components/<?php echo $option;?>/images/excel.png" border="0" /></a> -->
                    <td align="left">
                    <!--<a href="#" class = 'link_query' cb='cb<?php echo $i;?>'>-->
                    <a href="index2.php?option=<?php echo $option;?>&act=<?php echo $act;?>&cid=<?php echo $row->id;?>">
                    <?php
                      echo $row->title;
                    ?></a>
                    </td>
                    <td align="left" nowrap="nowrap">
                        <?php echo $row->memo;?>
                    </td>
                    <td align="left" style="display: none" class="query_column">
                    <?php echo $row->jquery;?>
                    </td>

                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
            </table>
<?php

            // Option (current component folder name) is available
            // as global variable

            echo $pageNav->getListFooter();
            echo '<input type="hidden" name="option" value="'.$option.'" />';

            // Act is also available as global variable
            echo '<input type="hidden" name="act" value="'.$act.'" />';

            // The value of task will be set by Mambo upon submit,
            // depending on which button is clicked
            echo '<input type="hidden" name="task" value="" />';

            echo '<input type="hidden" name="boxchecked" value="0" />';
            echo '<input type="hidden" name="hidemainmenu" value="0" />';
            echo '</form>';
      }

  function PrepReport($cid, $option, $act, $title, $jquery, $rows){
          global $mosConfig_live_site, $mainframe, $database;
          echo "<script type=\"text/javascript\" src=\"$mosConfig_live_site/includes/js/joomla.javascript.js\"></script>";
          mosCommonHTML::loadOverlib();
          mosCommonHTML::loadCalendar();
?>
            <script type="text/javascript">
              function checkform (form)
              {
                  return true ;
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
            <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
              <tr>
                  <th>
                  <?php echo $title;?> - Report Variables
                  </th>
              </tr>
             </table>
            <input type="hidden" name="cid[]" value="<?php echo $cid;?>" />
             <table class="adminlist">
            <?php
              foreach($rows as $row){
                echo "<tr>";
                  echo "<td width='15%'>".str_replace('_', ' ', $row->fieldname)."</td>";
                  echo "<td>";
                  if($row->fieldkindid == 1){ //DateBox
                      echo "<input class=\"text_area\" type=\"text\" name=\"".$row->fieldname."\" id=\"".$row->fieldname."\" size=\"10\"  value=\"".(isset($fromdate) ? $fromdate : '')."\" />
                      <input name=\"reset\" type=\"reset\" class=\"button\" onclick=\"return showCalendar('".$row->fieldname."', 'y-mm-dd');\" value=\"...\" />";
                  }elseif($row->fieldkindid == 2 || $row->fieldkindid == 3){//SelectBox && //MultiSelectBox
                      $options = array();
                     if($row->fieldkindid == 3){
                         $list='size="4" class="inputbox form-control selectpicker" multiple="true"  data-live-search="true"';
                         $selectName=$row->fieldname.'[]';
                     }else{
                         $list='size="4" class="inputbox form-control selectpicker"  data-live-search="true"';
                         $selectName=$row->fieldname;
                         $options[0]->value = '';
                         $options[0]->text = 'Select';
                     }
                     if(stristr($row->fieldcode, "select")){
                          $database->setQuery( $row->fieldcode );
                              if(count($database->loadRowList())>0){
                                foreach($database->loadRowList() as $field)
                                {
                                  $options[] = mosHTML::makeOption( $field[0], $field[1]);
                                }
                              }
                     }
                     elseif(is_array(explode ("|", $row->fieldcode))){
                         $fields = explode ("|", $row->fieldcode);
                         foreach($fields as $field)
                          {
                            $options[] = mosHTML::makeOption( $field, $field);
                          }
                     }
                      echo '<div class="container-fluid"><div class="row"><div class="col-xs-12 padding0">';
                            echo mosHTML::selectList( $options, $selectName, $list, 'value', 'text');
                      echo '</div></div></div>';
                  }elseif($row->fieldkindid == 4){//StringField
                      echo "<input class=\"text_area\" type=\"text\" name=\"".$row->fieldname."\" id=\"date1\" size=\"20\"  value=\"".$fromdate."\" />";
                  }elseif ($row->fieldkindid == 5) {
                      echo "<input class=\"text_area datetimepicker\" type=\"text\" name=\"" . $row->fieldname . "\" id=\"" . $row->fieldname . "\" size=\"20\"  value=\"\" />";
                  }
                  echo "</td>";
                echo " </tr>";
              }
            echo "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js\"></script>";
            echo "<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js\"></script>";
            echo "<link href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\" rel=\"stylesheet\" />";
            echo "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js\"></script>";
            echo "<link href=\"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css\" rel=\"stylesheet\" />";
            echo "<style>
                        .btn-group, .btn-group-vertical{position: static !important;}
                        .bootstrap-select.btn-group .dropdown-menu {min-width: max-content;}
                        .padding0{padding: 0}
                  </style>";

?>

             </table>
<?php
            echo '<input name="imageField" alt="Show Report on Screen" title="Show Report on Screen" type="image" src="'.$mosConfig_live_site.'/components/'.$option.'/images/jfile.gif" border="0" OnClick="if(checkform(this))submitbutton(\'showreport\'); return false" />';

            echo '<input name="imageField" alt="Export Report to MS Excel" title="Export Report to MS Excel" type="image" src="'.$mosConfig_live_site.'/components/'.$option.'/images/jexcel.gif" border="0" OnClick="if(checkform(this))submitbutton(\'showexcell\'); return false" /> ';
            echo '<input name="imageField" alt="Show Report on Screen with Explain" title="Show Report on Screen with Explain" type="image" src="'.$mosConfig_live_site.'/components/'.$option.'/images/jfile.gif" border="0" OnClick="if(checkform(this))submitbutton(\'showexplain\'); return false" /> ';
//            echo '<input name="imageField" alt="Export Report to Google sheet" title="Export Report to Google sheet" type="image" src="'.$mosConfig_live_site.'/components/'.$option.'/images/google_sheet.png" border="0" OnClick="if(checkform(this))submitbutton(\'google_sheets\'); return false" /> ';
            // Option (current component folder name) is available
            // as global variable
            echo '<input type="hidden" name="option" value="'.$option.'" />';

            // Act is also available as global variable
            echo '<input type="hidden" name="act" value="'.$act.'">';

            // The value of task will be set by Mambo upon submit,
            // depending on which button is clicked
            echo '<input type="hidden" name="task" value="" />';

            echo '<input type="hidden" name="boxchecked" value="1" />';
            echo '<input type="hidden" name="hidemainmenu" value="0" />';
            echo '</form>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"/ >
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
        <script>
            jQuery(\'.datetimepicker\').datetimepicker({
                  format:\'Y-m-d H:i:s\',
                  step:30
                });
            $(function() {
                 $(\'.selectpicker\').selectpicker();
            });
        </script>';
}

  function show_tables($ar){
      foreach ($ar as $k => $v ) {
        echo '<h3 class="toggler" onclick="switchMenu(\''.$k.'\');">(<a href="javascript:void(0)" OnClick="insertAtCursor(document.adminForm.jquery, \'`'.$k.'` \')">add</a>) '.$k.'</h3>';
          if (is_array($ar[$k])) {
            HTML_BrightReporter::show_fields($ar[$k], $k);  
           }
        }
      
      echo '<script type="text/javascript">';
      foreach ($ar as $k => $v ) {
        echo 'document.getElementById("'.$k.'").style.display = "none";';
      }
      echo '</script>';
    }

  function show_fields($ar, $k){
        echo '<div class="element atStart" id="'.$k.'"><P>';
        echo '(<a href="javascript:void(0)" OnClick="insertAtCursor(document.adminForm.jquery, \'* \')">add</a>) * (all fields)<br />'; 
        foreach ($ar as $k => $v ) {		
            echo '(<a href="javascript:void(0)" OnClick="insertAtCursor(document.adminForm.jquery, \'`'.$k.'` \')">add</a>) '.$k.' ('.$v.')<br />';
          if (is_array($ar[$k])) {
            HTML_BrightReporter::show_fields($ar[$k]);  
            }        
         }
        echo '</P></div>';
    }

  function addEditReport($option, $cid, $title, $jquery, $act, $tablelist, $variables, $fieldid, $memo, $levels = array())
      {
      global $database;
        ?>
      	<script type="text/javascript">
          <!--
          function switchMenu(obj) {
          	var el = document.getElementById(obj);
          	if ( el.style.display != "none" ) {
          		el.style.display = 'none';
          	}
          	else {
          		el.style.display = '';
          	}
          }
          //-->
    	</script>
      
      <style>
        .toggler {
        	color: #222;
        	margin: 0;
        	padding: 2px 5px;
        	background: #eee;
        	border-bottom: 1px solid #ddd;
        	border-right: 1px solid #ddd;
        	border-top: 1px solid #f5f5f5;
        	border-left: 1px solid #f5f5f5;
        	font-size: 11px;
        	font-weight: normal;
        	font-family: 'Andale Mono', sans-serif;
          text-align:left;
          cursor: pointer
        }
         
        .element {
         text-align:left;
        	font-size: 10px;
        	font-weight: normal;
        	font-family: 'Andale Mono', sans-serif;   
        }
         
        .element p {
        	margin: 0;
        	padding: 4px 4px 4px 10px;
          text-align:left;
        	font-size: 10px;
        	font-weight: normal;
        	font-family: 'Andale Mono', sans-serif;    
        }
         
        .float-right {
        	padding:10px 20px;
        	float:right;
        }
         
        blockquote {
        	text-style:italic;
        	padding:5px 0 5px 30px;
        }
      </style>
    <script language="javascript" type="text/javascript">
    <!--
    /* This script and many more are available free online at
    The JavaScript Source!! http://javascript.internet.com
    Created by: Turnea Iulian :: http://www.eurografic.ro */

    function iObject() {
          this.i;
          return this;
    }

    var myObject=new iObject();
    myObject.i=0;
    var myObject2=new iObject();
    myObject2.i=0;
    store_text=new Array();

    //store_text[0] store initial textarea value
    store_text[0]="";


    function insertAtCursor(myField, myValue) {
    //IE support
    countclik(myField)

          if (document.selection) {
            myField.focus();
            sel = document.selection.createRange();
            sel.text = myValue;
          }
          //MOZILLA/NETSCAPE support
          else if (myField.selectionStart || myField.selectionStart == '0') {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            myField.value = myField.value.substring(0, startPos)
            + myValue
            + myField.value.substring(endPos, myField.value.length);
          } else {
            myField.value += myValue;
          }
    }

    function countclik(tag) {
          myObject.i++;
          var y=myObject.i;
          var x=tag.value;
          store_text[y]=x;
    }

    function undo(tag) {
          if ((myObject2.i)<(myObject.i)) {
            myObject2.i++;
          } else {
            alert("Finish Undo Action");
          }
          var z=store_text.length;
          z=z-myObject2.i;
          if (store_text[z]) {
          	tag.value=store_text[z];
          } else {
          	tag.value=store_text[0];
          }
    }

    function redo(tag) {
          if((myObject2.i)>1) {
            myObject2.i--;
          } else {

          }
          var z=store_text.length;
          z=z-myObject2.i;
          if (store_text[z]) {
            tag.value=store_text[z];
          } else {
          tag.value=store_text[0];
          }
    }
    // -->
    </script>

           <form action="index2.php" method="post" name="adminForm">
          <table class="adminheading" width="100%">
            <tr>
                <th>
                <?php if($cid>0){echo "Edit Report";}else{echo "New Report";}?>
                </th>
            </tr>
           </table>
           <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top">
                
                  <table  class="adminlist">
                    <tr>
                      <td align="left">Title</td>
                      <td align="left"><input type="text" name="title" value="<?php echo $title;?>" />
                      <input type="hidden" name="cid" value="<?php echo $cid;?>" />
                      <input type="hidden" name="id" value="<?php echo $cid;?>" /></td>
                    </tr>
                    <tr>
                      <td align="left">Description</td>
                      <td align="left"><textarea name="memo" id="memo" cols="70" rows="2"><?php echo $memo;?></textarea></td>
                    </tr>
                    <tr>
                      <td align="left"></td>        
                      <td align="left">
                      <a href="javascript:void(0)" OnClick="insertAtCursor(document.adminForm.jquery, 'SELECT ')">SELECT</a> | 
                      <a href="javascript:void(0)" OnClick="insertAtCursor(document.adminForm.jquery, 'FROM ')">FROM</a> | 
                      <a href="javascript:void(0)" OnClick="insertAtCursor(document.adminForm.jquery, 'WHERE ')">WHERE</a> | 
                      <a href="javascript:void(0)" OnClick="insertAtCursor(document.adminForm.jquery, 'ORDER BY ')">ORDER BY</a> | 
                      <a href="javascript:void(0)" OnClick="undo(document.adminForm.jquery);">Undo last</a> | 
                      <a href="javascript:void(0)" OnClick="if (confirm('Are you sure you want to clear the Query?')) document.adminForm.jquery.value='';">Clear Query</a>
                      
                      </td>
                    </tr>
                    <tr>
                      <td align="left">Query</td>
                      <td align="left"><textarea class="autoExpand" name="jquery" id="jquery" cols="100" rows="5" onkeydown="countclik(document.adminForm.jquery);"><?php echo $jquery;?></textarea></td>
                    </tr>
                    <?php
                      $access_levels = array(
                          1 => 'Director+',
                          2 => 'Sales Managers',
                          3 => 'Customer Service Managers',
                          4 => 'Sales and Customer Service',
                          5 => 'Production'
                      );
                      ?>
                      <tr>
                          <td align="left">Access levels</td>
                          <td align="left">
                              <?php
                              foreach ($access_levels as $k => $v) {
                                  ?>
                                  <div>
                                      <input type="checkbox" id="levels" name="levels[]" value="<?php echo $k; ?>" <?php echo in_array($k, $levels) ? 'checked' : ''; ?>>
                                      <label for="levels"><?php echo $v; ?></label>
                                  </div>
                                  <?php
                              }
                              ?>
                          </td>
                      </tr>
                  </table>
<?php 
        if($cid>0){
?>
                <table class="adminheading" width="100%">
                  <tr>
                      <th>
                      Variable fields
                      </th>
                  </tr>
                 </table>
                <table width="100%"  class="adminform">
                  <tr>
                    <th>&nbsp;</th>
                    <th>Name</th>
                    <th>Kind</th>
                    <th>Code</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>			
                  </tr>
<?php 
                function GetKindName($nr)
                  {
                  switch ($nr)
              			{		
                			case 1: 
                        return "DateBox";
                        break;
                      case 2:
                        return "SelectBox";
                        break;
                      case 3:
                        return "MultiSelectBox";                  
                        break;
                      case 4:
                        return "StringField";                  
                        break;
                      case 5:
                          return "DateTimeBox";
                          break;
                    }
                  }
               if($variables!=0){
                foreach($variables as $row){ 
                  if($row->id != $fieldid){
?>  
                  <tr>
                    <td valign="top"><?php echo $row->id;?></td>
                    <td valign="top">(<a href="javascript:void(0)" OnClick="insertAtCursor(document.adminForm.jquery, '${<?php echo $row->fieldname;?>}')">add</a>) <?php echo $row->fieldname;?></td>
                    <td valign="top"><?php echo GetKindName($row->fieldkindid);?></td>
                    <td valign="top"><?php echo $row->fieldcode;?></td>
                    <td valign="top"><a href="javascript:void(0)" OnClick="document.adminForm.fieldid.value=<?php echo $row->id;?>;submitbutton('editfield');">edit</a></td>
                    <td valign="top"><a href="javascript:void(0)" OnClick="if(confirm('Are you sure you want to delete this field?')){document.adminForm.fieldid.value=<?php echo $row->id;?>;submitbutton('deletefield');}">delete</a></td>	
                  </tr>
<?php 
                  }else{
?>
                  <tr>
                    <td valign="top">&nbsp;<input name="fieldid" type="hidden" value="<?php echo $row->id;?>" /></td>
                    <td valign="top"><input type="text" name="fieldname" value="<?php echo $row->fieldname;?>" /></td>
                    <td valign="top"><select name="fieldkindid" id="fieldkindid">
                        <option value="1" <?php if(1==$row->fieldkindid){echo "selected";}?>>DateBox</option>
                        <option value="2" <?php if(2==$row->fieldkindid){echo "selected";}?>>SelectBox</option>
                        <!-- <option value="3" <?php if(3==$row->fieldkindid){echo "selected";}?>>MultiSelectBox</option> -->
                        <option value="4" <?php if(4==$row->fieldkindid){echo "selected";}?>>StringField</option>
                        <option value="5" <?php if (5== $row->fieldkindid) { echo "selected";} ?>>DateTimeBox</option>
                        </select>
                    </td>
                    <td valign="top"><textarea name="fieldcode" cols="40" rows="4" ><?php echo $row->fieldcode;?></textarea></td>
                    <td valign="top">&nbsp;</td>
                    <td valign="top">&nbsp;</td>	
                  </tr>
<?php
                  }
                }            
                
                if(!$fieldid || $fieldid==0) {
?>
                  <tr>
                    <td valign="top">&nbsp;<input name="fieldid" type="hidden" value="" /></td>
                    <td valign="top"><input type="text" name="fieldname" value="" /></td>
                    <td valign="top"><select name="fieldkindid" id="fieldkindid">
                        <option value="1">DateBox</option>
                        <option value="2">SelectBox</option>
                        <option value="3">MultiSelectBox</option>
                        <option value="4">StringField</option>
                        <option value="5">DateTimeBox</option>
                        </select>
                    </td>
                    <td valign="top"><textarea name="fieldcode" cols="40" rows="4" ></textarea><br />Query or list (eg. USER1|USER2|USER3|USER4)<br><br> Query must be like this SELECT shopper_group_id,shopper_group_name FROM `jos_vm_shopper_group` (value,text)</td>
                    <td valign="top">&nbsp;</td>
                    <td valign="top">&nbsp;</td>	
                  </tr>
<?php 
                  }
                }
?>
                
                </table>
                   <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
                 
                      <script> console.log("hello kitty");
                 
  $('textarea').each(function () {
  this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
}).on('input', function () {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});

                      </script>
                 
<?php   } else { ?>
        <B>You can add Variable fields after you save or apply the report.</B>
<?php 
        } 
?>
                </td>
                <td valign="top">

                <table class="adminform">
                  <tr>
                      <th>Joomla! Tables + Fields</th>
                  </tr>
                 </table>

            		<div style="width:100%;height:300px;background-color:#ffffff;overflow:auto;">
<?php 
                      $result = $database->getTableFields( $tablelist );
                      HTML_BrightReporter::show_tables($result);
?>
            		</div>	
                
            	</td>
              
              </tr>
            </table>
<?php

            echo '<input type="hidden" name="option" value="'.$option.'" />';
            echo '<input type="hidden" name="act" value="'.$act.'" />';
            echo '<input type="hidden" name="task" value="" />';
            echo '<input type="hidden" name="boxchecked" value="0" />';
            echo '<input type="hidden" name="hidemainmenu" value="0" />';
            echo '</form>';
      }
}
?>