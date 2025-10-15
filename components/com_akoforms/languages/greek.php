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

defined( '_VALID_MOS' ) or die( 'Απευθείας πρόσβαση στην τοποθεσία αυτή δεν επιτρέπεται.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Ναι';
  var $AKF_NO                  = 'Οχι';
  var $AKF_PUBLISHED           = 'Δημοσιεύθηκε';
  var $AKF_PUBLISHING          = 'Δημοσίευση';
  var $AKF_STARTPUBLISHING     = 'Εναρξη Δημοσίευσης:';
  var $AKF_FINISHPUBLISHING    = 'Τέλος Δημοσίευσης:';
  var $AKF_PUBPENDING          = 'Δημοσιευμένο, αλλά σε αναμονή';
  var $AKF_PUBCURRENT          = 'Δημοσιευμένο και ενημερωμένο';
  var $AKF_PUBEXPIRED          = 'Δημοσιεύθηκε αλλά έληξε';
  var $AKF_UNPUBLISHED         = 'Μη δημοσιευμένο';
  var $AKF_REORDER             = 'Ανακατάταξη';
  var $AKF_ORDERING            = 'Κατάταξη:';
  var $AKF_TITLE               = 'Τίτλος:';
  var $AKF_DESCRIPTION         = 'Περιγραφή:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Επεξεργασία Γλώσσας';
  var $AKF_PATH                = 'Μονοπάτι:';
  var $AKF_FILEWRITEABLE       = 'Προσοχή: Το αρχείο πρέπει να είναι εγγράψιμο για να αποθηκεύσετε τις αλλαγές σας.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Διαχειριστής Φορμών';
  var $AKF_FORMTITLE           = 'Τίτλος Φόρμας';
  var $AKF_SENDMAIL            = 'Αποστολή Email';
  var $AKF_STOREDB             = 'Αποθήκευση Βάσης';
  var $AKF_FINISHING           = 'Ολοκλήρωση';
  var $AKF_FORMPAGE            = 'Σελίδα Φόρμας';
  var $AKF_REDIRECTION         = 'Προώθηση';
  var $AKF_SHOWRESULT          = 'Εμφάνιση Αποτελέσματος';
  var $AKF_NUMBEROFFIELDS      = 'Αρ. Πεδίων';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Προσθήκη Φόρμας';
  var $AKF_EDITFORM            = 'Επεξεργασία Φόρμας';
  var $AKF_HEADER              = 'Επικεφαλίδα';
  var $AKF_HANDLING            = 'Επεξεργασία';
  var $AKF_SENDBYEMAIL         = 'Αποστολή με Email:';
  var $AKF_EMAILS              = 'Emails:';
  var $AKF_SAVETODATABASE      = 'Αποθήκευση στη Βάση:';
  var $AKF_ENDPAGETITLE        = 'Τίτλος Τελευταίας Σελίδας:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Περιγραφή Τελευταίας Σελίδας:';
  var $AKF_FORMTARGET          = 'Στόχος Φόρμας:';
  var $AKF_TARGETURL           = 'URL προώηθησης:';
  var $AKF_SHOWENDPAGE         = 'Εμφάνιση Τελευταίας Σελίδας';
  var $AKF_REDIRECTTOURL       = 'Μετάβαση στο URL';
  var $AKF_NEWFORMSLAST        = 'Νέες φόρμες πηγαίνουν στο τελευταίο μέρος.';
  var $AKF_SHOWFORMRESULT      = 'Εμφάνιση αποτελεσμάτων φόρμας:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Διαχειριστής πεδίων';
  var $AKF_FIELDTITLE          = 'Τίτλος Πεδίου';
  var $AKF_FIELDTYPE           = 'Τύπος Πεδίου';
  var $AKF_FIELDREQUIRED       = 'Απαιτούμενο Πεδίο';
  var $AKF_SELECTFORM          = 'Επιλέξτε Φόρμα';
  var $AKF_ALLFORMS            = '- Ολες οι φόρμες';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Προσθήκη Πεδίου';
  var $AKF_EDITFIELD           = 'Επεξεργασία Πεδίου';
  var $AKF_GENERAL             = 'Γενικά';
  var $AKF_FORM                = 'Φόρμα:';
  var $AKF_TYPE                = 'Τύπος:';
  var $AKF_VALUE               = 'Τιμή:';
  var $AKF_STYLE               = 'Στυλ:';
  var $AKF_REQUIRED            = 'Απαιτείται:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Επεξεργασία Επιλογών';
  var $AKF_MAILSUBJECT         = 'Θέμα Email:';
  var $AKF_SENDERNAME          = 'Ονομα Αποστολέα:';
  var $AKF_SENDEREMAIL         = 'Email Αποστολέα:';
  var $AKF_SETTINGSSAVED       = 'Οι επιλογές αποθηκεύθηκαν.';
  var $AKF_SETTINGSNOTSAVED    = 'Οι επιλογές δεν αποθηκεύθηκαν.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Αποθηκευμένες Φόρμες';
  var $AKF_NUMBEROFENTRIES     = 'Αρ. Εισαγωγών';
  var $AKF_STOREDDATA          = 'Αποθηκευμένα δεδομένα';
  var $AKF_STOREDIP            = 'IP αποστολέα';
  var $AKF_STOREDDATE          = 'Ημερομηνία Αποστολής';


  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Παρακαλούμε συμπληρώστε όλα τα απαραίτητα πεδία:';
  var $AKF_REQUIREDFIELD       = 'Απαραίτητα πεδίο';
  var $AKF_BUTTONSEND          = 'Αποστολή';
  var $AKF_BUTTONCLEAR         = 'Καθαρισμός';
  var $AKF_FORMEXPIRED         = 'Η φόρμα έχει λήξει!';
  var $AKF_FORMPENDING         = 'Η φόρμα βρίσκεται σε αναμονή!';
  var $AKF_MAILERHELLO         = 'Γεια σας';
  var $AKF_MAILERHEADER        = 'Ενας χρήστης του site σας χρησιμοποίησε μια φόρμα για να σας στείλει κάποια στοιχεία:';
  var $AKF_MAILERFOOTER        = 'Φιλικά';
  var $AKF_MAILERERROR         = 'Παρουσιάστηκε σφάλμα κατά την αποστολή του:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = 'Ορίστε το πεδίο σε κάθε φόρμα χρησιμοποιώντας το dropdown menu.';
  var $AKF_HELPTITLE           = 'Δώστε ένα τίτλο για τη φόρμα/πεδίο σας στο πεδίο αυτό.';
  var $AKF_HELPDESCRIPTION     = 'Μπορείτε να χρησιμοποιήσετε το πεδίο αυτό για να εισάγετε μια περιγραφή με δυνατότητες html για το πεδίο/φόρμα.';
  var $AKF_HELPTYPE            = 'Επιλέξτε από σχεδόν όλα τα βασικά πεδία φόρμα ή και τα προεπιλεγμένα. Εάν χρειάζεστε ειδικότερα πεδία και επιλογές επικοινωνήστε με τον Arthur Konze.';
  var $AKF_HELPVALUE           = 'Το πεδίο τιμής μπορεί να χρησιμοποιηθεί για να δίνετε μια προεπιλεγμένη τιμή σε πεδίο. Για να δημιουργήστε DROPDOWN επιλογές απλά εισάγετε κάθε επιλογή από τις dropdown επιλογές μέσα σε ξεχωριστή γραμμή. Το ίδιο ισχύει και για RADIOBUTTON και για SELECTBOXES. Σε CHECKBOX θα φαίνεται ως περιγραφικό κείμενο πίσω από το box.';
  var $AKF_HELPSTYLE           = 'Επιλέξτε την επιλογή του Στυλ για να προσθέσετε τις επιλογές CSS στο πεδίο. Π.χ. για κάνετε ένα πεδίο 200 pixels πλατύ εισάγετε: width:200px;';
  var $AKF_HELPREQUIRED        = 'Επιλέξτε εάν ένα πεδίο πρέπει να συμπληρωθεί από τον χρήστη ή όχι.';
  var $AKF_HELPORDERING        = 'Επιλέξτε την κατάταξη για να ορίσετε τη σειρά.';
  var $AKF_HELPSTARTFINISH     = 'Επιλέξτε τις ημερομηνίες έναρξης και τέλους των δημοσιεύσεων με αυτές τις δύο επιλογές.';
  var $AKF_HELPSENDMAIL        = 'Επιλέξτε εάν η φόρμα πρέπει να αποσταλεί με email ή όχι.';
  var $AKF_HELPEMAILS          = 'Δώστε το email εδώ. Μπορείτε να δώσετε πολλές διευθύνσεις μαζί βάζοντας ενδιάμεσα τους κόμα (,).';
  var $AKF_HELPSAVEDB          = 'Επιλέξτε ένα το αποτέλεσμα της φόρμας θα αποθηκεύεται στη φόρμα.';
  var $AKF_HELPTARGET          = 'Επιλέξτε εάν η παραπάνω σελίδα θα εμφανίζεται ή ο χρήστης θα προωθείται στο παρακάτω URL.';
  var $AKF_HELPTARGETURL       = 'Δώστε το URL μετάβασης του χρήστη εδώ. Μπορεί να είναι ένα οποιοδήποτε URL, ακόμη και άλλη φόρμα.';
  var $AKF_HELPSUBJECT         = 'Σε αυτό το πεδίο δώστε το θέμα για όλα τα εξερχόμενα email.';
  var $AKF_HELPSENDER          = 'Το όνομα που εισάγετε εδώ χρησιμοποιείτε ως αυτό του αποστολέα των email.';
  var $AKF_HELPEMAIL           = 'Δώστε ένα έγκυρο email αποστολέα για την εξερχόμενη αλληλογραφία.';
  var $AKF_HELPRESULT          = 'Επιλέξτε ένα θέλετε να δείχνετε τα αποτελέσματα της φόρμας στον χρήστη.';

  // NEW in version 1.01
  var $AKF_MAILCHARSET         = 'Kωδικοποίηση Email:';
  var $AKF_HELPCHARSET         = 'Διαλέξτε την κωδικοποίηση για τα εξερχόμενα email από το μενού.';
  var $AKF_MAILTABLEFIELD      = 'Πεδίο';
  var $AKF_MAILTABLEDATA       = 'Data';
  var $AKF_SELECTFIELD         = 'Επιλέξτε Πεδίο';

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