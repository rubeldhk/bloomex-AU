<?php

class cron_mysqli extends mysqli {

    private $file = '';

    public function __construct($host, $user, $password, $db, $file) {
        date_default_timezone_set('Australia/Sydney');

        parent::__construct($host, $user, $password, $db);

        $this->file = basename($file);
//        echo '<pre>$file<br/>';
//        print_r($this->file);
//        echo '</pre>';

        if ($this->connect_error) {
            die('Connection error (' . $this->connect_errno . ') ' . $this->connect_error);
        }

        $this->set_charset('utf8');
    }

    public function query($query) {
        $run_time = date('Y-m-d G:i:s');

        $log_query = "INSERT INTO `tbl_cron_queries`
        (
            `file`,
            `query`,
            `run_time`
        )
        VALUES (
            '" . $this->real_escape_string($this->file) . "',
            '" . $this->real_escape_string(str_replace("\r\n", ' ', $query)) . "',
            '" . $run_time . "'
        )";

        if (parent::query($log_query)) {
            $id = $this->insert_id;
            $time_start = microtime(true);

//            echo '<pre>$query<br/>';
//            print_r($query);
//            echo '</pre>';

            $result = parent::query($query);

            $time_end = microtime(true);
            $query_time = $time_end - $time_start;

//            echo '<pre>sec<br/>';
//            print_r($query_time);
//            echo '</pre>';

            if ($result) {
                $log_query = "UPDATE `tbl_cron_queries` AS `q`
                SET
                    `q`.`rows_count`=" . (int) $this->affected_rows . ",
                    `q`.`query_time`='" . $query_time . "'
                WHERE
                    `q`.`id`=" . $id . "
                ";

//                echo '<pre>num_rows<br/>';
//                print_r($log_query);
//                echo '</pre>';

                parent::query($log_query);

                return $result;
            } else {
                $log_query = "UPDATE `tbl_cron_queries` AS `q`
                SET
                    `q`.`error`='" . $this->real_escape_string($this->error) . "',
                    `q`.`query_time`='" . $query_time . "'
                WHERE
                    `q`.`id`=" . $id . "
                ";

                parent::query($log_query);

                die('error update tbl_cron_query:' . $log_query);
            }
        }
    }

}
