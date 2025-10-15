<?php

// ensure this file is being included by a parent file
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') || $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_dailymessage'))) {
    mosRedirect('index2.php', _NOT_AUTH);
}

class HTML_ExtendedReports
  {

  function showReport($rows, $script, $option){
    ?>
      <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
              <tr>
                  <th>
                    <?php echo $script;?>
                  </th>
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
                echo '<td>';
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
            echo '</form>';

      }

function showExcell($rows, $script,$variables){

          $data = "" ;
          $sep = "\t"; //tabbed character
     if(count($rows)>0)      {


                    $data .= "Report: \t ".$script."\t \t Date :\t" . date("d/m/Y"). "\n \n";
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
      else
      {$data = "\n(0) Records Found!\n";}
      $fileTitle = strip_tags(str_replace(' ', '_', str_replace('  ', ' ', $script))).'.xls';

          header("Content-type: application/octet-stream");
          header("Content-Disposition: attachment; filename=$fileTitle");
          header("Pragma: no-cache");
          header("Expires: 0");
          header("Lacation: excel.htm?id=yes");
          print $data ;
          die();
      }
      
      
    function showGoogleSheets($rows){
        
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


     }

function PrepReport($script, $option){
          global $mosConfig_live_site;
          echo "<script type=\"text/javascript\" src=\"$mosConfig_live_site/includes/js/joomla.javascript.js\"></script>";
          mosCommonHTML::loadOverlib();
          mosCommonHTML::loadCalendar();
?>
            <script type="text/javascript">

            function submitbutton(pressbutton) {
			var form = document.adminForm;
			form.task.value=pressbutton;
			  form.submit();
		    };
              function checkform ()
              {
                    var res = true
                  $('.include_script input').each(function( index ) {
                      if($(this).val()==''){
                          alert( "Please enter a "+$(this).attr('name')+"." );
                          res = false;
                      }
                  });
                    $('.include_script select').each(function( index ) {
                      if($(this).val()==''){
                          alert( "Please enter a "+$(this).attr('name')+"." );
                          res = false;
                      }
                  });
                return res ;
              }
            </script>
            <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
              <tr>
                  <th>
                  <?php echo $script;?> - Report Variables
                  </th>
              </tr>
             </table>
<div class="include_script">
            <?php
             include(__DIR__.'/scripts/'.$script.'.php');
            ?>
</div>
<?php
            echo '<br><br><input name="imageField" alt="Show Report on Screen" title="Show Report on Screen" type="image" src="components/'.$option.'/images/jfile.gif" border="0" OnClick="if(checkform())submitbutton(\'showreport\'); return false" />';
            echo '<input name="imageField" alt="Export Report to MS Excel" title="Export Report to MS Excel" type="image" src="components/'.$option.'/images/jexcel.gif" border="0" OnClick="if(checkform())submitbutton(\'showexcell\'); return false" /> ';
            echo '<input name="imageField" alt="Export Report to Google sheet" title="Export Report to Google sheet" type="image" src="components/'.$option.'/images/google_sheet.png" border="0" OnClick="if(checkform())submitbutton(\'google_sheets\'); return false" /> ';
            echo '<input type="hidden" name="option" value="'.$option.'" />';
            echo '<input type="hidden" name="task" value="" />';
            echo '<input type="hidden" name="script" value="'.$script.'" />';
            echo '</form>';
}

  function displayScripts(&$rows, $option ){

            mosCommonHTML::loadOverlib();
            ?>

            <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
              <tr>
                  <th>
                  Extended Reports List
                  </th>
              </tr>
             </table>
            <table class="adminlist">
              <tr>
                  <th width="20">
                  #
                  </th>
                  <th align="left" nowrap="nowrap">
                  Title
                  </th>
              </tr>
            <?php
            foreach($rows as  $k=>$row){
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td >
                    <?php echo $k+1; ?>
                    </td>
                    <td >
                    <a href="index2.php?option=<?php echo $option;?>&task=PrepReport&script=<?php echo $row;?>">
                    <?php
                      echo str_replace('_',' ',$row);
                    ?></a>
                </tr>
                <?php
            }
            ?>
            </table>
<?php
            echo '<input type="hidden" name="option" value="'.$option.'" />';
            echo '<input type="hidden" name="task" value="" />';
            echo '<input type="hidden" name="boxchecked" value="0" />';
            echo '<input type="hidden" name="hidemainmenu" value="0" />';
            echo '</form>';
      }


}
?>