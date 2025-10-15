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
global  $mosConfig_absolute_path;
/*require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_database.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_html.php" );*/
/**
* @package Joomla
* @subpackage Category
*/
class HTML_CustomForm {
	static function showQuoteForm( $aInfo ) {
		global $mosConfig_live_site, $mosConfig_lang;
		
		$bSuccess	= !empty( $_REQUEST['success'] ) ? intval($_REQUEST['success']) : 0;

		if( $bSuccess ) {
		?>
			<div style="text-align:center;">
				<b>Thank you - Your quote request has been successfully submitted.<br/>A Corporate Account Manager will get back to you shortly.</b> <br/><br/>
			</div>
		<?php	
		}else {
		?>
			 <!-- calendar stylesheet -->
			<style>
				/* The main calendar widget.  DIV containing a table. */
				div.calendar {
				  position: relative;
				  display: none;
				  border-top: 2px solid #fff;
				  border-right: 2px solid #000;
				  border-bottom: 2px solid #000;
				  border-left: 2px solid #fff;
				  font-size: 11px;
				  color: #000;
				  cursor: default;
				  background: #c8d0d4;
				  font-family: tahoma,verdana,sans-serif;
				}

				div.calendar table {
				  border-top: 1px solid #000;
				  border-right: 1px solid #fff;
				  border-bottom: 1px solid #fff;
				  border-left: 1px solid #000;
				  font-size: 11px;
				  color: #000;
				  cursor: default;
				  background: #c8d0d4;
				  font-family: tahoma,verdana,sans-serif;
				}

				/* Header part -- contains navigation buttons and day names. */

				div.calendar .button { /* "<<", "<", ">", ">>" buttons have this class */
				  text-align: center;
				  padding: 1px;
				  border-top: 1px solid #fff;
				  border-right: 1px solid #000;
				  border-bottom: 1px solid #000;
				  border-left: 1px solid #fff;
				}

				div.calendar .nav {
				  background: transparent url(menuarrow.gif) no-repeat 100% 100%;
				}

				div.calendar thead .title { /* This holds the current "month, year" */
				  font-weight: bold;
				  padding: 1px;
				  border: 1px solid #000;
				  background: #788084;
				  color: #fff;
				  text-align: center;
				}

				div.calendar thead .headrow { /* Row <TR> containing navigation buttons */
				}

				div.calendar thead .daynames { /* Row <TR> containing the day names */
				}

				div.calendar thead .name { /* Cells <TD> containing the day names */
				  border-bottom: 1px solid #000;
				  padding: 2px;
				  text-align: center;
				  background: #e8f0f4;
				}

				div.calendar thead .weekend { /* How a weekend day name shows in header */
				  color: #f00;
				}

				div.calendar thead .hilite { /* How do the buttons in header appear when hover */
				  border-top: 2px solid #fff;
				  border-right: 2px solid #000;
				  border-bottom: 2px solid #000;
				  border-left: 2px solid #fff;
				  padding: 0px;
				  background-color: #d8e0e4;
				}

				div.calendar thead .active { /* Active (pressed) buttons in header */
				  padding: 2px 0px 0px 2px;
				  border-top: 1px solid #000;
				  border-right: 1px solid #fff;
				  border-bottom: 1px solid #fff;
				  border-left: 1px solid #000;
				  background-color: #b8c0c4;
				}

				/* The body part -- contains all the days in month. */

				div.calendar tbody .day { /* Cells <TD> containing month days dates */
				  width: 2em;
				  text-align: right;
				  padding: 2px 4px 2px 2px;
				}
				div.calendar tbody .day.othermonth {
				  font-size: 80%;
				  color: #aaa;
				}
				div.calendar tbody .day.othermonth.oweekend {
				  color: #faa;
				}

				div.calendar table .wn {
				  padding: 2px 3px 2px 2px;
				  border-right: 1px solid #000;
				  background: #e8f4f0;
				}

				div.calendar tbody .rowhilite td {
				  background: #d8e4e0;
				}

				div.calendar tbody .rowhilite td.wn {
				  background: #c8d4d0;
				}

				div.calendar tbody td.hilite { /* Hovered cells <TD> */
				  padding: 1px 3px 1px 1px;
				  border: 1px solid;
				  border-color: #fff #000 #000 #fff;
				}

				div.calendar tbody td.active { /* Active (pressed) cells <TD> */
				  padding: 2px 2px 0px 2px;
				  border: 1px solid;
				  border-color: #000 #fff #fff #000;
				}

				div.calendar tbody td.selected { /* Cell showing selected date */
				  font-weight: bold;
				  padding: 2px 2px 0px 2px;
				  border: 1px solid;
				  border-color: #000 #fff #fff #000;
				  background: #d8e0e4;
				}

				div.calendar tbody td.weekend { /* Cells showing weekend days */
				  color: #f00;
				}

				div.calendar tbody td.today { /* Cell showing today date */
				  font-weight: bold;
				  color: #00f;
				}

				div.calendar tbody .disabled { color: #999; }

				div.calendar tbody .emptycell { /* Empty cells (the best is to hide them) */
				  visibility: hidden;
				}

				div.calendar tbody .emptyrow { /* Empty row (some months need less than 6 rows) */
				  display: none;
				}

				/* The footer part -- status bar and "Close" button */

				div.calendar tfoot .footrow { /* The <TR> in footer (only one right now) */
				}

				div.calendar tfoot .ttip { /* Tooltip (status bar) cell <TD> */
				  background: #e8f0f4;
				  padding: 1px;
				  border: 1px solid #000;
				  background: #788084;
				  color: #fff;
				  text-align: center;
				}

				div.calendar tfoot .hilite { /* Hover style for buttons in footer */
				  border-top: 1px solid #fff;
				  border-right: 1px solid #000;
				  border-bottom: 1px solid #000;
				  border-left: 1px solid #fff;
				  padding: 1px;
				  background: #d8e0e4;
				}

				div.calendar tfoot .active { /* Active (pressed) style for buttons in footer */
				  padding: 2px 0px 0px 2px;
				  border-top: 1px solid #000;
				  border-right: 1px solid #fff;
				  border-bottom: 1px solid #fff;
				  border-left: 1px solid #000;
				}

				/* Combo boxes (menus that display months/years for direct selection) */

				div.calendar .combo {
				  position: absolute;
				  display: none;
				  width: 4em;
				  top: 0px;
				  left: 0px;
				  cursor: default;
				  border-top: 1px solid #fff;
				  border-right: 1px solid #000;
				  border-bottom: 1px solid #000;
				  border-left: 1px solid #fff;
				  background: #d8e0e4;
				  font-size: 90%;
				  padding: 1px;
				  z-index: 100;
				}

				div.calendar .combo .label,
				div.calendar .combo .label-IEfix {
				  text-align: center;
				  padding: 1px;
				}

				div.calendar .combo .label-IEfix {
				  width: 4em;
				}

				div.calendar .combo .active {
				  background: #c8d0d4;
				  padding: 0px;
				  border-top: 1px solid #000;
				  border-right: 1px solid #fff;
				  border-bottom: 1px solid #fff;
				  border-left: 1px solid #000;
				}

				div.calendar .combo .hilite {
				  background: #048;
				  color: #aef;
				}

				div.calendar td.time {
				  border-top: 1px solid #000;
				  padding: 1px 0px;
				  text-align: center;
				  background-color: #e8f0f4;
				}

				div.calendar td.time .hour,
				div.calendar td.time .minute,
				div.calendar td.time .ampm {
				  padding: 0px 3px 0px 4px;
				  border: 1px solid #889;
				  font-weight: bold;
				  background-color: #fff;
				}

				div.calendar td.time .ampm {
				  text-align: center;
				}

				div.calendar td.time .colon {
				  padding: 0px 2px 0px 3px;
				  font-weight: bold;
				}

				div.calendar td.time span.hilite {
				  border-color: #000;
				  background-color: #667;
				  color: #fff;
				}

				div.calendar td.time span.active {
				  border-color: #f00;
				  background-color: #000;
				  color: #0f0;
				}
                .contentpaneopen td{
                    padding: 10px;
                }

			</style>
            <link rel="stylesheet" type="text/css" href="/templates/bloomex_adaptive/css/jquery-ui.css" />
            <script src="/templates/bloomex_adaptive/js/jquery-ui.js"></script>
 
			<div class="container pb-4" id="quoteFormContainer">
				<h1 class="text-center mb-4 pt-4">Quote Form</h1>
				<p align="center"><em><strong>Please fill out the following so we can provide a custom quote.</strong></em></p>
				<center>	
					<form name="FCorporateAccount" class="quote-request" element="quote-request" method="POST" id="quote-request" action="">
						<input  type="hidden" name="task" value="sendQuoteForm"/>
						<input  type="hidden" name="option" value="com_customform"/>
						<table class="contentpaneopen" cellpadding="5">
							<tbody>
								<tr>
									<td style="text-align:right;" width="30%"><label for="first_name"><b>Name: <font color="#ff0000">*</font></b></label></td>
									<td width="70%">
                                        <input type="text" size="40" name="full_name" class="form-control" maxlength="40" id="full_name">
                                        </td>
								</tr>
								<tr>
									<td style="text-align:right;" width="30%"><label for="first_name"><b>Email: <font color="#ff0000">*</font></b></label></td>
									<td width="70%">
                                        <input type="text" size="40" name="email" class="form-control" maxlength="40" id="email">
                                        </td>
								</tr>
								<tr>
									<td style="text-align:right;"><label for="phone"><b>Phone: <font color="#ff0000">*</font></b></label></td>
									<td>
										<input type="text" size="40" placeholder="10 digits, no spaces" class="form-control" name="phone" maxlength="10" id="phone"  />
                                        </td>
								</tr>
                                <tr>
                                    <td style="text-align:right;"><label for="number_of_gift_basket"><b>Number of Gift Baskets (or other): </b></label></td>
                                    <td>
                                        <input type="text" size="40"  class="form-control" name="number_of_gift_basket" maxlength="10" id="number_of_gift_basket"  />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:right;"><label for="estimated_budget"><b>Estimated Budget: </b></label></td>
                                    <td>
                                        <input type="text" size="40"  class="form-control" name="estimated_budget" maxlength="10" id="estimated_budget"  />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:right;"><label for="delivery_date"><b>Approximately Delivery Date: </b></label></td>
                                    <td>
                                        <input type="text" size="40"  class="form-control" value="" name="delivery_date" maxlength="10" id="delivery_date"  />
                                    </td>
                                </tr>
								<tr>
									<td style="text-align:right;"><label for="state"><b>State: <font color="#ff0000">*</font></b></label></td>
									<td>
										<select id="state" class="form-control" name="state">
											<option value="">- None -</option>
											<?php echo $aInfo['state']; ?>
										</select>
                                        </td>
								</tr>
								<tr>
									<td style="text-align:right;vertical-align: top;"><label for="company"><b>Product description:</b></label></td>
									<td><textarea rows="7" class="form-control" cols="35" name="product_desc" id="product_desc"></textarea></td>
								</tr>
								<tr>
									<td style="text-align:right;">&nbsp;</td>
									<td style="text-align:left;">
                                        <button type="button" onclick="checkForm()" class="submit_button btn btn-success">Submit</button>
                                        <button style="visibility: hidden" class="g-recaptcha capcha_validate"
                                                data-sitekey="6LdJvGgUAAAAAM_cyb03MYOn5oxYZlGwAonw7Npi"
                                                data-callback="submitform">
                                        </button>
                                    </td>
								</tr>
							</tbody>
						</table>
					</form>
					<script type="text/javascript">
                        function submitform() {
                            var oForm = document.FCorporateAccount;
                            oForm.submit();
                        }
						function checkForm() {
							oForm	= document.FCorporateAccount;
							
							if( oForm.full_name.value == ""  ) {
								 alert("Please enter your full name.");
								oForm.full_name.focus();
								return false;
							}
							
							if( oForm.email.value == ""  ) {
								 alert("Please enter your email address.");
								oForm.email.focus();
								return false;
							}
							
							if( !checkEmail2(oForm.email.value)  ) {
								 alert("Your email address is incorrect.");
								oForm.email.focus();
								return false;
							}
							


                            if( oForm.email.value == ""  ) {
                                alert("Please enter your phone number.");
                                oForm.email.focus();
                                return false;
                            }



														
							if( oForm.state.value == ""  ) {
								 alert("Please enter your state.");
								oForm.state.focus();
								return false;
							}
                            jQuery('.capcha_validate').click()
						}
                        $( function() {
                            $( "#delivery_date" ).datepicker({minDate:0,dateFormat:'yy-mm-dd'});
                        } );
						function checkEmail2(email){
							emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;
							if(emailRegExp.test(email)){
								return true;
							}else{
								return false;
							}
						}
					//-->
					</script>
                    <script src='https://www.google.com/recaptcha/api.js'></script>
				</center>
			</div>
<?php 
		} 	
	}
}
?>
