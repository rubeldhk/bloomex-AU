<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);

if (isset($_POST['submit']))
{
    include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $mysqli->set_charset('utf8');
    
    function generateRandomString($pattern) {
        global $mysqli;

        $prefix = str_replace('?', '', $pattern);
        $length = substr_count($pattern, '?');
        $characters = '2345679ABCDEHJKLMNOPQSUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        if(!$length){
            die('wrong pattern');
        }
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $randomString = $prefix.$randomString;

        $query = "SELECT 
            * 
        FROM `jos_vm_coupons` 
        WHERE `coupon_code`='".$mysqli->real_escape_string($randomString)."'";

        $result = $mysqli->query($query);

        if ($result->num_rows == 0) {
            $result->close();
            return $randomString;
        }
        else {
            $result->close();
            generateRandomString($pattern);
        }
    }

    $inserts = $inserts_csv = array();

    $number_of_coupons = (int)$_POST['number_of_coupons'];
    $value_of_coupons = floatval($_POST['value_of_coupons']);
    $coupons_type = $_POST['coupons_type'];
    $percent_or_total = $_POST['percent_or_total'];
    $pattern = $_POST['pattern_coupons'];
    $expiry_date = $_POST['expiry_date'];

    for ($i = 1; $i <= $number_of_coupons; $i++) {
        $new_coupon_code = generateRandomString($pattern);

        $inserts[] = "('".$new_coupon_code."', '".$percent_or_total."', '".$coupons_type."', '".$value_of_coupons."', '".$expiry_date."')";
        $inserts_csv[] = array(0 => $new_coupon_code);
    }

    if (sizeof($inserts) > 0) {
        
        $query = "INSERT INTO `jos_vm_coupons`
        (
            `coupon_code`, 
            `percent_or_total`, 
            `coupon_type`, 
            `coupon_value`,
            `expiry_date`
        ) 
        VALUES ".implode(',', $inserts)."
        ";
        $mysqli->query($query);

        unset($inserts);

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=new_coupons.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $out = fopen('php://output', 'w');

        foreach($inserts_csv as $item) {	
            fputcsv($out, array($item[0]));
        }	

        fclose($out);

        unset($inserts_csv);

        $mysqli->close();
    }
}
else {
    ?>
    <link rel="stylesheet" href="resources/bootstrap.min.css">
    <link rel="stylesheet" href="resources/jquery-ui.css">
    <script src="resources/jquery.js"></script>
    <script src="resources/jquery-ui.js"></script>
    <script>
        $( function() {
            $('#expiry_date').datepicker({ dateFormat: 'yy-mm-dd',minDate:0 });
        } )
    </script>
    <style>
        #coupon_generator{
            border-radius: 10px;
            background: #f0e1ed;
            width: 30%;
            margin: 10px auto;
            padding: 25px;
        }
        body{
            background: #b0c4de;
        }
        .width_full{
            width: 100%;
        }
    </style>
    <div id="coupon_generator">
    <form action="/scripts/coupons_creator.php"  method="post" name="adminForm" id="adminForm" class="form form-horizontal">
        <div class="form-group row">
            <label class="control-label">
                Number of coupons
            </label>
            <div class="controls">
                <input class="input-small form-control" type="number" name="number_of_coupons" id="number_of_coupons" size="5" maxlength="250" value="">
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label">
                Value of coupons
            </label>
            <div class="controls">
                <input class="input-small form-control" type="text" name="value_of_coupons" id="value_of_coupons" size="5" maxlength="250" value="">
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label">
                Percent or Total
            </label>
            <div class="controls">
                <select id="coupons_type" name="percent_or_total" class="form-control input-medium chzn-done">
                    <option value="total">Total</option>
                    <option value="percent">Percent</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label">
                Coupon Type
            </label>
            <div class="controls">
                <select id="coupons_type" name="coupons_type" class="form-control input-medium chzn-done">
                    <option value="gift">Gift Coupon</option>
                    <option value="permanent">Permanent Coupon</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label">
                Pattern
            </label>
            <div class="controls">
                <input class="form-control" type="text" name="pattern_coupons" id="pattern_coupons" size="20" maxlength="250" value="">
                (use ? for a single character replacement, example: PLT-?????)
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label">
                Expiry Date
            </label>
            <div class="controls">
                <input class="form-control" type="text" name="expiry_date" id="expiry_date" size="20" maxlength="250" value="">
            </div>
        </div>
        <div class="form-group row">
            <input type="submit" name="submit" class="btn btn-success width_full" value="ADD">
        </div>
    </form>
    </div>
    <?php
}
?>