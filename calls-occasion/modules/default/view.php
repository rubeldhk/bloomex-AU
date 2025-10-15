<?php

class default_view {

    public function setHeader($obj, $page_title, $page_description) {
        global $mosConfig_voicemail_number;
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
                <div style="display: none;">
                    <img src="<?php echo MY_PATH; ?>/static/images/logout.png" alt="logout" />
                </div>
                <div class="container">
                    <div class="row header">
                        <div class="col-md-8 col-xl-10 title">
                            Occasion calls 2025
                        </div>
                        <?php
                        if (isset($_SESSION['extension'])) {
                            ?>
                            <div class="col-md-4 col-xl-2 info">
                                <div class="extension">
                                    <?php echo htmlspecialchars($_SESSION['extension']); ?>
                                </div>
                                <a href="#pause" onclick="setPause();" class="pause" title="Take a pause">
                                    <a href="#play" onclick="setPlay();" class="play" title="Go to work">
                                </a>
                                <a href="<?php echo MY_PATH; ?>/?task=logout" class="logout" title="Logout"></a>
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
                        bloomex.ca
                    </div>
                </div>
            </div>

            <script src="<?php echo MY_PATH; ?>/static/js/jquery.min.js"></script>
            <script src="<?php echo MY_PATH; ?>/static/js/gijgo.min.js"></script>
            <script src="<?php echo MY_PATH; ?>/static/js/bootstrap.bundle.min.js"></script>
            <script src="<?php echo MY_PATH; ?>/static/js/main.js?v=1.0"></script>
            </body>
        </html>
    <?php
    }
    
}
