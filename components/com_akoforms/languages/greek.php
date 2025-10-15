<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Costas Karamoutsos
* Homepage   : www.quickoo.com
**/

defined( '_VALID_MOS' ) or die( '��������� �������� ���� ��������� ���� ��� �����������.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = '���';
  var $AKF_NO                  = '���';
  var $AKF_PUBLISHED           = '������������';
  var $AKF_PUBLISHING          = '����������';
  var $AKF_STARTPUBLISHING     = '������ �����������:';
  var $AKF_FINISHPUBLISHING    = '����� �����������:';
  var $AKF_PUBPENDING          = '������������, ���� �� �������';
  var $AKF_PUBCURRENT          = '������������ ��� �����������';
  var $AKF_PUBEXPIRED          = '������������ ���� �����';
  var $AKF_UNPUBLISHED         = '�� ������������';
  var $AKF_REORDER             = '�����������';
  var $AKF_ORDERING            = '��������:';
  var $AKF_TITLE               = '������:';
  var $AKF_DESCRIPTION         = '���������:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = '����������� �������';
  var $AKF_PATH                = '��������:';
  var $AKF_FILEWRITEABLE       = '�������: �� ������ ������ �� ����� ��������� ��� �� ������������ ��� ������� ���.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = '������������ ������';
  var $AKF_FORMTITLE           = '������ ������';
  var $AKF_SENDMAIL            = '�������� Email';
  var $AKF_STOREDB             = '���������� �����';
  var $AKF_FINISHING           = '����������';
  var $AKF_FORMPAGE            = '������ ������';
  var $AKF_REDIRECTION         = '��������';
  var $AKF_SHOWRESULT          = '�������� �������������';
  var $AKF_NUMBEROFFIELDS      = '��. ������';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = '�������� ������';
  var $AKF_EDITFORM            = '����������� ������';
  var $AKF_HEADER              = '�����������';
  var $AKF_HANDLING            = '�����������';
  var $AKF_SENDBYEMAIL         = '�������� �� Email:';
  var $AKF_EMAILS              = 'Emails:';
  var $AKF_SAVETODATABASE      = '���������� ��� ����:';
  var $AKF_ENDPAGETITLE        = '������ ���������� �������:';
  var $AKF_ENDPAGEDESCRIPTION  = '��������� ���������� �������:';
  var $AKF_FORMTARGET          = '������ ������:';
  var $AKF_TARGETURL           = 'URL ����������:';
  var $AKF_SHOWENDPAGE         = '�������� ���������� �������';
  var $AKF_REDIRECTTOURL       = '�������� ��� URL';
  var $AKF_NEWFORMSLAST        = '���� ������ ��������� ��� ��������� �����.';
  var $AKF_SHOWFORMRESULT      = '�������� ������������� ������:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = '������������ ������';
  var $AKF_FIELDTITLE          = '������ ������';
  var $AKF_FIELDTYPE           = '����� ������';
  var $AKF_FIELDREQUIRED       = '����������� �����';
  var $AKF_SELECTFORM          = '�������� �����';
  var $AKF_ALLFORMS            = '- ���� �� ������';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = '�������� ������';
  var $AKF_EDITFIELD           = '����������� ������';
  var $AKF_GENERAL             = '������';
  var $AKF_FORM                = '�����:';
  var $AKF_TYPE                = '�����:';
  var $AKF_VALUE               = '����:';
  var $AKF_STYLE               = '����:';
  var $AKF_REQUIRED            = '����������:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = '����������� ��������';
  var $AKF_MAILSUBJECT         = '���� Email:';
  var $AKF_SENDERNAME          = '����� ���������:';
  var $AKF_SENDEREMAIL         = 'Email ���������:';
  var $AKF_SETTINGSSAVED       = '�� �������� �������������.';
  var $AKF_SETTINGSNOTSAVED    = '�� �������� ��� �������������.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = '������������� ������';
  var $AKF_NUMBEROFENTRIES     = '��. ���������';
  var $AKF_STOREDDATA          = '������������ ��������';
  var $AKF_STOREDIP            = 'IP ���������';
  var $AKF_STOREDDATE          = '���������� ���������';


  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = '����������� ����������� ��� �� ���������� �����:';
  var $AKF_REQUIREDFIELD       = '���������� �����';
  var $AKF_BUTTONSEND          = '��������';
  var $AKF_BUTTONCLEAR         = '����������';
  var $AKF_FORMEXPIRED         = '� ����� ���� �����!';
  var $AKF_FORMPENDING         = '� ����� ��������� �� �������!';
  var $AKF_MAILERHELLO         = '���� ���';
  var $AKF_MAILERHEADER        = '���� ������� ��� site ��� ������������� ��� ����� ��� �� ��� ������� ������ ��������:';
  var $AKF_MAILERFOOTER        = '������';
  var $AKF_MAILERERROR         = '������������� ������ ���� ��� �������� ���:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = '������ �� ����� �� ���� ����� ��������������� �� dropdown menu.';
  var $AKF_HELPTITLE           = '����� ��� ����� ��� �� �����/����� ��� ��� ����� ����.';
  var $AKF_HELPDESCRIPTION     = '�������� �� ��������������� �� ����� ���� ��� �� �������� ��� ��������� �� ����������� html ��� �� �����/�����.';
  var $AKF_HELPTYPE            = '�������� ��� ������ ��� �� ������ ����� ����� � ��� �� �������������. ��� ���������� ���������� ����� ��� �������� ������������� �� ��� Arthur Konze.';
  var $AKF_HELPVALUE           = '�� ����� ����� ������ �� �������������� ��� �� ������ ��� ������������� ���� �� �����. ��� �� ������������ DROPDOWN �������� ���� �������� ���� ������� ��� ��� dropdown �������� ���� �� ��������� ������. �� ���� ������ ��� ��� RADIOBUTTON ��� ��� SELECTBOXES. �� CHECKBOX �� �������� �� ����������� ������� ���� ��� �� box.';
  var $AKF_HELPSTYLE           = '�������� ��� ������� ��� ���� ��� �� ���������� ��� �������� CSS ��� �����. �.�. ��� ������ ��� ����� 200 pixels ����� ��������: width:200px;';
  var $AKF_HELPREQUIRED        = '�������� ��� ��� ����� ������ �� ����������� ��� ��� ������ � ���.';
  var $AKF_HELPORDERING        = '�������� ��� �������� ��� �� ������� �� �����.';
  var $AKF_HELPSTARTFINISH     = '�������� ��� ����������� ������� ��� ������ ��� ������������ �� ����� ��� ��� ��������.';
  var $AKF_HELPSENDMAIL        = '�������� ��� � ����� ������ �� ��������� �� email � ���.';
  var $AKF_HELPEMAILS          = '����� �� email ���. �������� �� ������ ������ ����������� ���� �������� ��������� ���� ���� (,).';
  var $AKF_HELPSAVEDB          = '�������� ��� �� ���������� ��� ������ �� ������������ ��� �����.';
  var $AKF_HELPTARGET          = '�������� ��� � �������� ������ �� ����������� � � ������� �� ���������� ��� �������� URL.';
  var $AKF_HELPTARGETURL       = '����� �� URL ��������� ��� ������ ���. ������ �� ����� ��� ����������� URL, ����� ��� ���� �����.';
  var $AKF_HELPSUBJECT         = '�� ���� �� ����� ����� �� ���� ��� ��� �� ���������� email.';
  var $AKF_HELPSENDER          = '�� ����� ��� �������� ��� �������������� �� ���� ��� ��������� ��� email.';
  var $AKF_HELPEMAIL           = '����� ��� ������ email ��������� ��� ��� ���������� ������������.';
  var $AKF_HELPRESULT          = '�������� ��� ������ �� �������� �� ������������ ��� ������ ���� ������.';

  // NEW in version 1.01
  var $AKF_MAILCHARSET         = 'K����������� Email:';
  var $AKF_HELPCHARSET         = '�������� ��� ������������ ��� �� ���������� email ��� �� �����.';
  var $AKF_MAILTABLEFIELD      = '�����';
  var $AKF_MAILTABLEDATA       = 'Data';
  var $AKF_SELECTFIELD         = '�������� �����';

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