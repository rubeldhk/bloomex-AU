<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Allen Wu
* Homepage   : www.mambo.cn
**/

defined( '_VALID_MOS' ) or die( '您无权直接进入.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = '是';
  var $AKF_NO                  = '否';
  var $AKF_PUBLISHED           = '已发布';
  var $AKF_PUBLISHING          = '发布中';
  var $AKF_STARTPUBLISHING     = '开始发布:';
  var $AKF_FINISHPUBLISHING    = '结束发布:';
  var $AKF_PUBPENDING          = '已发布，还未审核';
  var $AKF_PUBCURRENT          = '已发布，使用中';
  var $AKF_PUBEXPIRED          = '已发布, 但已过期';
  var $AKF_UNPUBLISHED         = '未发布';
  var $AKF_REORDER             = '重新排序';
  var $AKF_ORDERING            = '排序:';
  var $AKF_TITLE               = '标题:';
  var $AKF_DESCRIPTION         = '描述:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = '编辑语言文件';
  var $AKF_PATH                = '路径:';
  var $AKF_FILEWRITEABLE       = '请注意: 此文件必须可写以保存更改.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = '表单管理';
  var $AKF_FORMTITLE           = '表单标题';
  var $AKF_SENDMAIL            = '发送邮件';
  var $AKF_STOREDB             = '保存于数据库';
  var $AKF_FINISHING           = '结束';
  var $AKF_FORMPAGE            = '表单页';
  var $AKF_REDIRECTION         = '重定位';
  var $AKF_SHOWRESULT          = '显示结果';
  var $AKF_NUMBEROFFIELDS      = '表单项数量';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = '增加表单';
  var $AKF_EDITFORM            = '编辑表单';
  var $AKF_HEADER              = '头部';
  var $AKF_HANDLING            = '处理中';
  var $AKF_SENDBYEMAIL         = '用Email发送:';
  var $AKF_EMAILS              = 'Email地址:';
  var $AKF_SAVETODATABASE      = '保存到数据库:';
  var $AKF_ENDPAGETITLE        = '结束页标题:';
  var $AKF_ENDPAGEDESCRIPTION  = '结束页描述:';
  var $AKF_FORMTARGET          = '表单目标:';
  var $AKF_TARGETURL           = '重定位链接地址:';
  var $AKF_SHOWENDPAGE         = '显示结束页';
  var $AKF_REDIRECTTOURL       = '重定位到页面';
  var $AKF_NEWFORMSLAST        = '新表单列在最后.';
  var $AKF_SHOWFORMRESULT      = '显示表单结果:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = '表单项管理';
  var $AKF_FIELDTITLE          = '项标题';
  var $AKF_FIELDTYPE           = '项类型';
  var $AKF_FIELDREQUIRED       = '必填选项';
  var $AKF_SELECTFORM          = '选择表单';
  var $AKF_ALLFORMS            = '- 所有表单';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = '增加表单项';
  var $AKF_EDITFIELD           = '编辑表单项';
  var $AKF_GENERAL             = '基本';
  var $AKF_FORM                = '表单:';
  var $AKF_TYPE                = '类型:';
  var $AKF_VALUE               = '值:';
  var $AKF_STYLE               = '类型:';
  var $AKF_REQUIRED            = '必填:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = '编辑设定';
  var $AKF_MAILSUBJECT         = 'Email主题:';
  var $AKF_SENDERNAME          = '发送人姓名:';
  var $AKF_SENDEREMAIL         = '发送人Email:';
  var $AKF_SETTINGSSAVED       = '设置已保存.';
  var $AKF_SETTINGSNOTSAVED    = '设置无法保存.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = '表单结果保存';
  var $AKF_NUMBEROFENTRIES     = '接受到的反馈数量';
  var $AKF_STOREDDATA          = '保存的数据';
  var $AKF_STOREDIP            = '发送人IP';
  var $AKF_STOREDDATE          = '发送日期';

  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = '请填写所有必填项:';
  var $AKF_REQUIREDFIELD       = '必填项';
  var $AKF_BUTTONSEND          = '发送';
  var $AKF_BUTTONCLEAR         = '清除';
  var $AKF_FORMEXPIRED         = '此表单已过期!';
  var $AKF_FORMPENDING         = '此表单还未审核!';
  var $AKF_MAILERHELLO         = '您好';
  var $AKF_MAILERHEADER        = '您的网站的用户已使用表单向您发送如下数据:';
  var $AKF_MAILERFOOTER        = 'Sincerely';
  var $AKF_MAILERERROR         = '发送邮件错送，发送信息为:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = '将特定表单中的表单项定义为一个列表项.';
  var $AKF_HELPTITLE           = '输入此表单/表单项的简短标.';
  var $AKF_HELPDESCRIPTION     = '您可以使用此表单项插入一个html格式的描述.';
  var $AKF_HELPTYPE            = '选择标准的表单项类型或是一些预定义的类型. 如果您需要定制此下拉列表，请联系contact Arthur Konze.';
  var $AKF_HELPVALUE           = '此处可以为表单项分配一个预定义的值. 要创建一个下拉列表菜单，请每行输入一个选项. 对于单选框或复选框也是一样的. 在复选框，显示在复选框边上的描述文字.';
  var $AKF_HELPSTYLE           = '在此可以为表单项定义样式表. 例如，要使表单项的宽度为200px即输入: width:200px;';
  var $AKF_HELPREQUIRED        = '选择此表单项是否必填.';
  var $AKF_HELPORDERING        = '使用排序来选择位置.';
  var $AKF_HELPSTARTFINISH     = '使用此两个选项来定义表单的发布开始日期及结束日期.';
  var $AKF_HELPSENDMAIL        = '选择表单结果是否发送Email.';
  var $AKF_HELPEMAILS          = '输入Email地址. 您可以输入多个地址，以逗号(,)分隔.';
  var $AKF_HELPSAVEDB          = '选择是否将表单结果保存到数据库中.';
  var $AKF_HELPTARGET          = '选择上面的页面显示，或是转向到下面的URL.';
  var $AKF_HELPTARGETURL       = '在此处输入的一个跳转的URL. 可以是任意的URL, 甚至是另一个表单.';
  var $AKF_HELPSUBJECT         = '在此处输入一个作为所有外发邮件显示的主题信息.';
  var $AKF_HELPSENDER          = '此处输入的姓名将显示为外发邮件中的发送人姓名.';
  var $AKF_HELPEMAIL           = '此处输入的Email地址将显示为外发邮件中的发送人Email地址.';
  var $AKF_HELPRESULT          = '此选项决定您是否显示表单的结果页给用户.';

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