<?php

class calls_view {

    public function setDefault($obj, $default_class) {
        ?>
        <script type="text/javascript">
            var needAttempt = true;
        </script>
        <div class="container call">
            <div class="row count">
<!--                <div class="">Available now: <span id="now_count"></span></div>-->
                <div class="">All: <span id="all_count"></span></div>
            </div>
            <div class="row any loader">
                <div class="col-xs-12 col-sm-offset-1 col-sm-10 col-md-offset-3 col-md-6 col-lg-offset-4 col-lg-4">
                </div>
            </div>
            <div class="row any info">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 order">
                    <table class="table">
                        <tbody></tbody>
                    </table>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 company">
                    <form role="form" id="company">
                        <div class="form-group">
                            <label for="company_name">Company name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="company_domain">Company Domain</label>
                            <input type="text" class="form-control" id="company_domain" name="company_domain" placeholder="">
                        </div>
                        <input type="hidden" id="id">
                        <button type="submit" class="btn btn-success" id="btn_update">CORP20</button>
                        <button type="submit" class="btn btn-warning" id="btn_no" style="">CORP20 & StakeHolder</button>
                        <button type="submit" class="btn btn-warning" id="btn_requeue" style="float: right;">RE-QUEUE</button>
                        <button type="submit" class="btn btn-danger" id="btn_no_corp" style="float: right; margin-right: 10px;">NOT CORP</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    public function setLoginForm($obj, $default_class) {
        ?>
        <div class="container">
            <div class="row any">
                <div class="col-xs-12 col-sm-offset-1 col-sm-10 col-md-offset-3 col-md-6 col-lg-offset-4 col-lg-4">
                    <form class="form-inline login-form" role="form" method="POST" action="<?php echo MY_PATH; ?>/?task=login">
                        <div class="form-group">
                            <label class="sr-only" for="extension">Extension</label>
                            <input type="text" class="form-control" id="extension" name="extension" placeholder="Extension">
                        </div>
                        <button type="submit" class="btn btn-default">Login</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

}

?>