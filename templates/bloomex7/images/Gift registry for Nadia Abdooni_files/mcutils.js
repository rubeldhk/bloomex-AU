// Some error defines for easy reference
var ERROR_REQUIRED = 'error_required';
var ERROR_MIN_LENGTH = 'error_min_length';
var ERROR_MAX_LENGTH = 'error_max_length';
var bSecurity = false; //If you need to make the links secure, you will need to override this in your template
						//or in the actual page!!!!!!!!!
						
var unloadHandler = null;

// Used to communicate a namve value pair to be sent to the next transaction in function go
function TxnVar( name, value )
{
	this.name = name;
	this.value = value;
	this.newString = TxnVarNewString;
}

function TxnVarNewString()
{
	return "new TxnVar( '" + this.name + "', '" + this.value + "' )";
}

// Causes navigation to occur but allows required fields to be ignored
function goNoRequired( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses )
{
	go( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, true );
}

// this is for allowing https urls to be forwarded
function gos( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff )
{
	if( bSecurity )
	{
		var theForm   = document.forms['navform'];
		var theAction = theForm.action;

		// check to protect against quick double clicks
		if ( theAction.substring(0,5) != 'https' )
		{
		
			var newAction = null;
			
			var i = theAction.indexOf('p');
			i++;
		
			newAction = 'https'+theAction.substr(i);	
			//now change port if test environment port is 8080
			var x = newAction.indexOf('9');
			if( x > 0 )
			{
				var rest = x +4;
				var c    = x - 1;
				newAction = newAction.substr(0,c) + ':9443' + newAction.substr(rest);
			}
			theForm.action = newAction;
		}		
	}

	go( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff );
}


// Navigates forward but resets th e state to it's original condition
function goReset( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff )
{
	var txnVar = new TxnVar( 'ResetState', 'true' );
	if( txnVars == null )
		txnVars = new Array( txnVar );
	else
		txnVars[ txnVars.length ] = txnVar;
		
	go( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff );
}

// Navigates forward but resets th e state to it's original condition
function goResets( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff )
{
	var txnVar = new TxnVar( 'ResetState', 'true' );
	if( txnVars == null )
		txnVars = new Array( txnVar );
	else
		txnVars[ txnVars.length ] = txnVar;
		
	gos( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff );
}

function go( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff, bNoExpire, groupid )
{
	// Make sure that the form elements have been created.  The variable initPageInvoked was created in the template file when 
	// the page is first loaded.  When the onload event occurs the initPage function is called.  However, the page may not have 
	// finished loading because of slow modems and images.  Call initPage in this case so that everything is set up to move on to
	// the next transaction.
	if( !initPageInvoked )
		initPage();

	var form = document.navform;
	form.navkey.value = navkey;
	form.transaction.value = transaction;
	if( langid != null )
		form.LANGID.value = langid;

	// Override the ForceExpire boolean if present
	if( bNoExpire && form.elements.ForceExpire && form.elements.ForceExpire.value != '' )
		form.elements.ForceExpire.value = false;

	// Make sure these are cleared
	form.elements.FieldErrors.value = '';
	form.elements.ExcludeProcesses.value = '';
	if( bNoSave == null )
		form.elements.NoSave.value = '';
	else
		form.elements.NoSave.value = bNoSave ? 'true' : 'false';

	var i;
	if( excludeProcesses != null )
	{
		var ep = null;
		for( i=0; i < excludeProcesses.length; i++ )
		{
			if( ep == null )
				ep = excludeProcesses[i];
			else
				ep += ',' + excludeProcesses[i];
		}
		form.elements.ExcludeProcesses.value = ep;
	}
	var fieldErrors = null;

	if( !bNoSave && formElems != null )
	{
		fieldErrors = formElems.validateFields( enforceRequiredOff, groupid );
		if( fieldErrors != null )
		{
			// There are errors on the page.  Set the error string into the request and send this request right back to this
			// page so that we can redisplay it with some errors embedded
			form.elements.FieldErrors.value = fieldErrors;
			form.navkey.value = form.elements.lastNavkey.value;
			form.transaction.value = form.elements.lastTransaction.value;
		}
	}

	var changedString = null;
	if( txnVars != null )
	{
		for( i=0; i < txnVars.length; i++ )
		{
			if( changedString != null && changedString.length > 0 )
				changedString += '~~';
			else changedString = '';				
			
			var txnVar = txnVars[i];
			var changedPost = '[RE]' + txnVar.name + "^^" + txnVar.value;
			if( changedString == null )
				changedString = changedPost;
			else
				changedString += changedPost;
		}
	}

	if( formElems != null && fieldErrors == null ) 
	{
		var formElemsString = formElems.getChangedString( bNoSave ); 
		if ( formElemsString != null && formElemsString != '' ) 
		{
			if( changedString != null && changedString.length > 0 )
				changedString += '~~';
			else
				changedString = '';
			changedString += formElemsString;
		}
	}

	if( changedString != null )
		form.elements.UpdateValues.value = changedString;
	else
		form.elements.UpdateValues.value = '';
	if( unloadHandler != null )
	{
		unloadHandler();
		unloadHandler = null;
	}

	if( formElems != null && formElems.postOriginalValues && formElems.originalValues != null )
	{
		// alert( originalValues.getPostValue() );
		form.elements.original_values.value = formElems.originalValues.getPostValue();
	}

	form.submit();
}

// Cause the transaction to redisplay itself
function redisplay( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff )
{
	var redisplayVar = new TxnVar( 'redisplay', 'true' );
	if( txnVars != null )
	{
		var txnVarsLength = txnVars.length + 1;
		txnVars[ txnVars.length ] = redisplayVar;
		txnVars.length = txnVarsLength;
	}
	else
		txnVars = new Array( redisplayVar );
		
	go( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff );
}

// Cause the transaction to redisplay itself
function redisplays( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff )
{
	var redisplayVar = new TxnVar( 'redisplay', 'true' );
	if( txnVars != null )
	{
		var txnVarsLength = txnVars.length + 1;
		txnVars[ txnVars.length ] = redisplayVar;
		txnVars.length = txnVarsLength;
	}
	else
		txnVars = new Array( redisplayVar );
		
	gos( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses, enforceRequiredOff );
}

// Cause the transaction to redisplay itself without any fields being required
function redisplayNoRequired( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses )
{
	var redisplayVar = new TxnVar( 'redisplay', 'true' );
	if( txnVars != null )
	{
		var txnVarsLength = txnVars.length + 1;
		txnVars[ txnVars.length ] = redisplayVar;
		txnVars.length = txnVarsLength;
	}
	else
		txnVars = new Array( redisplayVar );
		
	goNoRequired( navkey, transaction, langid, bNoSave, txnVars, excludeProcesses );
}

// For switching images as rollovers
function switchImage( myIMGName, myImageObj2Switch2 ) 
{
	var myIMGObjRef = eval( "document." + myIMGName );
 	myIMGObjRef.src = myImageObj2Switch2.src;
}

// Validating forms and passing data to the controller servlet
function FormElements( dataFormName )
{
	// Store a reference to the data form
	this.dataform = eval( 'document.forms.' + dataFormName );
	this.updateValues = document.forms.navform.elements.UpdateValues;
	this.formElements = new Array();
	this.preNavFcn = null;
	this.hadError = false;
	this.originalValues = null;
	this.postOriginalValues = false;
	this.change_protected = false;
	this.change_field_pattern = null;
	this.force_error = false;

	// Set some function pointers
	this.addElement = FormElementsAddElement;
	this.addSessionElement = FormElementsAddSessionElement;
	this.addRequestElement = FormElementsAddRequestElement;
	this.addStateElement = FormElementsAddStateElement;
	this.addDataElement = FormElementsAddDataElement;
	this.getChangedString = FormElementsGetChangedString;
	this.findFormElement = FormElementsFindFormElement;
	this.validateFields = FormElementsValidateFields;
	this.enableField  = FormElementsEnableField;
	this.enableFields = FormElementsEnableFields;
	this.hasChanged = FormElementsHasChanged;
	this.focusField = FormElementsFocusField;
	this.setHadError = FormElementsSetHadError;
	this.clearFields = FormElementsClearFields;
	this.visit = FormElementsVisitFormElements;
	this.toString = FormElementsToString;
	this.setForceSend = FormElementsSetForceSend;
	this.getFieldValue = FormElementsGetFieldValue;
}

function FormElementsToString()
{
	var ret = '';
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		ret += formElem.element.name + '\n';
	}

	return ret;
}

function FormElementsVisitFormElements( visitor )
{
	var i;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		visitor.visit( formElem );
	}
}

function FormElementsClearFields()
{
	var i;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		formElem.clearField();
	}
}

function FormElementsFocusField( fieldName, bFieldSearch )
{
	var i;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		if( bFieldSearch )
		{
			if( doesFieldMatch( formElem.elementName, fieldName ) )
			{
				formElem.setFocus();
				return;
			}
		}
		else
		if( fieldName == formElem.elementName )
		{
			formElem.setFocus();
			return;
		}
	}
}

function FormElementsEnableFields( bEnable, fieldMatch )
{
	var i;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		if( fieldMatch != null )
		{
			if( doesFieldMatch( formElem.elementName, fieldMatch ) )
				formElem.enable( bEnable );
		}
		else
			formElem.enable( bEnable );
	}
}

function FormElementsGetFieldValue( fieldName )
{
	var field = this.findFormElement( fieldName );
	return field == null ? null : field.getValue();
}

/**
 * Sets all fields to always send when the name of the field matches the pattern given
 */
function FormElementsSetForceSend( fieldMatch, doSend )
{
	var i;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		if( fieldMatch != null )
		{
			if( doesFieldMatch( formElem.elementName, fieldMatch ) )
				formElem.bAlwaysSend = doSend;
		}
		else
			formElem.bAlwaysSend = doSend;
	}
}

function FormElementsEnableField( bEnable, fieldMatch )
{
	var i;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		if( fieldMatch != null )
		{
			if( formElem.elementName == fieldMatch ) 
			{
				formElem.enable( bEnable );
				if( bEnable == false )
					bRequired = false;
				else
					bRequired = true;
			}
			
		}
		else
			formElem.enable( bEnable );
	}
}

function FormElementsSetHadError( fieldMatch )
{
	var i;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		if( formElem.elementName == fieldMatch ) 
		{
			formElem.bHadError = true;
			return;
		}
	}
}

function doesFieldMatch( fieldName, matchString )
{
	var i = matchString.indexOf( '*' );
	var fieldStartMatch = null;
	var fieldEndMatch = null;
	
	if( i != 0 )
	{
		fieldStartMatch = fieldName.substr( 0, i );
		var matchStart = matchString.substr( 0, i );
		var iMatchIndex = fieldStartMatch.indexOf( matchStart );
		if( iMatchIndex != 0 )
			return false;
		fieldName = fieldName.substr( i );	
	}
	
	matchString = matchString.substr( i+1 );
	var nextWildIndex = matchString.indexOf( '*' );
	var strToMatch = matchString;
	if( nextWildIndex != -1 )
		strToMatch = matchString.substr( 0, nextWildIndex );
		
	var iFieldIndex = fieldName.indexOf( strToMatch );
	if( iFieldIndex == -1 )
		return false;
	fieldName = fieldName.substr( iFieldIndex );
	if( nextWildIndex != -1 )
		return doesFieldMatch( fieldName, matchString );
		
	if( matchString == '' )
		return true;
	else
		return matchString == fieldName ? true : false;
}

function FormElementsAddElement( elementName, typeName, persistenceType, bRequired, bAlwaysSend, xrefFieldNames, bEnabled, minLength, maxLength, bAlwaysSendHidden, groupid )
{
	// Check to see if the elementName refers to multiple occurances of an element
	var i = elementName.indexOf( '*' );
	if( i != -1 )
	{
		var formElems = this.dataform.elements;
		var iFields;
		for( iFields = 0; iFields < formElems.length; iFields++ )
		{
			var formElem = formElems[ iFields ];
			if( doesFieldMatch( formElem.name, elementName ) )
			{
				if( xrefFieldNames != null )
				{
					// This is a little tricky.  The xrefFieldNames are likely not to have a match since the 
					// parent field has a wild card character in it, so this probably does to.  It is assumed
					// that in this case the wildcard character is the last in both the parent and 
					// xref fields.  If a need arises in the future for the wildcard characters to be in the
					// middle of a string, this needs to be reworked
					//
					// i is the position in the original string that contains the wildcard character,
					// so grab the matched field name at that position and save that string.  This is 
					// going to be appened to the xref fields
					
					var xrefAddendum = formElem.name.substring( i );
					var iField;
					for( iField=0; iField < xrefFieldNames.length; iField++ )
					{
						xrefFieldNames[iField] = 
							xrefFieldNames[iField].substring( 0, xrefFieldNames[iField].length - 1 ) +
							xrefAddendum;

					}
				}				
				this.addElement( formElem.name, typeName, persistenceType, bRequired, bAlwaysSend, xrefFieldNames, bEnabled, minLength, maxLength, bAlwaysSendHidden, groupid );
			}
		}
	}
	else
	{
		if( typeName.indexOf( 'drop' ) == -1 )
		{
			if( this.dataform.elements[elementName] == null )
				return;
		}
		else
		{
			if( this.dataform.elements[elementName+'_year'] == null ||
				this.dataform.elements[elementName+'_month'] == null ||
				this.dataform.elements[elementName+'_day'] == null )
			{
				return;
			}
		}
		
		var xrefFields = null;
		if( xrefFieldNames != null )
		{
			xrefFields = new Array();
			var iField;
			for( iField=0; iField < xrefFieldNames.length; iField++ )
			{
				var xrefField = this.dataform.elements[xrefFieldNames[iField]];
				xrefFields[iField] = xrefField;
			}
		}
		var formElement = new FormElement( this.dataform, elementName, typeName, persistenceType, bRequired, bAlwaysSend, xrefFields, bEnabled, minLength, maxLength, bAlwaysSendHidden, groupid );
		var arrayLength = this.formElements.length++;
		this.formElements[arrayLength] = formElement;
	}
}

// Not Used?
function FormElementsAddSessionElement( elementName, typeName, bRequired, bAlwaysSend )
{
	this.addElement( elementName, typeName, 'session', bRequired, bAlwaysSend );
}

// Not Used?
function FormElementsAddRequestElement( elementName, typeName, bRequired, bAlwaysSend )
{
	this.addElement( elementName, typeName, 'request', bRequired, bAlwaysSend );
}

// Not Used?
function FormElementsAddStateElement( elementName, typeName, bRequired, bAlwaysSend )
{
	this.addElement( elementName, typeName, 'state', bRequired, bAlwaysSend );
}

// Not Used?
function FormElementsAddDataElement( elementName, typeName, bRequired, bAlwaysSend )
{
	this.addElement( elementName, typeName, 'data', bRequired, bAlwaysSend );
}

function FormElementsGetChangedString( bNoSave )
{
	var i;
	var changedString = '';
	var bStarted = false;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		var s = formElem.getChangedString( bNoSave );
		if( s != null )
		{
			// Encode the '~'s in user input so they don't interfere with '~~' delimiters
			s = s.split("~").join("§");

			if( bStarted )
				changedString += "~~";
			else
				bStarted = true;
			changedString += s;
		}
	}
	
	return changedString;
}

function FormElementsHasChanged()
{
	/* This is the old way
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		if( formElem.getValue() != formElem.originalValue )
			return true;
	}
	
	return false;
	*/
	if( this.originalValues == null )
		return false;

	return this.originalValues.hasChanged( this );
}

function FormElementsFindFormElement( formElementName )
{
	var i;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		if( formElem.elementName == formElementName )
			return formElem;
	}
	
	return null;
}

function formatErrorElement( validateStr, formElem, errorStr, errorValue )
{
	if( validateStr != null )
		validateStr += '~~';
	else
		validateStr = '';
		
	var thisStr = formElem.getErrorPrefix() + ( errorStr == null ? '' : errorStr  ) + '^^' + errorValue;
	thisStr = thisStr.split( '~' ).join( '§' );
	validateStr += thisStr;
	return validateStr;
}

function FormElementsValidateFields( enforceRequiredOff, groupid )
{
	var validateStr = null;
	var bHadErrors = this.force_error;
	var errorStr;
	
	// Allows pages to have custom validation logic here
	if( formElems.preNavFcn != null )
	{
		validateStr = formElems.preNavFcn( enforceRequiredOff );
		if( validateStr != null )
			bHadErrors = true;
	}

	var i;
	for( i=0; i < this.formElements.length; i++ )
	{
		var formElem = this.formElements[i];
		if( formElem.bEnabled )
		{
			var checkField = true;
			
			if( groupid != null )
				checkField = formElem.groupid == groupid;
			else
			if( formElem.groupid != null )
				checkField = false;
			
			if( checkField )
			{
				// An error has been deliberately/manually set into this form element
				if ( formElem.errorString != null )
				{
					bHadErrors = true;
					errorStr = formElem.errorString;
					validateStr = formatErrorElement( validateStr, formElem, errorStr, formElem.getValue() );
				} 
				else
				{
					var errorStr = formElem.defValidate( formElem.element, !enforceRequiredOff ? !formElem.bRequired : true, formElem.xrefFields );
					if( errorStr == null && formElem.validate != null )
						errorStr = formElem.validate( formElem.element, !enforceRequiredOff ? !formElem.bRequired : true, formElem.xrefFields );
					if( errorStr != null )
					{
						validateStr = formatErrorElement( validateStr, formElem, errorStr, formElem.getValue() );
						if( !bHadErrors )
							bHadErrors = true;
					}
					else
					{
						var formElemValue = formElem.getValue();
						if( formElemValue != null )
							validateStr = formatErrorElement( validateStr, formElem, errorStr, formElemValue );
					}
				}
			}
		}
	}
	return bHadErrors ? validateStr : null;
}

// An individual form element
// 	param dataForm is the data form reference given to us by FormElements
// 	param elementName is the name of the element in the data form
//		param typeName is the type of the element if applicable
//		param persistenceType is either 'session', 'request', 'state', 'data', or 'listdata'
//		param bRequired is true if the field is required, false otherwise
//		param value is the original value of the element to set
//		param bAlwaysSend if this is specified then it will override the default setting of this value when the type of 
//								of the form element is not data.  In the case of data elements we only want changed values.
// 	param xrefField a field to use in conjunction with this edit
function FormElement( dataform, elementName, typeName, persistenceType, bRequired, bAlwaysSend, xrefFields, bEnabled, minLength, maxLength, bAlwaysSendHidden, groupid )
{
	this.dataform = dataform;
	this.elementName = elementName;
	this.element = eval( 'this.dataform.elements[\'' + elementName + '\']' );
	this.persistenceType = persistenceType;
	this.bRequired = bRequired;
	this.bOriginallyRequired = bRequired;
	this.originalValue = null;
	this.getChangedValue = FormElementGetChangedValue;
	this.getChangedString = FormElementGetChangedString;
	this.getPersistenceTypePrefix = FormElementGetPersistenceTypePrefix;
	this.validate = null;
	this.defValidate = FormElementValidateDefault;
	this.getErrorPrefix = FormElementGetErrorPrefix;
	this.xrefFields = xrefFields;
	this.enable = FormElementEnable;
	this.bEnabled = true;
	this.minLength = minLength;
	this.maxLength = maxLength;
	this.bHadError = false; // This is set to true for elements on the page that had errors when the page was initialized
	this.setFocus = FormElementSetFocus;
	this.errorString = null; // This is when you want to set a field to a certain error condition deliberately and immediately.
	this.bAlwaysSendHidden = bAlwaysSendHidden;
	this.groupid = groupid;
	
	if( typeName == 'dropShortDate' || typeName == 'dropDateRangeStart' || typeName == 'dropDateRangeEnd' )
	{
		this.getValue = FormElementGetDropShortDateValue;
		this.setValue = FormElementSetDropShortDateValue;
		this.hasValue = FormElementDropShortDateHasValue;
		this.clearField = FormElementClearShortDateField;
		this.element = eval( 'this.dataform.elements[\'' + elementName + '_month\']' );
		if( typeName == 'dropShortDate' )
			this.validate = verifyDropDate;
		else
		if( typeName == 'dropDateRangeStart' )
			this.validate = verifyDropDateStart;
		else
			this.validate = verifyDropDateEnd;
	}
	else
	if( this.element.type == 'checkbox' )
	{
		this.getValue = FormElementGetCheckValue;
		this.setValue = FormElementSetCheckValue;
		this.hasValue = FormElementCheckBoxHasValue;
		this.clearField = FormElementClearCheckField;
	}
	else
	if( !this.element.type ) // This means that it is a radio button
	{
		this.getValue = FormElementGetRadioValue;
		this.setValue = FormElementSetRadioValue;
		this.hasValue = FormElementRadioHasValue;
		this.clearField = FormElementClearRadioField;
	}
	else
	if( this.element.type.indexOf( 'select' ) == 0 )  // matches types 'select' and 'select-one'
	{
		this.getValue = FormElementGetSelectValue;
		this.setValue = FormElementSetSelectValue;
		this.hasValue = FormElementSelectHasValue;
		this.clearField = FormElementClearSelectField;

		if( typeName == 'CreditCard' )
			this.validate = checkCreditCardFields;
		else
		if( typeName == 'futureShortDate' ) 			
			this.validate = checkFutureShortDate;
	}
	else
	if( this.element.type == 'textarea' )
	{
		this.getValue = FormElementGetTextareaValue;
		this.setValue = FormElementSetTextareaValue;
		this.hasValue = FormElementHasValue;
		this.clearField = FormElementClearField;
	}
	else
	{
		this.getValue = FormElementGetTextValue;
		this.setValue = FormElementSetTextValue;
		this.hasValue = FormElementHasValue;
		this.clearField = FormElementClearField;
		
		if( typeName == 'CreditCard' ) // Not sure why we get here
			this.validate = checkCreditCardFields;
		else
		if( typeName == 'StateOrProvince' )
			this.validate = checkStateOrProvince;
		else
		if( typeName == 'USState' )
			this.validate = checkStateCode;
		else
		if( typeName == 'USZipCode' )
			this.validate = checkZIPCode;
		else
		if( typeName == 'PostalCode' )
			this.validate = checkPostalCode;
		else
		if( typeName == 'USPhone' )
		{
			this.validate = checkUSPhone;
			this.getValue = FormElementGetPhoneValue;
		}
		else
		if( typeName == 'Phone' )
		{
			this.validate = checkPhone;
			this.getValue = FormElementGetPhoneValue;
		}
		else
		if( typeName == 'Email' )
			this.validate = checkEmail;
		else
		if( typeName == 'Password' )
			this.validate = checkPassword;
		else
		if( typeName == 'PosInteger' )
			this.validate = checkPositiveInteger;
		else
		if( typeName == 'NonnegInteger' )
			this.validate = checkNonnegativeInteger;
		else
		if( typeName == 'PosIntegerAllowNulls' )
			this.validate = checkPositiveIntegerAllowNulls;
		else
		if( typeName == 'MonetaryAllowNulls' )
			this.validate = checkMonetaryAllowNulls;			
		else		
		if( typeName == 'PosFloat' )
			this.validate = checkPositiveFloat;
		else
		if( typeName == 'Date' )
			this.validate = verifyDate;
		else
		if( typeName == 'PastDate' )
			this.validate = verifyPastDate;		
		else
		if( typeName == 'Sku' )
			this.validate = verifySku;
		else
		if( typeName == 'Upccd' )
			this.validate = verifyUpccd;
		else
		if( typeName == 'Money' )
			this.validate = verifyMoney;
	}

	// Determine if the value should always be posted
	if( bAlwaysSend != null )
		this.bAlwaysSend = bAlwaysSend;
	else
	if( this.element.type == 'data' )
		this.bAlwaysSend = false;
	else
	if( this.element.type == 'hidden' && !this.bAlwaysSendHidden )
		this.bAlwaysSend = false;
	else
	if( this.element.type == 'hidden')
		this.bAlwaysSend = true;
	else
		this.bAlwaysSend = false;
		
	this.originalValue = this.getValue();
}

function FormElementGetErrorPrefix()
{
	return this.elementName + '^^';
}

function FormElementSetFocus()
{
	if( this.element )
		if(this.element.type!='hidden')
			this.element.focus();
}

// Everything is always ok by default
function FormElementValidateDefault( theField, emptyOk, otherFields )
{
	var bHasValue = this.hasValue();
	
	if( !emptyOk && this.bRequired && !bHasValue )
		return ERROR_REQUIRED;

	if( !emptyOk && ( this.minLength > -1 || this.maxLength > -1)  )
	{
		var thisValue = this.getValue();
		if( thisValue == null || thisValue.length < this.minLength )
			return ERROR_MIN_LENGTH;
		else
		if( thisValue != null && this.maxLength != -1 && thisValue.length > this.maxLength )
			return ERROR_MAX_LENGTH;
	}
	return null;
}

function FormElementEnable( bDoEnable )
{
	this.element.disabled = !bDoEnable;
	this.bEnabled = bDoEnable;
}

/*** Text field processing ***/

function FormElementGetTextValue()
{
	return btrim( this.element.value );	
}

function FormElementSetTextValue( newValue )
{
	return this.element.value = newValue;
}

function FormElementGetChangedValue( bNoSave )
{
	var bIsHidden = this.element != null && this.element.type == 'hidden';
	if( ( bNoSave && !bIsHidden ) || !this.bEnabled )
		return null;
	
	var value = this.getValue();
	if( this.bAlwaysSend )
		return value;
	
	if( formElems.originalValues == null )
		return value != this.originalValue ? value : null;
	else
	{
		var originalValue = formElems.originalValues.findValue( this.elementName );
		return value != originalValue ? value : null;
	}
}

function FormElementGetChangedString( bNoSave )
{
	var value = this.getChangedValue( bNoSave );
	if( value != null )
		return this.getPersistenceTypePrefix() + this.elementName + "^^" + value;
	return null;
}

function FormElementHasValue()
{
	return this.getValue().length > 0;
}

function FormElementClearField()
{
	this.element.value = '';
}

/*** Text area field processing ***/
function FormElementGetTextareaValue()
{
//	return encodeXlateValue( this.element.value );
	return this.element.value;
}

function FormElementSetTextareaValue( newValue )
{
//	return decodeXlateValue( this.element.value = newValue );
	this.element.value = newValue;
}

/*** Select field processing ***/
function FormElementGetSelectValue()
{
		if(this.element.selectedIndex>=0&&this.element.selectedIndex<this.element.length)
			return this.element.options[ this.element.selectedIndex ].value;
		else
			return "~~~";
}

function FormElementSetSelectValue( newValue )
{
	var i;
	for( i=0; i < this.element.length; i++ )
	{
		if( this.element.options[i].value == newValue )
		{
			this.element.setSelectedIndex( i );
			return;
		}
	}
}

function FormElementSelectHasValue()
{
	// Assumes that the first option in the select is an indicator to choose one of the other options below it
	return this.element.selectedIndex > 0;
}

function FormElementClearSelectField()
{
	this.element.options[0].selected = true;
}

/*** Checkbox field processing ***/

function FormElementGetCheckValue()
{
	return this.element.checked;
}

function FormElementSetCheckValue( newValue )
{
	this.element.checked = newValue;
}

function FormElementCheckBoxHasValue()
{
	// Always return true so that a required check on this field will be ok, but a check box has a value
	// no matter what
	return true;
}

function FormElementClearCheckField()
{
	this.element.checked = false;
}

/*** Radio button processing ***/

function FormElementGetRadioValue()
{
	var radios = this.dataform.elements[this.elementName];
	var iRadio;
	for( iRadio=0; iRadio < radios.length; iRadio++ )
	{
		var thisRadio = radios[iRadio];
		if( thisRadio.checked )
			return thisRadio.value;
	}
	return null;
}

function FormElementSetRadioValue( newValue )
{
	var radios = this.dataform.elements[this.elementName];
	var iRadio;
	for( iRadio=0; iRadio < radios.length; iRadio++ )
	{
		var thisRadio = radios[iRadio];
		if( thisRadio.value == newValue )
		{
			thisRadio.checked = true;
			return;
		}
	}
}

function FormElementRadioHasValue()
{
	return this.getValue() != null;
}

function FormElementClearRadioField()
{
	var radios = this.dataform.elements[this.elementName];
	
	// Set the first radio to selected ???
	radios[0].checked = true;
}

/*** Phone field processing ***/

function FormElementGetPhoneValue()
{
/* phone numbers and phone extensions are now saved as separate fields.
	if( this.xrefFields != null && this.xrefFields.length > 1 )
	{
		// In this case the second xref field is the extension field.
		// Note that if the first xref field is not the country then this needs to be changed to find
		// the extension field in the xref fields array
		var extFieldValue = btrim( this.xrefFields[1].value );
		return extFieldValue.length > 0 ? this.element.value + ' x' + extFieldValue : this.element.value;
	}
	else
*/
		return this.element.value;
}

/*** Utility functions ***/

// Get a prefix for transmitting the persistence type to the servlet 
function FormElementGetPersistenceTypePrefix()
{
	if( this.persistenceType == 'session' )
		return '[SE]';
	else
	if( this.persistenceType == 'request' )
		return '[RE]';
	else
	if( this.persistenceType == 'state' )
		return '[ST]';
	else
	if( this.persistenceType == 'data' )
		return '[DA]';
	else
		return '[LI]';
}

/**** Encoding functions ****/
var xlateChars = new Array(
	new TxnVar( '<', '3C' ),
	new TxnVar( '>', '3E' ),
	new TxnVar( '&', '26' ),
	new TxnVar( "'", '27' ),
	new TxnVar( '"', '22' ),
	new TxnVar( '\r', '0D' ),
	new TxnVar( '\n', '0A' ),
	new TxnVar( '\t', '09' ),
	new TxnVar( '%', '25' )
);
	
function charXlateValue( charToXlate )
{
	var i;
	for( i=0; i < xlateChars.length; i++ )
	{
		var txnVar = xlateChars[i];
		if( txnVar.name == charToXlate )
			return '%' + txnVar.value;
	}
	return charToXlate;
}

function hexToChar( hexValue )
{
	var i;
	for( i=0; i < xlateChars.length; i++ )
	{
		var txnVar = xlateChars[i];
		if( txnVar.value == hexValue )
			return txnVar.name;
	}
	return '';
}

function encodeXlateValue( textValue )
{
	if( textValue == null || textValue.length == 0 )
		return null;
	if ( isString( textValue ) )
	{
		var i;
		var encodedValue = '';
		for( i=0; i < textValue.length; i++ )
			encodedValue += charXlateValue( textValue.charAt( i ) );
		return encodedValue;
	}
	else
	{
		return textValue;	
	}
}

function decodeXlateValue( textValue )
{
	if( textValue == null || textValue.length == 0 )
		return null;

	if ( isString( textValue ) )
	{
		var i;
		var decodedValue = '';
		for( i=0; i < textValue.length; i++ )
		{
			var c = textValue.charAt( i );
			switch( c )
			{
				case '%':
					decodedValue += hexToChar( textValue.substring( i+1, i+3 ) );
					i += 2;
					break;
				default:
					decodedValue += c;
					break;
			}
		}
		return decodedValue;
	}
	else
	{
		return textValue;
	}	
}

function isString( text ) 
{
	if ( typeof text == 'string' ) 
		return true;
	if ( typeof text == 'object' ) 
	{  
		var criterion =  text.constructor.toString().match(/string/i); 
		return ( criterion != null );  
	}
	return false;
}

/******* Date drop down functions **********/

// Assumes the existence of mcutils.js
function monthSelected( formName, fieldName )
{
	var theForm = document.forms[formName];
	var monthSelect = theForm.elements[fieldName + '_month'];
	var daySelect = theForm.elements[fieldName + '_day'];
	
	var monthSelected = monthSelect.options[monthSelect.selectedIndex].value;
	var daySelectedIndex = daySelect.selectedIndex;
	var daySelected = daySelect.options[daySelect.selectedIndex].value;

	var numDaysCurrent = daySelect.length - 1;
	var numDaysNext = monthSelected > 0 ? daysInMonth[monthSelected] : 31;

	// This block accounts for 29 days in a leap year.
	var yearSelect = theForm.elements[fieldName + '_year'];
	var yearSelected = yearSelect.options[yearSelect.selectedIndex].value;	
	if ( (yearSelected > 0) && (monthSelected == 2) && 
		 ( (yearSelected % 4 == 0) && !((yearSelected % 100 == 0) && (yearSelected % 400 != 0)) ) )
	{
		numDaysNext = 29;
	}
		
	var i;
	if( numDaysCurrent < numDaysNext )
	{
		for( i=numDaysCurrent; i <= numDaysNext; i++ )
		{
			if( i != 0 )
				daySelect.options[i] = new Option( i, i );
		}
	}
	else
	{
		for( i=numDaysCurrent; i > numDaysNext; i-- )
		{
			if( i != 0 )
				daySelect.options[i] = null;
		}
	}
	
	if( daySelectedIndex > daySelect.length )
		daySelect.options[0].selected = true;
	else
		daySelect.options[daySelectedIndex].selected = true;
}

function FormElementGetDropShortDateValue()
{
	var monthFieldName = this.elementName + '_month';
	var yearFieldName = this.elementName + '_year';
	var dayFieldName = this.elementName + '_day';
	
	var monthField = this.dataform.elements[monthFieldName];
	var yearField = this.dataform.elements[yearFieldName];
	var dayField = this.dataform.elements[dayFieldName];
	
	var monthValue = null;
	var yearValue = null;
	var dayValue = null;
	
	if( monthField.selectedIndex > 0 )
		monthValue = monthField.options[monthField.selectedIndex].value;
	if( yearField.selectedIndex > 0 )
		yearValue = yearField.options[yearField.selectedIndex].value;
	if( dayField.selectedIndex > 0 )
		dayValue = dayField.options[dayField.selectedIndex].value;
		
	if( monthValue == null && dayValue == null && yearValue == null )
		return '';

	var retValue = monthValue == null ? '0/' : monthValue + '/';
	retValue += dayValue == null ? '0/' : dayValue + '/';
	retValue += yearValue == null ? '0' : yearValue;
	
	return retValue;
}

function FormElementSetDropShortDateValue( newValue )
{
	var t = Date.parse( newValue )
	if( isNaN( t ) ) 
		return;
		
	var dobj = new Date( t );
	// dobj.getMonth()
	// dobj.getDate()
	// dobj.getFullYear()
}

function FormElementDropShortDateHasValue()
{
	var monthFieldName = this.elementName + '_month';
	var yearFieldName = this.elementName + '_year';
	var dayFieldName = this.elementName + '_day';
	
	var monthField = this.dataform.elements[monthFieldName];
	var yearField = this.dataform.elements[yearFieldName];
	var dayField = this.dataform.elements[dayFieldName];
	
	return monthField.selectedIndex > 0 && yearField.selectedIndex > 0 && dayField.selectedIndex > 0;
}

function FormElementClearShortDateField()
{
	var monthFieldName = this.elementName + '_month';
	var yearFieldName = this.elementName + '_year';
	var dayFieldName = this.elementName + '_day';
	
	var monthField = this.dataform.elements[monthFieldName];
	var yearField = this.dataform.elements[yearFieldName];
	var dayField = this.dataform.elements[dayFieldName];
	
	monthField.options[0].selected = true;
	yearField.options[0].selected = true;
	dayField.options[0].selected = true;
}

function goWithBackInfo( navkey, transaction )
{
	var lastNavkey = document.forms['navform'].elements.lastNavkey.value;
	var lastTxn = document.forms['navform'].elements.lastTransaction.value;
	var txnVars = new Array( new TxnVar( 'BackNavNavkey', lastNavkey ), 
		new TxnVar( 'BackNavTransaction', lastTxn ) );
	go( navkey, transaction, null, null, txnVars );
}

function gosWithBackInfo( navkey, transaction )
{
	var lastNavkey = document.forms['navform'].elements.lastNavkey.value;
	var lastTxn = document.forms['navform'].elements.lastTransaction.value;
	var txnVars = new Array( new TxnVar( 'BackNavNavkey', lastNavkey ), 
		new TxnVar( 'BackNavTransaction', lastTxn ) );
	gos( navkey, transaction, null, null, txnVars );
}

function restoreStyles(){
    inputList = document.getElementsByTagName("INPUT");
    for(i=0;i<inputList.length;i++)
      inputList[i].style.backgroundColor = "";
    selectList = document.getElementsByTagName("SELECT");
    for(i=0;i<selectList.length;i++)
      selectList[i].style.backgroundColor = "";
}

function setSecure()
{
	var theForm   = document.forms['navform'];
	var theAction = theForm.action;
	// check to protect against quick double clicks
	if ( theAction.substring(0,5) != 'https' )
	{
		var newAction = null;
		
		var i = theAction.indexOf('p');
		i++;
	
		newAction = 'https'+theAction.substr(i);	
		//now change port if test environment port is 8080
		var x = newAction.indexOf('9');
		if( x > 0 )
		{
			var rest = x +4;
			var c    = x - 1;
			newAction = newAction.substr(0,c) + ':9443' + newAction.substr(rest);
		}
		document.forms.navform.action = newAction;
	}
}