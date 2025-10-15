<?php

class default_view {
    
    public function setHeader($obj, $page_title, $page_description) {
        global $mosConfig_voicemail_number,$title;
        ?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <title><?php echo $page_title; ?></title>
                <?php
                if (!empty($page_description)) {
                    ?>
                    <meta name="description" content="<?php echo $page_description; ?>">
                    <?php
                }
                ?>
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
                <link href="https://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet">
                <link rel="shortcut icon" href="<?php echo MY_PATH; ?>/static/images/favicon.ico" type="image/x-icon">
                <link rel="stylesheet" href="<?php echo MY_PATH; ?>/static/css/bootstrap.min.css">
                <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
                <link rel="stylesheet" href="<?php echo MY_PATH; ?>/static/css/main.css">    
                <script type="text/javascript">
                    var voicemail_number = '<?php echo $mosConfig_voicemail_number; ?>';
                </script>
            </head>
            <body>
                <div onclick="playMusicFirst()" class="clickable shadow">
                    <img style="width:200px;position:absolute;margin:15px" src="<?php echo MY_PATH; ?>/images/ding.png" alt="ding" />
                </div>
                <div onclick="playMusicSecond()" class="clickable shadow">
                    <img style="width:200px;position:absolute;right:0;margin:15px" src="<?php echo MY_PATH; ?>/images/santa.png" alt="santa" />
                </div>
                <div class="moving-image">
                    <img style="width:30%; position:absolute; right:0; right: 0;bottom: 0;" src="<?php echo MY_PATH; ?>/images/car.png" alt="santa" />
                </div>
                <div style="display: none;">
                    <img src="<?php echo MY_PATH; ?>/static/images/logout.png" alt="logout" />
                </div>
                <audio id="background-music" loop>
                    <source src="<?php echo MY_PATH; ?>/static/jingle-bells-violin-main(chosic.com).mp3" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
                <audio id="background-music-let-it-snow" loop>
                    <source src="<?php echo MY_PATH; ?>/static/Frank Sinatra – Let It Snow.mp3" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
                <div class="container">
                    <div class="row header">
                        <div class="col-md-8 col-xl-8 title">
                            <span style="display: none" id="order_status"></span>
                           <?= $title ?>
                        </div>
                        <?php
                        if (isset($_SESSION['extension'])) {
                            ?>
                            <div class="col-md-8 col-xl-4 col-xs-4 info">
                                <button type="submit" class="btn btn-primary" id="btn_prev" onclick="getNextPrevCorpUser('prev');" style="margin-right: 15px"><< PREV</button>
                                <button type="submit" class="btn btn-primary" id="btn_next" onclick="getNextPrevCorpUser('next');" style="margin-right: 10px">NEXT >></button>
                                <div class="extension">
                                    <?php echo htmlspecialchars($_SESSION['extension']); ?>
                                </div>
                                <a href="#pause" onclick="setPause();" class="pause" title="Take a pause">
                                <a href="#play" onclick="setPlay();" class="play" title="Go to work">
                                </a>
                                <a href="<?php echo MY_PATH; ?>/index.php?task=logout" class="logout" title="Logout">
                                </a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
        <?php
    }
    
    public function setFooter() {
        ?>
                <div class="container">
                    <div class="row justify-content-end footer">
                        <?php
                        if (isset($_SESSION['extension'])) {
                            ?>
                            <div class="col-sm-3">
                                Calls in queue: <span id="all_count"></span>
                            </div>
                            <div class="col-sm-3">
                                Calls today: <span id="all_ext_count"></span> (Yours: <span id="ext_count"></span>)
                            </div>
                            <?php
                        }
                        ?>
                        <div class="col-sm-6 title">
                            bloomex.com.au
                        </div>
                    </div>
                </div>

		<script src="<?php echo MY_PATH; ?>/static/js/jquery.min.js"></script>
		<script src="<?php echo MY_PATH; ?>/static/js/gijgo.min.js"></script>
		<script src="<?php echo MY_PATH; ?>/static/js/bootstrap.bundle.min.js"></script>
		<script src="<?php echo MY_PATH; ?>/static/js/main.js?v=<?= time() ?>""></script>
            </body>
        </html>
    <?php
    }
}
?>