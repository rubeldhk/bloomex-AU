<?php

define( "LM_SUBSCRIBE_SUBJECT", "Uw nieuwsbrief inschrijving van [mosConfig_live_site]" );
define( "LM_SUBSCRIBE_MESSAGE", 
"Beste [NAME],

U bent succesvol toegevoegd aan onze nieuwsbrief op 
[mosConfig_live_site].
Bedankt!

Wij vragen u wel om op de onderstaande bevestigingslink te klikken of 
de onderstaande bevestigingslink in uw Internetbrouwser te kopieren:

[LINK]

Met vriendelijke groeten,
_________________________
[mosConfig_live_site]" );

define( "LM_UNSUBSCRIBE_SUBJECT", "Nieuwsbrief service van [mosConfig_live_site]: uitschrijving" );
define( "LM_UNSUBSCRIBE_MESSAGE", 
"Hallo [NAME],

U bent succesvol uitgeschreven van onze nieuwsbrief van [mosConfig_live_site].

Met vriendelijke groeten,
________________________
[mosConfig_live_site]" );

define( "LM_NEWSLETTER_FOOTER", 
"<br/><br/>___________________________________________________________<br/>
U krijgt deze nieuwsbrief van [mosConfig_live_site],<br/>
waar u uzelf heeft toegevoegd aan deze nieuwsbrief.<br/>
Om u uit te schrijven volg deze link: [UNLINK]" );


/* Module */
define( "LM_FORM_NOEMAIL", "Het ingevulde e-mailadres is niet geldig" );
define( "LM_FORM_SHORTERNAME", "Vul een kortere naam in" );
define( "LM_FORM_NONAME", "Vul een naam in" );
define( "LM_SUBSCRIBE", "Inschrijven" );
define( "LM_UNSUBSCRIBE", "Uitschrijven" );
define( "LM_BUTTON_SUBMIT", "Verzend" );

/* Frontend */
define( "LM_ALREADY_SUBSCRIBED", "U bent al ingeschreven voor onze nieuwsbrief" );
define( "LM_NOT_SUBSCRIBED", "U bent niet ingeschreven voor onze nieuwsbrief" );
define( "LM_YOUR_DETAILS", "Uw gegevens:" );
define( "LM_SUBSCRIBE_TO", "Inschrijven" );
define( "LM_UNSUBSCRIBE_FROM", "Uitschrijven" );
define( "LM_VALID_EMAIL_PLEASE", "Het ingevulde e-mail adres is niet geldig" );
define( "LM_SAME_EMAIL_TWICE", "U bent al ingeschreven voor onze nieuwsbrief" );
define( "LM_ERROR_SENDING_SUBSCRIBE", "Het bericht van inschrijven kan niet verzonden worden" );
define( "LM_SUCCESS_SUBSCRIBE", "Uw e-mailaddress is aan onze nieuwsbrieflijst toegevoegd" );
define( "LM_RETURN_TO_NL", "Terug naar de nieuwsbrief" );
define( "LM_ERROR_UNSUBSCRIBE_OTHER_USER", "Sorry, maar u kunt niet andere gebruikers van de nieuwsbrief uitschrijven" );
define( "LM_ERROR_SENDING_UNSUBSCRIBE", "Het bericht van uitschrijven kan niet verzonden worden" );
define( "LM_SUCCESS_UNSUBSCRIBE", "U bent uitgeschreven van onze nieuwsbrief" );
define( "LM_SUCCESS_CONFIRMATION", "Uw inschrijving is bevestigd" );
define( "LM_ERROR_CONFIRM_ACC_NOTFOUND", "De gegevens van uw bevestigingslink zijn niet gevonden" );

define( "LM_CONFIRMED_ACCOUNTS_ONLY", "Alleen bevestigde accounts?" );
define( "LM_CONFIRMED_ACCOUNTS_ONLY_TIP", "Stuur de nieuwsbrief alleen aan <strong>bevestigde</strong> abonnee accounts. Abonnees die het account niet bevestigd hebben zullen de nieuwsbrief niet ontvangen." );

define( "LM_NAME_TAG_USAGE", "U kunt de code <strong>[NAME]</strong> in de tekst nieuwsbrief gebruiken om persoonlijke nieuwsbrieven te versturen. <br/>Als u de nieuwsbrief verstuurd, wordt [NAME] vervangen door de naam van de gebruiker/abonnee." );

define( "LM_USERS_TO_SUBSCRIBERS", "Maak gebruikers tot abonnee." );
define( "LM_ASSIGN_USERS", "Ken gebruikers toe" );

/* Backend */
define( "LM_ERROR_NEWSLETTER_COULDNTBESENT", "De nieuwsbrief kon niet verzonden worden." );
define( "LM_NEWSLETTER_SENDTO_X_USERS", "De nieuwsbrief is aan {X} gebruikers verzonden." );
define( "LM_IMPORT_USERS", "Importeer abonnees" );
define( "LM_EXPORT_USERS", "Exporteer abonnees" );
define( "LM_UPLAOD_FAILED", "Upload mislukt" );
define( "LM_ERROR_PARSING_XML", "Fout bij het verwerken van het XML bestand" );
define( "LM_ERROR_NO_XML", "Alleen xml bestanden uploaden" );
define( "LM_ERROR_EMAIL_ALREADY_ONLIST", "Het e-mailadres staat al in de lijst." );
define( "LM_SUCCESS_ON_IMPORT", "Met succes {X} abonnees ge&iuml;mporteerd." );
define( "LM_IMPORT_FINISHED", "Klaar met importeren" );
define( "LM_ERROR_DELETING_FILE", "Bestand kon niet worden verwijderd." );
define( "LM_DIR_NOT_WRITABLE", "Kan niet naar directory ".$GLOBALS['mosConfig_cachepath']." schrijven." );
define( "LM_ERROR_INVALID_EMAIL", "Ongeldig e-mailadres" );
define( "LM_ERROR_EMPTY_EMAIL", "Leeg e-mailadres" );
define( "LM_ERROR_EMPTY_FILE", "Fout: leeg bestand" );
define( "LM_ERROR_ONLY_TEXT", "Alleen tekst" );

define( "LM_SELECT_FILE", "Selecteer alstublieft een bestand" );
define( "LM_YOUR_XML_FILE", "Uw YaNC/Letterman XML exportbestand" );
define( "LM_YOUR_CSV_FILE", "CSV importbestand" );
define( "LM_POSITION_NAME", "Positie van de -Naam- kolom" );
define( "LM_NAME_COL", "Naam kolom" );
define( "LM_POSITION_EMAIL", "Positie van de -Email- kolom" );
define( "LM_EMAIL_COL", "E-mail kolom" );
define( "LM_STARTFROM", "Start importeren vanaf regel..." );
define( "LM_STARTFROMLINE", "Start vanaf regel" );
define( "LM_CSV_DELIMITER", "CSV scheidingsteken" );
define( "LM_CSV_DELIMITER_TIP", "CSV scheidingsteken: , ; of tab" );

/* Newsletter Management */
define( "LM_NM", "Nieuwsbrief Manager" );
define( "LM_MESSAGE", "Bericht" );
define( "LM_LAST_SENT", "Het laatst verzonden" );
define( "LM_SEND_NOW", "Verzend nu" );
define( "LM_CHECKED_OUT", "Checked Out" );
define( "LM_NO_EXPIRY", "Finish: Geen Einde" );
define( "LM_WARNING_SEND_NEWSLETTER", "Weet u zeker dat u de nieuwsbrief wilt versturen? \\Waarschuwing: als u een bericht verstuurd aan een grote groep abonnees kan dit een tijd duren!" );
define( "LM_SEND_NEWSLETTER", "Verstuur nieuwsbrief" );
define( "LM_SEND_TO_GROUP", "Verstuur aan groep" );
define( "LM_MAIL_FROM", "Bericht van" );
define( "LM_DISABLE_TIMEOUT", "Timeout uitschakelen" );
define( "LM_DISABLE_TIMEOUT_TIP", "Vink aan om te voorkomen dat het script een timeout fout genereerd.<br/><strong>Dit werkt niet in safe mode!<strong>" );
define( "LM_REPLY_TO", "Reply to" );
define( "LM_MSG_HTML", "Bericht (HTML-WYSIWYG)" );
define( "LM_MSG", "Bericht (HTML-bron)" );
define( "LM_TEXT_MSG", "alternatief tekst bericht" );
define( "LM_NEWSLETTER_ITEM", "Nieuwsbrief item" );

/* Subscriber Management */
define( "LM_SUBSCRIBER", "Abonnee" );
define( "LM_NEW_SUBSCRIBER", "Nieuwe abonnee" );
define( "LM_EDIT_SUBSCRIBER", "Bewerk abonnee" );
define( "LM_SELECT_SUBSCRIBER", "Selecteer een abonnee" );
define( "LM_SUBSCRIBER_NAME", "Naam abonnee" );
define( "LM_SUBSCRIBER_EMAIL", "E-mail abonnee" );
define( "LM_SIGNUP_DATE", "Inschrijfdatum" );
define( "LM_CONFIRMED", "Bevestigd" );
define( "LM_SUBSCRIBER_SAVED", "De informatie van de abonnee is bewaard." );
define( "LM_SUBSCRIBERS_DELETED", "{X} abonnees zijn met succes verwijderd" );
define( "LM_SUBSCRIBER_DELETED", "De abonnee is met succes verwijderd." );


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