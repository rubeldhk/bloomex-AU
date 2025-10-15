<?php

class calls_view {

    public function setDefault($obj, $default_class) {
        ?>
        <script type="text/javascript">
            var needAttempt = true;
        </script>

        <div class="modal fade" id="resultModalNO" tabindex="-1" role="dialog" aria-labelledby="resultModalNO" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center d-block">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center d-block">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container panel">
            <div class="row loader" style="display: block;">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border m-5 text-info" style="width: 50px; height: 50px;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row any info">
                <div class="col order">
                    <table class="table">
                        <tbody></tbody>
                    </table>
                </div>
                <div class="col call">
                    <div class="history">
                    </div>
                    <button type="submit" class="btn btn-outline-secondary btn-sm d-none" id="btn_history">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        UPDATE HISTORY
                    </button>
                    <form role="form" id="company">
                        <div class="form-group">
                            <label for="company_name">Comment</label>
                            <textarea class="form-control" id="comment" name="comment" rows="5"></textarea>
                        </div>
                        <input type="hidden" id="id">
                        <div class="buttons first" style="margin-bottom: 1rem;">
                            <button type="submit" class="btn btn-primary" id="btn_buyer">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                BUYER
                            </button>
<!--                            <button type="submit" class="btn btn-warning" id="btn_requeue">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                RE-QUEUE
                            </button>-->
                            <input type="hidden" id="requeue_datetime" name="requeue_datetime" value="<?php echo date('Y-m-d H:i', strtotime('+1 hour')); ?>">

                            <button type="submit" class="btn btn-secondary" id="btn_voicemail">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                VOICEMAIL
                            </button>
                        </div>
                        <div class="buttons first">
                            <button type="submit" class="btn btn-success" id="btn_hot">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                MANAGER CALL BACK
                            </button>
<!--                            <button type="submit" class="btn btn-info" id="btn_resend">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                SEND EMAIL
                            </button>-->
                            <button type="submit" class="btn btn-danger" id="btn_not">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                NOT INTERESTED
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    public function setLoginForm($obj, $default_class) {
        ?>
        <script type="text/javascript">
            var needAttempt = false;
        </script>
        <div class="container">
            <div class="row any justify-content-md-center">
                <form class="form-inline login-form" role="form" method="POST" action="<?php echo MY_PATH; ?>/?task=login">
                    <div class="form-group mx-sm-3 mt-2 mb-2">
                        <label class="sr-only" for="extension">Extension</label>
                        <input type="text" class="form-control" id="extension" name="extension" placeholder="Extension">
                    </div>
                    <button type="submit" class="btn btn-secondary mt-2 mb-2" style="margin-right: 10px;" name="project" value="1">GO</button>
                </form>
            </div>
        </div>
        <?php
    }
}
