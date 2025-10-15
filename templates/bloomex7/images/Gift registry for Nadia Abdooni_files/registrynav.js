function redisplayRegistry( bSave )
{
	redisplay( 'guest.registryDetail', 'RegistryDetail', null, !bSave );
}

function goItemDetail( itemKey )
{
	var txnVars = new Array( new TxnVar( 'ItemKey', itemKey ) );
	go( 'guest.item', 'RegistryItem', null, null, txnVars );
}

function goUpdateItemDetail( itemKey )
{
	var txnVars = new Array( new TxnVar( 'ItemKey', itemKey ) );
	go( 'update.item', 'UpdateItem', null, null, txnVars );
}

function redisplayUpdateRegistry()
{
	redisplay( 'update.registryDetail', 'UpdateRegistryDetail', null, false );
}

function goCollectionItemDetail( itemKey )
{
	var txnVars = new Array( new TxnVar( 'ItemKey', itemKey ) );
	go( 'items.collectionDetail', 'CollectionDetail', null, null, txnVars );
}

function redisplayPrintRegistry()
{
	redisplay( 'update.printRegistry', 'UpdatePrintRegistry', null, false, txnVars );
}

var popUpImageName = null;
var newPopUpImageName = null;

function popUpLoaded( myWindow )
{
	var curImage = eval( "myWindow.document.images['" + popUpImageName + "']" );
	curImage.src = newPopUpImageName;
}

function openPopUPImage( imgName, passedInImg, filename ) 
{
	popUpImageName = imgName;
	newPopUpImageName = passedInImg;
	window.open( filename,'','scrollbars=yes,resizable=yes,width=450,height=620');
}
