<?php

define( "LM_SUBSCRIBE_SUBJECT", "Suscripción al boletín de noricias [mosConfig_live_site]" );
define( "LM_SUBSCRIBE_MESSAGE", 
"Hello [NAME],

Usted se ha suscrito con éxito a nuestro boleín de noticias
[mosConfig_live_site].
Gracias!

Para confirmar su suscripción, haga click sobre el vinculo o copielo y peguelo en su browser.

[LINK]

_________________________
[mosConfig_live_site]" );

define( "LM_UNSUBSCRIBE_SUBJECT", "Desuscribirse del boletín de noticias [mosConfig_live_site]: " );
define( "LM_UNSUBSCRIBE_MESSAGE", 
"Hola [NAME],

Usted ha sido desuscrito de nuestro boletin noticioso[mosConfig_live_site].
Gracias por usar nuestro servicio.

________________________
[mosConfig_live_site]" );

define( "LM_NEWSLETTER_FOOTER", 
"<br/><br/>___________________________________________________________<br/>
Your're receiving this Newsletter because you have subscribed<br/>
to the  Newsletter Service at [mosConfig_live_site].<br/>
To unsubscribe please click here: [UNLINK]" );

/* Module */
define( "LM_FORM_NOEMAIL", "Sintaxis del email no valida." );
define( "LM_FORM_SHORTERNAME", "Ingrese un nombre de suscriptor más corto" );
define( "LM_FORM_NONAME", "Ingrese el nombre del suscriptor. Gracias." );
define( "LM_SUBSCRIBE", "Suscribirse" );
define( "LM_UNSUBSCRIBE", "Desuscribirse" );
define( "LM_BUTTON_SUBMIT", "Enviar!" );

/* Backend */
define( "LM_ERROR_NEWSLETTER_COULDNTBESENT", "El boletín de noticias no pudo ser enviado!" );
define( "LM_NEWSLETTER_SENDTO_X_USERS", "Boletín de noticias enviado a {X} usuarios" );
define( "LM_IMPORT_USERS", "Importar Suscriptores" );
define( "LM_EXPORT_USERS", "Exportar Suscriptores" );
define( "LM_UPLAOD_FAILED", "Carga fallida" );
define( "LM_ERROR_PARSING_XML", "Error al procesar el archivo XML" );
define( "LM_ERROR_NO_XML", "Cargue solo archivos xml" );
define( "LM_ERROR_EMAIL_ALREADY_ONLIST", "Este mail ya existe en la lista de suscriptores" );
define( "LM_SUCCESS_ON_IMPORT", "Importados con exito {X} Suscriptores." );
define( "LM_IMPORT_FINISHED", "Importación finalizada" );
define( "LM_ERROR_DELETING_FILE", "Detectada falla en el archivo" );
define( "LM_DIR_NOT_WRITABLE", "No se puede escribir en el directorio ".$GLOBALS['mosConfig_cachepath'] );
define( "LM_ERROR_INVALID_EMAIL", "Dirección de Email invalida" );
define( "LM_ERROR_EMPTY_EMAIL", "Dirección de correo requerida" );
define( "LM_ERROR_EMPTY_FILE", "Error: Archivo vacío" );
define( "LM_ERROR_ONLY_TEXT", "Solo texto" );

define( "LM_SELECT_FILE", "Seleccione un archivo" );
define( "LM_YOUR_XML_FILE", "Su archivo XML de exportación YaNC/Letterman" );
define( "LM_YOUR_CSV_FILE", "Impostar archivo CSV" );
define( "LM_POSITION_NAME", "Posición de la columna -Nombre-" );
define( "LM_NAME_COL", "Nombre de columna" );
define( "LM_POSITION_EMAIL", "Posisicón de la columna -Email-" );
define( "LM_EMAIL_COL", "Columna Email" );
define( "LM_STARTFROM", "Iniciar importación desde la línea..." );
define( "LM_STARTFROMLINE", "Iniciar desde la línea" );
define( "LM_CSV_DELIMITER", "Delimitador CSV" );
define( "LM_CSV_DELIMITER_TIP", "CSV Delimitador: , ; o Tabulador" );

/* Newsletter Management */
define( "LM_NM", "Administrador - Boletín de noticias" );
define( "LM_MESSAGE", "Mensaje" );
define( "LM_LAST_SENT", "Ultimo envío" );
define( "LM_SEND_NOW", "Enviar ahora" );
define( "LM_CHECKED_OUT", "Chequear" );
define( "LM_NO_EXPIRY", "Finalizado: Ningún inconveniente" );
define( "LM_WARNING_SEND_NEWSLETTER", "Esta seguro de querer enviar el Boletín de noticias ahora? \\Precaución: ¡Si envía el correo a un grupo grande de usuarios esto podría demorar un poco más de lo normal!" );
define( "LM_SEND_NEWSLETTER", "Enviar Boletín de noticias" );
define( "LM_SEND_TO_GROUP", "Enviar a un grupo" );
define( "LM_MAIL_FROM", "Email de:" );
define( "LM_DISABLE_TIMEOUT", "Deshabilitar control de tiempo de sesión sin actividad" );
define( "LM_DISABLE_TIMEOUT_TIP", "Chequear para prevenir que el script genere un error de timeout. <br/><strong>Al no estar trabajando en modo seguro!<strong>" );
define( "LM_REPLY_TO", "Reenviar a:" );
define( "LM_MSG_HTML", "Mensaje (HTML-WYSIWYG)" );
define( "LM_MSG", "Mensaje (HTML-source)" );
define( "LM_TEXT_MSG", "Opción de solo texto" );
define( "LM_NEWSLETTER_ITEM", "Item del Boletín de noticias" );

/* Subscriber Management */
define( "LM_SUBSCRIBER", "Suscriptor" );
define( "LM_NEW_SUBSCRIBER", "Nuevo Suscriptor" );
define( "LM_EDIT_SUBSCRIBER", "Editar Suscriptor" );
define( "LM_SELECT_SUBSCRIBER", "Seleccionar un Suscriptor" );
define( "LM_SUBSCRIBER_NAME", "Nombre del Suscriptor" );
define( "LM_SUBSCRIBER_EMAIL", "Email del Suscriptor" );
define( "LM_SIGNUP_DATE", "Fecha de registro" );
define( "LM_CONFIRMED", "Confirmado" );
define( "LM_SUBSCRIBER_SAVED", "La infotmación del suscriptor ha sido salvada" );
define( "LM_SUBSCRIBERS_DELETED", "Borrados exitosamente {X} Suscriptores" );
define( "LM_SUBSCRIBER_DELETED", "Suscriptor borrado exitosamente" );

/* Frontend */
define( "LM_ALREADY_SUBSCRIBED", "Usted ya esta suscrito a nuestro Boletín de noricias." );
define( "LM_NOT_SUBSCRIBED", "Usted NO esta suscrito actualmente a nuestro Bolerín de noticias" );
define( "LM_YOUR_DETAILS", "Sus datos:" );
define( "LM_SUBSCRIBE_TO", "Suscribirse" );
define( "LM_UNSUBSCRIBE_FROM", "Desuscribirse" );
define( "LM_VALID_EMAIL_PLEASE", "Ingrese una dirección de email valida!" );
define( "LM_SAME_EMAIL_TWICE", "La dirección de email suministrada ya se encuentra activa en nuestras listas!" );
define( "LM_ERROR_SENDING_SUBSCRIBE", "Mensaje para el suscriptor no pudo ser enviado:" );
define( "LM_SUCCESS_SUBSCRIBE", "Su dirección de email fue agregada a nuestro boletín de noticias." );
define( "LM_RETURN_TO_NL", "Regresar al Boletín de noticias" );
define( "LM_ERROR_UNSUBSCRIBE_OTHER_USER", "Usted no puede suprimir a otros usuarios de la lista" );
define( "LM_ERROR_SENDING_UNSUBSCRIBE", "Mensaje de desuscripción no pudo ser enviado:" );
define( "LM_SUCCESS_UNSUBSCRIBE", "Su dirección de email fue removida de nuestras listas de envio" );
define( "LM_SUCCESS_CONFIRMATION", "Su cuenta esta confirmada y activa ahora" );
define( "LM_ERROR_CONFIRM_ACC_NOTFOUND", "La cuenta asociada a su link de confirmación no fue encontrada." );

define( "LM_CONFIRMED_ACCOUNTS_ONLY", "Solo cuentas confirmadas?" );
define( "LM_CONFIRMED_ACCOUNTS_ONLY_TIP", "El Boletín de noticias se envia automáticamente a <strong> Suscriptores confirmados</strong> ,solo a sus cuentas. Los cuentas no confirmadas quedan por fuera de la lista de envio." );

define( "LM_NAME_TAG_USAGE", "Usted puede utilizar la etiqueta <strong>[NAME]</strong> en el Boletín de noticias si desea personalizarlo <br/> Cuando envie el Boletín de noticias, la etiqueta [NAME] es remplazada por el nombre del suscriptor" );

define( "LM_USERS_TO_SUBSCRIBERS", "Crear usuarios para suscriptores" );
define( "LM_ASSIGN_USERS", "Asignar usuarios" );

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