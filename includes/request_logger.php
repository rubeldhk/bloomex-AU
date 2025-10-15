<?php

Class requestLogger {
    private static $_instance = null;
    private $database = null;
    private $requestLoggerID = null;
    
    public function __construct() {
        global $database;

        self::$_instance->database = $database;  
        
        $ip = self::$_instance->database->getEscaped($_SERVER['REMOTE_ADDR']);
        $area = 'frontend';

        $option = (isset($_REQUEST['option']) ? self::$_instance->database->getEscaped($_REQUEST['option']) : '');
        $page = (isset($_REQUEST['page']) ? self::$_instance->database->getEscaped($_REQUEST['page']) : '');
        $get = (isset($_GET) ? self::$_instance->database->getEscaped(json_encode($_GET)) : '');
        $post = (isset($_POST) ? self::$_instance->database->getEscaped(json_encode($_POST)) : '');
        $file = self::$_instance->database->getEscaped($_SERVER['PHP_SELF']);


        $query = "INSERT INTO `tbl_access_log` 
        (
            `area`, 
            `file`, 
            `option`, 
            `page`, 
            `ip`, 
            `get`, 
            `post`, 
            `starttime`
        ) 
        VALUES (
            '".$area."',
            '".$file."',
            '".$option."',
            '".$page."',
            '".$ip."',
            '".$get."',
            '".$post."',
            NOW()
        )";

        self::$_instance->database->setQuery($query);
        self::$_instance->database->query();
        
        $this->requestLoggerID = self::$_instance->database->insertid();
    }
    
    public function destroy(){
        global $database;
        
        $query = "UPDATE `tbl_access_log` 
        SET 
            `endtime`=NOW(),
            `phpexectime`=(NOW()-`starttime`)
        WHERE 
            `id`=".$this->requestLoggerID."
        ";
            
        $database->setQuery($query);
        $database->query();
    }
    
    public function setMy($my) {
        global $database;
        
        $access_username = $database->getEscaped($my->username.'('.$my->id.')');
        $session = $database->getEscaped(json_encode($_SESSION));

        $query = "UPDATE `tbl_access_log` 
        SET 
            `username`='".$access_username."',
            `session`='".$session."' 
        WHERE 
            `id`=".$this->requestLoggerID."
        ";
            
        $database->setQuery($query);
        $database->query();
    }
       
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }
    
}

requestLogger::getInstance();
?>
