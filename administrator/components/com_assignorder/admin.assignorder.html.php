<?php
/**
* @version $Id: admin.Category.html.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Category
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* @package Joomla
* @subpackage Category
*/
class HTML_AssignOrder {
	
	//============================================= POSTAL CODE OPTION ===============================================
	function showAssignOrder( &$rows, $warehouses, &$pageNav, $option ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Postal Code For The WareHouse Manager</th>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td align="right">

                    <b>Postal Code :</b>&nbsp;<input name="postcode_name_filter" type="text" size="30" maxlength="30" value="<?php echo isset($_REQUEST['postcode_name_filter']) ? htmlspecialchars($_REQUEST['postcode_name_filter']) : ''; ?>">&nbsp;&nbsp;
                    <b>City Name :</b>&nbsp;<input name="postcode_city_filter" type="text" size="30" maxlength="30" value="<?php echo isset($_REQUEST['postcode_city_filter']) ? htmlspecialchars($_REQUEST['postcode_city_filter']) : ''; ?>">&nbsp;&nbsp;
					<b>Alphabet Short:</b>&nbsp;
					<?php 
						if(isset($_REQUEST['short_filter']) AND $_REQUEST['short_filter'] == "DESC" ) {
							$selected2	= "selected";
							$selected	= "";
						}else {
							$selected	= "selected";
							$selected2	= "";
						}
					?>
					
					<select name="short_filter" size="1" onchange="document.adminForm.submit();">
						<option value="ASC" <?php echo $selected; ?>>Ascending</option>
						<option value="DESC" <?php echo $selected2; ?>>Descending</option>
					</select>
                    <b>Warehouse Sort:</b>
                    <select name="warehouse_id_filter" size="1" onchange="document.adminForm.submit();">
                        <option value="0">All</option>
                        <?php
                        foreach ($warehouses as $wh_obj) {
                            ?>
                            <option value="<?php echo $wh_obj->warehouse_id; ?>" <?php echo (isset($_REQUEST['warehouse_id_filter']) AND $_REQUEST['warehouse_id_filter'] == $wh_obj->warehouse_id ? 'selected' : ''); ?>><?php echo $wh_obj->warehouse_name; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <b>Country Sort:</b>
                    <select name="country_filter" size="1" onchange="document.adminForm.submit();">
                            <option value="0">All</option>
                            <option <?php echo (isset($_REQUEST['country_filter']) AND $_REQUEST['country_filter'] =='AUS') ? 'selected' : '';?> value="AUS">Australia</option>
                            <option <?php echo (isset($_REQUEST['country_filter']) AND $_REQUEST['country_filter'] =='NZL') ? 'selected' : '';?> value="NZL">New Zealand</option>
                    </select>
                    <input class="button" type="submit" name="search" value="Search">
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="5%">#</th>
			<th width="5%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
            <th width="5%" class="title" align="left">Province</th>
            <th width="15%" class="title" align="left">City</th>
            <th width="5%" nowrap="nowrap" >Country</th>
            <th width="10%" class="title">Postal Code</th>
            <th width="20%" nowrap="nowrap" align="left">Warehouse Name</th>
            <th width="10%" nowrap="nowrap" align="left">Same Day WareHouse</th>
            <th width="5%" nowrap="nowrap" align="left">Days In Route</th>
            <th width="5%" nowrap="nowrap" >Delivery Surcharge</th>
            <th width="5%" nowrap="nowrap" align="left">Deliverable</th>
            <th width="5%" nowrap="nowrap" >Out of Town</th>
            <th width="5%" nowrap="nowrap" >Block Shipstation</th>
			<th width="5%" align="left">Published</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$link 	= 'index2.php?option=com_assignorder&act=postal_code&task=editA&hidemainmenu=1&id='. $row->id;

			$img 	= $row->deliverable ? 'tick.png' : 'publish_x.png';
			$task 	= $row->deliverable ? 'undeliverable' : 'deliverable';
			$alt 	= $row->deliverable ? 'Deliverable' : 'Undeliverable';

			$task2 	= $row->published ? 'unpublish' : 'publish';
            $img2 	= $row->published ? 'tick.png' : 'publish_x.png';
            $alt2 	= $row->published ? 'Published' : 'Unpublished';


            $img3 = $row->out_of_town ? 'tick.png' : 'publish_x.png';
            $task3 = $row->out_of_town ? 'not_out_of_town' : 'out_of_rown';
            $alt3 = $row->out_of_town ? 'Out of Town' : 'Default';

            $img4 = $row->block_shipstation ? 'tick.png' : 'publish_x.png';
            $task4 = $row->block_shipstation ? 'unblock_shipstation' : 'block_shipstation';
            $alt4 = $row->block_shipstation ? 'Block Shipstation' : 'Default';

			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>
                                <td align="left"><?php echo $row->province; ?></td>
                                <td align="left"><?php echo $row->city; ?></td>
                <td  align="center"><?php echo $row->country; ?></td>
				<td style="text-align:left;"><a href="<?php echo $link; ?>" title="Edit Postal Code Option"><?php echo $row->postal_code; ?></a></td>
				<td align="left"><?php echo ($row->warehouse_name)?$row->warehouse_name." ( {$row->warehouse_code} ) ":"NO WAREHOUSE ASSIGNED"; ?></td>
				<td align="left"><?php echo ($row->same_warehouse_name)?$row->same_warehouse_name." ( {$row->same_warehouse_code} ) ":"NO WAREHOUSE ASSIGNED"; ?></td>
                <td align="left"><?php echo $row->days_in_route; ?></td>
                <td  align="center"><?php echo $row->additional_delivery_fee; ?></td>
                <td align="left">
                    <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
                        <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                    </a>
                </td>
                <td  align="center">
                    <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>', '<?php echo $task3; ?>')">
                        <img src="images/<?php echo $img3; ?>" width="12" height="12" border="0" alt="<?php echo $alt3; ?>" />
                    </a>
                </td>
                <td  align="center">
                    <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>', '<?php echo $task4; ?>')">
                        <img src="images/<?php echo $img4; ?>" width="12" height="12" border="0" alt="<?php echo $alt4; ?>" />
                    </a>
                </td>
                <td align="left">
                    <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task2;?>')">
                        <img src="images/<?php echo $img2;?>" width="12" height="12" border="0" alt="<?php echo $alt2; ?>" />
                    </a>
                </td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="postal_code" />
		<input type="hidden" name="task" value="" />		
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
                <div id="assignorder_xlsx" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Parse CSV File</h4>
                            </div>
                            <div class="modal-body">
                                <form class="form-inline assignorder_form" role="form" enctype="multipart/form-data">
                                    <div class="form-group">
                                      <input type="file" name="xlsxfileform" id="xlsxfileform">
                                    </div>
                                    <div class="form-group">
                                      <button type="submit" class="btn btn-success parse_file" name="parse_file">Upload</button>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary get_file" name="get_file">Download Correct Format</button>
                                    </div>
                                </form>
                                <div class="assignorder_loader"></div>
                                <div class="assignorder_result"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <script type="text/javascript">
                    function tryAgain() {
                        //e.preventDefault();

                        $('.assignorder_result').html('').hide();
                        $('.assignorder_form').trigger('reset').show();
                    }

                    $( document ).ready(function() {

                        $('.get_file').click(function(e) {
                            e.preventDefault();

                            $('.assignorder_form').hide();
                            $('.assignorder_loader').show();

                            $.ajax({
                                url: './index2.php',
                                data: {
                                    'option': '<?php echo $option; ?>',
                                    'task': 'getCsv'
                                },
                                async: true,
                                cache: false,
                                method: 'GET',
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function (data) {
                                    $('.assignorder_loader').hide();
                                    tryAgain();
                                    var a = document.createElement('a');
                                    var url = window.URL.createObjectURL(data);
                                    a.href = url;
                                    a.download = 'postalcodes.csv';
                                    a.click();
                                    window.URL.revokeObjectURL(url);
                                },
                                error: function() {
                                    console.log('Ajax error.');
                                    $('.assignorder_result').html('Error: Ajax error. <button type="button" class="btn btn-default assignorder_try_again" onclick="tryAgain();">Try Again</button>').show();
                                    $('.assignorder_loader').hide();
                                }
                            });
                        });

                        $('.parse_file').click(function(e) {
                            e.preventDefault();

                            $('.assignorder_form').hide();
                            $('.assignorder_loader').show();

                            var file_data = $('#xlsxfileform').prop('files')[0];
                            var form_data = new FormData();
                            form_data.append('file', file_data);
                            form_data.append('option', '<?php echo $option; ?>');
                            form_data.append('task', 'parseCsv');

                            $.ajax({
                                url: './index2.php',
                                contentType: false,
                                processData: false,
                                data: form_data,
                                type: 'post',
                                dataType: 'json',
                                cache: false,
                                async: true,
                                success: function(json) {
                                    if (json.result) {
                                        $('.assignorder_result').text('Success: Added '+json.sizeof_inserts+' new rules! Page will be reload.').show();
                                        document.location.reload(true);
                                    }
                                    else {
                                        $('.assignorder_result').html('Error: '+json.error+'. <button type="button" class="btn btn-default assignorder_try_again" onclick="tryAgain();">Try Again</button>').show();
                                    }
                                    $('.assignorder_loader').hide();
                                },
                                error: function() {
                                    console.log('Ajax error.');
                                    $('.assignorder_result').html('Error: Ajax error. <button type="button" class="btn btn-default assignorder_try_again" onclick="tryAgain();">Try Again</button>').show();
                                    $('.assignorder_loader').hide();
                                }
                            });
                        });

                    });
                </script>
		<?php
	}


	function editAssignOrder( &$row, $option, &$lists ) {
	?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if ( form.postal_code.value == "" ) {
				alert( "You must provide a postal code." );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>

        <style>
            /* Always set the map height explicitly to define the size of the div
             * element that contains the map. */
            #map {
                height: 100%;
            }
            /* Optional: Makes the sample page fill the window. */
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
        </style>


        <script>
            var geocoder;
            var map;
            var markers = [];
            function initMap() {
                geocoder = new google.maps.Geocoder();
                //Default setup
                var latlng = new google.maps.LatLng(-34.397, 150.644);
                var myOptions = {
                    zoom: 6,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                map = new google.maps.Map(document.getElementById("map"), myOptions);
                codeAddress('<?php echo $row->postal_code;?>')
            }
            function reinitmap(text){
                codeAddress(text.value)

            }
            function codeAddress(zipCode) {
                geocoder.geocode( { 'address': zipCode+',Australia'}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        //Got result, center the map and put it out there
                        map.setCenter(results[0].geometry.location);
                        for (var i = 0; i < markers.length; i++) {
                            markers[i].setMap(null);
                        }
                        var marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location
                        });
                        markers.push(marker);
                    } else {
                        alert("Geocode was not successful for the following reason: " + status);
                    }
                });
            }
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDFRP59njojtx0eXlHmvYyGAtWZFwvRSLU&callback=initMap"
                async defer></script>
		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
			Postal Code Option:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Postal Code Option Detail</th>
			<tr>
			<tr>
				<td width="10%"><b>Postcode:</b></td>
				<td><input class="inputbox" type="text" onchange="reinitmap(this);" name="postal_code" size="10" maxlength="10" value="<?php echo $row->postal_code;?>" /></td>
			</tr>
            <tr>
                <td><b>Country:</b></td>
                <td>
                    <select name="country" id="country" size="1">
                        <option <?php echo $row->country=='AUS' ? 'selected' : '';?> value="AUS">Australia</option>
                        <option <?php echo $row->country=='NZL' ? 'selected' : '';?> value="NZL">New Zealand</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="10%"><b>City:</b></td>
                <td><input class="inputbox" type="text" min="0" name="city" size="10" maxlength="10" value="<?php echo $row->city;?>" /></td>
            </tr>
			<tr>
				<td><b>WareHouse:</b></td>
				<td><?php echo $lists['warehouse'];?></td>
			</tr>
			<tr>
				<td><b>Same Day WareHouse:</b></td>
				<td><?php echo $lists['same_day_warehouse'];?></td>
			</tr>
            <tr>
				<td width="10%"><b>Days In Route:</b></td>
				<td><input class="inputbox" type="number" min="0" name="days_in_route" size="10" maxlength="10" value="<?php echo $row->days_in_route;?>" /></td>
			</tr>
            <tr>
                <td><b>Deliverable:</b></td>
                <td><?php echo $lists['deliverable'];?></td>
            </tr>
            <tr>
                <td><b>Out of Town:</b></td>
                <td><?php echo $lists['out_of_town']; ?></td>
            </tr>
            <tr>
                <td><b>Block Shipstation:</b></td>
                <td><?php echo $lists['block_shipstation']; ?></td>
            </tr>
            <tr>
                <td width="10%"><b>Delivery Surcharge:</b></td>
                <td><input class="inputbox" type="text" min="0" name="additional_delivery_fee" size="10" maxlength="10" value="<?php echo $row->additional_delivery_fee; ?>" /></td>
            </tr>
            <tr>
                <td><b>Published:</b></td>
                <td><?php echo $lists['published'];?></td>
            </tr>
		</table>
            <div style="width: 100%">
                <div id="map"></div>
            </div>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="task2" value="<?php echo $_REQUEST['task']; ?>" />		
		</form>
		<?php
	}
	
	
}
?>
