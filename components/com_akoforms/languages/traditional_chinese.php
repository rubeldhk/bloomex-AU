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

defined( '_VALID_MOS' ) or die( '�z�L�v�����i�J.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = '�O';
  var $AKF_NO                  = '�_';
  var $AKF_PUBLISHED           = '�w�o��';
  var $AKF_PUBLISHING          = '�o��';
  var $AKF_STARTPUBLISHING     = '�}�l�o��:';
  var $AKF_FINISHPUBLISHING    = '�����o��:';
  var $AKF_PUBPENDING          = '�w�o��A�٥��f��';
  var $AKF_PUBCURRENT          = '�w�o��A�ϥΤ�';
  var $AKF_PUBEXPIRED          = '�w�o��, ���w�L��';
  var $AKF_UNPUBLISHED         = '���o��';
  var $AKF_REORDER             = '���s�Ƨ�';
  var $AKF_ORDERING            = '�Ƨ�:';
  var $AKF_TITLE               = '���D:';
  var $AKF_DESCRIPTION         = '�y�z:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = '�s��y�����';
  var $AKF_PATH                = '���|:';
  var $AKF_FILEWRITEABLE       = '�Ъ`�N: ����󥲶��i�g�H�x�s���.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = '���޲z';
  var $AKF_FORMTITLE           = '�����D';
  var $AKF_SENDMAIL            = '�o�e�l��';
  var $AKF_STOREDB             = '�x�s���Ʈw';
  var $AKF_FINISHING           = '����';
  var $AKF_FORMPAGE            = '��歶';
  var $AKF_REDIRECTION         = '���w��';
  var $AKF_SHOWRESULT          = '��ܵ��G';
  var $AKF_NUMBEROFFIELDS      = '��涵�ƶq';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = '�W�[���';
  var $AKF_EDITFORM            = '�s����';
  var $AKF_HEADER              = '�Y��';
  var $AKF_HANDLING            = '�B�z��';
  var $AKF_SENDBYEMAIL         = '��Email�o�e:';
  var $AKF_EMAILS              = 'Email�a�}:';
  var $AKF_SAVETODATABASE      = '�x�s���Ʈw:';
  var $AKF_ENDPAGETITLE        = '���������D:';
  var $AKF_ENDPAGEDESCRIPTION  = '�������y�z:';
  var $AKF_FORMTARGET          = '���ؼ�:';
  var $AKF_TARGETURL           = '���w���챵�a�}:';
  var $AKF_SHOWENDPAGE         = '��ܵ�����';
  var $AKF_REDIRECTTOURL       = '���w��쭶��';
  var $AKF_NEWFORMSLAST        = '�s���C�b�̫�.';
  var $AKF_SHOWFORMRESULT      = '��ܪ�浲�G:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = '��涵�޲z';
  var $AKF_FIELDTITLE          = '�����D';
  var $AKF_FIELDTYPE           = '������';
  var $AKF_FIELDREQUIRED       = '����ﶵ';
  var $AKF_SELECTFORM          = '��ܪ��';
  var $AKF_ALLFORMS            = '- �Ҧ����';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = '�W�[��涵';
  var $AKF_EDITFIELD           = '�s���涵';
  var $AKF_GENERAL             = '��';
  var $AKF_FORM                = '���:';
  var $AKF_TYPE                = '����:';
  var $AKF_VALUE               = '��:';
  var $AKF_STYLE               = '����:';
  var $AKF_REQUIRED            = '����:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = '�s��]�w';
  var $AKF_MAILSUBJECT         = 'Email�D�D:';
  var $AKF_SENDERNAME          = '�o�e�H�m�W:';
  var $AKF_SENDEREMAIL         = '�o�e�HEmail:';
  var $AKF_SETTINGSSAVED       = '�]�m�w�x�s.';
  var $AKF_SETTINGSNOTSAVED    = '�]�m�L�k�x�s.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = '��浲�G�x�s';
  var $AKF_NUMBEROFENTRIES     = '�����쪺���X�ƶq';
  var $AKF_STOREDDATA          = '�x�s���ƾ�';
  var $AKF_STOREDIP            = '�o�e�HIP';
  var $AKF_STOREDDATE          = '�o�e���';

  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = '�ж�g�Ҧ�����:';
  var $AKF_REQUIREDFIELD       = '����';
  var $AKF_BUTTONSEND          = '�o�e';
  var $AKF_BUTTONCLEAR         = '�M��';
  var $AKF_FORMEXPIRED         = '�����w�L��!';
  var $AKF_FORMPENDING         = '������٥��f��!';
  var $AKF_MAILERHELLO         = '�z�n';
  var $AKF_MAILERHEADER        = '�z���������Τ�w�ϥΪ��V�z�o�e�p�U���:';
  var $AKF_MAILERFOOTER        = 'Sincerely';
  var $AKF_MAILERERROR         = '�o�e�l����e�A�o�e�H����:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = '�N�S�w��椤����涵�w�q���@�ӦC��.';
  var $AKF_HELPTITLE           = '��J�����/��涵��²�u��.';
  var $AKF_HELPDESCRIPTION     = '�z�i�H�ϥΦ���涵���J�@��html�榡���y�z.';
  var $AKF_HELPTYPE            = '��ܼзǪ���涵�����άO�@�ǹw�w�q������. �p�G�z�ݭn�w��U�ԦC��A���pôcontact Arthur Konze.';
  var $AKF_HELPVALUE           = '���B�i�H����涵���t�@�ӹw�w�q����. �n�إߤ@�ӤU�ԦC��ؿ��A�ШC���J�@�ӿﶵ. �����ةδ_��ؤ]�O�@�˪�. �b�_��ءA��ܦb�_�����W���y�z��r.';
  var $AKF_HELPSTYLE           = '�b���i�H����涵�w�q�˦���. �Ҧp�A�n�Ϫ�涵���e�׬�200px�Y��J: width:200px;';
  var $AKF_HELPREQUIRED        = '��ܦ���涵�O�_����.';
  var $AKF_HELPORDERING        = '�ϥαƧǨӿ�ܦ�m.';
  var $AKF_HELPSTARTFINISH     = '�ϥΦ���ӿﶵ�өw�q��檺�o��}�l����ε������.';
  var $AKF_HELPSENDMAIL        = '��ܪ�浲�G�O�_�o�eEmail.';
  var $AKF_HELPEMAILS          = '��JEmail�a�}. �z�i�H��J�h�Ӧa�}�A�H�r��(,)���j.';
  var $AKF_HELPSAVEDB          = '��ܬO�_�N��浲�G�x�s���Ʈw��.';
  var $AKF_HELPTARGET          = '��ܤW����������ܡA�άO��V��U����URL.';
  var $AKF_HELPTARGETURL       = '�b���B��J���@�Ӹ��઺URL. �i�H�O���N��URL, �ƦܬO�t�@�Ӫ��.';
  var $AKF_HELPSUBJECT         = '�b���B��J�@�ӧ@���Ҧ��~�o�l����ܪ��D�D�H��.';
  var $AKF_HELPSENDER          = '���B��J���m�W�N��ܬ��~�o�l�󤤪��o�e�H�m�W.';
  var $AKF_HELPEMAIL           = '���B��J��Email�a�}�N��ܬ��~�o�l�󤤪��o�e�HEmail�a�}.';
  var $AKF_HELPRESULT          = '���ﶵ�M�w�z�O�_��ܪ�檺���G�����Τ�.';

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