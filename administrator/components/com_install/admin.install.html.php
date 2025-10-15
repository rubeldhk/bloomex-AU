<?php

ini_set('max_file_uploads', '30');
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

/**
 * @package HelloWorld
 */
class Install {
    function open($installed,$last_install_date){
       ?>
           
<form action="index2.php" method="post" name="adminForm">
        <table class="adminheading ">
                <tr>
                    <th colspan="2">
                        Install Manager
                        </th>
          </tr>
        </table>
     <table class=" table_class">
                <tr>
                </tr>
                <tr>
                    <td style="font-weight: bold;font-size: 22px;">
                     Need Install Yes/No
                    </td>
                    <td>
                        <select style="font-weight: bold;padding: 5px;font-size: 15px;" name="installed" class="installed">
                            <option <?php  if(!$installed) { echo 'selected'; }?> value="0">Yes</option>
                            <option <?php  if($installed) { echo 'selected'; }?> value="1">No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                      <td style="font-weight: bold;font-size: 22px;">
                        Last Install Date:
                    </td>
                    <td>
                        <?php echo $last_install_date;?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="button" name="submit" class="submit_button" value="Save">
                    </td>
                </tr>
        </table>
           </form>
<style>
    .table_class{
            width: 50%;
            float: left;
            border-collapse: separate;
            border-spacing: 10px;
    }
    .submit_button{
     width: 130px;
    padding: 5px;
    float: left;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    background-color: #C74934;
    border-color: #C74934;
    border-radius: 10px;
    }
</style>
<script>
$( document ).ready(function() {
    $('.submit_button').click(function(){
        var installed = $('.installed').val()
        $('.submit_button').val('Please Wait ... ')
        $.post("index2.php", {
                                        option: 'com_install',
                                        installed: installed,
                                        task:'save'},
                                    function (data) {
   $('.submit_button').val('Save')
                                    })
    })
});
</script>
           <?php
    }
}