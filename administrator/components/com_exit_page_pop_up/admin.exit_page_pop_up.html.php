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
class ExitPagePopUp {
//class ServerManagerScreens {  

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
    function ListPopUps() {
        global $database, $mosConfig_live_site;

        $q = "SELECT `id`, `title`, `host`, `login`, `password`, `db_name`, `start_date`, `interval`,FROM_UNIXTIME(`last_time`) AS last_time, `cod`, `search_user` FROM tbl_db_connect_data AS a, tbl_last_load AS b WHERE b.`db_id`=a.`id` ";
        $database->setquery($q);
        $database->query;
        $html = '<script type="text/javascript" src="' . $mosConfig_live_site . '/administrator/components/com_virtuemart/html/jquery.js" ></script>
<link rel="stylesheet" href="http://' . $_SERVER["SERVER_NAME"] . '/administrator/components/com_exit_page_pop_up/calendar/jquery-ui.css" />
<script src="http://' . $_SERVER["SERVER_NAME"] . '/administrator/components/com_exit_page_pop_up/calendar/jquery-1.9.1.js"></script>
<script src="http://' . $_SERVER["SERVER_NAME"] . '/administrator/components/com_exit_page_pop_up/calendar/jquery-ui.js"></script>
<script src="http://' . $_SERVER["SERVER_NAME"] . '/administrator/components/com_exit_page_pop_up/calendar/jquery-ui-timepicker-addon.js"></script>

<script>
    function activeCheck(id){
    $("#popup_active_"+id).val(1);
    $("#adminForm").attr("action", "index2.php?option=com_exit_page_pop_up&task=update&id="+id); 
    $("#adminForm").submit();
        
    }
</script>
<table  class="adminlist" summary="List of all registered domains">
            <thead>
            <tr style="text-align:left">                
                <th id="hMessage">Message</th>
                <th id="hLang">Lang</th>
                <th id="hSKU">Product id</th>
                <th id="hActive">Active</th>                
               
                <th id="hDelete">DELETE</th>    
            </tr>
            </thead>
            <tbody>';
        $query = "SELECT * FROM tbl_exit_page_pop_up";
        $result = $database->setQuery($query);
        $database->query();
        $result = $database->loadAssocList();
        $i = 0;
        $options = '';

        foreach ($result as $row) {

            $i ? $i = 0 : $i = 1;
            $html.='<tr class="row' . $i . '">';
            $html.='<td headers="hMessage"><a title="edit" href="index2.php?option=com_exit_page_pop_up&task=edit&id=' . $row['id'] . '">' . $row['message'] . '</a></td>';
            $html.='<td headers="hLang">' . $row['lang'] . '</td>';
            $html.='<td headers="hSKU">' . $row['product_id'] . '</td>';
            $html.='<td headers="hActive">';
            if ($row['active'] == 1) {
                $html.= "yes";
            } else {
                $html.= "no";
            }
            $html .='</td>';
          //  $html.='<td headers="hEdit"><a title="edit" href="index2.php?option=com_exit_page_pop_up&task=edit&id=' . $row['id'] . '"><img src="images/icon18_edit_allbkg.gif" width="12" height="12" border="0" alt="Delete" title="Delete"></a></td>';
            $html.='<td headers="hDelete"><a title="edit" href="index2.php?option=com_exit_page_pop_up&task=delete&id=' . $row['id'] . '"><img src="images/publish_x.png" width="12" height="12" border="0" alt="Delete" title="Delete"></a></th>';
            $html.="</tr>";
            $options .=" <option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
        }

        $html.='</tbody>
                  <tfoot>
                 <tr>
                  <th style="text-align:left" colspan="9"><a href="index2.php?option=com_exit_page_pop_up&task=new">Add New Pop Up Gift...</a></td>
                 </tr>
                 </tfoot>
                    </table>
                    <a style="display: block;
font: bold 16px Tahoma;
width: 200px;
margin: 30px auto 10px;" href="/administrator/index2.php">Back to adminpanel</a>';
        $html = str_replace('{options}', $options, $html);
        return $html;
    }

    function Add() {
        $tmpl = & ExitPagePopUp::createTemplate();
        $tmpl->setAttribute('body', 'src', 'exit_pop_up_edit.html');
        $tmpl->addVar('body', 'task', 'save');
        $tmpl->addVar('body', 'header', 'New Pop Up');
        $tmpl->addVar('body', 'host', 'db2.cbkfsmxfdx3h.us-west-2.rds.amazonaws.com');
         $tmpl->addVar('body', 'popup_active', 'checked="checked"');
            $tmpl->addVar('body', 'p', '1');
        $tmpl->displayParsedTemplate('form');
    }

    function Edit($id) {
        global $database;
        $q = "SELECT * FROM `tbl_exit_page_pop_up` WHERE id=" . $id;
        $html = $q;
        $database->setquery($q);
        $database->query();
        $row = $database->loadAssocList();
        $row = $row[0];
        $tmpl = & ExitPagePopUp::createTemplate();

        $tmpl->setAttribute('body', 'src', 'exit_pop_up_edit.html');
        $tmpl->addVar('body', 'task', 'update');
        $tmpl->addVar('body', 'id', $id);
        $tmpl->addVar('body', 'popup_message', $row['message']);
        if ($row['lang'] == 'en') {
            $tmpl->addVar('body', 'SELECTED_EN', 'selected="selected"');
            $tmpl->addVar('body', 'SELECTED_FR', '');
        } else {
            $tmpl->addVar('body', 'SELECTED_EN', '');
            $tmpl->addVar('body', 'SELECTED_FR', 'selected="selected"');
        }
        $tmpl->addVar('body', 'product_id', $row['product_id']);
        if ($row['active'] == 1) {
            $tmpl->addVar('body', 'popup_active', 'checked="checked"');
            $tmpl->addVar('body', 'p', '1');
        } else {
            $tmpl->addVar('body', 'popup_active', '');
        }
        $tmpl->addVar('body', 'header', 'Update Pop Up');
        $tmpl->displayParsedTemplate('form');
    }

    function Main($message = '') {
        // import the body of the page
        //$tmpl = & ExitPagePopUp::createTemplate();
        $tmpl = & ExitPagePopUp::createTemplate();
        $tmpl->setAttribute('body', 'src', 'exit_pop_up.html');
        $tmpl->addVar('body', 'message', $message);
        $tmpl->addVar('body', 'popupslist', ExitPagePopUp::ListPopUps());
        $tmpl->displayParsedTemplate('form');
    }

}

?>
