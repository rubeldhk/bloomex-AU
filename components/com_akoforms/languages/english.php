<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Arthur Konze
* Homepage   : www.konze.de
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Yes';
  var $AKF_NO                  = 'No';
  var $AKF_PUBLISHED           = 'Published';
  var $AKF_PUBLISHING          = 'Publishing';
  var $AKF_STARTPUBLISHING     = 'Start Publishing:';
  var $AKF_FINISHPUBLISHING    = 'Finish Publishing:';
  var $AKF_PUBPENDING          = 'Published, but is Pending';
  var $AKF_PUBCURRENT          = 'Published and is Current';
  var $AKF_PUBEXPIRED          = 'Published, but has Expired';
  var $AKF_UNPUBLISHED         = 'Not Published';
  var $AKF_REORDER             = 'Reorder';
  var $AKF_ORDERING            = 'Ordering:';
  var $AKF_TITLE               = 'Title:';
  var $AKF_DESCRIPTION         = 'Description:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Edit Language';
  var $AKF_PATH                = 'Path:';
  var $AKF_FILEWRITEABLE       = 'Please note: The file must be writable to save your changes.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Form Manager';
  var $AKF_FORMTITLE           = 'Form Title';
  var $AKF_SENDMAIL            = 'Send Email';
  var $AKF_STOREDB             = 'Store DB';
  var $AKF_FINISHING           = 'Finishing';
  var $AKF_FORMPAGE            = 'Form Page';
  var $AKF_REDIRECTION         = 'Redirection';
  var $AKF_SHOWRESULT          = 'Show Result';
  var $AKF_NUMBEROFFIELDS      = 'No. of Fields';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Add Form';
  var $AKF_EDITFORM            = 'Edit Form';
  var $AKF_HEADER              = 'Header';
  var $AKF_HANDLING            = 'Handling';
  var $AKF_SENDBYEMAIL         = 'Send by Email:';
  var $AKF_EMAILS              = 'Emails:';
  var $AKF_SAVETODATABASE      = 'Save to Database:';
  var $AKF_ENDPAGETITLE        = 'End Page Title:';
  var $AKF_ENDPAGEDESCRIPTION  = 'End Page Description:';
  var $AKF_FORMTARGET          = 'Form Target:';
  var $AKF_TARGETURL           = 'Redirection URL:';
  var $AKF_SHOWENDPAGE         = 'Show End page';
  var $AKF_REDIRECTTOURL       = 'Redirect to URL';
  var $AKF_NEWFORMSLAST        = 'New forms go to the last place.';
  var $AKF_SHOWFORMRESULT      = 'Show form result:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Field Manager';
  var $AKF_FIELDTITLE          = 'Field Title';
  var $AKF_FIELDTYPE           = 'Field Type';
  var $AKF_FIELDREQUIRED       = 'Field Required';
  var $AKF_SELECTFORM          = 'Select Form';
  var $AKF_ALLFORMS            = '- All Forms';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Add Field';
  var $AKF_EDITFIELD           = 'Edit Field';
  var $AKF_GENERAL             = 'General';
  var $AKF_FORM                = 'Form:';
  var $AKF_TYPE                = 'Type:';
  var $AKF_VALUE               = 'Value:';
  var $AKF_STYLE               = 'Style:';
  var $AKF_REQUIRED            = 'Required:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Edit Settings';
  var $AKF_MAILSUBJECT         = 'Email Subject:';
  var $AKF_SENDERNAME          = 'Sender Name:';
  var $AKF_SENDEREMAIL         = 'Sender Email:';
  var $AKF_SETTINGSSAVED       = 'Settings have been saved.';
  var $AKF_SETTINGSNOTSAVED    = 'Settings could not be saved.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Stored Forms';
  var $AKF_NUMBEROFENTRIES     = 'No. of Entries';
  var $AKF_STOREDDATA          = 'Stored Data';
  var $AKF_STOREDIP            = 'Sender IP';
  var $AKF_STOREDDATE          = 'Send Date';

  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Please fill all required fields:';
  var $AKF_REQUIREDFIELD       = 'Required field';
  var $AKF_BUTTONSEND          = 'Send';
  var $AKF_BUTTONCLEAR         = 'Clear';
  var $AKF_FORMEXPIRED         = 'This form has been expired!';
  var $AKF_FORMPENDING         = 'This form is currently pending!';
  var $AKF_MAILERHELLO         = 'Hello';
  var $AKF_MAILERHEADER        = 'A user of your website has used a form to submit the following data to you:';
  var $AKF_MAILERFOOTER        = 'Sincerely';
  var $AKF_MAILERERROR         = 'There has been a mail error, while sending to:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = 'Assign the field to a certain form using the dropdown menu.';
  var $AKF_HELPTITLE           = 'Enter a short title for your form/field in this field.';
  var $AKF_HELPDESCRIPTION     = 'You can use this field insert a html capable description for your field/form.';
  var $AKF_HELPTYPE            = 'Choose from nearly all standard form fields and a variety of predefined fields. If you need customized dropdown, contact Arthur Konze.';
  var $AKF_HELPVALUE           = 'The value field can be used to assign a predefined value to a field. To create DROPDOWN menus just enter every option for the dropdown menu into a separate line. The same applies to a RADIOBUTTON and the SELECTBOXES. In a CHECKBOX it will be shown as a descriptive text behind the box.';
  var $AKF_HELPSTYLE           = 'Use the style option to add CSS definitions to the field. For example to make a field 200 pixels wide enter: width:200px;';
  var $AKF_HELPREQUIRED        = 'Choose wether a field must be filled by the user or not.';
  var $AKF_HELPORDERING        = 'Use the ordering to choose the position.';
  var $AKF_HELPSTARTFINISH     = 'Choose a start and a finishing date for publishing using this two options.';
  var $AKF_HELPSENDMAIL        = 'Choose if the form result should be emailed or not.';
  var $AKF_HELPEMAILS          = 'Enter the email addresses here. You can enter multiple be separating them with a comma (,).';
  var $AKF_HELPSAVEDB          = 'Choose if the form result should be saved to the database.';
  var $AKF_HELPTARGET          = 'Choose if the above page should be displayed or if the user should be redirected to beneath URL.';
  var $AKF_HELPTARGETURL       = 'Enter a redirection URL here. It could be any URL, even another form.';
  var $AKF_HELPSUBJECT         = 'Enter a subject for all outgoing emails into this field.';
  var $AKF_HELPSENDER          = 'The name entered here will be used as sender for outgoing emails.';
  var $AKF_HELPEMAIL           = 'A valid email address of the sender for your outgoing emails.';
  var $AKF_HELPRESULT          = 'Decide if you want to show the result of the form to the user.';

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

$AKFLANG =new  akfLanguage();

?>