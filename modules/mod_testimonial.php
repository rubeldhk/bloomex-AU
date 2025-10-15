<?php
class mod_testimonial
{
    private $maxLength = 250;
    
    function result()
    {
        global $database;
        $query = "SELECT * FROM  `tbl_testimonials` WHERE published = '1' ORDER BY RAND()";
        $database->setQuery( $query );
        $result = $database->loadObjectList();
        $testimonials = null;
        if( $result ){
            foreach ( $result as $item ) {
                if( strlen( $item->msg ) > $this->maxLength ) $item->msg = preg_replace( '/[^ ]+$/s', '', substr ( $item->msg , 0, $this->maxLength ) ) . ' ...';
                $testimonials .= ( ( is_null( $testimonials ) ) ? "" : "[--1--]" ) . $item->msg . "[--2--]" . $item->client_name . "[--2--]" . $item->city_name . "[--2--]";
            }
        }
        return $this->clear( $testimonials );
    }
    
    function resultParth($part)
    {
        global $database;
        $query = "SELECT * FROM  `tbl_testimonials` WHERE published = '1' ORDER BY RAND()";
        $database->setQuery( $query );
        $result = $database->loadObjectList();
        $i = 0;
        $testimonials = null;
        if( $result ){
            foreach ( $result as $item ) {
                if( strlen( $item->msg ) > $this->maxLength ) $item->msg = preg_replace( '/[^ ]+$/s', '', substr ( $item->msg , 0, $this->maxLength ) ) . ' ...';
                $testimonials[$i] .= ( ( !$testimonials[$i] ) ? "" : "[--1--]" ) . $item->msg . "[--2--]" . $item->client_name . "[--2--]" . $item->city_name . "[--2--]";
                $i++;
                if( $i == $part ) $i = 0;
            }
        }
        return $this->clearParth( $testimonials, $part );
    }
    
    function clearParth( $data, $parth )
    {
        for( $i = 0; $i < $parth; $i++ ){
            $data[$i] = $this->clear( $data[$i] );
        }
        return $data;
    }
    
    function clear( $data )
    {
        $data = str_replace( "'", "`", $data );
        $data = str_replace( '"', "`", $data );
        $data = str_replace( "\\", "/", $data );
        return $data;
    }
}

$mod_testimonial = new mod_testimonial();
$resultParth = $mod_testimonial->resultParth(2);
$testimonials = $resultParth[0];
$testimonials2 = $resultParth[1];
?>
<table cellpadding="0" cellcpasing="0">
    <tr>
        <td>
            <div id="showTestimonial"></div>
        </td>
    </tr>
    <tr>
        <td>
            <div id="showTestimonial2"></div>
        </td>
    </tr>
</table>
    
<script>
    var testimonials = "<?php echo ( ( is_null( $testimonials ) ) ? "" : $testimonials ); ?>";
    var testimonials2 = "<?php echo ( ( is_null( $testimonials2 ) ) ? "" : $testimonials2 ); ?>";
    showTestimonials(testimonials, 'showTestimonial');
    showTestimonials(testimonials2, 'showTestimonial2');
    function showTestimonials(testimonials, addID){
        var testimonialsContainer = new Array();
        var limit = 0;
        var step = 0;
        var time = 10000;
        var fadeTime = 500;
        var limitText = 200;
        var images = new Array( /*"stars1.png",*/ "stars4.png", /*"stars3.png",*/ "stars5.png" );

        if(testimonials.length > 0){
            testimonialsSlides = testimonials.split("[--1--]");
            $j.each(testimonialsSlides, function(key, value){
                    var i = 0;
                    testimonialsSlidesItem = value.split("[--2--]");
                    testimonialsContainer[key] = new Array();
                    $j.each(testimonialsSlidesItem, function(keyItem, valueItem){
                        testimonialsContainer[key][i] = valueItem;
                        i++;
                   });
               });
            if( testimonialsContainer.length > 0 ){
                limit = testimonialsContainer.length;                
                run();
                setInterval(run, time);
            }
        }
        
        function run(){
            $j('#'+addID).css('display', 'block');
            if( step+1>limit ) step=0;
            var line = '';
            var line2 = '<div class=\"name-testimonialDescription\">';
            $j.each(testimonialsContainer[step], function(key, value){
                        if(value!=''){
                            if( value.length > limitText ){
                                value = value.substring(0,limitText);
                                value += ' ...';
                            }
                            var flag = ( line.length<1 ) ? true : false;
                            if( flag ){ 
                                line = "<div class=\"testimonialDescription\">"+line;
                                line += value;
                                line += '&nbsp;&nbsp;&nbsp;<img src="/images/'+images[getRandomArbitary(0, 1)]+'" />';
                                line = line+"</div>";
                            }
                            if(!flag){line2+=value+((key===2)?'.':', ');}
                        }
                   });
            line2 += '</div>';            
            $j('#'+addID).html(line+line2);
            
            
            step++;            
            setTimeout(function(){$j('#'+addID).fadeOut(fadeTime);},(time-fadeTime*2));
        }
        
        function getRandomArbitary(min, max)
        {
          return Math.round(Math.random() * (max - min) + min);
        }

    } 
</script>
