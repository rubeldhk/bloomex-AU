<?php
$checkLanding = ( isset( $_GET['option'] ) && ( $_GET['option'] == 'com_landingpages' || $_GET['option'] == 'com_landingbasketpages'  ) ) ? true : false;
?>
<div id="toplivedata">  
    <div id="toplivedataStandart">
    <?php /*<a href="javascript:void(0)" onclick="window.open(&quot;http://app.helponclick.com/help?lang=en&amp;a=4d9f177b48504f4bb25f0580b86aab83&quot;, &quot;hoc_chat_login&quot;, &quot;width=720,height=550,scrollbars=no,status=0,toolbar=no,location=no,resizable=no&quot;)">*/ ?>
    <a href="javascript:void(0)" onclick="lh_inst.lh_openchatWindow();">
        <img alt="chat" src="/images/<?php echo modulesLanguage::get('topLiveChat'); ?>" class="topLiveChatImg" />
    </a>
    <br>
    <?php
    require_once $mosConfig_absolute_path.'/Mobile_Detect.php';
    $detect = new Mobile_Detect;
    $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
            
    if ($deviceType != 'computer')
    {
        ?>
        <a style="display:inline-block" href="tel:+1300714537" target="_blank" id="topPhone">1-300-714-537</a>
        <?php
    }
    else
    {
        ?>
        <div id="topPhone">1-800-905-147</div>
        <?php
    }
    ?>
    </div>
</div>
<div id="toplivedataBottom">
</div>

<script>
    var checkLanding = <?php echo ($checkLanding) ? 1 : 0; ?>;
$j(document).ready (function(){
       if( checkLanding === 1 ){
           $j('#toplivedataBottom').html('<a href="javascript:void(0)" onclick="lh_inst.lh_openchatWindow();"><img alt="chat" src="/images/topLiveChat.jpg" class="topLiveChatImg" /></a><div id="topPhone">1-800-905-147</div>');
           $j('.banner-size').css('display', 'none');
           $j('#toplivedata').html($j('#landing-banner-div-data-funeral').html());
           $j('#landing-banner-div-data-funeral').html('');
       }
});
</script>