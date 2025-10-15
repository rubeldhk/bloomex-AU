/**
 * FileName: waticketsystem.html.php
 * Date: 15/12/2005
 * License: GNU General Public License
 * Script Version #: 2.0.3
 * JOS Version #: 1.0.x
 * Development James Kennard jg8949@aol.com (www.webamoeba.co.uk)
 **/

/**
 * Shows / Hides layers.
 */
function watsToggleLayer(whichLayer)
{
	if (document.getElementById)
	{
		// this is the way the standards work
		var style2 = document.getElementById(whichLayer).style;
	}
	else if (document.all)
	{
		// this is the way old msie versions work
		var style2 = document.all[whichLayer].style;
	}
	else if (document.layers)
	{
		// this is the way nn4 works
		var style2 = document.layers[whichLayer].style;
	}
	
	// toggle display type
	if ( style2.display == "block" )
	{
		style2.display = "none";
	}
	else
	{
		style2.display = "block";
	}
}

/**
 * Associtaions: post12007 (IE compatability)
 * implement 'jump' feature of category selection box
 */
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

/**
 * implement 'jump' feature of category selection box
 
function watsCategorySetSelect( catid, itemid )
{
	if ( catid == -1 )
	{
		// jump to location
		document.location.href = "index.php?option=com_waticketsystem&Itemid=" + itemid;
    }
	else
	{
		// jump to location
		document.location.href = "index.php?option=com_waticketsystem&Itemid=" + itemid + "&act=category&catid=" + catid + "&page=1&lifecycle=a";
	}
}*/

/**
 * Validates make ticket form
 */
function watsValidateTicketMake( form, errorMessage, defaultMsg )
{
	returnValue = true;
	// check fields
	if ( trim( form.ticketname.value ) == "" )
	{
		returnValue = false;
		form.ticketname.focus();
		alert( errorMessage );  
	}
	else if ( trim( form.msg.value ) == "" || form.msg.value == defaultMsg )
	{
		alert( errorMessage );
		returnValue = false;
		form.msg.focus();
	} // end check fields
	return returnValue;
}

/**
 * Validates reply ticket form
 */
function watsValidateTicketReply( form, errorMessage, defaultMsg )
{
	returnValue = true;
	// check fields
	if ( trim( form.msg.value ) == "" || form.msg.value == defaultMsg )
	{
		alert( errorMessage );
		form.msg.focus();
		returnValue = false;
	} // end check fields
	return returnValue;
}

/**
 * Validates reopen ticket form
 */
function watsValidateTicketReopen( form, errorMessage, defaultMsg )
{
	returnValue = true;
	// check fields
	if ( trim( form.msg.value ) == "" || form.msg.value == defaultMsg )
	{
		alert( errorMessage );
		form.msg.focus();
		returnValue = false;
	} // end check fields
	return returnValue;
}

/**
 * Validates new user form
 */
function watsValidateNewUser( form, user, errorMessage )
{
	returnValue = true;
	// check fields
	if(user.selectedIndex < 0)
	{
		alert( errorMessage );
		user.focus();
		returnValue = false;
	}
	else if ( trim( form.grpId.value ) == "" )
	{
		alert( errorMessage );
		form.grpId.focus();
		returnValue = false;
	}
	else if ( trim( form.organisation.value ) == "" )
	{
		alert( errorMessage );
		form.organisation.focus();
		returnValue = false;
	} // end check fields
	return returnValue;
}