<?php

function get_need_states() {
    global $mysqli, $min_hour, $max_hour;

    $now_hour = (int) date('H');
    $query_country = "SELECT
        `country_id` 
    FROM `jos_vm_country` 
    WHERE `country_3_code`='AUS'";
    
    $result_country = $mysqli->query($query_country);
    $obj_country = $result_country->fetch_object();
    $result_country->close();
    
    $query_states = "SELECT 
        `state_2_code`, 
        `timezone_offsets` 
    FROM `jos_vm_state` 
    WHERE 
        `country_id`=".$obj_country->country_id."
    ";
    
    $result_states = $mysqli->query($query_states);

    $need_states = array();

    while ($obj_state = $result_states->fetch_object()) {
        $state_hour = $now_hour + $obj_state->timezone_offsets; //system behind 4 hrs

        if ($min_hour <= $state_hour AND $state_hour <= $max_hour) {
            $need_states[] = $obj_state->state_2_code;
        }
    }
    $result_states->close();
    $mysqli->close();
    
    return $need_states;
}

function show_need_states() {
    global $mysqli, $min_hour, $max_hour;

    $now_hour = (int) date('H');
    $query_country = "SELECT
        `country_id` 
    FROM `jos_vm_country` 
    WHERE 
        `country_3_code`='AUS'
    ";

    $result_country = $mysqli->query($query_country);
    $obj_country = $result_country->fetch_object();
    $result_country->close();

    $query_states = "SELECT
        `state_2_code`, 
        `timezone_offsets` 
    FROM `jos_vm_state` 
    WHERE 
        `country_id`=".$obj_country->country_id."
    ";

    $result_states = $mysqli->query($query_states);

    $need_states = array();
    $state_hours = array();
    while ($obj_state = $result_states->fetch_object()) {
        $state_hour = $now_hour + $obj_state->timezone_offsets; //system behind 4 hrs
        $state_hours[$obj_state->state_2_code] = $state_hour;
        if ($min_hour <= $state_hour AND $state_hour <= $max_hour) {
            $need_states[] = $obj_state->state_2_code;
        }
    }
    
    $result_states->close();
    $mysqli->close();
    
    return $state_hours;
}
