<?php

global $database;

$query = "SELECT *
FROM  `tbl_testimonials` 
WHERE `published` = '1' 
ORDER BY RAND()";

$database->setQuery($query);
$testimonials = $database->loadObjectList();

foreach ($testimonials as $testimonial) {
    $rnd = mt_rand(0,1);
    
    ?>
    <div class="testimonial">
        <div class="title">
            <?php echo $testimonial->client_name; ?>, <?php echo $testimonial->city_name; ?>
        </div>
        <div class="description">
            <?php echo $testimonial->msg; ?>
        </div>
    </div>
    <?php
}

