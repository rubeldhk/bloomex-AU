<?php
class BalloonProperty
{
    protected static $instance;
    private $result = null;
    private $values_english = array(
                                "I Love You",
                                "Happy Birthday",
                                "Get Well",
                                "Congratulations",
                                "Happy Face",
                                "It's a Boy",
                                "It's A Girl"
                           );
    private $values_french = array(
                                "Je t'aime",
                                "Happy Birthday",
                                "Get Well",
                                "Félicitations",
                                "Happy Face",
                                "C'est un garçon",
                                "C'est une fille" 
                                );
    
    private function __construct() {
        global $mosConfig_lang;
        if( !isset($_SESSION['balloon_value'])) $_SESSION['balloon_value'] = ( $mosConfig_lang == 'french' ) ? $this->first( 'french' ) : $this->first();
        $_SESSION['input_balloon_value'] = '<input type="hidden" name="balloon_value" id="balloon_value" value="'.$_SESSION['balloon_value'].'">';
    }
    private function __wakeup() {}
    private function __clone() {}
    
    public static function instance()
    {
        if( is_null( self::$instance ) ) self::$instance = new self;
        return self::$instance;
    }
    
    private function check_first()
    {
        $_SESSION['balloon_value'] = ( $mosConfig_lang == 'french' ) ? $this->first( 'french' ) : $this->first();
        $_SESSION['input_balloon_value'] = '<input type="hidden" name="balloon_value" id="balloon_value" value="'.$_SESSION['balloon_value'].'">';
    }
    
    public function get( $lang = null )
    {
        $this->check_first();
        $this->result = "<select name='balloon_select' id='balloon_select' onchange='select_balloon();'>";
        $array = ( $lang ) ? $this->values_french : $this->values_english;
        foreach ($array as $value) {
            $this->result .= "<option>".$value."</option>";
        }
        $this->result .= "</select>";
        $this->result .= '
        <script src="shttp://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script> 
        <script type="text/javascript" src="https://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
        <script src="https://malsup.github.com/jquery.form.js"></script>
        <script language="javascript">
        function select_balloon()
        {
            var balloon_select = document.getElementById(\'balloon_select\');
            var option = ( balloon_select ) ? balloon_select.value : "";
            var $j2 = jQuery.noConflict();
               $j2(function() { 
               $j2.post("/administrator/components/com_virtuemart/html/templates/product_details/BalloonAjax.php", { option:option, session_id:"'.session_id().'" },
                    function(data){
                        });
                }); 
        }
        </script>    
        ';
        return $this->result;
    }
    
    public function get_admin()
    {
        $this->result = "<select name='balloon_{noItem}' id='balloon_{noItem}'>";
        $array = ( isset($lang) ) ? $this->values_french : $this->values_english;
        foreach ($array as $value) {
            $this->result .= "<option>".$value."</option>";
        }
        return $this->result .= "</select>";
    }
    
    public function first( $lang = null )
    {
        if( $lang ) return $this->values_french[0]; 
        return $this->values_english[0];
    } 
}
?>
