<?php

// *************************************************************
// Greek Translation provided by: Fotis Evangelou - www.webpr.gr
// *************************************************************

define( "LM_SUBSCRIBE_SUBJECT", "Η εγγραφή σας στο newsletter του [mosConfig_live_site]" );
define( "LM_SUBSCRIBE_MESSAGE", "Καλησπέρα [NAME],

(πιθανότατα εσείς) ζητήσατε να εγγραφείτε στο Newsletter του [mosConfig_live_site].

Για να επιβεβαιώσετε την εγγραφή σας, παρακαλούμε ακολουθήστε τον παρακάτω σύνδεσμο (link) ενεργοποίησης.

[LINK]

Εάν δεν επιλέξατε εσείς να εγγραφείτε στο Newsletter (π.χ. κάποιος άλλος κατά λάθος χρησιμοποίησε την διεύθυνση του e-mail σας, αγνοήστε το παρόν μήνυμα.
_________________________

[mosConfig_live_site]" );

define( "LM_UNSUBSCRIBE_SUBJECT", "Διαγραφή από το newsletter του [mosConfig_live_site]" );
define( "LM_UNSUBSCRIBE_MESSAGE", 

"Καλησπέρα [NAME],

έχετε διαγραφεί από τη λίστα newsletter του [mosConfig_live_site].

Ευχαριστούμε που ήσασταν μαζί μας.
________________________

[mosConfig_live_site]" );

define( "LM_NEWSLETTER_FOOTER", "

___________________________________________________________

Λαμβάνετε αυτό το newsletter γιατί έχετε ζητήσει την εγγραφή σας στην σχετική λίστα του [mosConfig_live_site].

Για να διαγραφείτε από τη λίστα newsletter ακολουθήστε τον παρακάτω σύνδεσμο διαγραφής:
[UNLINK]" );

/* Module */

define( "LM_FORM_NOEMAIL", "Παρακαλούμε εισάγετε μία έγκυρη διεύθυνση e-mail." );
define( "LM_FORM_SHORTERNAME", "Παρακαλούμε χρησιμοποιήστε ένα πιο σύντομο όνομα χρήστη. Ευχαριστούμε." );
define( "LM_FORM_NONAME", "Παρακαλούμε εισάγετε ένα όνομα χρήστη. Ευχαριστούμε." );
define( "LM_SUBSCRIBE", "Εγγραφή" );
define( "LM_UNSUBSCRIBE", "Διαγραφή" );
define( "LM_BUTTON_SUBMIT", "ΟΚ!" );

/* Backend */

define( "LM_ERROR_NEWSLETTER_COULDNTBESENT", "Το Newsletter δεν εστάλη!" );
define( "LM_NEWSLETTER_SENDTO_X_USERS", "Το Newsletter εστάλη σε {X} συνδρομητές" );
define( "LM_IMPORT_USERS", "Εισαγωγή<br />συνδρομητών" );
define( "LM_EXPORT_USERS", "Εξαγωγή<br />συνδρομητών" );
define( "LM_UPLAOD_FAILED", "Το φόρτωμα απέτυχε" );
define( "LM_ERROR_PARSING_XML", "Πρόβλημα στην επεξεργασία του αρχείου XML" );
define( "LM_ERROR_NO_XML", "Παρακαλούμε φορτώστε μόνο αρχεία XML" );

define( "LM_ERROR_EMAIL_ALREADY_ONLIST", "Αυτό το e-mail υπάρχει ήδη στη λίστα" );
define( "LM_SUCCESS_ON_IMPORT", "Εισήχθησαν επιτυχώς {X} συνδρομητές." );
define( "LM_IMPORT_FINISHED", "Η εισαγωγή ολοκληρώθηκε" );
define( "LM_ERROR_DELETING_FILE", "Η διαγραφή αρχείου απέτυχε" );
define( "LM_DIR_NOT_WRITABLE", "Αδύνατη η εγγραφή στον φάκελο ".$GLOBALS['mosConfig_cachepath'] );
define( "LM_ERROR_INVALID_EMAIL", "Μη έγκυρη διεύθυνση e-mail" );
define( "LM_ERROR_EMPTY_EMAIL", "Κενή διεύθυνση e-mail" );
define( "LM_ERROR_EMPTY_FILE", "Σφάλμα: Κενό αρχείο" );
define( "LM_ERROR_ONLY_TEXT", "Μόνο κείμενο (text)" );

define( "LM_SELECT_FILE", "Παρακαλούμε επιλέξτε ένα αρχείο" );
define( "LM_YOUR_XML_FILE", "Το αρχείο XML YaNC/Letterman εξαγωγής συνδρομητών" );
define( "LM_YOUR_CSV_FILE", "Εισαγωγή αρχείου CSV" );
define( "LM_POSITION_NAME", "Θέση της στήλης -Όνομα-" );
define( "LM_NAME_COL", "Στήλη Ονόματος" );
define( "LM_POSITION_EMAIL", "Θέση της στήλης -Διευθύνσεις E-mail-" );
define( "LM_EMAIL_COL", "Στήλη Διευθύνσεων E-mail" );
define( "LM_STARTFROM", "Έναρξη εισαγωγής από τη γραμμή..." );
define( "LM_STARTFROMLINE", "Έναρξη από τη γραμμή" );
define( "LM_CSV_DELIMITER", "Διαχωριστικό εισαγωγής CSV" );
define( "LM_CSV_DELIMITER_TIP", "Διαχωριστικό εισαγωγής CSV: , ; ή κενό" );

/* Newsletter Management */

define( "LM_NM", "Διαχείριση Newsletter" );
define( "LM_MESSAGE", "Διαχείριση" );
define( "LM_LAST_SENT", "Τελευταία αποστολή" );
define( "LM_SEND_NOW", "Αποστολή τώρα" );
define( "LM_CHECKED_OUT", "Επιβεβαιώμενο" );
define( "LM_NO_EXPIRY", "Ολοκλήρωση: Χωρίς λήξη" );
define( "LM_WARNING_SEND_NEWSLETTER", "Είστε βέβαιοι για την αποστολή αυτού του newsletter;\\nΠροσοχή: Αν ο αριθμός παραληπτών είναι μεγάλος, η διαδικασία αυτή θα πάρει λίγο χρόνο!" );
define( "LM_SEND_NEWSLETTER", "Αποστολή Newsletter" );
define( "LM_SEND_TO_GROUP", "Αποστολή σε ομάδα" );
define( "LM_MAIL_FROM", "Αποστολέας" );
define( "LM_DISABLE_TIMEOUT", "Απενεργοποίηση χρόνου λήξης διαδικασίας" );
define( "LM_DISABLE_TIMEOUT_TIP", "Επιλέξτε για να αποτρέψετε τον μηχανισμό από σφάλμα timeout. <br /><strong>Η λειτουργία αυτή δεν υποστηρίζεται κάτω από safe mode!<strong>" );
define( "LM_REPLY_TO", "Απάντηση σε" );
define( "LM_MSG_HTML", "Μήνυμα (HTML-WYSIWYG)" );
define( "LM_MSG", "Μήνυμα (HTML-κώδικας)" );
define( "LM_TEXT_MSG", "εναλλακτικό μήνυμα σε μορφή Text" );
define( "LM_NEWSLETTER_ITEM", "Newsletter Item" );

/* Subscriber Management */
define( "LM_SUBSCRIBER", "Συνδρομητής" );
define( "LM_NEW_SUBSCRIBER", "Νέος<br />συνδρομητής" );
define( "LM_EDIT_SUBSCRIBER", "Επεξεργασία<br />συνδρομητή" );
define( "LM_SELECT_SUBSCRIBER", "Επιλογή συνδρομητή" );
define( "LM_SUBSCRIBER_NAME", "Όνομα συνδρομητή" );
define( "LM_SUBSCRIBER_EMAIL", "E-mail συνδρομητή" );
define( "LM_SIGNUP_DATE", "Ημερομηνία εγγραφής" );
define( "LM_CONFIRMED", "Επιβεβαιωμένος" );
define( "LM_SUBSCRIBER_SAVED", "Τα στοιχεία συνδρομητή έχουν σωθεί" );
define( "LM_SUBSCRIBERS_DELETED", "{X} συνδρομητές διαγράφησαν επιτυχώς" );
define( "LM_SUBSCRIBER_DELETED", "Ο συνδρομητής διαγράφηκε επιτυχώς." );

/* Frontend */

define( "LM_ALREADY_SUBSCRIBED", "Είστε ήδη εγγεγραμμένοι στο Newsletter μας." );
define( "LM_NOT_SUBSCRIBED", "ΔΕΝ είστε εγγεγραμμένοι στο Newsletter μας." );
define( "LM_YOUR_DETAILS", "Τα στοιχεία σας:" );
define( "LM_SUBSCRIBE_TO", "Εγγραφείτε στο Newsletter μας" );
define( "LM_UNSUBSCRIBE_FROM", "Διαγραφείτε από το Newsletter μας" );
define( "LM_VALID_EMAIL_PLEASE", "Παρακαλούμε εισάγετε μία έγκυρη διεύθυνση e-mail!" );
define( "LM_SAME_EMAIL_TWICE", "Η διεύθυνση email που δώσατε υπάρχει ήδη στη λίστα του Newsletter μας!" );
define( "LM_ERROR_SENDING_SUBSCRIBE", "Το μήνυμα εγγραφής δεν μπόρεσε να σταλεί:" );
define( "LM_SUCCESS_SUBSCRIBE", "Το e-mail σας προστέθηκε επιτυχώς στη λίστα Newsletter!" );
define( "LM_RETURN_TO_NL", "Επιστροφή στα Newsletter" );
define( "LM_ERROR_UNSUBSCRIBE_OTHER_USER", "Λυπούμαστε, αλλά δεν μπορείτε να διαγράψετε άλλους συνδρομητές από τη λίστα" );
define( "LM_ERROR_SENDING_UNSUBSCRIBE", "Το μήνυμα διαγραφής δεν μπόρεσε να σταλεί:" );
define( "LM_SUCCESS_UNSUBSCRIBE", "Το e-mail σας διαγράφηκε επιτυχώς από τη λίστα του Newsletter μας." );
define( "LM_SUCCESS_CONFIRMATION", "Η συνδρομή σας επιβεβαιώθηκε." );
define( "LM_ERROR_CONFIRM_ACC_NOTFOUND", "Δεν βρέθηκε συνδρομή που να σχετίζεται με τον σύνδεσμο επιβεβαίωσης." );

define( "LM_CONFIRMED_ACCOUNTS_ONLY", "Μόνο επιβεβαιωμένοι συνδρομητές;" );
define( "LM_CONFIRMED_ACCOUNTS_ONLY_TIP", "Αποστολή του Newsletter μόνο σε <strong>επιβεβαιωμένους</strong> συνδρομητές. Οι συνδρομητές εκείνοι που δεν έχουν επιβεβαιώσει την εγγραφή τους στη λίστα δεν θα λάβουν το Newsletter." );

define( "LM_NAME_TAG_USAGE", "Μπορείτε να χρησιμοποιήσετε την ετικέτα <strong>[NAME]</strong> στο περιεχόμενο του Newsletter για να στείλετε προσωποποιημένα μηνύματα. \\nΌταν στέλνετε το Newsletter, η ετικέττα [NAME] αντικαθίσταται από το όνομα του χρήστη/συνδρομητή." );

define( "LM_USERS_TO_SUBSCRIBERS", "Κάντε τους χρήστες του site και συνδρομητές στο Newsletter" );
define( "LM_ASSIGN_USERS", "<br />Εισαγωγή<br />χρηστών" );

/**

 * @since Letterman 1.2.0

 */

define( 'LM_SEND_LOG', 'Αρχείο καταγραφής αποστολών Newsletter' );
define( 'LM_NUMBER_OF_MAILS_SENT', '%s από %s e-mail έχουν σταλεί μέχρι στιγμής.');
define( 'LM_SEND_NEXT_X_MAILS', 'Πατήστε το κουμπί για την αποστολή των επόμενων %s e-mail.');
define( 'LM_CHANGE_MAILS_PER_STEP', 'Αλλαγή του αριθμού αποστολής e-mail ανά βήμα');
define( 'LM_CONFIRM_ABORT_SENDING', 'Είστε βέβαιοι για την ακύρωση της αποστολής αυτού του newsletter;');
define( 'LM_MAILS_PER_STEP', 'Πόσα e-mail να σταλούν κάθε φορά;');
define( 'LM_CONFIRM_UNSUBSCRIBE', 'Είστε βέβαιοι για την διαγραφή από τη λίστα Newsletter μας;');

/**

 * @since Letterman 1.2.1

 */

define( 'LM_COMPOSE_NEWSLETTER', 'Σύνθεση Newsletter από άρθρα του site');
define( 'LM_USABLE_TAGS', 'Ετικέτες που μπορείτε να χρησιμοποιήσετε' );
define( 'LM_CONTENT_ITEMS', 'Αντικείμενα περιεχομένου' );
define( 'LM_ADD_CONTENT', 'Προσθήκη αντικειμένων περιεχομένου/άρθρων' );
define( 'LM_ADD_CONTENT_TOOLTIP', 'Αν επιλέξετε ένα αντικείμενο περιεχομένο από τη λίστα, μία ετικέτα θα εισαχθεί στην περιοχή εισαγωγής κειμένου. Αυτή η ετικέτα θα αντικατασταθεί από το πλήρες άρθρο (εισαγωγικό κείμενο με φωτογραφίες) κατά την αποθήκευση (Save).' );

define( 'LM_ATTACHMENTS', 'Επισυνάψεις' );
define( 'LM_ATTACHMENTS_TOOLTIP', 'Μπορείτε να επιλέξετε ένα ή περισσότερα αρχεία από τον φάκελο %s, τα οποία θα ενσωματωθούν στο e-mail κατά την αποστολή. Παρακαλώ μην δώσετε σημασία στην ετικέτα [ATTACHMENT ..] - θα αφαιρεθεί κατά την αποστολή του newsletter!' );
define( 'LM_MULTISELECT', 'Επιλέξτε πολλαπλά αρχεία κρατώντας πατημένο το πλήκτρο Ctrl (PC) ή Command (Mac)' );

?>
