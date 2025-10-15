<?php

ini_set('max_file_uploads', '30');
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

/**
 * @package HelloWorld
 */
class LandingProducts {

    private $count = 32;

    private function createList()
    {
        global $database;
        $this->save();

        $q = "SELECT sku FROM jos_vm_landing_products";
        $database->setQuery($q);
        $result = $database->loadObjectList();
        $html = '<table class="adminlist" width="100%">
                    <tr>
                        <th width="15">
                            #
                        </th>
                        <th>
                            SKU
                        </th>
                        <th width="90%">
                        </th>
                    </tr>';
        for ($index = 0; $index < $this->count; $index++) {
            $html .= '<tr>
                        <td align="center">
                            '.( $index + 1 ).'
                        </td>
                        <td align="center">
                            <input type="text" size="10" name="sku_'.( $index + 1 ).'" value="'.( ( $result && $result[$index] ) ? $result[$index]->sku : '' ).'">
                        </td>
                        <td>
                        </td>
                    </tr>';
        }

        $html .= '<tr>
                    <td colspan="3">
                        <input type="submit" name="save_products" value="Save Products">
                    </td>
                 </tr>
            </table>';
        return $html;
    }

    function save()
    {
        global $database;
        //$this->createTable();
        if( isset( $_POST['save_products'] ) )
        {
            for ($index = 0; $index < $this->count; $index++) {
                $q = "UPDATE jos_vm_landing_products SET sku='{$_POST['sku_'.( $index + 1 )]}' WHERE id='".( $index + 1 )."'";
                $database->setQuery($q);
                $database->query();
            }
        }
    }

    private function createTable()
    {
        global $database;
        if( isset( $_SESSION['table_landing_products'] ) && $_SESSION['table_landing_products'] ) return true;
        $q = "SELECT id FROM jos_vm_landing_products LIMIT 1";
        $database->setQuery($q);
        $result = $database->loadObjectList();
        if( !$result )
        {
            for ($index = 0; $index < $this->count; $index++) {
                $q = "INSERT INTO jos_vm_landing_products (id) VALUES ( '".( $index + 1 )."' )";
                $database->setQuery($q);
                $database->query();
            }
        }
        $_SESSION['table_landing_products'] = true;
        return true;
    }



    // -----------------------------------------------------------------------------------------------------------------------------
    function &createTemplate() {
        global $option, $mosConfig_absolute_path;
        require_once( $mosConfig_absolute_path
                . '/includes/patTemplate/patTemplate.php' );

        $tmpl = & patFactory::createTemplate($option, true, false);
        $tmpl->setRoot(dirname(__FILE__) . '/tmpl');
        return $tmpl;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    function create($message = '') {
        $tmpl = & LandingProducts::createTemplate();
        $tmpl->setAttribute('body', 'src', 'landing_products.html');
        $tmpl->addVar('body', 'list', $this->createList());
        $tmpl->displayParsedTemplate('form');
    }

}

?>