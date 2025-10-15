/* 
 * Holds item specific properties sent to external retailer system
 */
function ExternalRetailerItemLinker()
{
	this.itemName = null;
	this.departmentId = null;
	this.departmentName = null;
	this.price = null;
	this.salePrice = null;
	this.salePriceStart = null;
	this.salePriceEnd = null;
	this.giftItemComment = null;
	this.itemKey = null;
	this.has = null;
	this.upccd = null;
	this.wants = null;
	
	this.setItemName = setExternalRetailerItemLinkerSetItemName;
	this.setDepartmentId = setExternalRetailerItemLinkerSetDepartmentId;
	this.setDepartmentName = setExternalRetailerItemSetDepartmentName;
	this.setPrice = setExternalRetailerItemLinkerSetPrice;
	this.setSalePrice = setExternalRetailerItemLinkerSetSalePrice;
	this.setSalePriceStart = setExternalRetailerItemLinkerSetSalePriceStart;
	this.setSalePriceEnd = setExternalRetailerItemLinkerSetSalePriceEnd;
	this.setGiftItemComment = setExternalRetailerItemLinkerSetGiftItemComment;
	this.setItemKey = setExternalRetailerItemLinkerSetItemKey;
	this.setHas = setExternalRetailerItemLinkerHas;
	this.setUpccd = setExternalRetailerItemLinkerUpccd;
	this.setWants = setExternalRetailerItemLinkerWants;
	
	this.toQueryString = ExternalRetailerItemLinkerToQueryString;	
}
function setExternalRetailerItemLinkerSetItemName( value )
{
	this.itemName = value;
}
function setExternalRetailerItemLinkerSetDepartmentId( value )
{
	this.departmentId = value;
}
function setExternalRetailerItemSetDepartmentName( value )
{
	this.departmentName = value;
}
function setExternalRetailerItemLinkerSetPrice( value )
{
	this.price = value;
}
function setExternalRetailerItemLinkerSetSalePrice( value )
{
	this.salePrice = value;
}
function setExternalRetailerItemLinkerSetSalePriceStart( value )
{
	this.salePriceStart = value;
}
function setExternalRetailerItemLinkerSetSalePriceEnd( value )
{
	this.salePriceEnd = value;
}
function setExternalRetailerItemLinkerSetGiftItemComment( value )
{
	this.giftItemComment = value;
}
function setExternalRetailerItemLinkerSetItemKey( value )
{
	this.itemKey = value;
}
function setExternalRetailerItemLinkerHas( value )
{
	this.has = value;
}
function setExternalRetailerItemLinkerUpccd( value )
{
	this.upccd = value;
}
function setExternalRetailerItemLinkerWants( value )
{
	this.wants = value;
}
function ExternalRetailerItemLinkerToQueryString( )
{
	var queryString = '';
	queryString += ( this.itemName != null ? 'itemName=' + escape(this.itemName) + '&' : '' );
 	queryString += ( this.departmentId != null ? 'departmentId=' + this.departmentId + '&' : '' );
	queryString += ( this.departmentName != null ? 'departmentName=' + this.departmentName + '&' : '' );
	queryString += ( this.price != null ? 'price=' + this.price + '&' : '' );
	queryString += ( this.salePrice != null ? 'salePrice=' + this.salePrice + '&' : '' );
	queryString += ( this.salePriceStart != null ? 'salePriceStart=' + this.salePriceStart + '&' : '' );	
	queryString += ( this.salePriceEnd != null ? 'salePriceEnd=' + this.salePriceEnd + '&' : '' );	
	queryString += ( this.giftItemComment != null ? 'giftItemComment=' + escape(this.giftItemComment) + '&' : '' );	
	queryString += ( this.itemKey != null ? 'itemKey=' + this.itemKey + '&' : '' );
	queryString += ( this.has != null ? 'qtyReceived=' + this.has + '&' : '' );	
	queryString += ( this.wants != null ? 'qty_wanted=' + this.wants + '&' : '' );		
	queryString += ( this.upccd != null ? 'upccd=' + this.upccd + '&' : '' );	
	return queryString;
}


/*
 * Holds registry specific properties sent to external retailer system
 */
function ExternalRetailerRegistryLinker()
{

	this.registryId = null;
	this.registrantLastName = null;
	this.registrantFirstName = null;
	this.coRegistrantLastName = null;
	this.coRegistrantFirstName = null;
	this.registrantPhoneNo = null;
	this.registrantStoreNo = null;
	this.retailerId = null;
	this.registryEventDate = null;
	this.eventName = null; 
//	this.returnLink = null;
	this.languageId = null;
	this.messageToGuests = null;
	this.emailAddress = null;
	
	this.setRegistryId = setExternalRetailerRegistryLinkerSetRegistryId;
	this.setRegistrantLastName = setExternalRetailerRegistryLinkerSetRegistrantLastName;
	this.setRegistrantFirstName = setExternalRetailerRegistryLinkerSetRegistrantFirstName;
	this.setCoRegistrantLastName = setExternalRetailerRegistryLinkerSetCoRegistrantLastName;
	this.setCoRegistrantFirstName = setExternalRetailerRegistryLinkerSetCoRegistrantFirstName;
	this.setRegistrantPhoneNo = setExternalRetailerRegistryLinkerSetRegistrantPhoneNo;
	this.setRegistrantStoreNo = setExternalRetailerRegistryLinkerSetRegistrantStoreNo;
	this.setRetailerId = setExternalRetailerRegistryLinkerSetRetailerId;
	this.setRegistryEventDate = setExternalRetailerRegistryLinkerSetRegistryEventDate;
	this.setEventName = setExternalRetailerRegistryLinkerSetEventName;
	this.setLanguageId = setExternalRetailerRegistryLinkerSetLanguageId;
	this.setMessageToGuests = setExternalRetailerRegistryLinkerSetMessageToGuests;
	this.setEmailAddress = setExternalRetailerRegistryLinkerSetEmailAddress;
	this.toQueryString = ExternalRetailerRegistryLinkerToQueryString;
}
function setExternalRetailerRegistryLinkerSetRegistryId( value )
{
	this.registryId = value;
}
function setExternalRetailerRegistryLinkerSetRegistrantLastName( value )
{
	this.registrantLastName = value;
}
function setExternalRetailerRegistryLinkerSetRegistrantFirstName( value )
{
	this.registrantFirstName = value;
}
function setExternalRetailerRegistryLinkerSetCoRegistrantLastName( value )
{
	this.coRegistrantLastName = value;
}
function setExternalRetailerRegistryLinkerSetCoRegistrantFirstName( value )
{
	this.coRegistrantFirstName = value;
}
function setExternalRetailerRegistryLinkerSetRegistrantPhoneNo( value )
{
	this.registrantPhoneNo = value;
}
function setExternalRetailerRegistryLinkerSetRegistrantStoreNo( value )
{
	this.registrantStoreNo = value;
}
function setExternalRetailerRegistryLinkerSetRetailerId( value )
{
	this.retailerId = value;
}
function setExternalRetailerRegistryLinkerSetRegistryEventDate( value )
{
	this.registryEventDate = value;
}
function setExternalRetailerRegistryLinkerSetEventName( value )
{
	this.eventName = value;
}
function setExternalRetailerRegistryLinkerSetLanguageId( value )
{
	this.languageId = value;
}
function setExternalRetailerRegistryLinkerSetMessageToGuests( value )
{
	this.messageToGuests = value;
}
function setExternalRetailerRegistryLinkerSetEmailAddress( value )
{
	this.emailAddress = value;
}
function ExternalRetailerRegistryLinkerToQueryString( )
{
	var queryString = '';
	queryString += ( this.registryId != null ? 'registryId=' + this.registryId + '&' : '' );
	queryString += ( this.registrantLastName != null ? 'registrantLastName=' + this.registrantLastName + '&' : '' );
	queryString += ( this.registrantFirstName != null ? 'registrantFirstName=' + this.registrantFirstName + '&' : '' );	
	queryString += ( this.coRegistrantLastName != null ? 'coRegistrantLastName=' + this.coRegistrantLastName + '&' : '' );
	queryString += ( this.coRegistrantFirstName != null ? 'coRegistrantFirstName=' + this.coRegistrantFirstName + '&' : '' );	
	queryString += ( this.registrantPhoneNo != null ? 'registrantPhoneNo=' + this.registrantPhoneNo + '&' : '' );
	queryString += ( this.registrantStoreNo != null ? 'registrantStoreNo=' + this.registrantStoreNo + '&' : '' );
	queryString += ( this.retailerId != null ? 'retailerId=' + this.retailerId + '&' : '' );
	queryString += ( this.registryEventDate != null ? 'registryEventDate=' + this.registryEventDate + '&' : '' );
	queryString += ( this.eventName != null ? 'eventName=' + this.eventName + '&' : '' );
	queryString += ( this.messageToGuests != null ? 'messageToGuests=' + escape(this.messageToGuests) + '&' : '' );
	queryString += ( this.emailAddress != null ? 'registrant_email=' + escape(this.emailAddress) + '&' : '' );
	queryString += ( this.languageId != null ? 'languageId=' + this.languageId : '' );	
	return queryString;
}