/**
 * Object for storing the parameters for navigation.  The go method is becoming long
 * and unwieldy, and our needs keep growing over time.  A new go method exists that
 * accepts the NavigationData object as it's only parameter.
 */

function Navigator( navkey, transaction )
{
	this.navkey = navkey;
	this.transaction = transaction;
	
	this.langid = null;
	this.bNoSave = null;
	this.txnVars = null;
	this.excludeProcesses = null;
	this.enforceRequiredOff = null;
	this.isReset = false;
	this.isRedisplay = false;
	this.bNoExpire = null;
	this.ignoreChangeProtect = false;
	
	this.groupid = null;
	
	this.addTxnVar = NavigatorAddTxnVar;
	this.replaceTxnVar = NavigatorReplaceTxnVar;
	this.go = NavigatorGo;

	this.setSecure = NavigatorSetSecure;
	this.setGroupid = NavigatorSetGroupid;
	this.setRequiredOff = NavigatorSetRequiredOff;
	this.setNoSave = NavigatorSetNoSave;
	this.setResetOriginalValues = NavigatorSetResetOriginalValues;
	this.setIgnoreChangeProtect = NavigatorSetIgnoreChangeProtect;	
	this.setRedisplay = NavigatorSetRedisplay;
	this.hasTxnVar = NavigatorHasTxnVar;
	this.setInterruptValues = NavigatorSetInterruptValues;
	this.setForceSend = NavigatorSetForceSend;
}

function NavigatorHasTxnVar( varName )
{
	if( this.txnVars == null )
		return false;
		
	var i;
	for( i=0; i < this.txnVars.length; i++ )
	{
		if( this.txnVars[i].name == varName )
			return true;
	}
	
	return false;
}

function NavigatorAddTxnVar( varName, varValue )
{
	if( this.txnVars == null )
		this.txnVars = new Array();
		
	var len = this.txnVars.length;
	this.txnVars[ len ] = new TxnVar( varName, varValue );
	this.txnVars.length = len + 1;
}

function NavigatorReplaceTxnVar( varName, varValue )
{
	if( this.txnVars != null )
	{
		var i;
		for( i=0; i < this.txnVars.length; i++ )
		{
			if( this.txnVars[i].name == varName )
			{
				this.txnVars[i].value = varValue;
				return;
			}
		}
	}

	if( this.txnVars == null )
		this.txnVars = new Array();
		
	var len = this.txnVars.length;
	this.txnVars[ len ] = new TxnVar( varName, varValue );
	this.txnVars.length = len + 1;
}

function NavigatorGo()
{
	if( formElems != null )
	{
		if( !this.bNoSave && formElems.change_protected && !this.ignoreChangeProtect )
		{
			if( formElems.hasChanged() )
			{
				formElems.force_error = true;
				document.forms.navform.unsaved_changes.value = 'true';
				this.setRequiredOff( true );
				
				this.setInterruptValues();
			
				// This better exist!!!
				returnNavigator( this );
			}
			else
				this.setNoSave( true );
		}
	}

	if( this.isReset )
		this.addTxnVar( 'ResetState', 'true' );

	if( this.isRedisplay )
		this.addTxnVar( 'redisplay', 'true' );

	go( this.navkey, this.transaction, this.langid, this.bNoSave, 
		this.txnVars, this.excludeProcesses, this.enforceRequiredOff, 
		this.bNoExpire, this.groupid );
}

function NavigatorSetInterruptValues()
{
	var interValue = 'navkey^^' + this.navkey;
	interValue += '~~' + 'transaction^^' + this.transaction;
	if( this.txnVars != null )
	{
		var i;
		for( i=0; i < this.txnVars.length; i++ )
		{
			var txnVar = this.txnVars[i];
			interValue += '~~' + txnVar.name;
			interValue += '^^' + txnVar.value;
		}
	}
	
	document.forms.navform.elements.interrupt_nav_values.value = interValue;
}

function NavigatorSetSecure()
{
	var theForm   = document.forms['navform'];
	var theAction = theForm.action;

	// check to protect against quick double clicks
	if( theAction.substring( 0, 5 ) != 'https' )
	{
		var newAction = null;
		
		var i = theAction.indexOf( 'p' );
		i++;

		newAction = 'https' + theAction.substr( i );	
		
		//now change port if test environment port is 8080
		var x = newAction.indexOf( '9' );
		if( x > 0 )
		{
			var rest = x + 4;
			var c    = x - 1;
			newAction = newAction.substr( 0, c ) + ':9443' + newAction.substr( rest );
		}
		theForm.action = newAction;
	}		
}

/**
 * All fields that match the pattern of fieldMatch will be sent regardless if they have
 * changed or not.  If fieldMatch is null, all fields will be sent.
 */
function NavigatorSetForceSend( fieldMatch )
{
	if( formElems != null )
		formElems.setForceSend( fieldMatch );
}

function NavigatorSetGroupid( groupid )
{
	this.groupid = groupid;
}

function NavigatorSetRequiredOff()
{
	this.enforceRequiredOff = true;
}

function NavigatorSetNoSave( noSaveValue )
{
	this.bNoSave = noSaveValue;
}

function NavigatorSetRedisplay( bRedisplay )
{
	this.isRedisplay = bRedisplay;

	if( bRedisplay )
	{
		this.setResetOriginalValues( false );
		this.setIgnoreChangeProtect( true );
	}
}

function NavigatorSetResetOriginalValues( doResetOriginalValues )
{
	var theForm = document.forms['navform'];
	theForm.elements.original_values_reset.value = doResetOriginalValues ? 'true' : 'false';
}

function NavigatorSetIgnoreChangeProtect( bIgnore )
{
	this.ignoreChangeProtect = bIgnore;
}
