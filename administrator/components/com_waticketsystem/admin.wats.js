/**
 * FileName: admin.wats.js
 * Date: 25/05/2006
 * License: GNU General Public License
 * Script Version #: 2.0.0
 * JOS Version #: 1.0.x
 * Development James Kennard jg8949@aol.com (www.webamoeba.co.uk)
 **/

/**
 * Updates all form elements names in array from control
 */
function updateControls( array, control )
{
	// loop through array and change values to control
	for (key in array)
			getElement( array[ key ] ).value = control.value;
}

/**
 * Gets element by id
 */
function getElement( id )
{
   if( document.all )
   {
      return document.all[ id ];
   }
   else
   {
      return document.getElementById( id );
   }
} 