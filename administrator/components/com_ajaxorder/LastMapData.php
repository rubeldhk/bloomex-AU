<?php
class LastMapData{
    
    private $table_name = 'jos_vm_last_map_data';
    private $id = '';
    public $warehouse = '';
    public $number = 1;
    public $driver = '';
    // ------------------------------------------------------------------------
    function LastMapData()
    {
        global $my;
        $this->id = $my->id; 
    }
    
    // ------------------------------------------------------------------------
    function set( $warehouse, $driver, $data )
    {
        global $database, $my;
        $this->number = 1;
        $count = count($data);
        for( $i = 0; $i < $count; $i++ )
        {
            $order_id = explode( '[--1--]', $data[$i] );
            $query = "SELECT * FROM ".$this->table_name." WHERE user_id='" . $my->id . "' && order_id='" .(int)$order_id[1]. "'";
            $database->setQuery($query);
            $result = $database->loadObjectList();
            if( !$result )
            {
                $query2 = "SELECT * FROM ".$this->table_name." ORDER BY id DESC LIMIT 1";
                $database->setQuery($query2);
                $result2 = $database->loadObjectList();
                $id = ( $result2 ) ? ($result2[0]->id + 1) : 1;
                $query  = "INSERT INTO ".$this->table_name." (id,order_id,user_id, warehouse, driver, address) VALUES ('".$id."','".(int)$order_id[1]."', '".$this->id."', '".$warehouse."', '".$driver."', '".$data[$i]."' )";
                $database->setQuery($query);
                $database->query();
                return $data[$i];
            }
        }
        return null;
    }
    
    // ------------------------------------------------------------------------
    function get()
    {
        global $database;
        $query = "SELECT * FROM ".$this->table_name." WHERE user_id='" . $this->id . "' ORDER BY id ASC";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if (isset($result[0])) { 
            $this->warehouse = $result[0]->warehouse; 
            $this->driver = $result[0]->driver; 
        }
        return (isset($result[0])) ? $result : null;
    }
    
    function get_table_content( $information_line, $order_id )
    { 
        return '<tr id="line_address_'.(int)$order_id.'">
                    <td style="text-align:center;vertical-align:top;">'.($this->number++).'</td>
                    <td style="text-align:left;vertical-align:top;"><strong>'.(int)$order_id.'</strong></td>
                    <td style="text-align:left;vertical-align:top;">'.$information_line[2].'</td>
                    <td style="text-align:left;padding-right:10px;vertical-align:top;font-size:11px;">
                    <span>'.$information_line[3].'</span>
                    </td>
                    <td style="text-align:left;padding-right:10px;vertical-align:top;font-size:11px;">'.$information_line[4].'</td>
                    <td style="text-align:center;padding-right:10px;vertical-align:top;font-size:11px;" width="100">
                        <input type="button" name="remove" value="Remove" onclick="removeShipOrder('.(int)$order_id.');" />
                    </td>
                </tr>';
    }
    
    function table_start()
    {
        return '
            <table width="100%" class="adminform" border="1">
                        <tr>
                            <th width="30">#</th>
                            <th width="120" style="text-align:left;">Order ID</th>
                            <th width="250" style="text-align:left;">Order Status</th>
                            <th style="text-align:left;">Driver Option</th>
                            <th style="text-align:left;">Driver Addresses</th>
                            <th style="text-align:center;">Remove In List</th>														
                        </tr>';
    }
    
    function table_end()
    {
        return ' 
                        <tr>
                            <td style="padding:20px 0px 20px 20px;" colspan="6">
                               

                            </td>
                        </tr>
                    </table>
                    ';
    }
    
    function warehouse()
    {
        return $this->warehouse;
    }
    
    function driver()
    {
        return $this->driver;
    }
       
}
?>
