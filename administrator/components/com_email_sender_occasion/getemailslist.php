<?php
   $emails = ''; 
if (isset($_FILES['file_emails']) && $_FILES['file_emails']['size']!=0) {
    $oF = fopen($_FILES['file_emails']['tmp_name'], 'rb');
        $vZ = fread($oF, filesize( $_FILES['file_emails']['tmp_name'] )); 
          fclose($oF);
          $user_emails = unserialize($vZ);
         if(!empty($user_emails)){
            
            foreach($user_emails as $em){
               $emails .=  $em['user_email'].",";
            }
        }
    }
    die($emails);

