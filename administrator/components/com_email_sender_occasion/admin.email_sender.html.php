<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
  <script src="/administrator/components/com_email_sender/js/jquery.datetimepicker.js"></script>
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
class HTML_email_sender {

	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/
//	function showemail_texts( &$rows,  $pageNav,$search,&$lists ) {
	function showemail_texts( &$rows,  $pageNav,$search ) {
		global $my, $acl, $database, $mosConfig_offset;
		mosCommonHTML::loadOverlib();
		?>


		<form action="index2.php?option=com_email_sender" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap>

			</th>

			<td width="right" valign="top">

			</td>
			<td width="right" valign="top">
			<?php echo $lists['Sent'];?>
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
			<th align="center" width="30%">
			Subject
			</th>
                        <th width="10%" align="center">
                           Published
                        </th>
			<th align="center" width="10%">
			Sent
			</th>

			<th align="center" width="10%">
			Date
			</th>
		  </tr>
		<?php
		$k = 0;
		$nullDate = $database->getNullDate();
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?option=com_email_sender&task=edit&hidemainmenu=1&id='. $row->id;

			$date = mosFormatDate( $row->date, _CURRENT_SERVER_TIME_FORMAT );

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

					<a href="<?php echo $link; ?>" title="Edit Email">
					<?php echo htmlspecialchars($row->subject, ENT_QUOTES); ?>
					</a>

				</td>



				<td align="center">
                                <?php if( $row->sent){
                                $alt = 'Published';
				$img = 'publish_g.png';
                                }else{
                                $alt = 'Unpublished';
				$img = 'publish_x.png';
                                }
                                ?>
                                <a href="javascript: void(0);"  onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->sent ? "unpublish" : "publish";?>')">
					<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
					</a>
				</td>


				<td align="center">
				<?php echo $row->sentvstotal; ?>
				</td>
				<td align="center">
				<?php echo $date; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<?php mosCommonHTML::ContentLegend(); ?>

		<input type="hidden" name="option" value="com_email_sender" />
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
	function editemailtext( &$row, $params,$option,$list ) {
		global $database;

		mosMakeHtmlSafe( $row );

		$nullDate 		= $database->getNullDate();
		$create_date 	= null;

		if ( $row->date != $nullDate ) {
			$create_date 	= mosFormatDate( $row->date, _CURRENT_SERVER_TIME_FORMAT );
		}
		$tabs = new mosTabs(1);
		?>
  <style>

      .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
    .ui-timepicker-div dl { text-align: left; }
    .ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
    .ui-timepicker-div dl dd { margin: 0 10px 10px 40%; }
    .ui-timepicker-div td { font-size: 90%; }
    .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

    .ui-timepicker-rtl{ direction: rtl; }
    .ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
    .ui-timepicker-rtl dl dt{ float: right; clear: right; }
    .ui-timepicker-rtl dl dd { margin: 0 40% 10px 10px; }
  </style>
		<script language="javascript" type="text/javascript">
               jQuery(document).ready(function() {

                    jQuery('#date_send').datetimepicker({
                        	timeFormat: 'HH:mm:ss',
                                stepHour: 2,
                                stepMinute: 10,
                                stepSecond: 10,
                                	minDate: 0,
                                        dateFormat: 'yy-mm-dd',
                                        constrainInput: true
                    })


       jQuery( '#file-form' ).submit( function( e ) {
                         jQuery('#upload-button').val('Uploading .... ')
                         jQuery('#error_text').hide()
            jQuery.ajax( {
              url: '/administrator/components/com_email_sender/getemailslist.php',
              type: 'POST',
              data: new FormData( this ),
              processData: false,
              contentType: false,
             success: function(data)
                 {
                     if(data){
                         jQuery('#emails').val(data)
                         jQuery('#upload-button').val('Upload')
                     }else{
                         jQuery('#error_text').show()
                     }
                 }
            } );
            e.preventDefault();
        } );

                    });

		function submitbutton(pressbutton) {
			var form = document.adminForm;
                        if(pressbutton != 'cancel'){



                                        // do field validation
                                        if (form.subject.value == ""){
                                                alert( "Email item must have a subject" );
                                        } else {
                                        if (form.to.value == ""){
                                                alert( "Email item must have emails to send " );
                                        } else {
                                                <?php getEditorContents( 'editor1', 'introtext' ) ; ?>
                                                submitform( pressbutton );
                                        }
                                        }
                        }else{

                                                submitform( pressbutton );

                        }
                }
		//-->
		</script>



		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="edit">
			Email Item:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>

			</th>
		</tr>
		</table>

		<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="60%" valign="top">
				<table width="100%" class="adminform">
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<th colspan="2">
							Item Details
							</th>
						</tr>
						<tr>
							<td>
							Subject:
							</td>
							<td>
							<input class="text_area" type="text" name="subject" size="30" maxlength="100" value="<?php echo $row->subject; ?>" />
							</td>

						</tr>

						</table>
					</td>
				</tr>
				<tr>
					<td width="100%">
					Email Text:
					<br /><br />
                                        <strong> You can use {user_name} , {user_last_name} ,{unsubscribe}, {user_email} instead name,lastname,unsubscribe and email and information will  inserted  automatically for each user
                                         </strong> <br /><br />  <?php
					// parameters : areaname, content, hidden field, width, height, rows, cols
//					editorArea( 'editor1', $row->text, 'introtext', '100%;', '350', '75', '20' ) ; ?>
                                           <script src='/ckeditor/ckeditor.js'></script>
 					<textarea class="text_area" id='introtext' name="introtext"><?php echo $row->text; ?></textarea>
                                           <script> CKEDITOR.replace("introtext");</script>
					</td>


				</table>
			</td>

			<td valign="top" width="40%">
				<?php
				$tabs->startPane("content-pane");
				$tabs->startTab("Email Info","publish-page");
				?>
				<table class="adminform">
				<tr>
					<th colspan="2">
					Email Info
					</th>
				</tr>




				<tr>
					<td valign="top" align="right">
					Published:
					</td>
					<td>
					<input type="checkbox" name="publish" value="1" <?php echo $row->sent ? 'checked="checked"' : ''; ?> />
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<strong>To:</strong>
					</td>
					<td>
                                           <p> add emails separated by comma  </p>
                                           <?php

                                           $row->to = str_replace(',',','. PHP_EOL,$row->to);

                                           ?>
 					<textarea class="text_area" id="emails" cols="100" rows="13" style="width: 350px; height: 250px;font-size: 13px;" name="to"><?php  echo str_replace('&','&amp;', (isset($row->to) ? $row->to : "")); ?></textarea>


					</td>
				</tr>



				<tr>
					<td valign="top" align="right">
					<strong>
					Date Emailing
					</strong>
					</td>
					<td>
						        <?php if ( !$create_date ) { ?>
                                            <input type="text" name="date_send" id="date_send" value="<?php echo date('Y-m-d H:i:s'); ?> ">
							<?php } else {?>
                                            <input type="text" name="date_send" id="date_send" value="<?php echo $create_date; ?> ">
							<?php } ?>
					</td>
				</tr>

				</table><br />


				<?php
				$tabs->endTab();
				$tabs->startTab("Email sending","publish-page");
				?>
				<table class="adminform">
				<tr>
					<th >
					Email sending info
					</th>
				</tr>




				<tr>

					<td valign="top" align="left">
					<?php echo $list; ?>
					</td>

				</tr>

				</table><br />


				<?php
				$tabs->endTab();
				$tabs->endPane();
				?>
			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="mask" value="0" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>

             <form id="file-form" style="float: right;margin-right: 30px;margin-top: 20px;" action="/administrator/components/com_email_sender/getemailslist.php" method="POST">
                 <input type="file" id="file-select" name="file_emails" multiple/>
                 <button type="submit" id="upload-button">Upload</button>
                 <div style="color:red;display: none;" id="error_text">File is empty or has wrong format, please try another file</div>
              </form>

		<?php

	}


}
?>