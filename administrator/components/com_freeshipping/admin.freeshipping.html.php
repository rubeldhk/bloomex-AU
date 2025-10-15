<?php

ini_set('max_file_uploads', '30');
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

/**
 * @package HelloWorld
 */
class EditFreeShipping {
    function open($price,$public){
       ?>
           
<form action="index2.php" method="post" name="adminForm">
        <table class="adminheading ">
                <tr>
                    <th colspan="2">
                        Free Shipping Manager 
                        </th>
          </tr>
        </table>
     <table class=" table_class">
                <tr>
                </tr>
                <tr>
                    <td style="font-weight: bold;font-size: 22px;">
                    On/Off
                    </td>
                    <td>
                        <select style="font-weight: bold;padding: 5px;font-size: 15px;" name="publish" class="public">
                            <option <?php  if($public) { echo 'selected'; }?> value="1">ON</option>
                            <option <?php  if(!$public) { echo 'selected'; }?> value="0">OFF</option>
                        </select>
                    </td>
                </tr>
                <tr>
                      <td style="font-weight: bold;font-size: 22px;">
                        Price Threshold:
                    </td>
                    <td>
                        <input  style="font-weight: bold;padding: 5px;font-size: 15px;" type="text" name="price" value="<?php echo $price;?>" class="price" placeholder="price">
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
        var price = $('.price').val()
        var public = $('.public').val()
        $('.submit_button').val('Please Wait ... ')
        $.post("index2.php", {
                                        option: 'com_freeshipping',
                                        price: price,
                                        public: public,
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