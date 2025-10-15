// JavaScript Document
 var $j = jQuery.noConflict();

$j(document).ready(function(){
	$j(".add-to-cart").click(function () {
		var formId	= $j(this).attr("name");
		var product_id_value	= $j("input[name='product_id_" + formId + "']").val();
		var category_id_value	= $j("input[name='category_id_" + formId + "']").val();
		var quantity_value		= $j("input[name='quantity_" + formId + "']").val();
		var price_value			= $j("input[name='price_" + formId + "']").val();
		
		if( parseInt(quantity_value) < 0 || !isInteger(quantity_value) )  {
			alert("Please enter quantity number!");	
			return;
		}		
				
		$j.post( "index.php",
			{ 	option: 			"com_virtuemart", 
				page:				"shop.cart",
			  	func: 				"cartadd", 
				action: 			"ajax", 
			  	product_id: 		product_id_value, 
			  	category_id: 		category_id_value,
				quantity:			quantity_value,
				price:				price_value,
			  	ajaxSend: function(){
				 	$j("#div_" + formId).html('<img src="'+ sImgLoading +'" align="absmiddle"/>&nbsp;&nbsp;Updating...'); 
		   	  	}
			},			
			function(data){
				aData	= data.split("[--1--]");
				if( aData[0] == "success" ) {	
					if( bMember ) {
						sCartLink	= sSecurityUrl + "index.php?page=checkout.index&option=com_virtuemart&Itemid=80";
					}else {
						sCartLink	= sSecurityUrl + "index.php?page=shop.cart&option=com_virtuemart&Itemid=80";
					}			
					
					$j("#div_" + formId).html("Product was added to shopping cart<br/><a class='show-cart-now' href='" + sCartLink + "'>&nbsp;</a>");
					
					$j("#modCartItem").html(aData[1]);					
				}else {
					$j("#div_" + formId).html("Product was not added to shopping cart"); 
				}
			}
		);
	});	
	
	
	//=======================================================================================================================
	$j(".delete-cart-item").click(function () {
		var product_id_value	= $j(this).attr("rel");
		
		if ( !confirm("Do you want to delete this item?") ){
			return;
		}
		
		$j.post( "index.php",
			{ 	option: 			"com_virtuemart", 
				page:				"shop.cart",
			  	func: 				"cartDelete", 
				action: 			"ajax", 
			  	product_id: 		product_id_value,
			  	ajaxSend: function(){
				 	$j("#msgReportBlock_" + product_id_value).html('<img src="'+ sImgLoading +'" align="absmiddle"/><br/>Deleting...'); 
		   	  	}
			},			
			function(data){
				aData	= data.split("[--1--]");
				if( aData[0] == "success" ) {		
					/*	
						0 =  "success[--1--]
						1 = {$nTotalItem} $sResult,<br/>{$sTotalPrice}[--1--]
						2 = {$sTotalPrice}[--1--]
						3 = {$nTotalPrice}[--1--]
						4 = {$sSubTotalPrice}[--1--]
						5 = {$nSubTotalPrice}[--1--]
						6 = {$sTotalTax}[--1--]
						7 = {$nTotalTax}";	
					*/					
							
					//alert($j("input[name='product_id_string']").val() + "===" + product_id_value);
					aProductId	= $j("input[name='product_id_string']").val().split(",");						
					if( aProductId.length ) {
						for( k = 0; k < aProductId.length; k++ ) {
							//alert(trim(aProductId[k]) + "===" + trim(product_id_value) + "===" + (trim(aProductId[k]) == trim(product_id_value)));
							if( trim(aProductId[k]) == trim(product_id_value) ) aProductId[k] = "";
						}						
					}
					
					sProductId	= aProductId.join(",");
					//alert($j("input[name='product_id_string']").val() + "===" + product_id_value + "===" + sProductId);
					$j("input[name='product_id_string']").val(sProductId);
					//alert($j("input[name='product_id_string']"))
					
					$j("#msgReport").css('display','block'); 
					$j("#msgReport").html('Delete Cart Item Successful!');
					$j("#cartItem_" + product_id_value).css("display","none");
					$j("#modCartItem").html(aData[1]);	
					$j("#totalPrice").html(aData[2]);
					$j("#totalSubPrice").html(aData[4]);
					if( $j("#totalTax").html() ) {
						$j("#totalTax").html(aData[6]);
					}	
					$j("input[name='total_price']").val(aData[3]);		
					$j("input[name='sub_total_price']").val(aData[5]);		
					$j("input[name='total_tax']").val(aData[7]);
					$j("#totalCouponDiscount").html("- " + aData[8]);			
					$j("input[name='coupon_discount']").val(aData[9]);
				}else {
					$j("#msgReport").css('display','none'); 
					$j("#msgReportBlock_" + product_id_value).html("Delete Cart Item Wrong!"); 
				}
			}
		);
	});	
	
	
	//=======================================================================================================================
	$j(".update-cart-item").click(function () {
		var product_id_value	= $j(this).attr("rel");
		var quantity_value		= $j("input[name='quantity_" + product_id_value + "']").val();
		
		if( parseInt(quantity_value) < 0 || !isInteger(quantity_value) )  {
			alert("Please enter quantity number!");	
			return;
		}
				
		$j.post( "index.php",
			{ 	option: 			"com_virtuemart", 
				page:				"shop.cart",
			  	func: 				"cartUpdate", 
				action: 			"ajax", 
			  	product_id: 		product_id_value, 
				quantity:			quantity_value,
			  	ajaxSend: function(){
				 	$j("#msgReportBlock_" + product_id_value).html('<img src="'+ sImgLoading +'" align="absmiddle"/><br/>Updating ...'); 
		   	  	}
			},			
			function(data){
				aData	= data.split("[--1--]");
				if( aData[0] == "success" ) {					
					$j("#msgReport").css('display','block'); 
					$j("#msgReport").html('Update Cart Item Successful!');
					$j("#msgReportBlock_" + product_id_value).html('');
					$j("#modCartItem").html(aData[1]);	
					$j("#totalPrice").html(aData[2]);
					$j("#totalSubPrice").html(aData[4]);	
					
					//alert($j("input[name='product_id_string']").val() + "===" + product_id_value);
					aProductId	= $j("input[name='product_id_string']").val().split(",");						
					aQuantity	= $j("input[name='quantity_string']").val().split(",");						
					if( aProductId.length ) {
						for( k = 0; k < aProductId.length; k++ ) {
							//alert(trim(aProductId[k]) + "===" + trim(product_id_value) + "===" + (trim(aProductId[k]) == trim(product_id_value)));
							if( trim(aProductId[k]) == trim(product_id_value) ) {
								aQuantity[k] = quantity_value;
							}
						}						
					}
					$j("input[name='quantity_string']").val(aQuantity.join(","));					
					
					//alert( ($j("input[name='item_price_" + product_id_value + "']").val())  + "===" + parseFloat($j("input[name='item_price_" + product_id_value + "']").val())  + "===" +  parseInt(quantity_value) );				
					$j("#subtotal_price_" + product_id_value).html( $j("input[name='vendor_currency_string']").val() + "\n $" + formatAsMoney(parseFloat($j("input[name='item_price_" + product_id_value + "']").val()) *  parseInt(quantity_value)) );
					if( $j("#totalTax").html() ) {
						$j("#totalTax").html(aData[6]);
					}	
					$j("input[name='total_price']").val(aData[3]);		
					$j("input[name='sub_total_price']").val(aData[5]);		
					$j("input[name='total_tax']").val(aData[7]);
					$j("#totalCouponDiscount").html("- " + aData[8]);			
					$j("input[name='coupon_discount']").val(aData[9]);
				}else if( aData[0] == "error" ) { 
					$j("#msgReportBlock_" + product_id_value).html(aData[1]); 
				}else{
					$j("#msgReportBlock_" + product_id_value).html("Update Cart Item Wrong!"); 
				}
			}
		);
	});	
	
	
	//=======================================================================================================================	
	$j("#displayUpdateBillingInfo").click(function () { 
		$j.post( "index.php",
			{ 	option: 			"com_ajaxorder", 
				task: 				"getUserBillingAddress", 
				user_id: 			$j("input[name='user_id_billing']").val(), 
				user_info_id: 		$j("input[name='user_info_id_billing']").val()
			},			
			function(data){
				aData	= data.split("[--1--]");
				if( aData[0] == "success" ) {				
					
					aData2	= aData[1].split("[--2--]");					
					$j("select[name='title_billing']").val(aData2[16]);
					$j("input[name='user_info_id_shipping']").val(aData2[1]);
					$j("input[name='address_type_name_shipping']").val(aData2[2]);
					$j("input[name='first_name_billing']").val(aData2[3]);
					$j("input[name='last_name_billing']").val(aData2[4]);
					$j("input[name='middle_name_billing']").val(aData2[5]);
					$j("input[name='company_billing']").val(aData2[6]);
					$j("input[name='address_1_billing']").val(aData2[7]);
					$j("input[name='address_2_billing']").val(aData2[8]);
					$j("input[name='city_billing']").val(aData2[9]);
					$j("input[name='zip_billing']").val(aData2[10]);
					$j("select[name='country_billing']").val(aData2[11]);
					changeStateList("state_billing", "country_billing");
					$j("select[name='state_billing']").val(aData2[12]);
					$j("input[name='phone_1_billing']").val(aData2[13]);
					$j("input[name='phone_2_billing']").val(aData2[14]);
					$j("input[name='fax_billing']").val(aData2[15]);	
					$j("input[name='email_billing']").val(aData2[17]);	
					
					$j('#updateBillingInfoForm').modal({onOpen: modalOpen, position: ["25%","25%"]});					
				}else{
					/*$j("#msgReportDeliverInfo").css('display','block'); 
					$j("#msgReportDeliverInfo").html('Edit Deliver Information Operation Wrong!');		*/			
				}
			}
		);
	});
	
	
	$j("#updateBillingInfo").click(function () {
		var id_value				= $j("input[name='id_billing']").val();
		var user_id_value			= $j("input[name='user_id_billing']").val();
		var user_info_id_value		= $j("input[name='user_info_id_billing']").val();
		var company_value			= $j("input[name='company_billing']").val();
		var title_value				= $j("select[name='title_billing']").val();
		var first_name_value		= $j("input[name='first_name_billing']").val();
		var last_name_value			= $j("input[name='last_name_billing']").val();
		var middle_name_value		= $j("input[name='middle_name_billing']").val();
		var address_1_value			= $j("input[name='address_1_billing']").val();
		var address_2_value			= $j("input[name='address_2_billing']").val();
		var city_value				= $j("input[name='city_billing']").val();
		var zip_value				= $j("input[name='zip_billing']").val();
		var country_value			= $j("select[name='country_billing']").val();
		var state_value				= $j("select[name='state_billing']").val();
		var phone_1_value			= $j("input[name='phone_1_billing']").val();
		var phone_2_value			= $j("input[name='phone_2_billing']").val();
		var fax_value				= $j("input[name='fax_billing']").val();
		var email_value				= $j("input[name='email_billing']").val();
				
		if( first_name_value == "" ) {
			alert("Please enter First Name!");
			$j('#first_name_field').css("color","red");
			return;
		}else {
			$j('#first_name_field').css("color","");
		}
		
		if( last_name_value == "" ) {
			alert("Please enter Last Name!");
			$j('#last_name_field').css("color","red");
			return;
		}else {
			$j('#last_name_field').css("color","");
		}
		
		if( address_1_value == "" ) {
			alert("Please enter Address!");
			$j('#address_1_field').css("color","red");
			return;
		}else {
			$j('#address_1_field').css("color","");
		}
		
		if( city_value == "" ) {
			alert("Please enter City!");
			$j('#city_field').css("color","red");
			return;
		}else {
			$j('#city_field').css("color","");
		}
		
		if( zip_value == "" ) {
			alert("Please enter Zip Code!");
			$j('#zip_field').css("color","red");
			return;
		}else {
			$j('#zip_field').css("color","");
		}
		
		if( !isValidZipCode( zip_value ) ) {
			alert("Please enter your zip code again!\n Example: K2E 7X3 (LDL DLD . Where L= letter , D = number)");
			$j('#zip_field').css("color","red");
			return;
		}else {
			$j('#zip_field').css("color","");
		}
				
		if( country_value == "" ) {
			alert("Please select Country!");
			$j('#td_country_field').css("color","red");
			return;
		}else {
			$j('#td_country_field').css("color","");
		}
		
		if( state_value == "" ) {
			alert("Please enter State!");
			$j('#td_state_field').css("color","red");
			return;
		}else {
			$j('#td_state_field').css("color","");
		}
		
		if( phone_1_value == "" ) {
			alert("Please enter Phone Number!");
			$j('#phone_1_field').css("color","red");
			return;
		}else {
			$j('#phone_1_field').css("color","");
		}
						
		if( !(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(email_value)) ) {
			alert("Please enter Email!");
			$j('#email_field').css("color","red");
			return;
		}else {
			$j('#email_field').css("color","");
		}	
				
				
		$j.post( "index.php",
			{ 	option: 			"com_virtuemart", 
				page:				"checkout.index",
			  	func: 				"shopperupdate", 
				action: 			"ajax", 
				address_type: 		"BT", 
			  	id: 				id_value, 
				user_id: 			user_id_value, 
				user_info_id: 		user_info_id_value,
				company: 			company_value, 
				title: 				title_value, 
				first_name: 		first_name_value, 
				last_name: 			last_name_value, 
				middle_name: 		middle_name_value, 
				address_1: 			address_1_value, 
				address_2: 			address_2_value, 
				city: 				city_value, 
				zip: 				zip_value, 
				country: 			country_value, 
				state: 				state_value, 
				phone_1: 			phone_1_value, 
				phone_2: 			phone_2_value, 
				fax: 				fax_value, 
				email: 				email_value, 
			  	ajaxSend: function(){
					$j("#msgReportUpdateBilling").css('display','block'); 					
				 	$j("#msgReportUpdateBilling").html('<img width="24" src="'+ sImgLoading +'" align="absmiddle"/>&nbsp;&nbsp;Updating Billing Information...'); 
		   	  	}
			},			
			function(data){
				if( data == "success" ) {					
					$j("#company_field_value").html(company_value);			
					$j("#name_field_value").html(first_name_value + ' ' + middle_name_value + ' ' + last_name_value);			
					$j("#address_field_value").html(address_1_value + ', ' + city_value + ', ' + state_value + ', ' + country_value);			
					$j("#phone_field_value").html(phone_1_value);			
					$j("#fax_field_value").html(fax_value);			
					$j("#email_field_value").html(email_value);			
					$j("#msgReportUpdateBilling").css('display','block'); 
					$j("#msgReportUpdateBilling").html('Update Billing Information Successful!');					
				}else{
					$j("#msgReportUpdateBilling").css('display','block'); 
					$j("#msgReportUpdateBilling").html('Update Billing Information Wrong!');					
				}
			}
		);
	});	
	
	
	//=======================================================================================================================
	$j("#closeShippingInfo").click(function () {
		$j.modal.close();
	});	
	
	$j(".add-shipping-address").click(function () {
		$j("input[name='address_type_name_shipping']").val("");
		$j("input[name='first_name_shipping']").val("");
		$j("input[name='last_name_shipping']").val("");
		$j("input[name='middle_name_shipping']").val("");
		$j("input[name='company_shipping']").val("");
		$j("input[name='address_1_shipping']").val("");
		$j("input[name='address_2_shipping']").val("");
		$j("input[name='city_shipping']").val("");
		$j("input[name='zip_shipping']").val("");
		$j("select[name='country_shipping']").val("CAN");
//		changeStateList("state_shipping", "country_shipping");
		$j("select[name='state_shipping']").val("AB");
		$j("input[name='phone_1_shipping']").val("");
		$j("input[name='phone_2_shipping']").val("");
		$j("input[name='fax_shipping']").val("");
		$j("input[name='func_shipping']").val("userAddressAdd");
		sCurrentZipChecked = $j("input[name='ship_to_info_id']:checked").val();
		$j('#addShippingInfoForm').modal({onOpen: modalOpen, position: ["25%","25%"]});
		$j("#updateShippingInfo").attr("value", "Add Address");
	});	
	
	
	
	$j("#updateShippingInfo").click(function () { 
		var func_shipping_value					= $j("input[name='func_shipping']").val();									 
		var user_id_value						= $j("input[name='user_id_shipping']").val();
		var user_info_id_value					= $j("input[name='user_info_id_shipping']").val();
		var address_type_name_shipping_value	= $j("input[name='address_type_name_shipping']").val();
		var first_name_shipping_value			= $j("input[name='first_name_shipping']").val();
		var last_name_shipping_value			= $j("input[name='last_name_shipping']").val();
		var middle_name_shipping_value			= $j("input[name='middle_name_shipping']").val();
		var company_shipping_value				= $j("input[name='company_shipping']").val();
		var address_1_shipping_value			= $j("input[name='address_1_shipping']").val();
		var address_2_shipping_value			= $j("input[name='address_2_shipping']").val();
		var city_shipping_value					= $j("input[name='city_shipping']").val();
		var zip_shipping_value					= $j("input[name='zip_shipping']").val();
//		var country_shipping_value				= $j("select[name='country_shipping']").val();
		var country_shipping_value				= $j("input[name='country_shipping']").val();
		var state_shipping_value				= $j("select[name='state_shipping']").val();
		var phone_1_shipping_value				= $j("input[name='phone_1_shipping']").val();
		var phone_2_shipping_value				= $j("input[name='phone_2_shipping']").val();
		var fax_shipping_value					= $j("input[name='fax_shipping']").val();
		
				
		if( address_type_name_shipping_value == "" ) {
			alert("Please enter Address Nickname!");
			$j('#address_type_name_shipping').css("color","red");
			return;
		}else {
			$j('#address_type_name_shipping').css("color","");
		}
		
		if( first_name_shipping_value == "" ) {
			alert("Please enter First Name!");
			$j('#first_name_shipping').css("color","red");
			return;
		}else {
			$j('#first_name_shipping').css("color","");
		}
		
		if( last_name_shipping_value == "" ) {
			alert("Please enter Last Name!");
			$j('#last_name_shipping').css("color","red");
			return;
		}else {
			$j('#last_name_shipping').css("color","");
		}
		
		if( address_1_shipping_value == "" ) {
			alert("Please enter Address!");
			$j('#address_1_shipping').css("color","red");
			return;
		}else {
			$j('#address_1_shipping').css("color","");
		}
		
		if( city_shipping_value == "" ) {
			alert("Please enter City!");
			$j('#city_shipping').css("color","red");
			return;
		}else {
			$j('#city_shipping').css("color","");
		}
		
		if( zip_shipping_value == "" ) {
			alert("Please enter Zip Code!");
			$j('#zip_shipping').css("color","red");
			return;
		}else {
			$j('#zip_shipping').css("color","");
		}
		
		if( !isValidZipCode( zip_shipping_value ) ) {
			alert("Please enter your zip code again!\n Example: K2E 7X3 (LDL DLD . Where L= letter , D = number)");
			$j('#zip_shipping').css("color","red");
			return;
		}else {
			$j('#zip_shipping').css("color","");
		}
				
		if( country_shipping_value == "" ) {
			alert("Please select Country!");
			$j('#td_country_shipping').css("color","red");
			return;
		}else {
			$j('#td_country_shipping').css("color","");
		}
		
		if( state_shipping_value == "" ) {
			alert("Please enter State!");
			$j('#td_state_shipping').css("color","red");
			return;
		}else {
			$j('#td_state_shipping').css("color","");
		}
		
		if( phone_1_shipping_value == "" ) {
			alert("Please enter Phone Number!");
			$j('#phone_1_shipping').css("color","red");
			return;
		}else {
			$j('#phone_1_shipping').css("color","");
		}
								
				
		$j.post( "index.php",
			{ 	option: 				"com_virtuemart", 
				page:					"checkout.index",
				func: 					func_shipping_value, 
				action: 				"ajax",
				address_type:			"ST",
				user_id: 				user_id_value, 
				user_info_id: 			user_info_id_value,
				company: 				company_shipping_value, 
				address_type_name: 		address_type_name_shipping_value, 
				first_name: 			first_name_shipping_value, 
				last_name: 				last_name_shipping_value, 
				middle_name: 			middle_name_shipping_value, 
				address_1: 				address_1_shipping_value, 
				address_2: 				address_2_shipping_value, 
				city: 					city_shipping_value, 
				zip: 					zip_shipping_value, 
				country: 				country_shipping_value, 
				state: 					state_shipping_value, 
				phone_1: 				phone_1_shipping_value, 
				phone_2: 				phone_2_shipping_value, 
				fax: 					fax_shipping_value,
				current_zip_checked:	sCurrentZipChecked, 		
				ajaxSend: function(){					
					$j("#msgReportUpdateShipping").html('<img width="24" src="'+ sImgLoading +'" align="absmiddle"/>&nbsp;&nbsp;Updating Deliver Information...'); 
				}
			},			
			function(data){
				aData	= data.split("[--3--]");
				if( aData[0] == "success" ) {
					$j("#msgReportDeliverInfo").css('display','block'); 
					if( $j("input[name='func_shipping']").val() == "userAddressUpdate" ) {
						$j("#msgReportDeliverInfo").html('Update Deliver Information Successful!');
					}else {
						$j("#msgReportDeliverInfo").html('Delivery address was added successfully!');
						$j("input[name='user_id_shipping']").val("");
						$j("input[name='user_info_id_shipping']").val("");
						$j("input[name='address_type_name_shipping']").val("");
						$j("input[name='first_name_shipping']").val("");
						$j("input[name='last_name_shipping']").val("");
						$j("input[name='middle_name_shipping']").val("");
						$j("input[name='company_shipping']").val("");
						$j("input[name='address_1_shipping']").val("");
						$j("input[name='address_2_shipping']").val("");
						$j("input[name='city_shipping']").val("");
						$j("input[name='zip_shipping']").val("");
//						$j("select[name='country_shipping']").val("");
						$j("select[name='state_shipping']").val("");
						$j("input[name='phone_1_shipping']").val("");
						$j("input[name='phone_2_shipping']").val("");
						$j("input[name='fax_shipping']").val("");
					}
					$j("#listDeliverInfo").html(aData[1]);										
					changDeliver(oForm.zip_checked.value, aData[2]);
					$j.modal.close();
					$j("#mainCheckOutForm").css("display","block");	
					$j("#msgReportShippingAddressRadio").css("display","none");						
				}else{
					if( $j("input[name='func_shipping']").val() == "userAddressUpdate" ) {
						$j("#msgReportUpdateShipping").html('Update Deliver Information Wrong!');
					}else {
						$j("#msgReportUpdateShipping").html('Add Deliver Information Wrong!');
					}
				}
			}
		);
	});
	
	
	//=======================================================================================================================
	$j("#calculateOrderPrice").click( function() {				
		sCurrentStateTax 	= $j("input[name='current_state_tax']").val();
		if( sCurrentStateTax ) {
			aCurrentStateTax	= sCurrentStateTax.split('_');					
			nStateTax	= getTaxFollowDeliveryInfo( aCurrentStateTax[0], aCurrentStateTax[1] );
		}
		
		if( nStateTax >= 0 ) {
			$j("input[name='state_tax']").val(nStateTax);
			nTotalItemTax	= parseFloat($j("input[name=sub_total_price]").val()*nStateTax)			
			$j("#calcualte-total-items-price").html( "$"+formatAsMoney($j("input[name=sub_total_price]").val()) );
			$j("#calcualte-tax-items-price").html( "$"+formatAsMoney(nTotalItemTax) );
			nDeliverFee	= 0;       			
			nDeliverFee	= getDeliverMethodFee($j("input[name='shipping_method']:checked").val(), "fee");
			$j("#calcualte-deliver-fee").html("$"+formatAsMoney(nDeliverFee));
			
			nDeliverExtraFee	= 0;
			nDeliverExtraFee	= $j("input[name=deliver_extra_price]").val();
						
			nDeliverTaxRate		= getDeliverMethodFee($j("input[name='shipping_method']:checked").val(), "tax");  
			nTotalDeliverTax	= ( parseFloat(nDeliverFee) + parseFloat(nDeliverExtraFee) ) * parseFloat(nStateTax);			
				
			$j("#calcualte-extra-deliver-fee").html("$" + formatAsMoney( parseFloat(nDeliverExtraFee) )); 			      			
			$j("#calcualte-total-deliver-fee").html("$" + formatAsMoney( parseFloat(nDeliverFee) + parseFloat(nDeliverExtraFee) + parseFloat(nTotalDeliverTax) ));// + "(With deliver fee:" + (parseFloat(nDeliverTaxRate)*100) + "%)"  );       			
			
			$j("input[name='total_deliver_tax_fee']").val(nTotalDeliverTax);
			$j("input[name='deliver_fee']").val(parseFloat(nDeliverFee) + parseFloat(nDeliverExtraFee) + parseFloat(nTotalDeliverTax));
	
			nTotalPrice		= 0;  			
			nTotalPrice		= parseFloat($j("input[name=sub_total_price]").val()) + parseFloat(nDeliverFee) + parseFloat(nDeliverExtraFee) + parseFloat(nTotalDeliverTax) + nTotalItemTax;  	
			$j("#calcualte-total-price").html("$"+formatAsMoney(nTotalPrice));
			$j("input[name='total_price']").val(parseFloat(nTotalPrice)); 			
			$j("input[name='total_tax']").val(parseFloat(nTotalItemTax) + nTotalDeliverTax); 	
		}else {
			$j("#calcualte-total-items-price").html( "$"+formatAsMoney($j("input[name=sub_total_price]").val()) );
			$j("#calcualte-tax-items-price").html( "$"+formatAsMoney($j("input[name=total_tax]").val()) );
			nDeliverFee	= 0;       			
			nDeliverFee	= getDeliverMethodFee($j("input[name='shipping_method']:checked").val(), "fee");
			$j("#calcualte-deliver-fee").html("$"+formatAsMoney(nDeliverFee));
			
			nDeliverExtraFee	= 0;
			nDeliverExtraFee	= $j("input[name=deliver_extra_price]").val();			
			
			nDeliverTaxRate		= getDeliverMethodFee($j("input[name='shipping_method']:checked").val(), "tax");  
			nTotalDeliverTax	= ( parseFloat(nDeliverFee) + parseFloat(nDeliverExtraFee) ) * parseFloat(nDeliverTaxRate);			
				
			$j("#calcualte-extra-deliver-fee").html("$" + formatAsMoney( parseFloat(nDeliverExtraFee) )); 			      			
			$j("#calcualte-total-deliver-fee").html("$" + formatAsMoney( parseFloat(nDeliverFee) + parseFloat(nDeliverExtraFee) + parseFloat(nTotalDeliverTax) ));// + "(With deliver fee:" + (parseFloat(nDeliverTaxRate)*100) + "%)"  );       			
			$j("input[name='total_deliver_tax_fee']").val(nTotalDeliverTax);
			$j("input[name='deliver_fee']").val(parseFloat(nDeliverFee) + parseFloat(nDeliverExtraFee) + parseFloat(nTotalDeliverTax));
	
			nTotalPrice	= 0;  	
			nTotalPrice	= parseFloat($j("input[name=sub_total_price]").val()) + parseFloat(nDeliverFee) + parseFloat(nDeliverExtraFee) + parseFloat(nTotalDeliverTax) + parseFloat($j("input[name=total_tax]").val());  
			//alert($j("input[name=sub_total_price]").val() + "=====" + parseFloat(nDeliverFee) + "=====" + parseFloat(nDeliverExtraFee) + "=====" + parseFloat(nTotalDeliverTax))	;
			$j("#calcualte-total-price").html("$"+formatAsMoney(nTotalPrice));
			$j("input[name='total_price']").val(parseFloat(nTotalPrice)); 
		}
//	   			    			
	});
	
	
	//=======================================================================================================================
	$j("#saveOrder").click( function() {   
		$j("#calculateOrderPrice").trigger('click');  
		
		if( parseFloat($j("input[name='total_price']").val()) <= 0 ) {
			alert(sEmptyCart);
			return;
		}
		
		if( bIsValidDate <= 0 ) {
			alert(sInValidDateTime);
			return;
		}
		
		myArray = { "payment_method" 			: "Payment Method", 
					"name_on_card" 				: "Name On Card", 
					"credit_card_number" 		: "Credit Card Number", 
					"credit_card_security_code" : "Cedit Card Security Code", 
					"expire_month" 				: "Expire Month"
				  };
	
		selectElementArray	= new Array("payment_method", "expire_month" );
		
		var objValid	= "";
		for ( key in myArray ) {
			if( jQuery.inArray( key, selectElementArray ) != -1 ) {
				objValid	= "select[name='" + key + "']";
			}else {
				objValid	= "input[name='" + key + "']";
			}
			
			if( jQuery.trim($j(objValid).val()) == "" ) {
				alert('Please enter your "' + myArray[key] + '" !');
				$j(objValid).focus();
				return;
			}
		}
				
		//if ( confirm('Do you want to save this order?') == true ) {
			$j.post( "index.php",
				{ 	option: 					"com_ajaxorder", 
					task: 						"confirmOrder",
					user_id: 					$j("input[name='user_id']").val(), 
					user_name: 					$j("input[name='user_name']").val(), 
					account_email: 				$j("input[name='account_email']").val(), 
					user_info_id: 				$j("input[name='ship_to_info_id']:checked").val(), 
					occasion: 					$j("select[name='customer_occasion']").val(), 
					shipping_method: 			$j("input[name='shipping_method']:checked").val(), 
					card_msg: 					$j("textarea[name='card_msg']").val(), 
					signature: 					$j("textarea[name='signature']").val(), 
					card_comment: 				$j("textarea[name='card_comment']").val(), 
					deliver_day: 				$j("select[name='delivery_day2']").val(), 
					deliver_month: 				$j("select[name='delivery_month2']").val(), 
					payment_method_state:		$j("input[name='payment_method_state']:checked").val(), 
					payment_method: 			$j("select[name='payment_method']").val(), 
					name_on_card: 				$j("input[name='name_on_card']").val(), 
					credit_card_number: 		$j("input[name='credit_card_number']").val(), 
					credit_card_security_code: 	$j("input[name='credit_card_security_code']").val(), 
					expire_month: 				$j("select[name='expire_month']").val(), 
					expire_year: 				$j("select[name='expire_year']").val(), 
					product_id_string: 			$j("input[name='product_id_string']").val(), 
					quantity_string: 			$j("input[name='quantity_string']").val(), 
					total_price: 				$j("input[name='total_price']").val(), 
					deliver_fee: 				$j("input[name='deliver_fee']").val(), 
					sub_total_price: 			$j("input[name='sub_total_price']").val(), 
					total_tax: 					$j("input[name='total_tax']").val(), 
					state_tax: 					$j("input[name='state_tax']").val(), 
					total_deliver_tax_fee: 		$j("input[name='total_deliver_tax_fee']").val(), 
					coupon_discount: 			$j("input[name='coupon_discount']").val(), 
					vendor_currency_string: 	$j("input[name='vendor_currency_string']").val(), 
					vendor_currency_string: 	$j("input[name='vendor_currency_string']").val(), 
					find_us: 					$j("input[name='find_us']:checked").val(), 
					ajaxSend: function(){
						$j("#msgCheckoutReport").html('<img src="'+ sImgLoading +'" align="absmiddle"/>&nbsp;&nbsp;Order checkout is processing...'); 
					}
				},			
				function(data){
					aData	= data.split("[--1--]");
					if( jQuery.trim( aData[0] ) == "success" ) {
						window.location.href = "index.php?page=checkout.thankyou&order_id=" + aData[1] + "&option=com_virtuemart&Itemid=80&msg=" + aData[2];
					}else {
						$j("#msgCheckoutReport").html( aData[1] );
					}
				}
			);
		//}
		
	});
});	

//=======================================================================================================================
function editDeliver ( user_info_id_value, user_id_value ) {				
	$j("#updateShippingInfo").attr("value", "Update Address");
	sCurrentZipChecked = $j("input[name='ship_to_info_id']:checked").val();
	$j.post( "index.php",
		{ 	option: 				"com_ajaxorder", 
			task: 					"getUserAddress", 
			user_id: 				user_id_value, 
			current_zip_checked:	sCurrentZipChecked, 
			user_info_id: 			user_info_id_value
		},			
		function(data){
			aData	= data.split("[--1--]");
			if( aData[0] == "success" ) {				
				
				aData2	= aData[1].split("[--2--]");
				$j("input[name='func_shipping']").val("userAddressUpdate");	
				$j("input[name='user_id_shipping']").val(aData2[0]);
				$j("input[name='user_info_id_shipping']").val(aData2[1]);
				$j("input[name='address_type_name_shipping']").val(aData2[2]);
				$j("input[name='first_name_shipping']").val(aData2[3]);
				$j("input[name='last_name_shipping']").val(aData2[4]);
				$j("input[name='middle_name_shipping']").val(aData2[5]);
				$j("input[name='company_shipping']").val(aData2[6]);
				$j("input[name='address_1_shipping']").val(aData2[7]);
				$j("input[name='address_2_shipping']").val(aData2[8]);
				$j("input[name='city_shipping']").val(aData2[9]);
				$j("input[name='zip_shipping']").val(aData2[10]);
				$j("select[name='country_shipping']").val(aData2[11]);
				changeStateList("state_shipping", "country_shipping");
				$j("select[name='state_shipping']").val(aData2[12]);
				$j("input[name='phone_1_shipping']").val(aData2[13]);
				$j("input[name='phone_2_shipping']").val(aData2[14]);
				$j("input[name='fax_shipping']").val(aData2[15]);	
				$j('#addShippingInfoForm').modal({onOpen: modalOpen, position: ["25%","25%"]});					
			}else{
				$j("#msgReportDeliverInfo").css('display','block'); 
				$j("#msgReportDeliverInfo").html('Edit Deliver Information Operation Wrong!');					
			}
		}
	);
}

//=======================================================================================================================
function deleteDeliver ( user_info_id_value, user_id_value ) {		
	sCurrentZipChecked = $j("input[name='ship_to_info_id']:checked").val();	
	$j.post( "index.php",
		{ 	option: 			"com_virtuemart", 
			page:				"account.shipping",
			func: 				"useraddressdelete", 
			action: 			"ajax",
			user_id: 			user_id_value, 
			user_info_id: 		user_info_id_value, 
			current_zip_checked:	sCurrentZipChecked,
			ajaxSend: function(){
				$j("#msgReportDeliverInfo").css('display','block'); 
				$j("#msgReportDeliverInfo").html('<img src="'+ sImgLoading +'" align="absmiddle"/>&nbsp;&nbsp;Deleting Deliver Information...'); 					
			}
		},			
		function(data){
			aData	= data.split("[--3--]");
			if( aData[0] == "success" ) {
				$j("#msgReportDeliverInfo").css('display','block'); 
				$j("#msgReportDeliverInfo").html('Delivery address was deleted successfully!');	
				
				if( aData[1] == "noshipping" ) {					
					$j("#msgReportShippingAddressRadio").css("display","block");
					$j("#listDeliverInfo").html("<input type='hidden' name='zip_checked' value='' >");
					$j("#mainCheckOutForm").css("display","none");
				}else {
					$j("#msgReportShippingAddressRadio").css("display","none");
					$j("#mainCheckOutForm").css("display","block");
					$j("#listDeliverInfo").html(aData[1]);
					//$j("input[name='current_state_tax']").val(aData[2]);
					changDeliver(oForm.zip_checked.value, aData[2]);
				}					
			
			}else{
				$j("#msgReportDeliverInfo").css('display','block'); 
				$j("#msgReportDeliverInfo").html('Delete Deliver Information Wrong!');					
			}
		}
	);
}


function modalOpen (dialog) {
	dialog.overlay.fadeIn('fast', function () {
		dialog.container.fadeIn('fast', function () {
			dialog.data.hide().slideDown('fast');	 
		});
	});
}


//=======================================================================================================================
function changDeliver( sOptionString, sCurrentStateTax ) {
	if( !sOptionString )  {
		sOptionString	= "";
	}
	//alert(sOptionString);
	
	if( !$j("input[name='current_state_tax']").val() ) {		
		$j("input[name='current_state_tax']").val($j("input[name='current_state_tax']").val('current_state_tax_tmp'));
	}else {		
		$j("input[name='current_state_tax']").val(sCurrentStateTax);
		//alert($j("input[name='current_state_tax']").val());
	}				
	
	nExtraDayMonth 		= 0;
	nDaysValid			= 0;
	nMonthsValid		= 0;  
	var sDeliverExtra3	= "";
		           			

	oForm.zip_checked_value.value = sOptionString;
	aOption = sOptionString.split("[--1--]");
	
	if( isNaN(aOption[2]) ) {
		aOption[2] = 0;
	}
	
	if( isNaN(aOption[1]) ) {
		aOption[1] = 0;
	}               			
	
	if( aOption[1] == 0 ) {
		sDeliverExtra3		= sDeliverExtra2;
	}else {
		sDeliverExtra3		= sDeliverExtra;
	}
	       			
	
	nDeliverExtraPrice	= aOption[2];
	$j("input[name=deliver_extra_price]").val(aOption[2]);
	$j("input[name=deliver_extra_day]").val(aOption[1]);
	nDaysOfMonth		= parseFloat(oForm.daysofmonthnow.value);
	nDaysValid			= nIndex + parseFloat(aOption[1]);
	               			
	
	if( nDaysOfMonth < nDaysValid ) {
		nExtraDayMonth	= nDaysValid - nDaysOfMonth;
	}
	
	
	if( nExtraDayMonth || parseFloat(oForm.daysofmonthnow.value) <= nDaysValid  ) {
		               				
		if( parseFloat(oForm.daysofmonthnow.value) <= nDaysValid  ) {
			nDaysValid = 1;
		}else {
			nDaysValid	= nExtraDayMonth;
		}
		
		if( parseFloat(oForm.monthnow.value) == 12 ) {
			nMonthsValid = 1;
		}else {
			nMonthsValid = parseFloat(oForm.monthnow.value) + 1;
		}
		
		changeUnAvailableDate( parseFloat(nMonthsValid) );
	}else {
		if( parseFloat(oForm.cutofftime.value) > 0 && !parseFloat(aOption[1]) ) {
			nDaysValid = nDaysValid + 1;
		}
//		alert(parseFloat(nDaysValid));
		nMonthsValid = parseFloat(oForm.monthnow.value);
	}     
		        			
	
	if( parseFloat(aOption[1]) > 0 || parseFloat(aOption[2]) > 0) {
		sDeliverExtra3 	= sDeliverExtra3.replace( "{price}", aOption[2] );
		sDeliverExtra3 	= sDeliverExtra3.replace( "{day}", aOption[1] );  
		$j("#deliver_extra_post_code").html(sDeliverExtra3);
		$j("#deliver_extra_post_code").css("display","block");
	}else {
		$j("#deliver_extra_post_code").html("");
		$j("#deliver_extra_post_code").css("display","none");
	}		
	
				
	if( parseFloat(oForm.delivery_month2.value) <= nMonthsValid && parseFloat(oForm.delivery_day2.value) <= nDaysValid) {              			
		oForm.delivery_day2.options[nDaysValid].selected = true;               			
		if( nMonthsValid ) {
			oForm.delivery_month2.options[nMonthsValid].selected = true; 
		}else {
			nMonthsValid = parseFloat(oForm.monthnow.value);
			oForm.delivery_month2.options[nMonthsValid].selected = true; 
		}
	}
	
	//Deliver Extra Free for The Same Day
	changeDeliverExtraSameDay();	
	
}


function changeDeliverExtraSameDay( ) {	
//	alert( parseFloat(oForm.delivery_month2.value) + "===" + parseFloat(oForm.monthnow.value) + "===" + parseFloat(oForm.delivery_day2.value) + "===" + parseFloat(oForm.daynow.value) + "===" + parseFloat(oForm.cutofftime.value) );
	if( parseFloat(oForm.delivery_month2.value) == parseFloat(oForm.monthnow.value) &&  
		parseFloat(oForm.delivery_day2.value) == parseFloat(oForm.daynow.value) &&
		parseFloat(oForm.cutofftime.value) <= 0
	   ) {		
		sDeliverFeeExtraSameDay4 		= sDeliverFeeExtraSameDay.replace( "{price}", parseFloat(nDeliverFeeExtraSameDay) );  	
		$j("#deliver_extra_same_day").html(sDeliverFeeExtraSameDay4);
		$j("#deliver_extra_same_day").css("display","block");
		$j("input[name=deliver_extra_price]").val(parseFloat(nDeliverExtraPrice) + parseFloat(nDeliverFeeExtraSameDay));	               			
	
	} else if( 	parseFloat(oForm.delivery_month2.value) > nMonthsValid || 
				(parseFloat(oForm.delivery_month2.value) == nMonthsValid && parseFloat(oForm.delivery_day2.value) > nDaysValid) ||
				(parseFloat(oForm.delivery_month2.value) == nMonthsValid && parseFloat(oForm.delivery_day2.value) == nDaysValid && parseFloat(oForm.cutofftime.value) > 0 )               				
	) {
		$j("#deliver_extra_same_day").html("");
		$j("#deliver_extra_same_day").css("display","none");
		if( parseFloat($j("input[name=deliver_extra_price]").val()) >= parseFloat(nDeliverFeeExtraSameDay) ) {
			$j("input[name=deliver_extra_price]").val(parseFloat($j("input[name=deliver_extra_price]").val()) - parseFloat(nDeliverFeeExtraSameDay));	
		}
		$j("input[name=deliver_extra_day]").val(0);
	}                  			
	
//	alert(parseFloat(oForm.delivery_day2.value));
	nSpecialDeliverExtraPrice = isSpecialDate( parseFloat(oForm.delivery_month2.value), parseFloat(oForm.delivery_day2.value) );
	if( nSpecialDeliverExtraPrice > 0 ) {
		sDeliverFeeExtraSameDay4 		= sDeliverFeeExtraSameDay.replace( "{price}", parseFloat(nSpecialDeliverExtraPrice) );  	
		$j("#deliver_extra_same_day").html(sDeliverFeeExtraSameDay4);
		$j("#deliver_extra_same_day").css("display","block");		
		$j("input[name=deliver_extra_price]").val(parseFloat($j("input[name=deliver_extra_price]").val() + parseFloat(nSpecialDeliverExtraPrice)));
	}	
	//alert(oForm.cutofftime.value + "====="  +$j("input[name=deliver_extra_price]").val()	);
}


function noticeDeliver() {
	var sDeliverExtra3	= "";
	aOption = oForm.zip_checked_value.value.split("[--1--]");
	
	
	if( parseFloat(oForm.delivery_month2.value) < nMonthNow || ( parseFloat(oForm.delivery_month2.value) == nMonthNow && parseFloat(oForm.delivery_day2.value) < nDayNow ) ) {
		alert(sInValidDateTime);
		bIsValidDate	= 0;
		return;
	}
	
	
	//alert(oForm.delivery_month2.value + "====" + oForm.delivery_day2.value );
	if( isUnAvailableDate( parseFloat(oForm.delivery_month2.value), parseFloat(oForm.delivery_day2.value)) ) {
		$j("#deliver_extra_same_day").html(sNoDeliveryService);
		$j("#deliver_extra_same_day").css("display","block");
		bIsValidDate	= 0;
		return;
	}

			  			
//	alert(parseFloat(oForm.delivery_month2.value) +"=-===="+ nMonthsValid +"=-===="+ parseFloat(oForm.delivery_day2.value)+"=-===="+nDaysValid);
	if( parseFloat(oForm.delivery_month2.value) < nMonthsValid ||  parseFloat(oForm.delivery_day2.value) < nDaysValid ) {
		$j("#deliver_extra_same_day").html(sInValidDateTime);
		$j("#deliver_extra_same_day").css("display","block");
		$j("#deliver_extra_post_code").css("display","none");	
		if( parseFloat($j("input[name=deliver_extra_price]").val()) >= parseFloat(nDeliverFeeExtraSameDay) ) {
			$j("input[name=deliver_extra_price]").val(parseFloat($j("input[name=deliver_extra_price]").val() - parseFloat(nDeliverFeeExtraSameDay)));	
		}
		$j("input[name=deliver_extra_day]").val(0);
		bIsValidDate	= 0;
	}else {
		$j("#deliver_extra_post_code").css("display","block");	
		bIsValidDate	= 1;
	}
	
	if( !isUnAvailableDate( parseFloat(oForm.delivery_month2.value), parseFloat(oForm.delivery_day2.value)) ) {
		//Deliver Extra Free for The Same Day
		changeDeliverExtraSameDay(); 	
	}	
}


function changeUnAvailableDate( nCurrentMonth ) {
	//alert(nCurrentMonth + "===" + sUnAvailableDate);
	if( sUnAvailableDate ) {               				
		aUnAvailableDate = sUnAvailableDate.split("[--1--]");
		nCurrentMonth    = parseFloat(nCurrentMonth);			
		
		for( i = 0; i < aUnAvailableDate.length ; i++  ) {
			if( aUnAvailableDate[i] != "" ) {
				aUnAvailableItem = aUnAvailableDate[i].split("/");
				if( nCurrentMonth == aUnAvailableItem[0] ) {
					oForm.delivery_day2.options[aUnAvailableItem[1]].style.color = "red";
					oForm.delivery_day2.options[aUnAvailableItem[1]].text		= oForm.delivery_day2.options[aUnAvailableItem[1]].text + " - No delivery service  ";
				}else {
					oForm.delivery_day2.options[aUnAvailableItem[1]].style.color = "black";
					oForm.delivery_day2.options[aUnAvailableItem[1]].text		= oForm.delivery_day2.options[aUnAvailableItem[1]].value;
				}
			}
		}
	
	}
}


function getTaxFollowDeliveryInfo( sCountry, sState ) {
//	alert(sCountry + "===" + sState+ "===" + sStateTax);
	if( sStateTax && sCountry && sState ) {               				
		aStateTax 		= sStateTax.split("[--2--]");
		
		for( i = 0; i < aStateTax.length ; i++  ) {
			aStateTaxItem 	= aStateTax[i].split("[--1--]");
			if( aStateTaxItem[0] == sCountry && aStateTaxItem[1] == sState ) {
				return aStateTaxItem[2];	
			}
		}
	}
	return -1;
}


function isUnAvailableDate( nCurrentMonth, nCurrentDay ) {
	if( sUnAvailableDate ) {               				
		aUnAvailableDate = sUnAvailableDate.split("[--1--]");           					
		for( i = 0; i < aUnAvailableDate.length ; i++  ) {
			if( aUnAvailableDate[i] ) {
				aUnAvailableItem = aUnAvailableDate[i].split("/");
				if( nCurrentMonth == aUnAvailableItem[0] && nCurrentDay == aUnAvailableItem[1] ) {
					return true;
					break;
				}
			}
		}
		
		return false;
	}
}    

function isSpecialDate( nCurrentMonth, nCurrentDay ) {
	if( sSpecialDeliver ) {               				
		aSpecialDeliver = sSpecialDeliver.split("[--1--]");           					
		for( i = 0; i < aSpecialDeliver.length ; i++  ) {
			if( aSpecialDeliver[i] ) {
				aSpecialDeliverItem = aSpecialDeliver[i].split("/");
				if( nCurrentMonth == aSpecialDeliverItem[0] && nCurrentDay == aSpecialDeliverItem[1] ) {
					return aSpecialDeliverItem[2];
					break;
				}
			}
		}
		
		return 0;
	}
}     


function validDeliverDate( submit_action ) {
	oForm		= document.adminForm;
	$nExtraDay	= $j("input[name=deliver_extra_day]").val();
	
	if( oForm.delivery_day2.value == 0 ) {
		alert(sEnterDeliverDay);
		return;
	}
	
	if( oForm.delivery_month2.value == 0 ) {
		alert(sEnterDeliverMonth);
		return;
	}             	               		
	
	//alert( parseFloat(oForm.delivery_month2.value) + "====" + nMonthsValid + "====" + parseFloat(oForm.delivery_day2.value) + "====" + nDaysValid + "====" + (parseFloat(oForm.delivery_month2.value) < nMonthsValid) + "====" + (parseFloat(oForm.delivery_day2.value) < nDaysValid ));
	if( parseFloat(oForm.delivery_month2.value) < nMonthsValid ||  parseFloat(oForm.delivery_day2.value) < nDaysValid ) {
		alert(sInValidDateTime);
		return;
	}
	
	if( isUnAvailableDate( parseFloat(oForm.delivery_month2.value), parseFloat(oForm.delivery_day2.value)) ) {
		alert(sNoDeliveryService);
		return;
	}
	
	oForm.delivery_day.value 	= oForm.delivery_day2.value;
	oForm.delivery_month.value = oForm.delivery_month2.value;
	
	if( submit_action ) {
		oForm.submit();
	}
}

function getDeliverMethodFee( methodID, sType ) {
	if( sDeliverMethodFee ) {               				
		aDeliverMethodFee = sDeliverMethodFee.split("[--2--]");           					
		for( i = 0; i < aDeliverMethodFee.length ; i++  ) {
			if( aDeliverMethodFee[i] ) {
				aDeliverMethodFeeItem = aDeliverMethodFee[i].split("[--1--]");
				if( methodID == aDeliverMethodFeeItem[0] ) {
					if( sType == "tax" ) {
						return aDeliverMethodFeeItem[2];
					}else {
						return aDeliverMethodFeeItem[1];
					}
					break;
				}
			}
		}   					
		return 0;
	}
}     
	
//=======================================================================================================================
function changeStateList( state_list_name, element_country_id ) { 
	if( !element_country_id ) {
		element_country_id	= 'country_billing';
	}
	
	if( !state_list_name ) {
		state_list_name	= 'state_billing';
	}	
	
	if( element_country_id == "country_shipping" ) {
		selected_country	= "CAN";
	}else {
		var nSelectIndex		= document.getElementById(element_country_id).selectedIndex;
		var selected_country 	= document.getElementById(element_country_id).options[nSelectIndex].value;
	}

		
//		alert(selected_country);
	changeDynaList2(state_list_name, eval(state_list_name), selected_country, "", "");
	
}

function changeDynaList2( listname, source, key, orig_key, orig_val ) {
//	alert(listname + "==" + (listname == "state_billing") );
	
	var list2 = document.getElementById(listname);
//	alert(list2);
//	alert( listname + "===" + list2.options.length);
	
	// empty the list
	for (i in list2.options.length) {
		list2.options[i] = null;
	}
	
	i = 0;
	s = "";
	for (x in source) {
		if (source[x][0] == key) {
			opt = new Option();
			opt.value = source[x][1];
			opt.text = source[x][2];
			if ((orig_key == key && orig_val == opt.value) || i == 0) {
				opt.selected = true;
			}
			
			list2.options[i++] = opt;
		}
	}
	list2.length = i;
}
		
	
//=======================================================================================================================
function isValidZipCode( value ) {
   var re = /^[A-Za-z0-9\s]{6,7}$/;
   return (re.test(value));
}
			
			
function isInteger (s)  {
	var i;	
	if (isEmpty(s))
	if (isInteger.arguments.length == 1) return 0;
	else return (isInteger.arguments[1] == true);
	
	for (i = 0; i < s.length; i++) {
	 	var c = s.charAt(i);	
	 	if (!isDigit(c)) return false;
	}
	
	return true;
}

function isEmpty(s) {
  	return ((s == null) || (s.length == 0));
}

function isDigit (c) {
  	return ((c >= "0") && (c <= "9"));
}

function formatAsMoney(mnt) {
    mnt -= 0;
    mnt = (Math.round(mnt*100))/100;
    return (mnt == Math.floor(mnt)) ? mnt + '.00' : ( (mnt*10 == Math.floor(mnt*10)) ? mnt + '0' : mnt);
}	

function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}