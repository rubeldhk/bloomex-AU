<?php
class FooterFeatureContent{
    
    private $batabaseTable = "jos_vm_footer_feature";
    private $catalogFooter = "../images_upload/footer_feature/line_{LINE}/"; 
    private $line = 0;
    private $countSlides = 3;
    public $emptySlide = "<div id='slide-{LINE}-{COUNT}' onclick=\"uploadAjaxFile('{LINE}-{COUNT}');\"><div id='empty-slide'>
                                Click to download the picture.
                           </div></div>";
    private $fullSlide = "<div id='slide-{LINE}-{COUNT}' onclick=\"uploadAjaxFile('{LINE}-{COUNT}');\">
                            <img src='{CATALOG}{SRC}' width='150' height='150'>
                          </div>";
    private $dataset = array();
    
    private $slide = array();
    private $href = array();
                         // -----------------------------------------------------------------------------------------------------------------------------
    function &createTemplate() {
        global $option, $mosConfig_absolute_path;
        require_once( $mosConfig_absolute_path
                . '/includes/patTemplate/patTemplate.php' );
        $tmpl = & patFactory::createTemplate($option, true, false);
        $tmpl->setRoot(dirname(__FILE__) . '/tmp');
        return $tmpl;
    }
    
    function slide($line, $count){
        $slide = str_replace( "{LINE}", $line, $this->emptySlide );
        return str_replace( "{COUNT}", $count, $slide );
    }
    
    function Fullslide($line, $count, $src){
        $slide = str_replace( "{CATALOG}", $this->catalogFooter, $this->fullSlide );
        $slide = str_replace( "{LINE}", $line, $slide );
        $slide = str_replace( "{COUNT}", $count, $slide );
        return str_replace( "{SRC}", $src, $slide );
    }
    
    function body(){
        global $database;
        $this->slide = array();
        $this->href = array();
        $this->dataset = array();
        $query = "SELECT * FROM $this->batabaseTable WHERE line='$this->line'";
        $database->setQuery( $query );
        $result = $database->loadObjectList();
        if( $result ){
            if( count( $result ) > 0 ){
                $images = explode( '[--1--]', $result[0]->images );
                for ($i = 0; $i < $this->countSlides; $i++){
                    if( strlen( $images[$i] ) > 0 ){
                        $this->slide[$i] = $this->Fullslide($result[0]->line, $i, $images[$i]);
                    }
                    else{
                        $this->slide[$i] = $this->slide($result[0]->line, $i);
                    }
                }
                $this->dataset['title'] = $result[0]->title;
                $this->dataset['subtitle'] = $result[0]->subtitle;
                $this->dataset['text'] = $result[0]->text;
                
                $hrefs = explode('[--1--]', $result[0]->hrefs);
                for ($i = 0; $i < $this->countSlides; $i++) $this->href[$i] = $hrefs[$i];
            }
        }
        
        if ( !isset( $this->slide[0] ) )for ($i = 0; $i < $this->countSlides; $i++) $this->slide[$i] = $this->slide($this->line, $i);
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function create() {
         $this->body();
        
         $tmpl = & self::createTemplate();
         $tmpl->setAttribute('body', 'src', 'tmp.html');
         $tmpl->addVar('body', 'line', $this->line );
         for ($i = 0; $i < $this->countSlides; $i++) {
             $tmpl->addVar('body', 'slide_'.$this->line.'_'.$i, $this->slide[$i] );
             $tmpl->addVar('body', 'href_'.$i, $this->href[$i] );
         }
         $tmpl->addVar('body', 'title_'.$this->line,        $this->dataset['title'] );
         $tmpl->addVar('body', 'subtitle_'.$this->line,     $this->dataset['subtitle'] );
         $tmpl->addVar('body', 'text_'.$this->line,         $this->dataset['text'] );
         $tmpl->addVar('body', 'atitle_'.$this->line,       nl2br($this->dataset['title']) );
         $tmpl->addVar('body', 'asubtitle_'.$this->line,    nl2br($this->dataset['subtitle']) );
         $tmpl->addVar('body', 'atext_'.$this->line,        nl2br($this->dataset['text']) );
         
         $this->line++;
         $tmpl->displayParsedTemplate('form');
     }
}

require_once 'SaveFooterFeature.php';
?>
<script type="text/javascript" >
	function uploadAjaxFile(clideCount){ 
                var file=$('#file-'+clideCount);	
                file.trigger('click');
	}
        
        function upload(line, i){
            console.log( '#slide-'+line+'-'+i +' == '+ '#file-'+line+'-'+i);
            var slide = $('#slide-'+line+'-'+i);
            var file = $('#file-'+line+'-'+i);
            if( file.val().length > 0 ){
                slide.html("<div id='empty-slide'>New image.</div>");
            }
        }

        function presentation(name,line){
            $('#p'+name+'_'+line).html($('textarea[name="'+name+'_'+line+'"]').val().replace(/\n/g,'<br>'));
        }
        
        function saveForm(line){
            $('#form_'+line).submit();
        }
</script>
<?php
$FooterFeatureContent = new FooterFeatureContent();
$FooterFeatureContent->create();
$FooterFeatureContent->create(); 
?>