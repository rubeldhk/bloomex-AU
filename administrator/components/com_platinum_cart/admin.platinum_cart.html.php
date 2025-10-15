<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
  <script src="/administrator/components/com_platinum_cart/js/jquery.datetimepicker.js"></script>
<?php
/**
* @version $Id: admin.content.html.php 4070 2006-06-20 16:09:29Z stingrey $
* @package Joomla
* @subpackage Content
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
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
* @subpackage Content
*/
class HTML_platinum_cart {

	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/

	function show_platinum_club_list( &$rows,  $pageNav,$search ) {
		global $my, $acl, $database, $mosConfig_offset;
		mosCommonHTML::loadOverlib();
		?>


		<form action="index2.php?option=com_platinum_cart" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap>

			</th>

			<td width="right" valign="top">

			</td>
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

		<table class="adminlist">
		<tr>
			<th width="2%">
			#
			</th>
			<th width="2%">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th align="center" width="10%">
			User Id
			</th>
                        <th width="20%" align="center">

                          Name
                        </th>
                        <th width="10%" align="center">
                         User Email
                        </th>
                        <th width="10%" align="center">
                        Order Date
                        </th>
                        <th width="10%" align="center">
                        End Date
                        </th>
                        <th width="10%" align="center">
                        Number if uses
                        </th>
			
		  </tr>
		<?php
		$k = 0;
		$nullDate = $database->getNullDate();
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?page=admin.user_form&user_id='. $row->user_id.'&option=com_virtuemart';

			//$date = mosFormatDate( $row->date, _CURRENT_SERVER_TIME_FORMAT );

			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td  align="center">
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td align="center">
				<?php echo $checked; ?>
				</td>
				<td align="center">
					<a href="<?php echo $link; ?>" title="Edit SMS">
					<?php echo htmlspecialchars($row->user_id, ENT_QUOTES); ?>
					</a>

				</td>

				<td align="center">
				<?php echo $row->name; ?>
				</td>
				<td align="center">
				<?php echo $row->email; ?>
				</td>
				<td align="center">
				<?php echo $row->start_datetime; //echo $newdate = date ( "Y-m-d H:i:s" , $row->cdate ) ; ?>
				</td>
                                <td align="center">
				<?php echo $row->end_datetime; ?>
				</td>
                                <td align="center">
				<?php echo $row->uses; ?>
				</td>

			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<?php mosCommonHTML::ContentLegend(); ?>

		<input type="hidden" name="option" value="com_platinum_cart" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}


	/**
	* Writes the edit form for new and existing content item
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param mosContent The category object
	* @param string The html for the groups select list
	*/
	function editplatinum( &$row, $option ) {
		global $database;

		mosMakeHtmlSafe( $row );

		$tabs = new mosTabs(1);
		?>

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="edit">
			Platinum Club Member:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>

			</th>
		</tr>
		</table>


						<table class="adminform" cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<th colspan="2">
							Member Details
							</th>
						</tr>
						<tr>
							<td>
							User Id:
							</td>
							<td>
							<input type="text" name="user_id" id="user_id" value="<?php echo $row->user_id; ?>">
					
                                                        </td>

						</tr>
						<tr>

							<td>
							Order Date
							</td>
					<td>
                                            <input type="text" name="date_platinum" id="date_platinum">
					</td>
					

				</tr>

				</table>
		<script language="javascript" type="text/javascript">
               $(document).ready(function() {
                   
                          jQuery( '#file-form' ).submit( function( e ) {
                         jQuery('#check-button').val('checking .... ')
                         jQuery('#error_text').hide()
                            jQuery.ajax( {
                              url: '/administrator/components/com_platinum_cart/getusersslist.php',
                              type: 'POST',
                              data: new FormData( this ),
                              processData: false,
                              contentType: false,
                             success: function(data)
                                 {
                                     if(data){
                                         jQuery('#user_list').html(data)
                                         jQuery('#check-button').val('Check')
                                         
                                         jQuery('#user_list_select').change(function(){
                                             if(jQuery(this).val()!=0){
                                                jQuery('#user_id').val(jQuery(this).val())
                                             }else{
                                                jQuery('#user_id').val('')
                                             }
                                         })
                                         
                                     }else{
                                         jQuery('#error_text').show()
                                     }
                                 }
                            } );
                            e.preventDefault();
                        } );
                   
                   
                    });
              jQuery('#date_platinum').datetimepicker({
                                       timeFormat: 'HH:mm:ss',
                                stepHour: 2,
                                stepMinute: 10,
                                stepSecond: 10,
                                        dateFormat: 'yy-mm-dd',
                                        constrainInput: true
                    })
               
               
           $('#date_platinum').datetimepicker("setDate" , "<?php $row->start_datetime ? ($row->start_datetime):(date('Y-m-d H:i:s'));?>")



		</script>
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="mask" value="0" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
  
      <form id="file-form" style="float: right;margin-right: 30px;margin-top: 20px;" action="/administrator/components/com_platinum_cart/getusersslist.php" method="POST">
        Find User Id By Email,First Name,Last Name: <input type="text" id="user_email" name="user_email"/>
        <button type="submit" id="check-button">Check</button>
        <br>
        <div id="user_list"></div>
            <div style="color:red;display: none;" id="error_text">there is no matched users with this email</div>
             
     </form>
  <style>
      #user_list_select{
          width: 100%;
    height: 28px;
    font-size: 14px;
    font-weight: bold;
      }
  </style>
		<?php

	}


}
?>