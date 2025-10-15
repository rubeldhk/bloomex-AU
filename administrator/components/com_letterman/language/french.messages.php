<?php
//french translation by dr.corbeille
define( "LM_SUBSCRIBE_SUBJECT", "Votre inscription à la lettre d'information de [mosConfig_live_site]" );
define( "LM_SUBSCRIBE_MESSAGE", 
"Bonjour [NAME],

Vous êtes inscrit à la lettre d'information de
[mosConfig_live_site].
Merci!

Pour valider votre inscription, merci de cliquer sur le lien ci-dessous ou de le copier
et de le coller dans votre navigateur.

[LINK]

_________________________
[mosConfig_live_site]" );

define( "LM_UNSUBSCRIBE_SUBJECT", "Lettre d'information de [mosConfig_live_site]: Désinscription" );
define( "LM_UNSUBSCRIBE_MESSAGE", 
"Bonjour [NAME],

Vous n'êtes plus inscrit à la lettre d'information de [mosConfig_live_site].
Merci d'avoir utilisé nos services.

________________________
[mosConfig_live_site]" );

define( "LM_NEWSLETTER_FOOTER", 
"___________________________________________________________<br/>
Vous recevez cette lettre d'information car vous êtes inscrit<br/>
sur [mosConfig_live_site].<br/>
Pour annuler votre abonnement cliquez ici : [UNLINK]" );


/* Module */
define( "LM_FORM_NOEMAIL", "Veuillez saisir une adresse email correcte." );
define( "LM_FORM_SHORTERNAME", "Veuillez choisir un nom plus court." );
define( "LM_FORM_NONAME", "Veuillez entrer un nom de souscription" );
define( "LM_SUBSCRIBE", "Inscrire" );
define( "LM_UNSUBSCRIBE", "Annuler" );
define( "LM_BUTTON_SUBMIT", "Go!" );

/* Backend */
define( "LM_ERROR_NEWSLETTER_COULDNTBESENT", "La newsletter n’a pas pu être envoyée!" );
define( "LM_NEWSLETTER_SENDTO_X_USERS", "La newsletter a été envoyée à {X} utilisateurs" );
define( "LM_IMPORT_USERS", "Importer inscriptions " );
define( "LM_EXPORT_USERS", "Exporter inscriptions " );
define( "LM_UPLAOD_FAILED", "Echec du téléchargement" );
define( "LM_ERROR_PARSING_XML", "Erreur d’interprétation du fichier XML " );
define( "LM_ERROR_NO_XML", "Veuillez télécharger des fichiers XML uniquement" );
define( "LM_ERROR_EMAIL_ALREADY_ONLIST", "Cette adresse email est déjà dans la liste" );
define( "LM_SUCCESS_ON_IMPORT", "{X} inscriptions importées." );
define( "LM_IMPORT_FINISHED", "Importation accomplie");
define( "LM_ERROR_DELETING_FILE", "Echec de l’effacement de fichier" );
define( "LM_DIR_NOT_WRITABLE", "Echec d’écriture vers ".$GLOBALS['mosConfig_cachepath'] );
define( "LM_ERROR_INVALID_EMAIL", "Adresse email non valide" );
define( "LM_ERROR_EMPTY_EMAIL", "Adresse mail vide" );
define( "LM_ERROR_EMPTY_FILE", "Erreur: Fichier vide" );
define( "LM_ERROR_ONLY_TEXT", "Texte uniquement" );

define( "LM_SELECT_FILE", "Veuillez sélectionner un fichier" );
define( "LM_YOUR_XML_FILE", "Votre fichier XML YaNC/Letterman" );
define( "LM_YOUR_CSV_FILE", "Fichier d’importation CSV " );
define( "LM_POSITION_NAME", "Position de la colonne -Nom-" );
define( "LM_NAME_COL", "Colonne Nom" );
define( "LM_POSITION_EMAIL", "Position de la colonne -Email-" );
define( "LM_EMAIL_COL", "Colonne Email " );
define( "LM_STARTFROM", "Démarrer l’importation depuis la ligne..." );
define( "LM_STARTFROMLINE", "Démarrer depuis la ligne" );
define( "LM_CSV_DELIMITER", "Signe de délimitation CSV" );
define( "LM_CSV_DELIMITER_TIP", " Signe de délimitation CSV: , ; ou tabulateur" );

/* Newsletter Management */
define( "LM_NM", "Gestion de newsletter" );
define( "LM_MESSAGE", "Message" );
define( "LM_LAST_SENT", "Dernier envoi" );
define( "LM_SEND_NOW", "Envoyer maintenant" );
define( "LM_CHECKED_OUT", "Checked Out" );
define( "LM_NO_EXPIRY", "Fin: Pas d’expiration" );
define( "LM_WARNING_SEND_NEWSLETTER", "Etes-vous sur de vouloir envoyer la newsletter?\\nAttention: S’il y a beaucoup de destinataires, cela prendra du temps! " );
define( "LM_SEND_NEWSLETTER", "Envoyer la newsletter" );
define( "LM_SEND_TO_GROUP", "Envoyer à un groupe" );
define( "LM_MAIL_FROM", "Expéditeur email" );
define( "LM_DISABLE_TIMEOUT", "Empêcher timeout" );
define( "LM_DISABLE_TIMEOUT_TIP", "Cocher pour prévenir une erreur de timeout.<br/><strong>Ne fonctionne pas en Safe Mode!</strong>" );
define( "LM_REPLY_TO", "Répondre à" );
define( "LM_MSG_HTML", "Message (HTML-WYSIWYG)" );
define( "LM_MSG", "Message (HTML-source)" );
define( "LM_TEXT_MSG", "Message alternatif en mode texte" );
define( "LM_NEWSLETTER_ITEM", "Item de newsletter" );

/* Subscriber Management */
define( "LM_SUBSCRIBER", "Abonné" );
define( "LM_NEW_SUBSCRIBER", "Nouvel abonné" );
define( "LM_EDIT_SUBSCRIBER", "Editer abonné " );
define( "LM_SELECT_SUBSCRIBER", "Choisir un abonné " );
define( "LM_SUBSCRIBER_NAME", "Nom de l’ abonné " );
define( "LM_SUBSCRIBER_EMAIL", "Email de l’ abonné " );
define( "LM_SIGNUP_DATE", "Date d’inscription" );
define( "LM_CONFIRMED", "Confirmé" );
define( "LM_SUBSCRIBER_SAVED", "Les infos d’inscription ont été enregistrées." );
define( "LM_SUBSCRIBERS_DELETED", "{X} abonnés ont été supprimés" );
define( "LM_SUBSCRIBER_DELETED", "L’abonné a été supprimé." );

/* Frontend */
define( "LM_ALREADY_SUBSCRIBED", "Vous êtes déjà abonné à nos newsletters." );
define( "LM_NOT_SUBSCRIBED", "Vous n’êtes actuellement PAS abonné à nos newsletters." );
define( "LM_YOUR_DETAILS", "Vos détails:" );
define( "LM_SUBSCRIBE_TO", "Abonnez notre newsletter" );
define( "LM_UNSUBSCRIBE_FROM", "Annuler votre abonnement à la newsletter" );
define( "LM_VALID_EMAIL_PLEASE", "Veuillez entrer une adresse email valide");
define( "LM_SAME_EMAIL_TWICE", "Cette adresse est déjà inscrite dans notre liste!" );
define( "LM_ERROR_SENDING_SUBSCRIBE", "Le message de confirmation n’a pas pu être envoyé:" );
define( "LM_SUCCESS_SUBSCRIBE", "Votre adresse a été ajouté à notre liste d’abonnés." );
define( "LM_RETURN_TO_NL", "Retour à la page des newsletters" );
define( "LM_ERROR_UNSUBSCRIBE_OTHER_USER", "Désolé, vous ne pouvez pas supprimer d’autres utilisateurs de la liste. " );
define( "LM_ERROR_SENDING_UNSUBSCRIBE", "Le message d’annulation n’a pas pu être envoyé:" );
define( "LM_SUCCESS_UNSUBSCRIBE", "Votre adresse email a été supprimé de notre liste." );
define( "LM_SUCCESS_CONFIRMATION", "Votre compte a été vérifié avec succès." );
define( "LM_ERROR_CONFIRM_ACC_NOTFOUND", "Le compte correspondant à votre lien d’activation n’a pas pu être trouvé" );

define( "LM_CONFIRMED_ACCOUNTS_ONLY", "Comptes confirmés uniquement?" );
define( "LM_CONFIRMED_ACCOUNTS_ONLY_TIP", "Envoyer la newsletter uniquement aux comptes <strong>confirmés</strong>. Les abonnés n’ayant pas confirmé leur adresse ne recevront pas la newsletter. " );

define( "LM_NAME_TAG_USAGE", "Vous pouvez utiliser la balise <strong>[NAME]</strong> dans le contenu de la newsletter pour personnaliser celle-ci. [NAME] sera remplacé par le nom de l’abonné lors de l’envoi." );

define( "LM_USERS_TO_SUBSCRIBERS", "Abonner les utilisateurs inscrits" );
define( "LM_ASSIGN_USERS", "Assigner utilisateurs" );

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