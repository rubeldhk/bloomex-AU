var $j = jQuery.noConflict();
$j(function(){

    var config = {    
        sensitivity: 3, // number = sensitivity threshold (must be 1 or higher)    
        interval: 200,  // number = milliseconds for onMouseOver polling interval    
        over: doOpen,   // function = onMouseOver callback (REQUIRED)    
        timeout: 200,   // number = milliseconds delay before onMouseOut    
        out: doClose    // function = onMouseOut callback (REQUIRED)    
    };
    
    function doOpen() {
        $j(this).addClass("hover");
        $j('ul:first',this).css('visibility', 'visible');
    }
 
    function doClose() {
        $j(this).removeClass("hover");
        $j('ul:first',this).css('visibility', 'hidden');
    }

    $j("ul.dropdown li").hoverIntent(config);
    
    $j("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");

});