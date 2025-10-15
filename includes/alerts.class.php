<?php

class joomAlert {

    /**
     * saves alert to session
     * @param string message
     * @param string type : success(green), info(blue), warning(yellow), danger (red), primary(focused blue), secondary(gray), light(white), dark(gray)
     */
    static function add_alert($msg, $type = 'info') {
        
        $_SESSION['alrt'][md5($msg)] = array('msg' => $msg, 'type' => $type);
    }

    /**
     * prints and deletes alerts from section
     */
    static function print_alerts() {
        if (isset($_SESSION['alrt'])) {
            foreach ($_SESSION['alrt'] as $v) {
                echo '<div class="alert alert-' . $v["type"] . ' alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . $v["msg"] . '</div>';
            }
        }
        if (isset($_SESSION['alrt'])) {
            unset($_SESSION['alrt']);
        }
    }

}
