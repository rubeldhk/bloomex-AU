<?php
$timezones=array(
        'ACT'=>'Australia/Sydney',
        'NSW'=>'Australia/Sydney',
        'NT'=>'Australia/Darwin',
        'QLD'=>'Australia/Brisbane',
        'SA'=>'Australia/Adelaide',
        'TAS'=>'Australia/Hobart',
        'VIC'=>'Australia/Melbourne',
        'WA'=>'Australia/Perth'
    );
if($timezones[$Page['province']]){
    date_default_timezone_set($timezones[$Page['province']]);
}else{
    date_default_timezone_set('Australia/Sydney');
}
//date_default_timezone_set('Europe/Moscow');
$date_1 = time();
$all_time = strtotime(date('Y-m-d').' 13:00:00') - time();
$tomorrow = strtotime(date('Y-m-d H:i:s', strtotime('+11 hour', $all_time)));
$need_cloud_2 = 1;
$week_day = date('w',$date_1);
if ($all_time < 1)
{
    $need_cloud_2 = 0;
}
?>

<script>

    function loadjscssfile(filename, filetype){
        if (filetype=="js"){ //if filename is a external JavaScript file
            var fileref=document.createElement('script')
            fileref.setAttribute("type","text/javascript")
            fileref.setAttribute("src", filename)
        }
        else if (filetype=="css"){ //if filename is an external CSS file
            var fileref=document.createElement("link")
            fileref.setAttribute("rel", "stylesheet")
            fileref.setAttribute("type", "text/css")
            fileref.setAttribute("href", filename)
        }
        if (typeof fileref!="undefined")
            document.getElementsByTagName("head")[0].appendChild(fileref)
    }

    if(screen.width>767){
        loadjscssfile("<?php echo $mosConfig_live_site; ?>/templates/bloomex7/css/jquery.countdown.css", "css");
        loadjscssfile("<?php echo $mosConfig_live_site; ?>/templates/bloomex7/js/jquery.plugin.min.js", "js");
        loadjscssfile("<?php echo $mosConfig_live_site; ?>/templates/bloomex7/js/jquery.countdown.min.js", "js");
        loadjscssfile("<?php echo $mosConfig_live_site; ?>/templates/bloomex7/js/flipclock.js", "js");
        loadjscssfile("<?php echo $mosConfig_live_site; ?>/templates/bloomex7/css/flipclock.css", "css");
    }
</script>

<div class="container pop_div">
    <div class="row ">

            <div class="pop1 popup_div"   style="display:none;"><span class="close_popup">X</span><div id="Countdown"></div><div id="Countdown1"></div><div class="left_pop_div"></div></div>
            <div class="pop2"   style="display:none;"><?php echo $Page['center_pop'];?> </div>
            <div class="pop3 popup_div"   style="display:none;"><span class="close_popup">X</span><span><div class="right_pop_div"><?php echo $Page['right_pop'];?></div> </span></div>

    </div>
</div>
<script>
    if(screen.width>767) {
        jQuery(document).ready(function($) {
            jQuery('.close_popup').click(function () {
                jQuery(this).parent('.popup_div').hide()
            })

            setTimeout(function () {
                
                <?php if ($Page['right_pop_publish'] > 0){ ?>
                jQuery('.pop3').show();
                <?php } ?>
                    
                setTimeout(function () {
                    <?php if ($all_time < 21600 AND $all_time > 0){ ?>


                    jQuery('#Countdown').countdown({
                        until: +<?php echo $all_time; ?>,
                        format: 'HMS',
                        padZeroes: true,
                        compact: true,
                        onTick: watchCountdown
                    });

                    jQuery('.pop1').show();
                    <?php } ?>

                }, 5000) 
            }, 10000)

            function countdownmanual(elementName, minutes, seconds) {
                var element, endTime, hours, mins, msLeft, time;

                function twoDigits(n) {
                    return (n <= 9 ? "0" + n : n);
                }

                function updateTimer() {
                    msLeft = endTime - (+new Date);
                    if (msLeft < 1000) {
                        jQuery('.pop_div').hide();
                    } else {
                        time = new Date(msLeft);
                        hours = time.getUTCHours();
                        if (!hours) {
                            hours = "00";
                        }
                        mins = time.getUTCMinutes();
                        element.innerHTML = (hours ? hours + ':' + twoDigits(mins) : mins) + ':' + twoDigits(time.getUTCSeconds());
                        setTimeout(updateTimer, time.getUTCMilliseconds() + 500);
                    }
                }

                element = document.getElementById(elementName);
                endTime = (+new Date) + 1000 * (60 * minutes + seconds) + 500;
                updateTimer();
            }

            function isInteger(num) {
                var numCopy = parseFloat(num);
                return !isNaN(numCopy) && numCopy == numCopy.toFixed();
            }

            function get_countdown_minute(center_pop_text) {

                var s = center_pop_text.indexOf("{countdown:");
                var f = center_pop_text.indexOf("}");
                var l = f - s;

                var html_first = center_pop_text + '<div id="countdown_pop"></div>';
                var html = html_first.replace(html_first.substr(s, l + 1), "");
                var divs = document.getElementsByClassName('pop2');
                divs[0].innerHTML = html;

                var res = center_pop_text.substr(s + 1, l - 1).split(':');


                if (res) {
                    var min = res[1];
                    if (isInteger(min)) {

                        countdownmanual("countdown_pop", min, 00);
                    }
                }
            }

            var center_pop_text = '<div class="center_pop_div"><?php echo $Page['center_pop'];?><div class="center_pop_div">';
            get_countdown_minute(center_pop_text);




            function watchCountdown(periods) {
                var left_pop = "<?php echo $Page['left_pop'];?>";
                if (periods[4] == '00' && periods[5] == '00' && periods[6] == '00') {
                    jQuery('.pop1').hide();
                    jQuery('#Countdown1').countdown({
                        until: <?php echo $tomorrow;?>,
                        format: 'HMS',
                        padZeroes: true,
                        compact: true
                    })
                    jQuery('#Countdown').hide();
                    left_pop = left_pop.replace("Same Day", "Next Day");
                    left_pop = left_pop.replace("le même jour", "le lendemain");

                }
                jQuery('.left_pop_div').html(left_pop);
            }

            var need_cloud_2 = <?php echo $need_cloud_2; ?>;
            var week_day = <?php echo $week_day; ?>;

            if (need_cloud_2 == 1) {
                setTimeout(function () {
                    jQuery('#cloud_1').hide();
                    jQuery('#cloud_2').show();
                }, 5000);
            }
            if ((week_day == 0 && need_cloud_2 == 1) || (week_day == 6 && need_cloud_2 == 0)) {
                jQuery('#cloud_1').hide();
                jQuery('#cloud_2').hide();
                jQuery('#cloud_3').show();

            }
        });
    }
</script>
<style type="text/css" scoped>
    .left_pop_div{
        font-size: 18px;
        max-height: 90px;
    }
    .right_pop_div,.center_pop_div {
        font-size: 20px;
        max-height: 130px;
        margin-top: 20px;
    }
    .left_pop_div,.right_pop_div,.center_pop_div {
        padding-left: 3px;
        overflow: hidden;
        padding-right: 9px;
    }
    .pop_div{
        /*pointer-events: none;
        height: 215px;*/
        font-weight: bold;
        bottom: 0px;
        z-index: 100;
        width: 75%;
        position: fixed;
        left: 50%;
        margin-left: -37.5%;
    }
    .pop1{
        padding-top: 10px;
        font-size: 22px;
        position: absolute;
        bottom: 0;
        width: 270px;
        left: 20px;
        background-image: url('/images/pop_landing_bg.png');
        height: 180px;
        background-size: 270px 180px;
        background-repeat: no-repeat;
        text-align: center;
        color: #000;
    }
    .countdown-amount,#countdown_pop{
        font-size: 35px !important;
    }
    .pop3{
        padding-top: 10px;
        position: absolute;
        bottom: 0;
        right:  20px;
        width: 270px;
        background-image: url('/images/pop_landing_bg.png');
        height: 180px;
        background-size: 270px 180px;
        background-repeat: no-repeat;
        text-align: center;
        color: #000;
        font-size: 23px;
    }
    .pop3 span{
        display: block;
        padding-left: 14px;
        padding-right: 14px;

    }
    span.close_popup{
        position: absolute;
        right: 25px;
        display: block;
        background: #a62421;
        color: #fff;
        border-radius: 19px;
        padding: 2px 7px;
        font-size: 12px;
        z-index: 100;
    }
    span.close_popup:hover{
        background: red;
        cursor: pointer;
    }
    @media(max-width: 767px) {
        .pop_div{
            display: none !important;
        }

    }
</style>