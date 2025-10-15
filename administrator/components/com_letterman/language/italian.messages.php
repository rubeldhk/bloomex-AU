<?php

define( "LM_SUBSCRIBE_SUBJECT", "La tua iscrizione alla Newsletter su [mosConfig_live_site]" );
define( "LM_SUBSCRIBE_MESSAGE",
"Ciao [NAME],

sei stato iscritto con successo alla Newsletter su
[mosConfig_live_site].
Grazie!

Per confermare la tua iscrizione, clicca sul link sottostante oppure copialo
e incollalo nel tuo browser.

[LINK]

_________________________
[mosConfig_live_site]" );

define( "LM_UNSUBSCRIBE_SUBJECT", "Servizio di Newsletter su [mosConfig_live_site]: Cancellazione" );
define( "LM_UNSUBSCRIBE_MESSAGE",
"Ciao [NAME],

sei stato cancellato dal servizio di Newsletter su [mosConfig_live_site].
Grazie per aver utilizzato il nostro servizio.

________________________
[mosConfig_live_site]" );

define( "LM_NEWSLETTER_FOOTER",
"<br/><br/>___________________________________________________________<br/>
Stai ricevendo questa Newsletter perché ti sei iscritto<br/>
al servizio su [mosConfig_live_site].<br/>
Per cancellarti clicca su: [UNLINK]" );

/* Module */
define( "LM_FORM_NOEMAIL", "Inserisci un indirizzo email valido." );
define( "LM_FORM_SHORTERNAME", "Inserisci un nome di sottoscrizione più corto. Grazie." );
define( "LM_FORM_NONAME", "Inserisci un nome di sottoscrizione. Grazie." );
define( "LM_SUBSCRIBE", "Sottoscrivi" );
define( "LM_UNSUBSCRIBE", "Cancellati" );
define( "LM_BUTTON_SUBMIT", "Vai!" );

/* Backend */
define( "LM_ERROR_NEWSLETTER_COULDNTBESENT", "La Newsletter non può essere inviata!" );
define( "LM_NEWSLETTER_SENDTO_X_USERS", "Newsletter inviata a {X} utenti" );
define( "LM_IMPORT_USERS", "Importa gli iscritti" );
define( "LM_EXPORT_USERS", "Esporta gli iscritti" );
define( "LM_UPLAOD_FAILED", "Upload Fallito" );
define( "LM_ERROR_PARSING_XML", "Errore nell'analisi del file XML" );
define( "LM_ERROR_NO_XML", "L'upload è consentito solamente per file in formato XML" );
define( "LM_ERROR_EMAIL_ALREADY_ONLIST", "L'indirizzo email è già nella lista" );
define( "LM_SUCCESS_ON_IMPORT", "Importati con successo {X} iscritti alla Newsletter." );
define( "LM_IMPORT_FINISHED", "Importazione terminata" );
define( "LM_ERROR_DELETING_FILE", "Cancellazione del file fallita" );
define( "LM_DIR_NOT_WRITABLE", "Non posso scrivere nella directory ".$GLOBALS['mosConfig_cachepath'] );
define( "LM_ERROR_INVALID_EMAIL", "Indirizzo email non valido" );
define( "LM_ERROR_EMPTY_EMAIL", "Indirizzo email vuoto" );
define( "LM_ERROR_EMPTY_FILE", "Errore: file vuoto" );
define( "LM_ERROR_ONLY_TEXT", "Solo testo" );

define( "LM_SELECT_FILE", "Seleziona un file" );
define( "LM_YOUR_XML_FILE", "Il tuo file di esportazione Yanc/Letterman in XML" );
define( "LM_YOUR_CSV_FILE", "File di importazione CSV" );
define( "LM_POSITION_NAME", "Posizione della colonna -Nome-" );
define( "LM_NAME_COL", "Nome della colonna" );
define( "LM_POSITION_EMAIL", "Posizione della colonna -Email-" );
define( "LM_EMAIL_COL", "Colonna Email" );
define( "LM_STARTFROM", "Inizio dell'importazione dalla linea..." );
define( "LM_STARTFROMLINE", "Inizio dalla linea" );
define( "LM_CSV_DELIMITER", "Delimitatore CSV" );
define( "LM_CSV_DELIMITER_TIP", "Delimitatore CSV: , ; o carattere di Tabulazione" );

/* Newsletter Management */
define( "LM_NM", "Amministratore della Newsletter" );
define( "LM_MESSAGE", "Messaggio" );
define( "LM_LAST_SENT", "Ultimo inviato" );
define( "LM_SEND_NOW", "Invia adesso" );
define( "LM_CHECKED_OUT", "Controllato" );
define( "LM_NO_EXPIRY", "Fine: nessuna scadenza" );
define( "LM_WARNING_SEND_NEWSLETTER", "Sei sicuro di voler inviare la newsletter?\\nAttenzione: l'invio di email ad un grande numero di utenti potrebbe richiedere molto tempo!" );
define( "LM_SEND_NEWSLETTER", "Invia la Newsletter" );
define( "LM_SEND_TO_GROUP", "Invia al gruppo" );
define( "LM_MAIL_FROM", "Mail da" );
define( "LM_DISABLE_TIMEOUT", "Disabilita il timeout" );
define( "LM_DISABLE_TIMEOUT_TIP", "Controlla per evitare che lo script generi un errore di timeout. <br/><strong>Non funziona in safe mode!<strong>" );
define( "LM_REPLY_TO", "Replica a" );
define( "LM_MSG_HTML", "Messaggio (HTML-WYSIWYG)" );
define( "LM_MSG", "Messaggio (HTML-source)" );
define( "LM_TEXT_MSG", "Messaggio del testo alternativo" );
define( "LM_NEWSLETTER_ITEM", "Corpo della Newsletter" );

/* Subscriber Management */
define( "LM_SUBSCRIBER", "Iscritto" );
define( "LM_NEW_SUBSCRIBER", "Nuovo iscritto" );
define( "LM_EDIT_SUBSCRIBER", "Modifica iscritto" );
define( "LM_SELECT_SUBSCRIBER", "Seleziona un iscritto" );
define( "LM_SUBSCRIBER_NAME", "Nome dell'iscritto" );
define( "LM_SUBSCRIBER_EMAIL", "Email dell'iscritto" );
define( "LM_SIGNUP_DATE", "Data di iscrizione" );
define( "LM_CONFIRMED", "Confermato" );
define( "LM_SUBSCRIBER_SAVED", "Le informazioni dell'iscritto sono state salvate" );
define( "LM_SUBSCRIBERS_DELETED", "Hai cancellato con successo {X} iscritti" );
define( "LM_SUBSCRIBER_DELETED", "L'iscritto è stato cancellato con successo." );

/* Frontend */
define( "LM_ALREADY_SUBSCRIBED", "Sei già iscritto alle nostre Newsletters." );
define( "LM_NOT_SUBSCRIBED", "Attualmente non sei iscritto alle nostre Newsletters." );
define( "LM_YOUR_DETAILS", "I tuoi dati:" );
define( "LM_SUBSCRIBE_TO", "Iscriviti alla nostra Newsletter" );
define( "LM_UNSUBSCRIBE_FROM", "Cancellati dalla nostra Newsletter" );
define( "LM_VALID_EMAIL_PLEASE", "Inserisci un indirizzo email valido!" );
define( "LM_SAME_EMAIL_TWICE", "L'indirizzo email che hai inserito è già presente nella nostra lista di iscritti!" );
define( "LM_ERROR_SENDING_SUBSCRIBE", "Un messaggio di avvenuta iscrizione non può essere inviato:" );
define( "LM_SUCCESS_SUBSCRIBE", "Il tuo indirizzo email è stato aggiunto alla nostra Newsletter." );
define( "LM_RETURN_TO_NL", "Ritorna alle Newsletters" );
define( "LM_ERROR_UNSUBSCRIBE_OTHER_USER", "Spiacente, ma non puoi cancellare altri utenti dalla lista" );
define( "LM_ERROR_SENDING_UNSUBSCRIBE", "Un messaggio di avvenuta cancellazione non può essere inviato:" );
define( "LM_SUCCESS_UNSUBSCRIBE", "Il tuo indirizzo email è stato rimosso dalla nostra Newsletter" );
define( "LM_SUCCESS_CONFIRMATION", "Il tuo Account è stato confermato con successo" );
define( "LM_ERROR_CONFIRM_ACC_NOTFOUND", "L'Account associato con il tuo link di conferma non è stato trovato." );

define( "LM_CONFIRMED_ACCOUNTS_ONLY", "Solo Accounts confermati?" );
define( "LM_CONFIRMED_ACCOUNTS_ONLY_TIP", "Invia la Newsletter solamente ad Accounts di iscritti <strong>confermati</strong>. Gli iscritti che non hanno confermato la loro iscrizione non riceveranno la Newsletter." );

define( "LM_NAME_TAG_USAGE", "Non puoi usare il Tag <strong>[NAME]</strong> nel contenuto della Newsletter per inviare Newsletters personalizzate. <br/>Quando invii la Newsletter, [NAME] è sostituito dal Nome dell'Utente/Iscritto." );

define( "LM_USERS_TO_SUBSCRIBERS", "Iscrivi gli utenti" );
define( "LM_ASSIGN_USERS", "Assegna Utenti" );

/**
 * @since Letterman 1.2.0
 */
define( 'LM_SEND_LOG', 'Letterman Newsletter Send Log' );
define( 'LM_NUMBER_OF_MAILS_SENT', '%s of %s mails have been sent so far.');
define( 'LM_SEND_NEXT_X_MAILS', 'Click the button to send the next %s Mails.');
define( 'LM_CHANGE_MAILS_PER_STEP', 'Change the mails-per-step amount');
define( 'LM_CONFIRM_ABORT_SENDING', 'Do you really want to abort sending this newsletter?');
define( 'LM_MAILS_PER_STEP', 'How many mails to send at once?');
define( 'LM_CONFIRM_UNSUBSCRIBE', 'Do you really want to unsusbcribe from our Newsletter service?');
/**
 * @since Letterman 1.2.1
 */
define( 'LM_COMPOSE_NEWSLETTER', 'Compose a newsletter from content items');
define( 'LM_USABLE_TAGS', 'Tags you can use' );
define( 'LM_CONTENT_ITEMS', 'Content items' );
define( 'LM_ADD_CONTENT', 'Add content items/articles' );
define( 'LM_ADD_CONTENT_TOOLTIP', 'If you select a content item from the list, a tag will be inserted into the textarea. This tag will be rendered to the full article (Into Text only with images) when clicking on Save.' );
define( 'LM_ATTACHMENTS', 'Attachments' );
define( 'LM_ATTACHMENTS_TOOLTIP', 'You can select one or multiple files from the directory %s, which will be embedded into the mail when sending. Please do not care about the [ATTACHMENT ..] tag - it will be removed when sending the newsletter!' );
define( 'LM_MULTISELECT', 'Multi-select files with Ctrl-MouseClick' );
?>