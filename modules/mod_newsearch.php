<div id="new-search-container">
    <form action="index.php" method="post" name="instantSearchForm">
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <input type="text" name="searchword" id="new-search-input" placeholder="<?php echo modulesLanguage::get('newSearchInput'); ?>" />
                    <input type="hidden" name="option" value="search">                
                </td>
                <td>
                    <img alt="search icon" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/search.jpg" class="full-new-search" />
                </td>
            </tr>
        </table>
    </form>
    <div id="newCartRevealClose"></div>
    <table id="searchDisplay"></table>
</div>
<script>  
    
    $j('.full-new-search').click(function(){
        $j('form[name=instantSearchForm]').submit();
    });
    
    $j('#new-search-container #newCartRevealClose').click(function(){
        $j('#searchDisplay').css('display', 'none');
        $j('#new-search-container #newCartRevealClose').css('display', 'none');
    });
    
    runSearchContainer();
    function runSearchContainer(){
        var $j = jQuery.noConflict();
        
        var stepList = -1;
        var listBackgroundNew = '#CCCCCC';
        var listBackgroundInherit = 'inherit';
        var up = 38;
        var down = 40;
        var enter = 13;
        var itemList = 0;
        var itemListLimit = 5;

        $j('#new-search-container #new-search-input').keyup(startSearch);
        $j('#new-search-container #new-search-input').keyup(movedList);
        
        function checkMotion(event){
            var keycode = (event.which) ?  event.which : event.keyCode;
            return ( keycode == up || keycode == down || keycode == enter ) ? keycode : false;
        }
        
        function checkEnter(event){
            var keycode = (event.which) ?  event.which : event.keyCode;
            return ( keycode == enter ) ? keycode : false;
        }
        
        function movedList(event){
            var keycode = checkMotion(event);
            if(!keycode) keycode = checkEnter(event);
            if( !keycode ) return true;
            if( keycode == enter ){
                var link = '';
                link = $j('#new-search-container #search-list-step-'+(stepList)+'').html();
                link = link.replace(/&amp;/g,"&"); 
                link = link.substring(link.indexOf('<a href="')+9,link.length);
                link = link.substring(0,link.indexOf('"'));
                
                window.location = link;
                return false;
            }
            if( keycode == up ){
                if( stepList-1 >= 0 ){
                    $j('#new-search-container #search-list-step-'+(stepList)+'').css('background-color', listBackgroundInherit);
                    $j('#new-search-container #search-list-step-'+(stepList-1)+'').css('background-color', listBackgroundNew);
                    stepList--;
                }
                else{
                    stepList = -1;
                    $j('#new-search-container #search-list-step-0').css('background-color', listBackgroundInherit);
                }
            }
            if( keycode == down ){
                if( stepList+1 <= itemList ){
                    $j('#new-search-container #search-list-step-'+(stepList)+'').css('background-color', listBackgroundInherit);
                    $j('#new-search-container #search-list-step-'+(stepList+1)+'').css('background-color', listBackgroundNew);
                    stepList++;
                }
            }
        }

        function startSearch(event){     
            if( checkEnter(event) && stepList < 0 ){
                return false;
            } 
            if(checkMotion(event)) return false;
            var searchValue = $j('#new-search-container #new-search-input').val();
            if( searchValue.length < 2 ){
                if( $j('#new-search-container #searchDisplay').css( 'display' ) == 'block' ) $j('#new-search-container #searchDisplay').css( 'display', 'none' );
                if( $j('#new-search-container #newCartRevealClose').css( 'display' ) == 'block' ) $j('#new-search-container #newCartRevealClose').css( 'display', 'none' );
                return true;
            }
            $j.post(
                 "<?php echo $mosConfig_live_site; ?>/modules/mod_newsearchajax.php",
                 {
                   search: searchValue
                 },
                 onAjaxSuccess,
                 "json"
             );

             function onAjaxSuccess(data)
             {
                 var SD = $j('#new-search-container #searchDisplay');
                 SD.html(createLine("<div id='search-line-info-top-triangle'></div>",'search-line-info-top'));
                 if( data.result == 'success' ){
                    var count = 1;
                    var end = true;
                    itemList = 0;
                    $j.each(data.search, function(item){
                        $j.each(data.search[item], function(key, value){
                            SD.append(createLine(value,'search-list-step-'+itemList));
                            if( ++count >= itemListLimit+1 ){ 
                                end = false;
                                return end;
                            }        
                            itemList++;
                        });  
                        if( !end ) {
                            return end;
                        }
                    });  
                 }
                 else{
                     SD.append(createLine(data.result,''));
                 }
                 SD.css( 'display', 'block' );
                 $j('#new-search-container #newCartRevealClose').css('display', 'block');
                 stepList = -1;
             }
             
             function createLine( value, id ){
                if( id.length > 0 ) id = 'id="'+id+'"';
                return "<tr><td "+id+">"+value+"</td></tr>";
             }
        }
    }
    
</script>

