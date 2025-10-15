<?php

global $database;

class getEmailStatus {
    
    public function getStatus($email) {
        global $database;
        
        $return = (object)array('result' => false, 'email' => $email);
        
        $validate = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        if ($validate != false) {
            $email_a = explode('@', $email);
            $row = false;
            
            $query = "SELECT
                `b`.`id`
            FROM `tbl_bad_emails` AS `b`
            WHERE 
                `b`.`email`='".$database->getEscaped($email_a[1])."'
            ";
            
            $database->setQuery($query);
            $database->loadObject($row);
            
            if ($row == false) {
                $return->result = true;
                $return->status = 'email_is_real';
            }
            else {
                $return->status = 'domain_is_bad';
            }
        }
        else {
            $return->status = 'email_not_valid';
        }

        return $return;
    }
    
}

?>
