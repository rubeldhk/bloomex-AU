<?php
require_once('config.php');
require_once('bloomexorder.php');
$cfg = new DatabaseOptions();
$link = mysql_connect($cfg->host, $cfg->user, $cfg->pw);

function filter_($data) {
    $data = trim(htmlentities(strip_tags($data)));
    if (get_magic_quotes_gpc())
        $data = stripslashes($data);
    $data = mysql_real_escape_string($data);
    return $data;
}

$order = new BloomexOrder(filter_($_REQUEST['id']));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Canpar</title>
        <script src="/resourses/jquery-1.9.1.js"></script>
        <script src="/resourses/jquery.form.js"></script> 
        <script src="/resourses/jquery-ui.js"></script>
        <script src="/resourses/jquery.printElement.js"></script>
        <link rel="stylesheet" href="/resourses/style.css" />
        <link rel="stylesheet" href="/resourses/jquery-ui.css" />
        <script> 
            // wait for the DOM to be loaded 
            // prepare the form when the DOM is ready 
            $(document).ready(function() {
                $( "#datepicker" ).datepicker({ minDate: 0,dateFormat: "mm-dd-yy"});
                var options = { 
                    target:        '#Fedex',   
                    beforeSubmit:  showRequest,
                    success:       showResponse
                }; 
            }); 
            // pre-submit callback 
            function showRequest(formData, jqForm, options) { 
                $('#Fedex').html('<div id="loader"></div>');
                return true; 
            } 
 
            // post-submit callback 
            function showResponse(responseText, statusText, xhr, $form)  { 
            } 
            
        </script> 

    </head>
    <body><br/><br/><br/><br/>
        <div id="Fedex">
            <form id="myForm" class="form" action="CreateShipment.php" method="POST">
                <fieldset>
                    <legend>Service</legend>
                    <div><label for="service">Type:</label>
                        <select name="service" id="service">
                            <option selected="selected" value ="1">Ground</option>
                        </select>
                          <div><label for="nsr">Signature:</label>
                            <select name="nsr" id="service">
                                <option selected="selected" value ="0">Required(Standart)</option>
                                <option selected="" value ="2">No Signature Required</option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Date</legend>
                    <div><label for="date">Pickup Date:</label><input name="date" id="datepicker" value="<?php echo date("m-d-Y") ?>"/></div>
                    <div><label for="ddate">Delivery date:</label><span id="ddate" ><?php echo $order->_deliverydate ?></span></div>
                </fieldset>
                <div id="parentId">
                    <fieldset>
                        <legend>Package</legend>   
                        <div><label for="weight">Piece Weight:</label><input type="number" value="3" name="weight" id="weight"/> lb.</div>
                        <div><label for="height">Total Pieces:</label><input type="number" value="1" name="count" id="height"/> pcs.</div>
                      
                    </fieldset>
                </div>
                </br>
                <input class="submit" type="submit" value="Send to Canpar"/>
                <?php foreach ($_REQUEST as $k => $v) { ?>
                    <input type="hidden" value="<?php echo $v ?>" name="<?php echo $k ?>"/>
                <?php } ?>
            </form>

        </div>
        </div>
    </body>
</html>
<?php mysql_close();?>