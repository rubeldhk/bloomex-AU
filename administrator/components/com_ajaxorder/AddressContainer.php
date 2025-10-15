<?php
 class AddressContainer { 
    // class data
    // -----------------------------------------------------------------------
    private $container = array(); // array map address data
    private $container_names = array(); // array map address data
    protected static $instance;  // object instance
    
    // private singleton functions
    // -----------------------------------------------------------------------
    private function __construct(){}  // pritect construct
    private function __clone()    {}  // pritect clone
    private function __wakeup()   {}  // protect unserialize
    
    // public functions
    // -----------------------------------------------------------------------
    public static function instance()
    {
        if ( is_null(self::$instance) ) self::$instance = new AddressContainer ();
        return self::$instance;
    }
    
    public function get( $name )
    {
        return self::$instance->container[$name];
    }
    
    public function get_all()
    {
        return self::$instance->container;
    }
    
    public function get_names()
    {
        return self::$instance->container_names;
    }
    
    public function insert( $name, $data )
    {
        //if( self::$instance->isset_name( $name ) ) return true;
        self::$instance->container[$name] = $data;
        self::$instance->container_names[] = $name;
        return false;
    }
    
    public function clear()
    {
        self::$instance->container = array();
    }
    
    public function delete( $name )
    {
        if( self::$instance->isset_name( $name ) )
        {
            remove( self::$instance->container[$name] ); 
        }
    }
    
    // private class functions
    // -----------------------------------------------------------------------
    public function isset_name( $name )
    {
        if (array_key_exists($name, self::$instance->container)) return true;
        return false;
    }
 }
?>