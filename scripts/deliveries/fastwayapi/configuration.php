<?php

/*
  class FastWayCfg {

  #    var $api_host = "http://api.fastway.org/latest/fastlabel/";
  var $offset = 16; // hours
  var $api_host = "http://au.api.fastway.org/latest/fastlabel/";
  var $api_key = "d49edbb72909980637afbf7d28dcb38a";
  var $user_id = "24875";

  var $host = 'db1.cbkfsmxfdx3h.us-west-2.rds.amazonaws.com';
  var $user = 'BLCOMA_fastway';
  var $pw = 'a9dlPMlQDRYUcqMkDw9b';
  var $db = 'BLCOMA_prod';

  var $status_fast_label = 'H';
  var $status_cancel_fast_label = 'J';
  }


  $mosConfig_FastLabelUrl='http://fastwayapi.bloomex.com.au';
 */

Switch ($wh) {
    case 'vic':
        $mosConfig_fw_user_id = "46454";
        $mosConfig_fw_api_key = "ce23346ddc875150c895ed17092041e6";
        break;

    case 'bb':
        $mosConfig_fw_user_id = "48859";
        $mosConfig_fw_api_key = "2f576b56aa4720e4a539d81379e14e59";
        break;
//perth - our one
    case 'p01':
        $mosConfig_fw_user_id = "60944";
        $mosConfig_fw_api_key = "7b1a83150aee8b0d8b0ea23297e59eff";
        break;
    /* DO NOT DELETE - perth parthners (flowers in wonderland)
      case 'p01':
      $mosConfig_fw_user_id = "32882";
      $mosConfig_fw_api_key = "94ef53eea91137fda4cb5f429a357554";
      break;
     */
//sydney
    case 'WH12':
        $mosConfig_fw_user_id = "24875";
        $mosConfig_fw_api_key = "d49edbb72909980637afbf7d28dcb38a";
        break;
//brisbane
    case 'WH14':
        $mosConfig_fw_user_id = "52902";
        $mosConfig_fw_api_key = "dafcf6493dd9d6b71e7da56947349f9d";
        $mosConfig_fw_special_packaging = 17;
        break;
//melbourne
    case 'WH15':
        $mosConfig_fw_user_id = "58137";
        $mosConfig_fw_api_key = "73dead577f6e7aff8f666f48012ae8e7";
        $mosConfig_fw_special_packaging = 17;
        break;
}
