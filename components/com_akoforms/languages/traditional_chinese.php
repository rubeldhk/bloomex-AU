<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Pony King
* Homepage   : www.mambo.cn
**/

defined( '_VALID_MOS' ) or die( '您無權直接進入.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = '是';
  var $AKF_NO                  = '否';
  var $AKF_PUBLISHED           = '已發表';
  var $AKF_PUBLISHING          = '發表中';
  var $AKF_STARTPUBLISHING     = '開始發表:';
  var $AKF_FINISHPUBLISHING    = '結束發表:';
  var $AKF_PUBPENDING          = '已發表，還未審核';
  var $AKF_PUBCURRENT          = '已發表，使用中';
  var $AKF_PUBEXPIRED          = '已發表, 但已過期';
  var $AKF_UNPUBLISHED         = '未發表';
  var $AKF_REORDER             = '重新排序';
  var $AKF_ORDERING            = '排序:';
  var $AKF_TITLE               = '標題:';
  var $AKF_DESCRIPTION         = '描述:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = '編輯語言文件';
  var $AKF_PATH                = '路徑:';
  var $AKF_FILEWRITEABLE       = '請注意: 此文件必須可寫以儲存更改.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = '表單管理';
  var $AKF_FORMTITLE           = '表單標題';
  var $AKF_SENDMAIL            = '發送郵件';
  var $AKF_STOREDB             = '儲存於資料庫';
  var $AKF_FINISHING           = '結束';
  var $AKF_FORMPAGE            = '表單頁';
  var $AKF_REDIRECTION         = '重定位';
  var $AKF_SHOWRESULT          = '顯示結果';
  var $AKF_NUMBEROFFIELDS      = '表單項數量';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = '增加表單';
  var $AKF_EDITFORM            = '編輯表單';
  var $AKF_HEADER              = '頭部';
  var $AKF_HANDLING            = '處理中';
  var $AKF_SENDBYEMAIL         = '用Email發送:';
  var $AKF_EMAILS              = 'Email地址:';
  var $AKF_SAVETODATABASE      = '儲存到資料庫:';
  var $AKF_ENDPAGETITLE        = '結束頁標題:';
  var $AKF_ENDPAGEDESCRIPTION  = '結束頁描述:';
  var $AKF_FORMTARGET          = '表單目標:';
  var $AKF_TARGETURL           = '重定位鏈接地址:';
  var $AKF_SHOWENDPAGE         = '顯示結束頁';
  var $AKF_REDIRECTTOURL       = '重定位到頁面';
  var $AKF_NEWFORMSLAST        = '新表單列在最後.';
  var $AKF_SHOWFORMRESULT      = '顯示表單結果:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = '表單項管理';
  var $AKF_FIELDTITLE          = '項標題';
  var $AKF_FIELDTYPE           = '項類型';
  var $AKF_FIELDREQUIRED       = '必填選項';
  var $AKF_SELECTFORM          = '選擇表單';
  var $AKF_ALLFORMS            = '- 所有表單';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = '增加表單項';
  var $AKF_EDITFIELD           = '編輯表單項';
  var $AKF_GENERAL             = '基本';
  var $AKF_FORM                = '表單:';
  var $AKF_TYPE                = '類型:';
  var $AKF_VALUE               = '值:';
  var $AKF_STYLE               = '類型:';
  var $AKF_REQUIRED            = '必填:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = '編輯設定';
  var $AKF_MAILSUBJECT         = 'Email主題:';
  var $AKF_SENDERNAME          = '發送人姓名:';
  var $AKF_SENDEREMAIL         = '發送人Email:';
  var $AKF_SETTINGSSAVED       = '設置已儲存.';
  var $AKF_SETTINGSNOTSAVED    = '設置無法儲存.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = '表單結果儲存';
  var $AKF_NUMBEROFENTRIES     = '接受到的反饋數量';
  var $AKF_STOREDDATA          = '儲存的數據';
  var $AKF_STOREDIP            = '發送人IP';
  var $AKF_STOREDDATE          = '發送日期';

  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = '請填寫所有必填項:';
  var $AKF_REQUIREDFIELD       = '必填項';
  var $AKF_BUTTONSEND          = '發送';
  var $AKF_BUTTONCLEAR         = '清除';
  var $AKF_FORMEXPIRED         = '此表單已過期!';
  var $AKF_FORMPENDING         = '此表單還未審核!';
  var $AKF_MAILERHELLO         = '您好';
  var $AKF_MAILERHEADER        = '您的網站的用戶已使用表單向您發送如下資料:';
  var $AKF_MAILERFOOTER        = 'Sincerely';
  var $AKF_MAILERERROR         = '發送郵件錯送，發送信息為:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = '將特定表單中的表單項定義為一個列表項.';
  var $AKF_HELPTITLE           = '輸入此表單/表單項的簡短標.';
  var $AKF_HELPDESCRIPTION     = '您可以使用此表單項插入一個html格式的描述.';
  var $AKF_HELPTYPE            = '選擇標準的表單項類型或是一些預定義的類型. 如果您需要定制此下拉列表，請聯繫contact Arthur Konze.';
  var $AKF_HELPVALUE           = '此處可以為表單項分配一個預定義的值. 要建立一個下拉列表目錄，請每行輸入一個選項. 對於單選框或復選框也是一樣的. 在復選框，顯示在復選框邊上的描述文字.';
  var $AKF_HELPSTYLE           = '在此可以為表單項定義樣式表. 例如，要使表單項的寬度為200px即輸入: width:200px;';
  var $AKF_HELPREQUIRED        = '選擇此表單項是否必填.';
  var $AKF_HELPORDERING        = '使用排序來選擇位置.';
  var $AKF_HELPSTARTFINISH     = '使用此兩個選項來定義表單的發表開始日期及結束日期.';
  var $AKF_HELPSENDMAIL        = '選擇表單結果是否發送Email.';
  var $AKF_HELPEMAILS          = '輸入Email地址. 您可以輸入多個地址，以逗號(,)分隔.';
  var $AKF_HELPSAVEDB          = '選擇是否將表單結果儲存到資料庫中.';
  var $AKF_HELPTARGET          = '選擇上面的頁面顯示，或是轉向到下面的URL.';
  var $AKF_HELPTARGETURL       = '在此處輸入的一個跳轉的URL. 可以是任意的URL, 甚至是另一個表單.';
  var $AKF_HELPSUBJECT         = '在此處輸入一個作為所有外發郵件顯示的主題信息.';
  var $AKF_HELPSENDER          = '此處輸入的姓名將顯示為外發郵件中的發送人姓名.';
  var $AKF_HELPEMAIL           = '此處輸入的Email地址將顯示為外發郵件中的發送人Email地址.';
  var $AKF_HELPRESULT          = '此選項決定您是否顯示表單的結果頁給用戶.';

  // NEW in version 1.01
  var $AKF_MAILCHARSET         = 'Email Charset:';
  var $AKF_HELPCHARSET         = 'Choose a charset for your outgoing emails from the dropdown menu.';
  var $AKF_MAILTABLEFIELD      = 'Field';
  var $AKF_MAILTABLEDATA       = 'Data';
  var $AKF_SELECTFIELD         = 'Select Field';

  // NEW in version 1.1
  var $AKF_LAYOUTSETTINGS      = 'Layout Settings';
  var $AKF_EMAILSETTINGS       = 'Email Settings';
  var $AKF_LAYOUTSTART         = 'Layout Start:';
  var $AKF_LAYOUTROW           = 'Layout Row:';
  var $AKF_LAYOUTEND           = 'Layout End:';
  var $AKF_EMAILTITLECSS       = 'Email Title Style:';
  var $AKF_EMAILROW1CSS        = 'Email Row1 Style:';
  var $AKF_EMAILROW2CSS        = 'Email Row2 Style:';
  var $AKF_HELPLAYOUTSTART     = 'The HTML code inside this field will be displayed on top of the form before the rows with the fields start.';
  var $AKF_HELPLAYOUTROW       = 'This code will be used for every field row. You can use the following substitutes: ###AFTFIELDTITLE###, ###AFTFIELDREQ###, ###AFTFIELDDESC### and ###AFTFIELD###.';
  var $AKF_HELPLAYOUTEND       = 'After the rows this html code will display at the end of the form. You can use the following substitutes: ###AFTSENDBUTTON### and ###AFTCLEARBUTTON###.';
  var $AKF_HELPEMAILTITLECSS   = 'Enter CSS definitions for the title row of the submitted form data.';
  var $AKF_HELPEMAILROW1CSS    = 'Enter CSS definitions for the 1st, 3rd, 5th, ... data row of the submitted form data.';
  var $AKF_HELPEMAILROW2CSS    = 'Enter CSS definitions for the 2nd, 4th, 6th, ... data row of the submitted form data.';

}

$AKFLANG =& new akfLanguage();

?>