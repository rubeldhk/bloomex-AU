<?php
class ConstructBodyMapData
{
    private $result = array(); 
    private $last_lap_data = '';
    function ConstructBodyMapData()
    {
        require_once 'LastMapData.php';
        $this->last_lap_data = new LastMapData();
    }
    
    function work( $sOrderListID, $data )
    {
        global $database;
        $result_id = array();
        $hmlt = '';
            $show_map = true;
                if ($sOrderListID == "" && $sOrderListID !== false) {
                    if( ( $datamap = $this->last_lap_data->get() ) )
                    {
                        $hmlt .= $this->last_lap_data->table_start();  
                        $information_line = array(); // information line;
                        $addr_line = array();
                        $addr_line_number = array();
                        $addr_line[0] = '0'; // map show address
                        $address_print = ''; // print show address
                        $line_i = 1;
                        $i = 0;

                        while(isset($datamap[$i])) {
                            $row = explode( '[--1--]', $datamap[$i]->address );
                            $link_data = (string)((int)$row[1]);
                            $addr_line_number[$line_i] = $link_data;
                            $addr_line[$link_data] = $link_data.'[--2--]'.$row[4];
                            $address_print .=  ($i+1).'&nbsp;&nbsp;&nbsp;['.$row[1].']&nbsp;&nbsp;&nbsp;' . $row[4] . '<br>';
                            $hmlt .=  $this->last_lap_data->get_table_content( $row, $row[1] );
                            $line_i++;
                            $i++;
                        }
                    }
                } elseif ($sOrderListID) {
                    $all_orders = array();
                    $all_orders_status = array();
                    $hmlt .= $this->last_lap_data->table_start();
                    
                    $insert_data = array();
                    $sDriversName_array = array();
                    
                        $line_i = 0;
                        $count = 1;
                        $information_line = array(); // information line;
                        $addr_line = array();
                        $addr_line_number = array();
                        $addr_line['0'] = ''; // map show address
                        $address_print = ''; // print show address

                        if (count($data)) {
                            foreach ($data as $Item) {
                                $query = "SELECT * FROM tbl_driver_option_order WHERE order_id = $Item->order_id";
                                $database->setQuery($query);
                                $oDeliverOptions = $database->loadObjectList();

                                if ($oDeliverOptions[0]) {
                                    $sDeliverOptionsName = $oDeliverOptions[0]->description;
                                    $aDriversOptions = explode("[--2--]", $oDeliverOptions[0]->description);


                                    $sDriversName = "";
                                    for ($i = 0; $i < count($aDriversOptions); $i++) {
                                        if (trim($aDriversOptions[$i])) {
                                            $aDriversOptionItem = explode("[--1--]", $aDriversOptions[$i]);

                                            $sDriversName .= str_replace("_", " ", "<b>" . $aDriversOptionItem[0]) . ":</b> " . $aDriversOptionItem[1] . "<br/>";
                                            $sDriversName_array[(int)$Item->order_id] = $sDriversName;
                                        }
                                    }
                                }
                                $all_orders[] = $Item->order_id;
                                $all_orders_status[$Item->order_id] = $Item->order_status_name;

                            }
                            asort($all_orders);
                            $count_all_orders = count($all_orders);
                            for( $i = 0; $i < $count_all_orders; $i++ )
                            {
                                $order_id = (int)$all_orders[$i];
                                $query = "SELECT order_id,user_id,ddate FROM jos_vm_orders WHERE order_id='" . $order_id . "'";
                                $database->setQuery($query);
                                $result = $database->loadObjectList();
                                if ($result[0]) {
                                    $q = "SELECT * from jos_vm_order_user_info WHERE order_id='" . $order_id . "' ORDER BY address_type DESC LIMIT 2";
                                    $database->setQuery($q);
                                    $result2 = $database->loadObjectList();
                                    $link_data = (string)((int)$result[0]->order_id);
                                    $addr_line_number[$count] = $link_data;
                                    $database_addr = $result2[0]->city. ', ' . $result2[0]->address_1. ', ' . $result2[0]->zip. ', ' . 'Australia';
                                    $addr_line[$link_data] = $link_data.'[--2--]'.$database_addr;
                                }
                                
                                
                               $information_line[$line_i][] = $count;
                               $information_line[$line_i][] = $order_id;
                               $information_line[$line_i][] = $all_orders_status[$order_id];
                               $information_line[$line_i][] = $sDriversName_array[$order_id];
                               
                               $address_print .=  ($line_i+1).'&nbsp;&nbsp;&nbsp;['.$order_id.']&nbsp;&nbsp;&nbsp;' . $database_addr . '<br>';
                               
                               $information_line[$line_i][] = $database_addr;
                               $insert_data[$line_i] = implode( '[--1--]', $information_line[$line_i] );
                               
                                $hmlt .=  $this->last_lap_data->get_table_content( $information_line[$line_i], $order_id );
                               
                                $line_i++;
                                $count++;
                            }
                            
                        }
                }
                
                $this->result['html'] = $hmlt;
                $this->result['information_line'] = isset($information_line) ? $information_line : '';
                $this->result['addr_line'] = isset($addr_line) ? $addr_line : '';
                $this->result['addr_line_number'] = isset($addr_line_number) ? $addr_line_number : '';
                $this->result['address_print'] = isset($address_print) ? $address_print : '';
                $this->result['show_map'] = isset($show_map) ? $show_map : '';
                $this->result['insert_data'] = isset($insert_data) ? $insert_data : '';
            
    }
    
    function last_map_data_get()
    {
        return $this->last_lap_data->get();
    }
    
    function get()
    {
        return $this->result;
    }
    
    function set( $warehouse, $driver, $data )
    {
        return $this->last_lap_data->set( $warehouse, $driver, $data );
    }
    
    function warehouse()
    {
        return $this->last_lap_data->warehouse();
    }
    
    function driver()
    {
        return $this->last_lap_data->driver();
    }
    
    function table_end()
    {
        return $this->last_lap_data->table_end();
    }
    
    
    function get_html()
    {
        return $this->result['html'];
    }
}
?>
