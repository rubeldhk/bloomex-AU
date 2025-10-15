<?php

/**
 * @version 1.0
 * @package Citymanger
 * @copyright Copyright (C) 2012 Bloomex
 */
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

/**
 * @package HelloWorld
 */
class ServerManagerScreens {

    /**
     * Static method to create the template object
     * @return patTemplate
     */
    function &createTemplate() {
        global $option, $mosConfig_absolute_path;
        require_once( $mosConfig_absolute_path
                . '/includes/patTemplate/patTemplate.php' );

        $tmpl = & patFactory::createTemplate($option, true, false);
        $tmpl->setRoot(dirname(__FILE__) . '/tmpl');
        return $tmpl;
    }

    /**
     * main page - list of defined adresess
     */
    function ListDomains() {
        global $database,$mosConfig_live_site;
        
        $q = "SELECT `id`, `title`, `host`, `login`, `password`, `db_name`, `start_date`, `interval`,FROM_UNIXTIME(`last_time`) AS last_time, `cod`, `search_user` FROM tbl_db_connect_data AS a, tbl_last_load AS b WHERE b.`db_id`=a.`id` ";
        $database->setquery($q);
        $database->query;
        $html = '<script type="text/javascript" src="' . $mosConfig_live_site . '/administrator/components/com_virtuemart/html/jquery.js" ></script>
<link rel="stylesheet" href="http://'.$_SERVER["SERVER_NAME"].'/administrator/components/com_servermanager/calendar/jquery-ui.css" />
<script src="http://'.$_SERVER["SERVER_NAME"].'/administrator/components/com_servermanager/calendar/jquery-1.9.1.js"></script>
<script src="http://'.$_SERVER["SERVER_NAME"].'/administrator/components/com_servermanager/calendar/jquery-ui.js"></script>
 <script src="http://'.$_SERVER["SERVER_NAME"].'/administrator/components/com_servermanager/calendar/jquery-ui-timepicker-addon.js"></script>

<script>
$(function() {
$( "#dfrom" ).datetimepicker();
$( "#dto" ).datetimepicker();

});
</script>
            
     <table>
        <tr>
            <td>from date</td>
            <td>
                <input type="text" id="dfrom" value=\'\'/>  
            </td>
            
             <td>to date </td>
            <td>
                <input type="text" id="dto" value=\'\'/>  
            </td>
             <td>order number</td>
            <td>
                <input type="text" id="norder" value=\'\'/>  
            </td>
             <td>orders src</td>
            <td>
                <select id="ordersrc" >  <option selected value="">all</option> {options} </select>
            </td>
<td colspan=2>            
<input style="float:left;margin-left:30px;font-size:14px;"  type="button" id="importOrders" value="load orders" name="load orders" />
</td>
</tr>
</table>
<table  class="adminlist" summary="List of all registered domains">
            <thead>
            <tr style="text-align:left">
                <th id="hDomain">Domain</th>
                <th id="hPostal">Last Time</th>
                <th id="hPostal">Order id prefix</th>
                <th id="hPostal">Search by user</th>                
                <th id="hDelete">DELETE</th>    
            </tr>
            </thead>
            <tbody>';
        $result = $database->loadAssocList();
        $i = 0;
        $options='';
        foreach ($result as $row) {
            
            $i ? $i = 0 : $i = 1;
            $html.='<tr class="row' . $i . '">';
            $html.='<td headers="hDomain"><a title="edit" href="index2.php?option=com_servermanager&task=edit&id=' . $row['id'] . '">'.$row['title'].'</a></td>';
            $html.='<td headers="hLast Time">' . $row['last_time'] . '</td>';
            $html.='<td headers="hOrder id prefix">' . $row['cod'] . '</td>';
            $html.='<td headers="hSearch by user">' . $row['search_user'] . '</td>';
            $html.='<td headers="hDelete"><a title="edit" href="index2.php?option=com_servermanager&task=delete&id=' . $row['id'] . '"><img src="images/publish_x.png" width="12" height="12" border="0" alt="Delete" title="Delete"></a></th>';
            $html.="</tr>";
            $options .=" <option value='".$row['id']."'>".$row['title']."</option>";
        }
        
        $html.='</tbody>
                  <tfoot>
                 <tr>
                  <th style="text-align:left" colspan="9"><a href="index2.php?option=com_servermanager&task=new">Add New Domain...</a></td>
                 </tr>
                 </tfoot>
                    </table>
                    <a style="display: block;
font: bold 16px Tahoma;
width: 200px;
margin: 30px auto 10px;" href="/administrator/index2.php">Back to adminpanel</a>';
      $html= str_replace('{options}',$options ,$html);
        return $html;
    }
    function Add() {
        $tmpl = & ServerManagerScreens::createTemplate();
        $tmpl->setAttribute('body', 'src', 'serveredit.html');
        $tmpl->addVar('body', 'task', 'save');
        $tmpl->addVar('body', 'header', 'New Domain');
        $tmpl->addVar('body', 'host', 'db2.cbkfsmxfdx3h.us-west-2.rds.amazonaws.com');
        $tmpl->addVar('body', 'interval', '120');
        $tmpl->displayParsedTemplate('form');
    }

    function Edit($id) { 
        global $database;
        $key="LRWvbvldfgERUFG;AekghbnzyfgUKA956";//pass
        $q = "SELECT id,title, host, login,password,db_name, FROM_UNIXTIME(start_date) AS start_date ,`interval`, `cod`,`search_user` FROM tbl_db_connect_data WHERE id=" . $id;
        $html = $q;
        $database->setquery($q);
        $database->query();
        $row = $database->loadAssocList();
        $row = $row[0];
        $tmpl = & ServerManagerScreens::createTemplate();
        $password=encode( rtrim($row['password']), $key);
        $tmpl->setAttribute('body', 'src', 'serveredit.html');
        $tmpl->addVar('body', 'task', 'update');
        $tmpl->addVar('body', 'id', $id);
        $tmpl->addVar('body', 'title', $row['title']);
        $tmpl->addVar('body', 'host', $row['host']);
        $tmpl->addVar('body', 'login', $row['login']);
        $tmpl->addVar('body', 'password', $password);
        $tmpl->addVar('body', 'db_name', $row['db_name']);
        $tmpl->addVar('body', 'start_date', $row['start_date']);
        $tmpl->addVar('body', 'interval', $row['interval']);
         $tmpl->addVar('body', 'cod', $row['cod']);
                  $tmpl->addVar('body', 'search_user', $row['search_user']);
        $tmpl->addVar('body', 'header', 'Update Domain');
        $tmpl->displayParsedTemplate('form');
    }

    function Main($message='') {
        // import the body of the page
        $tmpl = & ServerManagerScreens::createTemplate();
        $tmpl->setAttribute('body', 'src', 'servermanager.html');
        $tmpl->addVar('body', 'message', $message);
        $tmpl->addVar('body', 'domainlist', ServerManagerScreens::ListDomains());
        $tmpl->displayParsedTemplate('form');
    }

}

?>
