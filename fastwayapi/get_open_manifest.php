<!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="en-US"
    lang="en-US">
    <head>
        
    </head>
    <body>
<?php

    require_once 'configuration.php';
    
    $sender = $_REQUEST['sender'];
    
    $cfg = new FastWayCfg();
    
    $curl_options = array(
        CURLOPT_URL => $cfg->api_host . 'getopenmanifest',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'UserID=' . $cfg->user_id . '&api_key=' . $cfg->api_key,
        CURLOPT_HTTP_VERSION => 1.0,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false
    );
    
    $curl = curl_init();
    curl_setopt_array($curl, $curl_options);
    $result_curl = curl_exec($curl);
       
    if (curl_errno($curl)) {
        echo 'Error : ' . curl_error($curl);
    } else {
        $rs = json_decode($result_curl,true); // json to object
    }
?>
        <table border="1" cellspacing="0">
    <tr>
        <td colspan="2"><h3>Information about manifest <?php echo $rs['result']['0']['ManifestID'];?></h3></td>
    </tr>
    <tr>
        <td>
            ManifestID
        </td>
        <td>
            <?php echo $rs['result']['0']['ManifestID'];?>
        </td>
    </tr>
    <tr>
        <td>
            Description
        </td>
        <td>
            <?php echo $rs['result']['0']['Description'];?>
        </td>
    </tr>
    <tr>
        <td>
            MultiBusinessID
        </td>
        <td>
            <?php echo $rs['result']['0']['MultiBusinessID'];?>
        </td>
    </tr>
    <tr>
        <td>
            CreateDate
        </td>
        <td>
            <?php echo $rs['result']['0']['CreateDate'];?>
        </td>
    </tr>
    <tr>
        <td>
            PrintDate
        </td>
        <td>
            <?php echo $rs['result']['0']['PrintDate'];?>
        </td>
    </tr>
    <tr>
        <td>
            CreatedBy_UserID
        </td>
        <td>
            <?php echo $rs['result']['0']['CreatedBy_UserID'];?>
        </td>
    </tr>
    <tr>
        <td>
            AutoImport
        </td>
        <td>
            <?php echo $rs['result']['0']['AutoImport'];?>
        </td>
    </tr>
    <tr>
        <td>
            NumberOfConsignments
        </td>
        <td>
            <?php echo $rs['result']['0']['NumberOfConsignments'];?>
        </td>
    </tr>
</table>
<p><b>To confirm closing manifest <?php echo $rs['result']['0']['ManifestID'];?>, press "Submit" button.</b></p>
<form method="get" action="/close_manifest.php">
    <input type="hidden" name="manifest_id" value="<?php echo $rs['result']['0']['ManifestID'];?>"/>
    <input type="hidden" name="sender" value="<?php echo $sender;?>"/>
    <button type="submit">Submit</button>
</form>
</body>
</html>