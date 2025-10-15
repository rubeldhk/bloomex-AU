<?php

class default_view {

    public function setHeader($obj, $page_title, $page_description) {
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
                <link rel="stylesheet" href="<?php echo MY_PATH; ?>/static/css/main.css">

            </head>
            <body>
                <div style="display: none;">
                    <img src="<?php echo MY_PATH; ?>/static/images/logout.png" alt="logout" />
                    <img src="<?php echo MY_PATH; ?>/static/images/attempt_loader.gif" alt="attempt_loader" />
                </div>
                <div class="container">
                    <div class="row header">
                        <div class="col-xs-8 title">
                            Corporate calls
                        </div>
                        <?php
                        if (isset($_COOKIE['extension'])) {
                            ?>
                            <div class="col-xs-4 info">
                                <div class="extension">
                                    <?php echo htmlspecialchars($_COOKIE['extension']); ?>
                                </div>
                                <a href="#pause" onclick="setPause();" class="pause" title="Take a pause">
                                    <a href="#play" onclick="setPlay();" class="play" title="Go to work">
                                    </a>
                                    <a href="<?php echo MY_PATH; ?>/?task=logout" class="logout" title="Logout">
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
                    <div class="row footer">
                        <div class="col-sm-offset-6 col-sm-6 title">
                            bloomex.com.au
                        </div>
                    </div>
                </div>

                <script src="<?php echo MY_PATH; ?>/static/js/jquery.min.js"></script>
                <script src="<?php echo MY_PATH; ?>/static/js/bootstrap.min.js"></script>
                <script src="<?php echo MY_PATH; ?>/static/js/main.js?ver=1"></script>
            </body>
        </html>
        <?php
    }

}
