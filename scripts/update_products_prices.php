
<?php
//Need Operator to get delivery addresses for orders


ini_set("display_errors", "1");
error_reporting(E_ALL);

if (isset($_POST['submit']))
{
    include_once '../configuration.php';
    require_once "simplexlsx.class.php";
    $xlsx = new SimpleXLSX($_FILES['product_list_file']['tmp_name']);

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

    $columns = array('Product_Name','Product_SKU','Regular_Price','Discount','Sale_Price');
    $list = array();

    if ($xlsx->rows(2)) {
        foreach ($xlsx->rows(2) as $m=>$r) {
            if($m==0)
                continue;
            $a = array();
            for ($i = 0; $i < count($columns); $i++) {
                $a[$columns[$i]] = $r[$i];
            }
            $list[]=$a;
        }
    }
    $big_list = array();
    $c = array();

    foreach($list as $m) {

        if ($m['Product_SKU']) {

            $query = "SELECT product_id FROM jos_vm_product WHERE  product_sku LIKE '" . $m['Product_SKU'] . "'";
            $result = $mysqli->query($query);
            if ($result->num_rows > 0) {
                $arr = $result->fetch_assoc();
                $b=$m;
                $b['Product_Id'] = $arr['product_id'];

                $c[] = $b;
                if (count($c) == 1000) {
                    $big_list[] = $c;
                    $c = array();
                }
                if ($m == end($list)) {
                    $big_list[] = $c;
                }
            }
        }

    }
    $k= 0;
    foreach($big_list as $small_list){
        foreach($small_list as $l){
            $k++;
            $price = round($l['Regular_Price']/1.1, 2);
            $query = "UPDATE `jos_vm_product_price` SET `saving_price`='".$l['Discount']."', product_price='".$price."' WHERE `product_id` like '".$l['Product_Id']."'";
            echo $query."<br>";
            $mysqli->query($query);
        }
    }
    echo "We Updated $k Products Prices";
    $mysqli->close();
}
else
{
    ?>
    <html>
    <head>

    </head>
    <body>
    <form action="?" enctype="multipart/form-data" method="post">
        Upload Product List File<br/><br/>
        <input type="file" name="product_list_file">
        <input type="submit" name="submit" value="Upload">
    </form>
    </body>
    </html>
    <?php
}
?>
