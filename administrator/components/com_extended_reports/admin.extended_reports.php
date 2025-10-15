<?php
// ensure this file is being included by a parent file
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') | $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_weblinks'))) {
    mosRedirect('index2.php', _NOT_AUTH);
}
// In file admin.mycomp.php
require_once($mainframe->getPath('admin_html'));

$script = mosGetParam($_REQUEST, 'script', false);


        switch ($task) {
            case 'PrepReport':
                PrepReport($option,$script);
                break;
            case 'showreport':
                showreport($option, 0,$script);
                break;
            case 'showexcell':
                showreport($option, 1,$script);
                break;
            case 'google_sheets':
                showreport($option, 2,$script);
                break;
            default:
                showlist($option);
                break;
        }


function PrepReport($option,$script){
            if($script){
                HTML_ExtendedReports::PrepReport($script, $option);
            }
}
function showlist($option) {
        $files = scandir(__DIR__.'/scripts');
        $rows = array();
        foreach($files as $file){
            if ($file == '.' || $file == '..') {
                continue;
            }
            $rows[]=basename($file, ".php");
        }
        HTML_ExtendedReports::displayScripts($rows, $option);
}


function showreport($option, $excel = 0,$script) {

    include(__DIR__.'/scripts/'.$script.'.php');

    if ($excel == 0) {
        HTML_ExtendedReports::showReport( $rows, $script, $option);
    } elseif ($excel == 1) {
        $variables='';
        $request = $_REQUEST;
        unset($request['option']);
        unset($request['task']);
        unset($request['script']);
//        foreach($request as $key=>$value){
//            $variables.=$key."\t".trim($value)."\t";
//        }
        HTML_ExtendedReports::showExcell($rows, $script,$variables);
    } elseif ($excel == 2) {
        ?>
        <style>
            .se-pre-con {
                position: fixed;
                left: 0px;
                top: 0px;
                width: 100%;
                height: 100%;
                z-index: 9999;
                background: url(/images/Preloader_8.gif) center no-repeat #fff;
            }
        </style>
        <div class="se-pre-con" id="report_loader"></div>
        <script type="text/javascript">
            window.onload = function () {
                document.getElementById('report_loader').style.display = 'none';
            }
        </script>
        <?php

        HTML_ExtendedReports::showGoogleSheets( $rows);
    }
}
?>