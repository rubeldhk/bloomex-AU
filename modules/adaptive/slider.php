<?php
global $database, $iso_client_lang, $mosConfig_live_site, $cur_template,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url;

$query = "SELECT `options` 
FROM `tbl_options` 
WHERE `type`='slider'";

$database->setQuery($query);
$slider_result = $database->loadResult();

$query = "SELECT `s`.* 
FROM `jos_vm_slider` AS `s`
WHERE `s`.`public`=1 and slider_type=1 and  
(now() >= date_start or date_start is null or date_start='0000-00-00') and 
(now()<= date_end or date_end is null or date_end='0000-00-00') ";

if ($slider_result == 'random') {
    $query .= " ORDER BY RAND()";
} else {
    $query .= " ORDER BY `s`.`queue` ASC";
}
$database->setQuery($query);
$slides = $database->loadObjectList();
$slides = array_map(function($slide) use ($showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url) {
     $slide->s3_image = $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? preg_replace('/\\.[^.]*$/', '', $slide->s3_image).'.jpeg' : preg_replace('/\\.[^.]*$/', '', $slide->s3_image).'.webp');
     return $slide;
 }, $slides);
?>
<div id="block-for-slider">
    <div id="viewport">
        <ul id="slidewrapper">
            <?php
            if ($slides) {
                ?>
                <li class="slide">
                    <div class="slider_image_loader"></div>
                    <a href="<?php echo $slides[0]->src; ?>">
                        <img style="display: none;" fetchpriority="high" loading="eager" class="slider_image_real"  src="<?php echo $slides[0]->s3_image; ?>"
                             alt="<?php echo $slides[0]->alt??$slides[0]->image; ?>" class="slide-img"/>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
        if (count($slides) > 1) {
        ?>
        <div id="prev-next-btns">
            <div id="prev-btn">
                <div class="slider_arrow left"></div>
            </div>
            <div id="next-btn">
                <div class="slider_arrow right"></div>
            </div>
        </div>
        <?php
        }
        ?>

    </div>
</div>
<script>
    var slideCount = <?php echo count($slides)?>;
    var slides = <?php echo json_encode($slides);?>;
</script>
<script src="/templates/<?php echo $cur_template; ?>/js/slider.js?ref=1"></script>

<script>
    document.addEventListener('touchstart', handleTouchStart, false);
    document.addEventListener('touchmove', handleTouchMove, false);

    var xDown = null;
    var yDown = null;

    function getTouches(evt) {
        return evt.touches ||
            evt.originalEvent.touches;
    }

    function handleTouchStart(evt) {
        const firstTouch = getTouches(evt)[0];
        xDown = firstTouch.clientX;
        yDown = firstTouch.clientY;
    };

    function handleTouchMove(evt) {
        if (!xDown || !yDown) {
            return;
        }

        var xUp = evt.touches[0].clientX;
        var yUp = evt.touches[0].clientY;

        var xDiff = xDown - xUp;
        var yDiff = yDown - yUp;

        if (Math.abs(xDiff) > Math.abs(yDiff)) {
            if (xDiff > 0) {
                $('#next-btn').click()
            } else {
                $('#prev-btn').click()
            }
        }
        xDown = null;
        yDown = null;
    };


</script>
