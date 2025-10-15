<?php
class QueryBase
{
    protected $titles = array( 'Pid', 'Fhid', 'Name', 'Descriptions', 'Start Date', 'End Date' );
    
    function __construct() {
    }
    
    protected function searchQuery()
    {
        if( isset( $_POST['search_pid'] ) )
        {
            $likes = null;
            foreach ($this->titles as $key => $value) {
                $name = $this->getName( $value );
                if( $_POST[$name] != '' ) $likes[$name] = $_POST[$name];
            }
        }
        else
        {
            return null;
        }
        $q = null;
        if( !empty( $likes ) )
        {
            $q = "SELECT *, 
                (CONCAT('http://bloomex.ca/index.php?pid=', pid, '&amp;fhid=', fhid, '&amp;cobrand=', cobrand, '&amp;fullname=', fullname)) as link 
                FROM spider_legacy WHERE ";
            $like_all = null;
            $like_date = null;
            foreach ($likes as $key => $value) {
                if( strpos( $key, "date" ) !== false )
                {
                    $like_date .= ( $like_date ) ? " '$value' " : " date BETWEEN '$value' AND ";
                }
                else
                {
                    $like_all .= ( $like_all ? " AND " : "" ).str_replace( "search_", "", $key )." LIKE '%$value%' ";
                }
            }
            
            if( $like_all ) $q .= $like_all;
            if( $like_all && $like_date ) $q .= " AND ";
            if( $like_date ) $q .= $like_date;
            $q .= " ORDER BY date DESC";
        }
        return $q;
    }
    
    protected function getName( $value )
    {
        return "search_".str_replace( " ", "_", strtolower( $value ) );
    }
}
?>
