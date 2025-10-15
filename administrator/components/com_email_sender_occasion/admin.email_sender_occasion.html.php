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
class HTML_occasion_email_sender {

	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/
//	function showemail_texts( &$rows,  $pageNav,$search,&$lists ) {
	function showemail_texts( &$rows,  $pageNav,$search ) {
		global $my, $acl, $database, $mosConfig_offset;
		mosCommonHTML::loadOverlib();
		?>


		<form action="index2.php?option=com_email_sender_occasion" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap>

			</th>

			<td width="right" valign="top">

			</td>
			<td width="right" valign="top">
			<?php //echo $lists['Sent'];?>
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
			First Price
			</th>

			<th align="center" width="10%">
			Last Price
			</th>
			<th align="center" width="10%">
			Occasion
			</th>
		  </tr>
		<?php
		$k = 0;
		$nullDate = $database->getNullDate();
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?option=com_email_sender_occasion&task=edit&hidemainmenu=1&id='. $row->id;


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
                                <?php if( $row->published){
                                $alt = 'Published';
				$img = 'publish_g.png';
                                }else{
                                $alt = 'Unpublished';
				$img = 'publish_x.png';
                                }
                                ?>
                                <a href="javascript: void(0);"  onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->published ? "unpublish" : "publish";?>')">
					<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
					</a>
				</td>


				<td align="center">
				<?php echo $row->first_price." $"; ?>
				</td>
				<td align="center">
				<?php echo $row->last_price." $"; ?>
				</td>
				<td align="center">
				<?php echo $row->occasion_name; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<?php mosCommonHTML::ContentLegend(); ?>

		<input type="hidden" name="option" value="com_email_sender_occasion" />
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
	function editemailtext( &$row, $params,$option ,$occasions) {
		global $database;

		mosMakeHtmlSafe( $row );


		$tabs = new mosTabs(1);
		?>

		<script language="javascript" type="text/javascript">


		function submitbutton(pressbutton) {
			var form = document.adminForm;
                        if(pressbutton != 'cancel'){



                                        // do field validation
                                        if (form.subject.value == ""){
                                                alert( "Email item must have a subject" );

                                        } else {
                                                <?php getEditorContents( 'editor1', 'introtext' ) ; ?>
                                                submitform( pressbutton );
                                        }
                                        
                        }else{

                                                submitform( pressbutton );

                        }
                }
		
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
                                        <!--<strong> You can use {user_name} , {user_last_name} ,{unsubscribe}, {user_email} instead name,lastname,unsubscribe and email and information will  inserted  automatically for each user-->
                                        <strong> You can use {unsubscribe}, {sender_name} ,{sender_email}, {sender_last_name} ,{date_of_purchase} ,{ddate} ,{customer_occasion},{sender_phone},{sender_province}, {recipient_name} , {recipient_last_name} ,{order_total},{order_id}
                                            instead sender unsubscribe, name,sender email,sender last name,order creat  date,order delivery date ,occasion,sender phone,sender province,recipient name,recipient last name ,  order total  price ,order id in that occasion  and email. Information will  inserted  automatically for each user
                                         </strong> <br /><br /> 
                                         
                                         <?php $text_default = ''; ?>
                                           <script src='/ckeditor/ckeditor.js'></script>
 					<textarea class="text_area" id='introtext' name="introtext"><?php echo $row->text ? $row->text : $text_default; ?></textarea>
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
                                                                                     <strong>
					Published:
                                                                                     </strong>
					</td>
					<td>
					<input type="checkbox" name="published" value="1" <?php echo $row->published ? 'checked="checked"' : ''; ?> />
					</td>
				</tr>

				<tr>
					<td valign="top" align="right">
					<strong>
					Day Count
					</strong>
					</td>
					<td>
                                            <input type="text" name="day_count" id="day_count" value="<?php echo $row->day_count; ?> ">
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<strong>
					First Price
					</strong>
					</td>
					<td>
                                            <input type="text" name="first_price"  value="<?php echo $row->first_price; ?> ">
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<strong>
					Last Price
					</strong>
					</td>
					<td>
                                            <input type="text" name="last_price"  value="<?php echo $row->last_price; ?> ">
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<strong>
					Occasion
					</strong>
					</td>
					<td>

                                            <select name='occasion'>
                                                
                                           <?php 
                                           foreach($occasions as $oc){
                                               if($oc->order_occasion_code == 'SYMP')
                                                   continue;
                                               if($oc->order_occasion_code == $row->occasion){
                                               ?>
                                                <option value="<?php echo $oc->order_occasion_code; ?>" selected="selected"><?php echo $oc->order_occasion_name?></option>>
                                               <?php
                                               }else{
                                                    ?>
                                                 <option value="<?php echo $oc->order_occasion_code; ?>"><?php echo $oc->order_occasion_name?></option>>
                                               <?php }
                                           }
                                           
                                           ?>
                                                </select>
					</td>
				</tr>

				</table><br />




				<?php
				
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


		<?php

	}


}
?>