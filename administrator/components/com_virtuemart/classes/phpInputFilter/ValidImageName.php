<?php
class ValidImageName
{
    // object instance
    protected static $instance;
    // class data
    private $path_product = '/components/com_virtuemart/shop_image/';
    private $extensions = array( 'jpg', 'jpeg', 'gif', 'bmp', 'png','webp' );
    private $mosConfig_absolute_path = '';
    
    // private singleton functions
    // -----------------------------------------------------------------------
    private function __construct()
    {
        require_once '../../../../../configuration.php';
        $this->mosConfig_absolute_path = $mosConfig_absolute_path;
    }
    private function __clone()    {}  // private clone
    private function __wakeup()   {}  // private unserialize
    
    // public functions
    // -----------------------------------------------------------------------
    public static function instance()
    {
        if ( is_null(self::$instance) ) self::$instance = new ValidImageName ();
        return self::$instance;
    }
    
    // -----------------------------------------------------------------------
    function isset_image_name_product( $name, $file_extension, $folder )
    {//return self::$instance->path_product.$name.'.'.$file_extension;
        if(preg_match('/[^0-9a-zA-Z\._-]/', $name))
                return 'The following characters are allowed only: ., _, -, 0-9, a-z, A-Z.';
        
        if( !in_array($file_extension, self::$instance->extensions) )
            return 'Not valid file extension.';
        
        if( strlen( $name ) < 3 )
            return 'Short file name.';

        $path = $this->mosConfig_absolute_path.self::$instance->path_product.$folder.'/'.$name.'.'.$file_extension;
        if( file_exists( $path ) )
                return 'File already exist.';
        
        return 'true';
    }
    
    function createNewName( $name, $file_extension, $folder )
    {
        $updateName = $name = self::instance()->clearName( $name );
        $i = 0;
        while (true) {
            if( self::instance()->isset_image_name_product( $updateName, $file_extension, $folder ) === 'true' )
            {
                return $updateName;
            }
            $updateName = $name.'_'.$i;
            $i++;
        }        
    }
    
    function clearName( $name )
    {
        $name = strtolower( $name );
        $name = str_replace( ' ', '_', $name );
        $name = preg_replace( "/[^a-zA-z0-9_.\s]/", "", $name );
        return $name;
    }
}
?>
