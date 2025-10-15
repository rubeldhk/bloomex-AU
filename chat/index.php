<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Bloomex.com.au chat</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            .chat_wrapper {
                width: 960px;
            }
            @media(max-width: 480px) {
                .chat_wrapper {
                    width: initial;
                }
            }
        </style>
    </head>
    <body style="background:#B0C4DE;margin:0px;">
        <div class="chat_wrapper" style="margin:0px auto;background:white;padding:10px;height:99%">
            <a href="/" title="to Bloomex.com.au (opens in a new window)" target="_blank"><img alt="chat" src="https://bloomex.com.au/templates/bloomex7/images/bloomexlogo.png" border="0" style="margin:5px auto;display:block"></a>
            <div id="lhc_status_container_page" ></div>
        </div>
    </body>
</html>

<?php
$pos_dev = strpos($_SERVER['SERVER_NAME'], 'dev1');
$pos_stage = strpos($_SERVER['SERVER_NAME'], 'stage1');
if ($pos_dev !== false || $pos_stage !== false) {
    $chatlink = 'https://dev1.amazon.chat.bloomex.ca';
} else {
    $chatlink = 'https://chat.bloomex.ca';
}
?>


<!-- Place this tag where you want the Live Helper Plugin to render. -->
<div id="lhc_status_container_page" ></div>
<!-- Place this tag after the Live Helper Plugin tag. -->




<?php
include ('../configuration.php');

if($mosConfig_enable_new_chat) {

    ?>

    <script>
        (function(d) {
            var cm = d.createElement('scr' + 'ipt'); cm.type = 'text/javascript'; cm.async = true;
            cm.src = 'https://kcsafexvff.chat.digital.ringcentral.com/chat/f6b2a033e9ef4884a58cf253/loader.js';
            var s = d.getElementsByTagName('scr' + 'ipt')[0]; s.parentNode.insertBefore(cm, s);
        }(document));

    </script>
    <?php
} else {

    ?>

    <script type="text/javascript">
        var LHCChatOptionsPage = {};
        LHCChatOptionsPage.opt = {};
        (function () {
            var po = document.createElement('script');
            po.type = 'text/javascript';
            po.async = true;
            po.src = '<?php echo $chatlink;?>/index.php/chat/getstatusembed/(theme)/1/(department)/2';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(po, s);
        })();
    </script>
    <?php
}