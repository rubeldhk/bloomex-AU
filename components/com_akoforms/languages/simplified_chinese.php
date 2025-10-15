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

defined( '_VALID_MOS' ) or die( '����Ȩֱ�ӽ���.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = '��';
  var $AKF_NO                  = '��';
  var $AKF_PUBLISHED           = '�ѷ���';
  var $AKF_PUBLISHING          = '������';
  var $AKF_STARTPUBLISHING     = '��ʼ����:';
  var $AKF_FINISHPUBLISHING    = '��������:';
  var $AKF_PUBPENDING          = '�ѷ�������δ���';
  var $AKF_PUBCURRENT          = '�ѷ�����ʹ����';
  var $AKF_PUBEXPIRED          = '�ѷ���, ���ѹ���';
  var $AKF_UNPUBLISHED         = 'δ����';
  var $AKF_REORDER             = '��������';
  var $AKF_ORDERING            = '����:';
  var $AKF_TITLE               = '����:';
  var $AKF_DESCRIPTION         = '����:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = '�༭�����ļ�';
  var $AKF_PATH                = '·��:';
  var $AKF_FILEWRITEABLE       = '��ע��: ���ļ������д�Ա������.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = '������';
  var $AKF_FORMTITLE           = '������';
  var $AKF_SENDMAIL            = '�����ʼ�';
  var $AKF_STOREDB             = '���������ݿ�';
  var $AKF_FINISHING           = '����';
  var $AKF_FORMPAGE            = '��ҳ';
  var $AKF_REDIRECTION         = '�ض�λ';
  var $AKF_SHOWRESULT          = '��ʾ���';
  var $AKF_NUMBEROFFIELDS      = '��������';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = '���ӱ�';
  var $AKF_EDITFORM            = '�༭��';
  var $AKF_HEADER              = 'ͷ��';
  var $AKF_HANDLING            = '������';
  var $AKF_SENDBYEMAIL         = '��Email����:';
  var $AKF_EMAILS              = 'Email��ַ:';
  var $AKF_SAVETODATABASE      = '���浽���ݿ�:';
  var $AKF_ENDPAGETITLE        = '����ҳ����:';
  var $AKF_ENDPAGEDESCRIPTION  = '����ҳ����:';
  var $AKF_FORMTARGET          = '��Ŀ��:';
  var $AKF_TARGETURL           = '�ض�λ���ӵ�ַ:';
  var $AKF_SHOWENDPAGE         = '��ʾ����ҳ';
  var $AKF_REDIRECTTOURL       = '�ض�λ��ҳ��';
  var $AKF_NEWFORMSLAST        = '�±��������.';
  var $AKF_SHOWFORMRESULT      = '��ʾ�����:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = '�������';
  var $AKF_FIELDTITLE          = '�����';
  var $AKF_FIELDTYPE           = '������';
  var $AKF_FIELDREQUIRED       = '����ѡ��';
  var $AKF_SELECTFORM          = 'ѡ���';
  var $AKF_ALLFORMS            = '- ���б�';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = '���ӱ���';
  var $AKF_EDITFIELD           = '�༭����';
  var $AKF_GENERAL             = '����';
  var $AKF_FORM                = '��:';
  var $AKF_TYPE                = '����:';
  var $AKF_VALUE               = 'ֵ:';
  var $AKF_STYLE               = '����:';
  var $AKF_REQUIRED            = '����:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = '�༭�趨';
  var $AKF_MAILSUBJECT         = 'Email����:';
  var $AKF_SENDERNAME          = '����������:';
  var $AKF_SENDEREMAIL         = '������Email:';
  var $AKF_SETTINGSSAVED       = '�����ѱ���.';
  var $AKF_SETTINGSNOTSAVED    = '�����޷�����.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = '���������';
  var $AKF_NUMBEROFENTRIES     = '���ܵ��ķ�������';
  var $AKF_STOREDDATA          = '���������';
  var $AKF_STOREDIP            = '������IP';
  var $AKF_STOREDDATE          = '��������';

  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = '����д���б�����:';
  var $AKF_REQUIREDFIELD       = '������';
  var $AKF_BUTTONSEND          = '����';
  var $AKF_BUTTONCLEAR         = '���';
  var $AKF_FORMEXPIRED         = '�˱��ѹ���!';
  var $AKF_FORMPENDING         = '�˱���δ���!';
  var $AKF_MAILERHELLO         = '����';
  var $AKF_MAILERHEADER        = '������վ���û���ʹ�ñ�����������������:';
  var $AKF_MAILERFOOTER        = 'Sincerely';
  var $AKF_MAILERERROR         = '�����ʼ����ͣ�������ϢΪ:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = '���ض����еı����Ϊһ���б���.';
  var $AKF_HELPTITLE           = '����˱�/����ļ�̱�.';
  var $AKF_HELPDESCRIPTION     = '������ʹ�ô˱������һ��html��ʽ������.';
  var $AKF_HELPTYPE            = 'ѡ���׼�ı������ͻ���һЩԤ���������. �������Ҫ���ƴ������б�����ϵcontact Arthur Konze.';
  var $AKF_HELPVALUE           = '�˴�����Ϊ�������һ��Ԥ�����ֵ. Ҫ����һ�������б�˵�����ÿ������һ��ѡ��. ���ڵ�ѡ���ѡ��Ҳ��һ����. �ڸ�ѡ����ʾ�ڸ�ѡ����ϵ���������.';
  var $AKF_HELPSTYLE           = '�ڴ˿���Ϊ�������ʽ��. ���磬Ҫʹ����Ŀ��Ϊ200px������: width:200px;';
  var $AKF_HELPREQUIRED        = 'ѡ��˱����Ƿ����.';
  var $AKF_HELPORDERING        = 'ʹ��������ѡ��λ��.';
  var $AKF_HELPSTARTFINISH     = 'ʹ�ô�����ѡ����������ķ�����ʼ���ڼ���������.';
  var $AKF_HELPSENDMAIL        = 'ѡ�������Ƿ���Email.';
  var $AKF_HELPEMAILS          = '����Email��ַ. ��������������ַ���Զ���(,)�ָ�.';
  var $AKF_HELPSAVEDB          = 'ѡ���Ƿ񽫱�������浽���ݿ���.';
  var $AKF_HELPTARGET          = 'ѡ�������ҳ����ʾ������ת�������URL.';
  var $AKF_HELPTARGETURL       = '�ڴ˴������һ����ת��URL. �����������URL, ��������һ����.';
  var $AKF_HELPSUBJECT         = '�ڴ˴�����һ����Ϊ�����ⷢ�ʼ���ʾ��������Ϣ.';
  var $AKF_HELPSENDER          = '�˴��������������ʾΪ�ⷢ�ʼ��еķ���������.';
  var $AKF_HELPEMAIL           = '�˴������Email��ַ����ʾΪ�ⷢ�ʼ��еķ�����Email��ַ.';
  var $AKF_HELPRESULT          = '��ѡ��������Ƿ���ʾ���Ľ��ҳ���û�.';

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