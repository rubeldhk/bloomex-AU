<?php

define( "LM_SUBSCRIBE_SUBJECT", "Subscrição de newsletter no [mosConfig_live_site]" );
define( "LM_SUBSCRIBE_MESSAGE", 
"Olá [NAME],

Subscreveu com sucesso a nossa Newsletter no 
[mosConfig_live_site].
Obrigado!

Para confirmar a sua subscrição, por favor clique no link abaixo ou copie o mesmo e cole no seu browser.

[LINK]

_________________________
[mosConfig_live_site]" );

define( "LM_UNSUBSCRIBE_SUBJECT", "Serviço de Newsletter no [mosConfig_live_site]: Unsubscription" );
define( "LM_UNSUBSCRIBE_MESSAGE", 
"Olá [NAME],

Anulou com sucesso a sua subscrição da Newsletter no [mosConfig_live_site].
Obrigado por ter usado o nosso serviço.

________________________
[mosConfig_live_site]" );

define( "LM_NEWSLETTER_FOOTER", 
"<br/><br/>___________________________________________________________<br/>
Está recebendo este e-mail porque subscreveu <br/>
o nosso serviço de Newsletter no [mosConfig_live_site].<br/>
para anular a subscrição clique em: [UNLINK]" );

/* Module */
define( "LM_FORM_NOEMAIL", "Por favor introduza um e-mail válido." );
define( "LM_FORM_SHORTERNAME", "Por favor introduza um nome de subscrição curto. Obrigado." );
define( "LM_FORM_NONAME", "Introduza um nome para subscrever. Obrigado." );
define( "LM_SUBSCRIBE", "Subscrever" );
define( "LM_UNSUBSCRIBE", "Cancelar Subscrição" );
define( "LM_BUTTON_SUBMIT", "Enviar!" );

/* Backend */
define( "LM_ERROR_NEWSLETTER_COULDNTBESENT", "A Newsletter não pode ser enviada!" );
define( "LM_NEWSLETTER_SENDTO_X_USERS", "Newsletter enviada a {X} membros" );
define( "LM_IMPORT_USERS", "Importar Subscritores" );
define( "LM_EXPORT_USERS", "Exportar Subscritores" );
define( "LM_UPLAOD_FAILED", "Falhou o Upload" );
define( "LM_ERROR_PARSING_XML", "Error Parsing the XML File" );
define( "LM_ERROR_NO_XML", "Por favor faça upload somente de ficheiros xml" );
define( "LM_ERROR_EMAIL_ALREADY_ONLIST", "Já existe este email na nossa lista" );
define( "LM_SUCCESS_ON_IMPORT", "Importou com sucesso {X} Subscritores." );
define( "LM_IMPORT_FINISHED", "Importação terminada" );
define( "LM_ERROR_DELETING_FILE", "Falhou a eliminação do ficheiro" );
define( "LM_DIR_NOT_WRITABLE", "Não pode escrever no Directório ".$GLOBALS['mosConfig_cachepath'] );
define( "LM_ERROR_INVALID_EMAIL", "Endereço de Email inválido" );
define( "LM_ERROR_EMPTY_EMAIL", "Campo endereço de Email vazio" );
define( "LM_ERROR_EMPTY_FILE", "Erro: Ficheiro vazio" );
define( "LM_ERROR_ONLY_TEXT", "Texto somente" );

define( "LM_SELECT_FILE", "Por favor selecione um ficheiro" );
define( "LM_YOUR_XML_FILE", "O seu YaNC/Letterman ficheiro de exportação XML" );
define( "LM_YOUR_CSV_FILE", "Ficheiro de importação CSV" );
define( "LM_POSITION_NAME", "Posição da coluna -Nome-" );
define( "LM_NAME_COL", "Coluna Nome" );
define( "LM_POSITION_EMAIL", "Posição da coluna -Email-" );
define( "LM_EMAIL_COL", "Coluna Email" );
define( "LM_STARTFROM", "Comece a importar ficheiro a partir da linha..." );
define( "LM_STARTFROMLINE", "Comece a partir da linha" );
define( "LM_CSV_DELIMITER", "Delimitador CSV" );
define( "LM_CSV_DELIMITER_TIP", "Delimitador CSV: , ; ou Tabela" );

/* Newsletter Management */
define( "LM_NM", "Gestor Newsletter" );
define( "LM_MESSAGE", "Mensagem" );
define( "LM_LAST_SENT", "Último envio" );
define( "LM_SEND_NOW", "Enviar agora" );
define( "LM_CHECKED_OUT", "Autor" );
define( "LM_NO_EXPIRY", "Termina: Não Expira" );
define( "LM_WARNING_SEND_NEWSLETTER", "Tem a certeza que deseja enviar a newsletter?\\nWarning: se enviar o mail para um grande grupo de membros o mesmo vai levar algum tempo!" );
define( "LM_SEND_NEWSLETTER", "Enviar Newsletter" );
define( "LM_SEND_TO_GROUP", "Enviar para o grupo" );
define( "LM_MAIL_FROM", "Mail a partir de" );
define( "LM_DISABLE_TIMEOUT", "Disable timeout" );
define( "LM_DISABLE_TIMEOUT_TIP", "Assinale para prevenir que seja gerado um script de erro. <br/><strong>Não funciona em modo seguro!<strong>" );
define( "LM_REPLY_TO", "Responder a" );
define( "LM_MSG_HTML", "Mensagem (HTML-WYSIWYG)" );
define( "LM_MSG", "Mensagem (HTML-source)" );
define( "LM_TEXT_MSG", "Mensagem de texto alternativa" );
define( "LM_NEWSLETTER_ITEM", "Newsletter Item" );

/* Subscriber Management */
define( "LM_SUBSCRIBER", "Subscritor" );
define( "LM_NEW_SUBSCRIBER", "Novo Subscritor" );
define( "LM_EDIT_SUBSCRIBER", "Editar Subscritor" );
define( "LM_SELECT_SUBSCRIBER", "Selecionar Subscritor" );
define( "LM_SUBSCRIBER_NAME", "Nome do Subscritor" );
define( "LM_SUBSCRIBER_EMAIL", "E-mail do Subscritor" );
define( "LM_SIGNUP_DATE", "Data de Subscrição" );
define( "LM_CONFIRMED", "Confirmado" );
define( "LM_SUBSCRIBER_SAVED", "A informação do subscritor foi salva" );
define( "LM_SUBSCRIBERS_DELETED", "Eliminou com sucesso {X} Subscritores" );
define( "LM_SUBSCRIBER_DELETED", "O subscritor foi eliminado com sucesso." );

/* Frontend */
define( "LM_ALREADY_SUBSCRIBED", "Já se encontra inscrito no nosso sistema de Newsletter." );
define( "LM_NOT_SUBSCRIBED", "Actualmente não se encontra inscrito no nosso sistema de Newsletter." );
define( "LM_YOUR_DETAILS", "Seus detalhes:" );
define( "LM_SUBSCRIBE_TO", "Subscreva a nossa Newsletter" );
define( "LM_UNSUBSCRIBE_FROM", "Cancele a subscrição da nossa Newsletter" );
define( "LM_VALID_EMAIL_PLEASE", "Por favor introduza um endereço de email válido!" );
define( "LM_SAME_EMAIL_TWICE", "O endereço de E-mail que introduziu já se encontra na nossa lista de subscritores!" );
define( "LM_ERROR_SENDING_SUBSCRIBE", "A mensagem de subscrição não pode ser enviada:" );
define( "LM_SUCCESS_SUBSCRIBE", "O seu endereço de email foi adicionado á nossa Newsletter." );
define( "LM_RETURN_TO_NL", "Voltar a Newsletter" );
define( "LM_ERROR_UNSUBSCRIBE_OTHER_USER", "Desculpe, mas não pode eliminar membros a partir da lista" );
define( "LM_ERROR_SENDING_UNSUBSCRIBE", "A mensagem de cancelamento não pode ser enviada:" );
define( "LM_SUCCESS_UNSUBSCRIBE", "O seu endereço de email foi removido da nossa Newsletter" );
define( "LM_SUCCESS_CONFIRMATION", "A sua conta foi confirmada com sucesso" );
define( "LM_ERROR_CONFIRM_ACC_NOTFOUND", "A conta associada ao link de confirmação não foi encontrada." );

define( "LM_CONFIRMED_ACCOUNTS_ONLY", "Somente contas confirmadas?" );
define( "LM_CONFIRMED_ACCOUNTS_ONLY_TIP", "Enviar Newsletter somente a <strong>confirmed</strong> Subscritor de conta. Subscritores que não confirmaram a sua subscrição não recebem a Newsletter." );

define( "LM_NAME_TAG_USAGE", "Pode usar a Tag <strong>[NAME]</strong> no conteudo da Newsletter para enviar Newsletters personalizadas. <br/>Quando enviar a Newsletter, [NAME] é substituido pelo nome do membro/subscritor." );

define( "LM_USERS_TO_SUBSCRIBERS", "Transforme membros em subscritores" );
define( "LM_ASSIGN_USERS", "Submeta membros" );

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