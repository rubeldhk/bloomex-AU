<?php

define( "LM_SUBSCRIBE_SUBJECT", "Sua inscrição de Newsletter em [mosConfig_live_site]" );
define( "LM_SUBSCRIBE_MESSAGE", 
"Olá [NAME],

sua inscrição foi recebida com sucesso para a nossa Newsletter em 
[mosConfig_live_site].
Obrigado!

Para confirmar sua inscrição, clieque no link a seguir ou copie / cole no seu navegador.

[LINK]

_________________________
[mosConfig_live_site]" );

define( "LM_UNSUBSCRIBE_SUBJECT", "Serviço de Newsletter em [mosConfig_live_site]: Cancelar Inscrição" );
define( "LM_UNSUBSCRIBE_MESSAGE", 
"Olá [NAME],

A inscrição de sua Newsletter em [mosConfig_live_site] foi cancelada.
Obrigado por usar o nosso serviço.

________________________
[mosConfig_live_site]" );

define( "LM_NEWSLETTER_FOOTER", 
"<br/><br/>___________________________________________________________<br/>
Você está recebendo essa mensagem porque se inscreveu no serviço de Newsletter em <br/>
[mosConfig_live_site].<br/>
Para cancelar a inscrição, clique aqui: [UNLINK]" );

/* Module */
define( "LM_FORM_NOEMAIL", "Por favor, forneça um endereço de e-mail válido." );
define( "LM_FORM_SHORTERNAME", "Favor fornecer um Nome para inscrição menor. Obrigado." );
define( "LM_FORM_NONAME", "Favor fornecer um Nome para inscrição. Obrigado." );
define( "LM_SUBSCRIBE", "Inscrever" );
define( "LM_UNSUBSCRIBE", "Cancelar inscrição" );
define( "LM_BUTTON_SUBMIT", "Ir!" );

/* Backend */
define( "LM_ERROR_NEWSLETTER_COULDNTBESENT", "A Newsletter não pode ser enviada!" );
define( "LM_NEWSLETTER_SENDTO_X_USERS", "Newsletter enviada para {X} usuários" );
define( "LM_IMPORT_USERS", "Importar Assinantes" );
define( "LM_EXPORT_USERS", "Exportar Assinantes" );
define( "LM_UPLAOD_FAILED", "Upload mal-sucecido" );
define( "LM_ERROR_PARSING_XML", "Erro lendo o arquivo XML" );
define( "LM_ERROR_NO_XML", "Favor fazer o upload somente de arquivos xml" );
define( "LM_ERROR_EMAIL_ALREADY_ONLIST", "O e-mail já está na lista" );
define( "LM_SUCCESS_ON_IMPORT", "Importados {X} Assinantes com sucesso." );
define( "LM_IMPORT_FINISHED", "Importação completa" );
define( "LM_ERROR_DELETING_FILE", "Falha ao apagar o arquivo" );
define( "LM_DIR_NOT_WRITABLE", "Não é possível escrever no diretório ".$GLOBALS['mosConfig_cachepath'] );
define( "LM_ERROR_INVALID_EMAIL", "E-mail inválido" );
define( "LM_ERROR_EMPTY_EMAIL", "E-mail vazio" );
define( "LM_ERROR_EMPTY_FILE", "Erro: Arquivo vazio" );
define( "LM_ERROR_ONLY_TEXT", "Somente texto" );

define( "LM_SELECT_FILE", "Favor selecionar um arquivo" );
define( "LM_YOUR_XML_FILE", "Seu arquivo YaNC/Letterman XML de exportação" );
define( "LM_YOUR_CSV_FILE", "Arquivo CSV de importação" );
define( "LM_POSITION_NAME", "Posição do -Nome- coluna" );
define( "LM_NAME_COL", "Coluna Nome" );
define( "LM_POSITION_EMAIL", "Posição do -Email- coluna" );
define( "LM_EMAIL_COL", "Coluna Email" );
define( "LM_STARTFROM", "Comçar a importar da linha..." );
define( "LM_STARTFROMLINE", "Começar da linha" );
define( "LM_CSV_DELIMITER", "Delimitador CSV" );
define( "LM_CSV_DELIMITER_TIP", "Delimitador CSV: , ; ou Tabulação" );

/* Newsletter Management */
define( "LM_NM", "Gerenciar Newsletter" );
define( "LM_MESSAGE", "Messagem" );
define( "LM_LAST_SENT", "Última enviada" );
define( "LM_SEND_NOW", "Enviar agora" );
define( "LM_CHECKED_OUT", "Verificada" );
define( "LM_NO_EXPIRY", "Terminado: Sem Expiração" );
define( "LM_WARNING_SEND_NEWSLETTER", "Você tem certeza que quer enviar a newsletter?\\nAviso: Se você estiver enviando e-mail para muitos usuários, isso pode demorar um pouco!" );
define( "LM_SEND_NEWSLETTER", "Enviar Newsletter" );
define( "LM_SEND_TO_GROUP", "Enviar para grupo" );
define( "LM_MAIL_FROM", "Mensagem de" );
define( "LM_DISABLE_TIMEOUT", "Desabilitar timeout" );
define( "LM_DISABLE_TIMEOUT_TIP", "Clique para prevenir o script de gerar um erro de timeout. <br/><strong>Não funciona em safe mode!<strong>" );
define( "LM_REPLY_TO", "Responder para" );
define( "LM_MSG_HTML", "Messagem (HTML-WYSIWYG)" );
define( "LM_MSG", "Messagem (HTML-fonte)" );
define( "LM_TEXT_MSG", "Texto alternativo - não-HTML" );
define( "LM_NEWSLETTER_ITEM", "Item da Newsletter" );

/* Subscriber Management */
define( "LM_SUBSCRIBER", "Assinante" );
define( "LM_NEW_SUBSCRIBER", "Nova inscrição" );
define( "LM_EDIT_SUBSCRIBER", "Editar inscrição" );
define( "LM_SELECT_SUBSCRIBER", "Selecionar um assinante" );
define( "LM_SUBSCRIBER_NAME", "Nome do Assinante" );
define( "LM_SUBSCRIBER_EMAIL", "E-mail do Assinante" );
define( "LM_SIGNUP_DATE", "Data da Assinatura" );
define( "LM_CONFIRMED", "Confirmado" );
define( "LM_SUBSCRIBER_SAVED", "A informação do assinante foi salva" );
define( "LM_SUBSCRIBERS_DELETED", "Você apagou com sucesso {X} Assinantes" );
define( "LM_SUBSCRIBER_DELETED", "O Assinante foi apagado com sucesso." );

/* Frontend */
define( "LM_ALREADY_SUBSCRIBED", "Você já está inscrito nas nossas Newsletters." );
define( "LM_NOT_SUBSCRIBED", "Você NÃO está inscrito nas nossas Newsletters." );
define( "LM_YOUR_DETAILS", "Seus detalhes:" );
define( "LM_SUBSCRIBE_TO", "Inscrever na nossa Newsletter" );
define( "LM_UNSUBSCRIBE_FROM", "Cancelar a inscrição da nossa Newsletter" );
define( "LM_VALID_EMAIL_PLEASE", "Favor fornecer um e-mail válido!" );
define( "LM_SAME_EMAIL_TWICE", "O e-mail que você forneceu já está inscrito!" );
define( "LM_ERROR_SENDING_SUBSCRIBE", "A mensagem de inscrição não pode ser enviada:" );
define( "LM_SUCCESS_SUBSCRIBE", "Seu e-mail foi adicionado à nossa Newsletter." );
define( "LM_RETURN_TO_NL", "Voltar às Newsletters" );
define( "LM_ERROR_UNSUBSCRIBE_OTHER_USER", "Desculpe, você não pode apagar outros Assinantes da lista" );
define( "LM_ERROR_SENDING_UNSUBSCRIBE", "A mensagem de cancelamento de inscrição não pode ser enviada:" );
define( "LM_SUCCESS_UNSUBSCRIBE", "Seu e-mail foi removido da nossa Newsletter" );
define( "LM_SUCCESS_CONFIRMATION", "Sua assinatura foi confirmada com sucesso" );
define( "LM_ERROR_CONFIRM_ACC_NOTFOUND", "A assinatura associada com a sua confirmação não foi encontrada." );

define( "LM_CONFIRMED_ACCOUNTS_ONLY", "Somente Assinaturas confirmadas?" );
define( "LM_CONFIRMED_ACCOUNTS_ONLY_TIP", "Enviar a Newsletter somente para <strong>Assinantes Confirmados</strong>. Assinantes que não confirmaram a inscrição não receberão a Newsletter." );

define( "LM_NAME_TAG_USAGE", "Você pode utilizar Marcação <strong>[NAME]</strong> no conteúdo da Newsletter para personalizá-la. <br/>Ao enviar a Newsletter, [NAME] será substituído pelo nome do Assinante." );

define( "LM_USERS_TO_SUBSCRIBERS", "Transformar Usuários do Mambo em Assinantes" );
define( "LM_ASSIGN_USERS", "Designar Usuários" );

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