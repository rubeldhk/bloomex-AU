/**
 * FormValues stores the original state of the form on a given page that has data to post.
 * The object is meant to persist this data in it's original form until the page has been 
 * accepted as valid both on the server and on the page.
 * The back end accepts the post of these values and gives them back to the page if there is
 * an error so that the page can determine if anything has changed even if an error has occurred and
 * the page was returned to it's original state.
 * 
 * Build with the FormElements ( new FormValues() then initializeFromFormElems() ),
 * object from GenericOnlineTemplate.jsp when the page is new and has form data
 * to post.  Otherwise, build from the ViewBean if an error has occurred and the original values were 
 * posted.
 *
 * ViewBean contains the the object as posted from the previous page, only build from the posted 
 * version if there are no errors.
 *
 * Call getPostValue() and put the results in the navform element original_values if this construct is 
 * not null when navigation is invoked.
 *
 * To determine if the values have changed, call hasChanged() with the current FormElements object
 */

function FormValues()
{
	this.formValues = new Array();
	this.addFormValue = FormValuesAddFormValue;
	this.hasChanged = FormValuesHasChanged;
	this.findValue = FormValuesGetValue;
	this.getPostValue = FormValuesGetPostValue;
	this.visit = FormValuesVisitFormElement;
	this.initializeFromFormElems = FormValuesInitializeFromFormElems;
	this.change_field_pattern = null;
}

function FormValuesInitializeFromFormElems( formElems )
{
	this.change_field_pattern = formElems.change_field_pattern;
	formElems.visit( this );
}

function FormValuesVisitFormElement( formElem )
{
	if( this.change_field_pattern == null || 
		doesFieldMatch( formElem.elementName, this.change_field_pattern ) )
	{
		this.addFormValue( formElem.elementName, encodeXlateValue( formElem.getValue() ) );
	}
}

/**
 * The elemValue MUST be encoded
 */
function FormValuesAddFormValue( elemName, elemValue )
{
	var len = this.formValues.length;
	this.formValues[ len ] = new FormValue( elemName, elemValue );
	this.formValues.length = len + 1;
}

function FormValuesHasChanged( formElems )
{
	var i;
	for( i=0; i < this.formValues.length; i++ )
	{
		var elem = this.formValues[i];
		var existingFormElement = formElems.findFormElement( elem.elemName );
		if( existingFormElement != null )
		{
			var existingFormElementValue = existingFormElement.getValue();
			if( elem.getValue() != existingFormElementValue )
				return true;
		}
	}

	return false;
}

function FormValuesGetValue( elemName )
{
	if( this.formValues.length == 0 )
		return "";
		
	var i;
	for( i=0; i < this.formValues.length; i++ )
	{
		var elem = this.formValues[i];
		if( elem.elemName == elemName )
			return elem.getValue();
	}

	return "";
}

function FormValuesGetPostValue()
{
	var i;
	var postValue = null;
	for( i=0; i < this.formValues.length; i++ )
	{
		var elem = this.formValues[i];
		postValue = elem.getPostValue( postValue );
	}

	return postValue;
}

function FormValue( elemName, elemValue )
{
	this.elemName = elemName;
	this.elemValue = elemValue;
	
	this.getValue = FormValueGetValue;
	this.getPostValue = FormValueGetPostValue;
}

function FormValueGetValue()
{
	var ret = decodeXlateValue( this.elemValue );
	return ret == null ? "" : ret;
}

function FormValueGetPostValue( postValue )
{
	if( postValue != null )
		postValue += '~~';
	else
		postValue = '';

	var thisVal = this.elemName + '^^' + this.getValue();
	postValue += thisVal.split( '~' ).join( '§' );
	return postValue;
}

