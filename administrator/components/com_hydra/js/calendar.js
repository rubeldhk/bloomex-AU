/**
* @author  Tobias Kuhn ( Sat Oct 07 22:55:33 CEST 2006 )
* @name    blockHover
* @version 1.0 
* @param   string block
* @return  void
* @desc    changes the border-style of a div element
**/
function blockHover(block)
{
	block.style.border = block_border;
}


/**
* @author  Tobias Kuhn ( Sat Oct 07 22:55:33 CEST 2006 )
* @name    blockOut
* @version 1.0 
* @param   string block
* @return  void
* @desc    changes the border-style of a div element
**/
function blockOut(block)
{
	block.style.border = '0px';
}


/**
* @author  Tobias Kuhn ( Sat Oct 07 22:55:33 CEST 2006 )
* @name    showDay
* @version 1.0 
* @param   int d
* @return  void
* @desc    switches calendar mode to day
**/
function showDay(d)
{
	document.adminForm.day.value = d;
	document.adminForm.display.selectedIndex = 1;
	document.adminForm.submit();
}