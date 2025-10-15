<?php
require_once 'QueryBase.php';
$ViewData = new ViewData();
$ViewData->create();

class ViewData extends QueryBase
{
    private $resultNames = array( 'pid', 'fhid', 'name', 'fullname', 'cobrand', 'date', 'descriptions', 'fulllink', 'funeral_page', 'link' );
    
    public $total = 0;
    public $limit = 0;
    public $limitstart = 0;
    public $query = null;
    
    function __construct() {
    }
    
    function create()
    {
        global $database, $mosConfig_list_limit, $mosConfig_absolute_path, $mainframe;
        require_once( $mosConfig_absolute_path . '/administrator/includes/pageNavigation.php' );
        
        $this->query = $this->searchQuery();
        $database->setQuery( str_replace( "*", "count(id) as counts", $this->query ) );
        $countLines = $database->loadObjectList();
        $this->total = ( !empty($countLines) && isset($countLines[0]) ) ? $countLines[0]->counts : 0;
        $this->limit 		= intval( $mainframe->getUserStateFromRequest( "limit", 'limit', $mosConfig_list_limit ) );
	$this->limitstart 	= intval( $mainframe->getUserStateFromRequest( "limitstart", 'limitstart', 0 ) );
	
        $pageNav = new mosPageNav( $this->total, $this->limitstart, $this->limit  );
        
        echo $this->searchLine( $pageNav->getListFooter() );
        echo $this->getResult( $this->query );
    }
    
    function searchLine( $navigation )
    {
        $html = "<form name='adminForm' id='adminForm' method='post'>
                <table id='search_table' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td colspan='2' align='center'>
                            Fill in only the fields to search:
                        </td>
                    </tr>";
        foreach ($this->titles as $key => $value) {
            $name = $this->getName( $value );
            $html .= "<tr>
                        <td align='center'>
                            $value:
                        </td>
                        <td align='center'>";
            switch (strpos( $name, "date" )) {
                case false:
                    $html .= "<input type='text' name='$name' id='$name' value='".( isset( $_POST[$name] ) ? $_POST[$name] : '' )."'>";
                    break;

                default:
                    $html .= "<input type='text' name='$name' id='$name' value='".( isset( $_POST[$name] ) ? $_POST[$name] : '' )."'>";
                    break;
            }
            $html .=    "</td>
                     </tr>";
        }
        $html .= "<tr>
                    <td colspan='2' align='center'>
                        <img src='components/com_legacy/images/jfile.gif' border='0' onclick='submitbutton(\"file\")' />
                        <img src='components/com_legacy/images/jexcel.gif' border='0' onclick='submitbutton(\"excel\")' />
                    </td>
                </tr>
                </table>
                <input type='hidden' name='submitvalue' id='submitvalue' />
                <br><br>
                $navigation
                <br><br>
                </form>
                ";
        return $html;
    }
            
    function getResult( $query )
    {
        global $database;

        if( !$query ) return "Create a search query.";
        $query .= " LIMIT $this->limitstart, $this->limit";
        $database->setQuery( $query );
        $result = $database->loadObjectList();       
        
        if( $result ) return $this->getTableResult( $result );
        return "Asked empty search query.";
    }
    
    private function getTableResult( $list )
    {
        $html = "<br><br><table id='resultTable' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td colspan='".count($this->resultNames)."' align='center'>
                            Results:
                        </td>
                    </tr>
                    <tr>";
        foreach ($this->resultNames as $key => $value) {
            $html .= "<td>$value</td>";
        }
        $html .= "</tr>";
        foreach ($list as $key => $value) {
            $html .= "<tr>";
                foreach ($this->resultNames as $keyNames => $valueNames) {
                    switch ($valueNames) {
                        case 'fulllink':
                            $html .= "<td><a href='{$value->{$valueNames}}' target='_blank'>Link</td>";
                            break;
                        case 'funeral_page':
                            $html .= "<td><a href='{$value->{$valueNames}}' target='_blank'>Funeral Page</td>";
                            break;
                        case 'link':
                            $html .= "<td><a href='/index.php?page=shop.browse&category_id=139&option=com_virtuemart&pid={$value->pid}&fhid={$value->fhid}&cobrand={$value->cobrand}' target='_blank'>Link</td>";
                            break;

                        default:
                            $html .= "<td>{$value->{$valueNames}}</td>";
                            break;
                    }
                }
            $html .= "</tr>";
        }
        $html .= "</table>";
        return $html;
    }
}
?>

<style>
    #search_table{
        width: 500px;
        font-weight: bold;
    }
    
    #search_table td{
        border: 1px solid #CCCCCC;
        padding: 10px;
    }
    
    #resultTable{
        width: 100%;
    }
    
    #resultTable td{
        border: 1px solid #CCCCCC;
        padding: 10px;
    }
</style>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<link rel="stylesheet" href="components/com_legacy/datepicker/jquery-ui-1.10.3.custom.css" />
<script type="text/javascript" src="components/com_legacy/datepicker/jquery-ui-1.10.3.custom.js"></script> 
<script>    
    function submitbutton( method )
    {
        if( ( $j('#search_start_date').val() == '' && $j('#search_end_date').val() == '' ) || $j('#search_start_date').val() != '' && $j('#search_end_date').val() != '' )
        {
            $j('#submitvalue').val( method );
            document.adminForm.submit();
        }
        else{
            alert("You can not specify only one date.");
        }
    }
        
    
    jQuery(document).ready(function() {
        jQuery('#search_start_date').datepicker({
                minDate: new Date(2013, 11, 5),
                maxDate: new Date(2020, 11, 31),
                dateFormat: 'yy-mm-dd',
                constrainInput: true
        });
        jQuery('#search_end_date').datepicker({
                minDate: new Date(2013, 11, 5),
                maxDate: new Date(2020, 11, 31),
                dateFormat: 'yy-mm-dd',
                constrainInput: true
        });
    });
</script>