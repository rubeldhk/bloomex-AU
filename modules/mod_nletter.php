<?php
/**
* @version $Id: mod_banners.php 2456 2006-02-18 01:36:30Z stingrey $
* @package Joomla
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

// clientids must be an integer
$moduleclass_sfx = $params->get( 'moduleclass_sfx', '' );
?>
<div class="caption">
	Sign up to receive special offers and promotions from Bloomex:
</div>
<div class="nletter">
	<form name="Fnletter" method="POST" action="">
		<input name="email_address" type="text" size="20" value="Enter Email" class="tbox" />
		<input  type="button" id="btn-nletter" name="btn-nletter" class="btn-nletter" value="&nbsp;" />
	</form>
	<span class="loading" id="msg-nletter">&nbsp;</span>	
</div>

<script type="text/javascript">
$j(document).ready(function(){
	$j("#btn-nletter").click(function () {
		$j("#msg-nletter").attr("style", "display:none"); 
		var email_address	= $j("input[name='email_address']").val();
		
		if( !jQuery.trim(email_address) )  {
			alert("Please enter your email address!");	
			return;
		}		
		
		if( !(/^\w+([\.-]*\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(email_address)) ) {
			alert("Your email address is incorrect! Eg: example@email.com");	
			return;
		}	
				
		$j.post( "index.php",
			{ 	option: 		"com_nletter", 
				task:		"send",
				email:		email_address,
			  	ajaxSend: function(){
				 	$j("#msg-nletter").html("Sending..."); 
				 	$j("#msg-nletter").attr("style", "display:block; color:#0000ff"); 
		   	  	}
			},			
			function(data){
				if( data == "exist" ) {	
					$j("#msg-nletter").html("Sorry, your email already exists in our mailing list"); 
					$j("#msg-nletter").attr("style", "display:block; color:#ff0000"); 						
				}else {
					$j("#msg-nletter").html("Your email was successfully added to our mailing list"); 
				 	$j("#msg-nletter").attr("style", "display:block; color:#0000ff"); 				
					$j("input[name='email_address']").val("Enter Email");
				}
			}
		);
	});	
	
	
	$j("input[name='email_address']").click(function () {
		if( $j("input[name='email_address']").val() == "Enter Email" ) 	$j("input[name='email_address']").val("");
	});	
});
</script>	
	