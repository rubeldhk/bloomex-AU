<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');
        
function signQuery($query, $privateKey) {
    $url = parse_url($query);
    $urlPartToSign = $url['path'].'?'.$url['query'];
    $decodedKey = base64_decode(str_replace(array('-', '_'), array('+', '/'), $privateKey));
    $signature = hash_hmac('sha1', $urlPartToSign, $decodedKey, true);
    $encodedSignature = str_replace(array('+', '/'), array('-', '_'), base64_encode($signature));
    return sprintf('%s&signature=%s', $query, $encodedSignature);
}

$query = "SELECT 
    `id`,
    `lat`,
    `lng`,
    `city`,
    `province`,
    `location_country`
FROM 
`tbl_landing_pages` 
ORDER BY `id` ASC LIMIT 1000,500";

$result = $mysqli->query($query);

$i = 1;
while ($obj = $result->fetch_object()) {

    $curl = curl_init(signQuery('https://maps.googleapis.com/maps/api/staticmap?center='.urlencode($obj->city.','.$obj->province.','.$obj->location_country).'&zoom=12&size=615x265&key=AIzaSyBTEd41u9X6a_9Mh7RIRSQD2vmGL40BcSY', 'na-q3Semn6WcGSvQU7_XuLAcaQY='));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 

    $image = curl_exec($curl);
    curl_close($curl);
    
    $handle = fopen($_SERVER['DOCUMENT_ROOT'].'/scripts/landing_images/'.$obj->id.'.png', 'w');
    fwrite($handle, $image);
    fclose($handle);
    
    $i++;
    //copy(signQuery('https://maps.googleapis.com/maps/api/staticmap?center='.$obj->lat.','.$obj->lng.'&zoom=12&size=615x265&key=AIzaSyA-5k_l-hm9ruA890GjU41NQoviorReTlA', '7gxW7cCZVF_2eyq_Yt19XrJJ7VU='), $_SERVER['DOCUMENT_ROOT'].'/scripts/landing_images/'.$obj->id.'.png');
}

echo $i;

?>