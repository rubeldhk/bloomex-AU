/*
** init function is called on the load of the page:
**   - it defines effect
**   - initializes state variable is_processing
**   - calls the callback function
*/
function init() {
	
	// we declare effect:
	effect = new fx.Height(to, {duration: tps});
	effect.hide();
	
	// initialization of the state variable:
	is_processing = false;
	
	// callback function:
	new Form.Element.Observer(from, 0.01, livesearch);
	
}

/*
** The callback function which will be called every 0.01s:
**   - switches on the number of characters typed
**   - and in case, launch the request
**/
function livesearch() {
	
	// if there is enough characters:
	if (document.getElementById(from).value.length >= mini) {
		// if livesearch already in progress: do nothing:
    if (is_processing) return false;
    
    // else:
    is_processing = true;
    Element.show($('wait')); //display eggtimer
    effect.clearTimer(); //stops the effect
    effect.hide();
    
    // used to transmit parameter to the php file(ex: the limit)
    pars = Form.serialize(form);
    
    // ajax request:
    var myAjax = new Ajax.Request(
      by,
      {
        method: 'post',
        parameters: pars,
        onComplete: livesearchLoad
      }
    );
	// no characters: we hide the results area
	} else if (document.getElementById(from).value.length <= 0) {
    effect.clearTimer(); //stops the effect
		effect.hide();
  // not enough characters
	} else {
		effect.clearTimer(); //stops the effect
		effect.hide();
		document.getElementById(to).innerHTML = mini+' characters minimum';
		effect.toggle();
	}
}
 /*
 ** function called when the request have been completed
 **   - loads results into results area
 */
function livesearchLoad(response) {
    $(to).innerHTML = response.responseText;
    effect.toggle();
    Element.hide($('wait')); //hide eggtimer
    is_processing = false;
}

//we execute init() function:
Event.observe(window, 'load', init, false);