<?php

define( "LM_SUBSCRIBE_SUBJECT", "A [mosConfig_live_site] hírlevelének MEGRENDELÉSE" );
define( "LM_SUBSCRIBE_MESSAGE", 
"Helló, [NAME]!

A [mosConfig_live_site] hírlevél megrendelésedet megkaptuk.
Köszönjük!

Megrendelésed jóváhagyásához kérjük, kattints az alábbi hivatkozásra vagy másold 
a vágólapra és illeszd be a böngészõ címsorába:

[LINK]

_________________________
   [mosConfig_live_site]" );

define( "LM_UNSUBSCRIBE_SUBJECT", "A [mosConfig_live_site] hírlevelének LEMONDÁSA" );
define( "LM_UNSUBSCRIBE_MESSAGE", 
"Helló, [NAME]!

A [mosConfig_live_site] hírlevelének lemondását megkaptuk, adataidat töröltük az adatbázisból.

Ha egy késõbbi idõpontban ismét meg szeretnéd rendelni a tájékoztatónkat, örömmel látunk viszont.
________________________
   [mosConfig_live_site]" );

define( "LM_NEWSLETTER_FOOTER", 
"<br/><br/>___________________________________________________________<br/>
Jelen tájékoztatót azért kapod, mert valamikor megrendelted<br/>
a [mosConfig_live_site] hírlevelét.<br/>
A lemondáshoz kattints ide: [UNLINK]" );

/* Module */
define( "LM_FORM_NOEMAIL", "Kérjük, hogy mûködõ e-mail címet adj meg." );
define( "LM_FORM_SHORTERNAME", "Kérjük, hogy rövidebb elõfizetõi nevet adj meg. Köszönjük." );
define( "LM_FORM_NONAME", "Kérjük, hogy írd be az elõfizetõi nevet. Köszönjük." );
define( "LM_SUBSCRIBE", "Megrendelés" );
define( "LM_UNSUBSCRIBE", "Lemondás" );
define( "LM_BUTTON_SUBMIT", "Feladom" );

/* Backend */
define( "LM_ERROR_NEWSLETTER_COULDNTBESENT", "Nem küldhetõ el a hírlevél!" );
define( "LM_NEWSLETTER_SENDTO_X_USERS", "A hírlevél {X} felhasználónak elküldve" );
define( "LM_IMPORT_USERS", "Megrendelõk importálása" );
define( "LM_EXPORT_USERS", "Megrendelõk exportálása" );
define( "LM_UPLAOD_FAILED", "A feltöltés nem sikerült" );
define( "LM_ERROR_PARSING_XML", "iba merült fel az XML fájl elemzésekor" );
define( "LM_ERROR_NO_XML", "Kérjük, hogy csak XML fájlokat tölts fel" );
define( "LM_ERROR_EMAIL_ALREADY_ONLIST", "Már nyilvántartásba vettük az e-mail címet" );
define( "LM_SUCCESS_ON_IMPORT", "A(z) {X} megrendelõ importálása sikerült." );
define( "LM_IMPORT_FINISHED", "Az importálás befejezõdött" );
define( "LM_ERROR_DELETING_FILE", "A fájltörlés nem sikerült" );
define( "LM_DIR_NOT_WRITABLE", "Nem írható a következõ könyvtár: ".$GLOBALS['mosConfig_cachepath'] );
define( "LM_ERROR_INVALID_EMAIL", "Érvénytelen e-mail cím" );
define( "LM_ERROR_EMPTY_EMAIL", "Üres e-mail cím" );
define( "LM_ERROR_EMPTY_FILE", "Hiba: üres a fájl" );
define( "LM_ERROR_ONLY_TEXT", "Csak szöveg" );

define( "LM_SELECT_FILE", "Kérjük, válassz ki egy fájlt" );
define( "LM_YOUR_XML_FILE", "YaNC/Letterman XML export fájlod" );
define( "LM_YOUR_CSV_FILE", "CSV import fájl" );
define( "LM_POSITION_NAME", "A -Név- oszlop helye" );
define( "LM_NAME_COL", "Név oszlop" );
define( "LM_POSITION_EMAIL", "Az -Email- oszlop helye" );
define( "LM_EMAIL_COL", "Email oszlop" );
define( "LM_STARTFROM", "Az importálás megkezdése sortól..." );
define( "LM_STARTFROMLINE", "Kezdés sortól" );
define( "LM_CSV_DELIMITER", "CSV elválasztó" );
define( "LM_CSV_DELIMITER_TIP", "CSV elválasztó: , ; vagy tabulátor" );

/* Newsletter Management */
define( "LM_NM", "Hírlevélkezelõ" );
define( "LM_MESSAGE", "Üzenet" );
define( "LM_LAST_SENT", "Utolsó küldés" );
define( "LM_SEND_NOW", "Küldés most" );
define( "LM_CHECKED_OUT", "Checked Out" );
define( "LM_NO_EXPIRY", "Befejezés: Nincs lejárat" );
define( "LM_WARNING_SEND_NEWSLETTER", "Valóban postázni akarod a hírlevelet?\\nFigyelem! ha nagy felhasználói csoportnak küldöd a levelet, akkor jóideig eltarthat!" );
define( "LM_SEND_NEWSLETTER", "Hírlevél küldése" );
define( "LM_SEND_TO_GROUP", "Küldés csoportnak" );
define( "LM_MAIL_FROM", "Feladó" );
define( "LM_DISABLE_TIMEOUT", "Idõtúllépés letiltása" );
define( "LM_DISABLE_TIMEOUT_TIP", "Jelöld be, ha meg akarod akadályozni, hogy a szkript idõtúllépési hibát generáljon. <br/><strong>Biztonságos módban nem mûködik!<strong>" );
define( "LM_REPLY_TO", "Válaszcím" );
define( "LM_MSG_HTML", "Üzenet (HTML-WYSIWYG)" );
define( "LM_MSG", "Üzenet (HTML-forrás)" );
define( "LM_TEXT_MSG", "választható szöveges üzenet" );
define( "LM_NEWSLETTER_ITEM", "Hírlevél elem" );

/* Subscriber Management */
define( "LM_SUBSCRIBER", "Megrendelõ" );
define( "LM_NEW_SUBSCRIBER", "Új megrendelõ" );
define( "LM_EDIT_SUBSCRIBER", "Adatmódosítás" );
define( "LM_SELECT_SUBSCRIBER", "Válassz megrendelõt" );
define( "LM_SUBSCRIBER_NAME", "Megrendelõ neve" );
define( "LM_SUBSCRIBER_EMAIL", "Megrendelõ e-mail címe" );
define( "LM_SIGNUP_DATE", "Megrendelés dátuma" );
define( "LM_CONFIRMED", "Jóváhagyva" );
define( "LM_SUBSCRIBER_SAVED", "A megrendelõ adatainak mentése megtörtént" );
define( "LM_SUBSCRIBERS_DELETED", "Sikerült törölnöd a(z) {X} megrendelõt" );
define( "LM_SUBSCRIBER_DELETED", "A megrendelõ törlése sikerült." );

/* Frontend */
define( "LM_ALREADY_SUBSCRIBED", "Te már megrendelted a hírlevelünket." );
define( "LM_NOT_SUBSCRIBED", "Jelenleg NEM vagy a hírlevelünk megrendelõje." );
define( "LM_YOUR_DETAILS", "Adataid:" );
define( "LM_SUBSCRIBE_TO", "Hírlevelünk megrendelése" );
define( "LM_UNSUBSCRIBE_FROM", "Hírlevelünk lemondása" );
define( "LM_VALID_EMAIL_PLEASE", "Írd be a mûködõ e-mail címedet!" );
define( "LM_SAME_EMAIL_TWICE", "Már szerepel a nyilvántartásunkban az általad megadott e-mail cím!" );
define( "LM_ERROR_SENDING_SUBSCRIBE", "Nem lehetett elküldeni a megrendelési üzenetet:" );
define( "LM_SUCCESS_SUBSCRIBE", "E-mail címedet nyilvántartásba vettük." );
define( "LM_RETURN_TO_NL", "Vissza a hírlevelekhez" );
define( "LM_ERROR_UNSUBSCRIBE_OTHER_USER", "Sajnos más felhasználó(ka)t nem törölhetsz a listáról" );
define( "LM_ERROR_SENDING_UNSUBSCRIBE", "Nem lehetett elküldeni a lemondási üzenetet:" );
define( "LM_SUCCESS_UNSUBSCRIBE", "Címedet töröltük a nyilvántartásunkból" );
define( "LM_SUCCESS_CONFIRMATION", "Fiókod létrehozásának jóváhagyása sikerült" );
define( "LM_ERROR_CONFIRM_ACC_NOTFOUND", "Nem található a jóváhagyási hivatkozáshoz kapcsolódó fiók." );

define( "LM_CONFIRMED_ACCOUNTS_ONLY", "Csak a jóváhagyott fiókok?" );
define( "LM_CONFIRMED_ACCOUNTS_ONLY_TIP", "A hírlevél küldése csak a <strong>jóváhagyott</strong> megrendelõknek. Azok a megrendelõk, akik nem hagyták jóvá a megrendelésüket, nem kapnak hírlevelet." );

define( "LM_NAME_TAG_USAGE", "A hírlevél tartalmában a <strong>[NAME]</strong> címkével küldhetsz személyre szabott hírleveleket. <br/>A hírlevél postázásakor a [NAME] a felhasználó/megrendelõ nevével kerül behelyettesítésre." );

define( "LM_USERS_TO_SUBSCRIBERS", "A felhasználók megrendelõvé tétele" );
define( "LM_ASSIGN_USERS", "Felhasználók hozzárendelése" );

/**
 * @since Letterman 1.2.0
 */
define( 'LM_SEND_LOG', 'Letterman hírlevélküldési napló' );
define( 'LM_NUMBER_OF_MAILS_SENT', 'Eddig %s / %s levél került elküldésre.');
define( 'LM_SEND_NEXT_X_MAILS', 'Kattints a gombra a következõ %s levél elküldéséhez.');
define( 'LM_CHANGE_MAILS_PER_STEP', 'A lépésenkénti üzenetek számának módosítása');
define( 'LM_CONFIRM_ABORT_SENDING', 'Valóban meg akarod szakítani a hírlevél postázását?');
define( 'LM_MAILS_PER_STEP', 'Hány levél küldendõ el egyszerre?');
define( 'LM_CONFIRM_UNSUBSCRIBE', 'Tényleg le akarod mondani a hírlevelünket?');

?>
