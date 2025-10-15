<?php
require_once('configuration.php');
$cfg = new FastWayCfg();
$link = mysql_connect($cfg->host, $cfg->user, $cfg->pw);

function filter_($data) {
    $data = trim(htmlentities(strip_tags($data)));
    if (get_magic_quotes_gpc())
        $data = stripslashes($data);
    $data = mysql_real_escape_string($data);
    return $data;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Fast Label</title>
        <link rel="stylesheet" href="/resourses/style.css" />
        <link rel="stylesheet" href="/resourses/style_t.css" />
<script>
var count_div = 1;
var count_fs = 1;
function AddItem() {
    var firstform = document.getElementById('fs1');
    var button = document.getElementById('add');
    count_fs++;
    var newitem = '<div id="pi' + count_div + '"><label for="reference' + count_div + '">Piece Reference:</label><input class="width" type="text" value="" name="Reference[' + count_div + ']" id="reference_' + count_div + '"/></div>';
    newitem += '<div id="pi' + count_div + '"><label for="weight' + count_div + '">Piece Weight:</label><input type="number" value="3" name="Weight[' + count_div + ']" id="weight_' + count_div + '"/> lb.</div>';
    newitem += '<div id="pi' + count_div + '"><label for="count' + count_div + '">Total Pieces:</label><input type="number" value="1" name="Count[' + count_div + ']" id="count_' + count_div + '"/> pcs.</div><br>';
    count_div++;
    
    var newnode = document.createElement('fieldset');
    newnode.id = 'fs1'+count_fs;
    newnode.innerHTML=newitem;
    firstform.insertBefore(newnode,button);
}
function DelItem() {
    if (count_fs > 1) {
        var firstform = document.getElementById('fs1');
        var last = document.getElementById('fs1'+count_fs);
        firstform.removeChild(last);
        count_fs--;
        count_div = count_div-3;
    }
}
</script>
    </head>
    <body><br/><br/><br/><br/>
        <div id="Fedex">
            <form id="myForm" class="form" action="addconsigment.php" method="POST">
                <div id="parentId">
                    <fieldset id="fs1">
                        <legend>Packages</legend>
                        <fieldset id="fs11">
                            <div id="pi1"><label for="reference">Piece Reference:</label><input class="width" type="text" size="40" value="" name="Reference[0]" id="reference_0"/></div>
                            <div id="pi2"><label for="weight">Piece Weight:</label><input type="number" value="3" name="Weight[0]" id="weight_0"/> lb.</div>
                            <div id="pi3"><label for="count">Total Pieces:</label><input type="number" value="1" name="Count[0]" id="count_0"/> pcs.</div>
                        </fieldset>
                        <input type="button" value="+" onClick="AddItem();" ID="add"></input>
                        <input type="button" value="-" onClick="DelItem();" ID="del"></input>
                    </fieldset>
                </div>
                <input class="submit" type="submit" value="Send to FastWay"/>
                <?php foreach ($_REQUEST as $k => $v) { ?>
                    <input type="hidden" value="<?php echo $v ?>" name="<?php echo $k ?>"/>
                <?php } ?>
            </form>
        </div>
    </body>
</html>
