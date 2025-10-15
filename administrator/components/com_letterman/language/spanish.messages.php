<?php

define( "LM_SUBSCRIBE_SUBJECT", "Su suscripción al boletín de noticias de  [mosConfig_live_site]" );
define( "LM_SUBSCRIBE_MESSAGE",
"Estimada/o [NAME]:

Gracias por suscribirse al boletín de noticias de [mosConfig_live_site].

Para confirmar su suscripción, por favor pulse en el enlace debajo o cópielo y péguelo en su navegador:
[LINK]

_________________________

[mosConfig_live_site]" );

define( "LM_UNSUBSCRIBE_SUBJECT", "Servicio del boletín de noticias de [mosConfig_live_site]: Unsubscription" );
define( "LM_UNSUBSCRIBE_MESSAGE",
"Estimada/o [NAME]:

Su desuscripción del boletín de noticias de [mosConfig_live_site] ha sido exitosa.
Gracias por usar nuestro servicio.

________________________

[mosConfig_live_site]" );

define( "LM_NEWSLETTER_FOOTER",
"<br/><br/>
___________________________________________________________<br/>

Ud. recibe este boletín de noticias de [mosConfig_live_site] porque se suscribió a este servicio.<br/>
Para cancelar la suscripción a este boletín hazle click al siguiente enlace:  [UNLINK]" );

/* Module */
define( "LM_FORM_NOEMAIL", "Por favor ingrese una dirección de eMail válida." );
define( "LM_FORM_SHORTERNAME", "Por favor ingrese un nombre de suscriptor más corto. Gracias." );
define( "LM_FORM_NONAME", "Por favor ingrese un nombre de suscriptor. Gracias." );
define( "LM_SUBSCRIBE", "Suscribirse" );
define( "LM_UNSUBSCRIBE", "Desuscribirse" );
define( "LM_BUTTON_SUBMIT", "Enviar" );

/* Backend */
define( "LM_ERROR_NEWSLETTER_COULDNTBESENT", "Boletín no se pudo mandar!" );
define( "LM_NEWSLETTER_SENDTO_X_USERS", "Boletín fue enviado a {X} usuarios" );
define( "LM_IMPORT_USERS", "Importar suscriptores" );
define( "LM_EXPORT_USERS", "Exportar suscriptores" );
define( "LM_UPLAOD_FAILED", "Error al cargar" );
define( "LM_ERROR_PARSING_XML", "Error al procesar fichero XML" );
define( "LM_ERROR_NO_XML", "Por favor cargar sólo ficheros XML" );
define( "LM_ERROR_EMAIL_ALREADY_ONLIST", "La dirección de eMail ya está en la lista" );
define( "LM_SUCCESS_ON_IMPORT", "Se importaron exitosamente {X} suscriptores." );
define( "LM_IMPORT_FINISHED", "Importación terminada" );
define( "LM_ERROR_DELETING_FILE", "Error al eliminar fichero" );
define( "LM_DIR_NOT_WRITABLE", "No se puede escribir en el directorio ".$GLOBALS['mosConfig_cachepath'] );
define( "LM_ERROR_INVALID_EMAIL", "Dirección de eMail inválida" );
define( "LM_ERROR_EMPTY_EMAIL", "Dirección de eMail vacía" );
define( "LM_ERROR_EMPTY_FILE", "Error: fichero vacío" );
define( "LM_ERROR_ONLY_TEXT", "Sólo texto" );

define( "LM_SELECT_FILE", "Por favor seleccione un fichero" );
define( "LM_YOUR_XML_FILE", "Su fichero YaNC/Letterman XML a exportar" );
define( "LM_YOUR_CSV_FILE", "Fichero CSV a importar" );
define( "LM_POSITION_NAME", "Posición de la columna -Nombre-" );
define( "LM_NAME_COL", "Columna -Nombre-" );
define( "LM_POSITION_EMAIL", "Posición de la columna -eMail-" );
define( "LM_EMAIL_COL", "Columna -eMail-" );
define( "LM_STARTFROM", "Importar a partir del renglón..." );
define( "LM_STARTFROMLINE", "Empezar en el renglón" );
define( "LM_CSV_DELIMITER", "Caracter de separación CSV" );
define( "LM_CSV_DELIMITER_TIP", "CSV separador: , ; o tabulador" );

/* Newsletter Management */
define( "LM_NM", "Administrador de boletines" );
define( "LM_MESSAGE", "Mensaje" );
define( "LM_LAST_SENT", "Último envío" );
define( "LM_SEND_NOW", "Enviar ahora" );
define( "LM_CHECKED_OUT", "En revisión" );
define( "LM_NO_EXPIRY", "Final: Sin vencimiento" );
define( "LM_WARNING_SEND_NEWSLETTER", "¿Está seguro que quiere enviar el boletín?\\nAdvertencia: Si envia el boletín a una gran cantidad de direcciones, ¡este proceso puede demorar un tiempo!" );
define( "LM_SEND_NEWSLETTER", "Enviar boletín" );
define( "LM_SEND_TO_GROUP", "Enviar a grupo" );
define( "LM_MAIL_FROM", "Remitente" );
define( "LM_DISABLE_TIMEOUT", "Deshabilitar timeout" );
define( "LM_DISABLE_TIMEOUT_TIP", "Marcar para evitar que el script genere un error timeout. <br/><strong>¡No funciona en modo seguro!<strong>" );
define( "LM_REPLY_TO", "Respuestas a" );
define( "LM_MSG_HTML", "Mensaje (HTML-WYSIWYG)" );
define( "LM_MSG", "Mensaje (HTML-source)" );
define( "LM_TEXT_MSG", "mensaje alternativa (sólo texto)" );
define( "LM_NEWSLETTER_ITEM", "Boletín de noticias" );

/* Subscriber Management */
define( "LM_SUBSCRIBER", "Suscriptor" );
define( "LM_NEW_SUBSCRIBER", "Nuevo suscriptor" );
define( "LM_EDIT_SUBSCRIBER", "Editar suscriptor" );
define( "LM_SELECT_SUBSCRIBER", "Seleccionar un suscriptor" );
define( "LM_SUBSCRIBER_NAME", "Nombre del suscriptor" );
define( "LM_SUBSCRIBER_EMAIL", "eMail del suscriptor" );
define( "LM_SIGNUP_DATE", "Fecha de suscripción" );
define( "LM_CONFIRMED", "Confirmado" );
define( "LM_SUBSCRIBER_SAVED", "Los datos del suscriptor han sido guardados." );
define( "LM_SUBSCRIBERS_DELETED", "{X} suscripciones fueron exitosamente borradas." );
define( "LM_SUBSCRIBER_DELETED", "La suscripción fue exitosamente borrada." );

/* Frontend */
define( "LM_ALREADY_SUBSCRIBED", "Ya está suscripto a nuestro boletín de noticias." );
define( "LM_NOT_SUBSCRIBED", "Actualmente NO está suscripto a nuestro boletín de noticias." );
define( "LM_YOUR_DETAILS", "Sus detalles:" );
define( "LM_SUBSCRIBE_TO", "Suscribirse a nuestro boletín" );
define( "LM_UNSUBSCRIBE_FROM", "Desuscribirse de nuestro boletín" );
define( "LM_VALID_EMAIL_PLEASE", "¡Por favor ingrese una dirección de eMail válida!" );
define( "LM_SAME_EMAIL_TWICE", "La dirección de eMail ingresada ya está en la lista de suscriptores." );
define( "LM_ERROR_SENDING_SUBSCRIBE", "El mensaje de suscripción no pudo ser mandado:" );
define( "LM_SUCCESS_SUBSCRIBE", "La dirección de eMail ha sido agregado a la lista." );
define( "LM_RETURN_TO_NL", "Volver a los boletines de noticias" );
define( "LM_ERROR_UNSUBSCRIBE_OTHER_USER", "No puede borrar otros usuarios de la lista" );
define( "LM_ERROR_SENDING_UNSUBSCRIBE", "El mensaje de desuscripción no pudo ser mandado:" );
define( "LM_SUCCESS_UNSUBSCRIBE", "Su dirección de eMail ha sido removido de la lista." );
define( "LM_SUCCESS_CONFIRMATION", "Su cuenta ha sido confirmada exitosamente." );
define( "LM_ERROR_CONFIRM_ACC_NOTFOUND", "La cuenta asociada con su enlace de confirmación no se encontró." );

define( "LM_CONFIRMED_ACCOUNTS_ONLY", "¿Sólo cuentas confirmadas?" );
define( "LM_CONFIRMED_ACCOUNTS_ONLY_TIP", "Enviar boletín de noticias sólo a cuentas <strong>confirmadas</strong> de suscriptores. Suscriptores que no han confirmado su suscripción no recibirán el boletín de noticias." );

define( "LM_NAME_TAG_USAGE", "El comando <strong>[NAME]</strong> insertará el nombre del suscriptor en el boletín de noticias. <br/>Cuando se envía el boletín, [NAME] está siendo remplazado por el nombre del usuario/suscriptor." );

define( "LM_USERS_TO_SUBSCRIBERS", "Hacer suscriptores de usuarios" );
define( "LM_ASSIGN_USERS", "Asignar usuarios" );

/**
 * @since Letterman 1.2.0
 */
define( 'LM_SEND_LOG', 'Historia de boletines mandados' );
define( 'LM_NUMBER_OF_MAILS_SENT', '%s de %s correos ha sido enviado hasta el momento. ');
define( 'LM_SEND_NEXT_X_MAILS', 'Hunda el botón para mandar los próximos %s correos.');
define( 'LM_CHANGE_MAILS_PER_STEP', 'Cambie la cantidad de correos que se mandan en cada envió.');
define( 'LM_CONFIRM_ABORT_SENDING', '¿Esta seguro que quieres abortar de mandar este boletín?');
define( 'LM_MAILS_PER_STEP', '¿Cuantos correos quieres mandar de una?');
define( 'LM_CONFIRM_UNSUBSCRIBE', '¿Estas seguro que te quieres cancelar la subscripción de este boletín?');
/**
 * @since Letterman 1.2.1
 */
define( 'LM_COMPOSE_NEWSLETTER', 'Crear boletín usando artículos del contenido.');
define( 'LM_USABLE_TAGS', 'Comandos que puedes usar' );
define( 'LM_CONTENT_ITEMS', 'Articulos' );
define( 'LM_ADD_CONTENT', 'Agregar contenido o articulos' );
define( 'LM_ADD_CONTENT_TOOLTIP', 'Si escogiste un artículo de la lista, un comando será incluido en el área de texto. Este comando será convertido en el articulo completo (texto de introducción con imágenes) cuando le haces click a guardar.' );
define( 'LM_ATTACHMENTS', 'Archivo Incluido' );
define( 'LM_ATTACHMENTS_TOOLTIP', 'Puedes escoger uno o más archivos del directorio %s, que serán incluidos dentro del cuerpo del boletín.  Por favor no le de importancia al comando [ATTACHMENT ..]  - cuando mandes el boletín será removido.' );
define( 'LM_MULTISELECT', 'Escoja mas de un archivo usando Ctrl + Click del Mouse' );
?>