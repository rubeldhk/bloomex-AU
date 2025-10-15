<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Hugo Carvalho
* Homepage   : www.metpage.org
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Sim';
  var $AKF_NO                  = 'Nao';
  var $AKF_PUBLISHED           = 'Publicado';
  var $AKF_PUBLISHING          = 'Em Publicacao';
  var $AKF_STARTPUBLISHING     = 'Comecar Publicacao:';
  var $AKF_FINISHPUBLISHING    = 'Acabar Publicacao:';
  var $AKF_PUBPENDING          = 'Publicado, mas esta pendente';
  var $AKF_PUBCURRENT          = 'Publicado e Corrente';
  var $AKF_PUBEXPIRED          = 'Publicado, mas expirou';
  var $AKF_UNPUBLISHED         = 'Nao Publicado';
  var $AKF_REORDER             = 'Reordenar';
  var $AKF_ORDERING            = 'Ordenar:';
  var $AKF_TITLE               = 'Titulo:';
  var $AKF_DESCRIPTION         = 'Descricao:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Editar Linguagem';
  var $AKF_PATH                = 'Caminho:';
  var $AKF_FILEWRITEABLE       = 'Nota: O ficheiro tem de ter permissoes de escrita para poder guardar as suas alteracoes.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Gestor de Formularios';
  var $AKF_FORMTITLE           = 'Titulo do Formulario';
  var $AKF_SENDMAIL            = 'Enviar Email';
  var $AKF_STOREDB             = 'Gravar na BD';
  var $AKF_FINISHING           = 'Rodape';
  var $AKF_FORMPAGE            = 'Pagina do Formulario';
  var $AKF_REDIRECTION         = 'Redirecionamento';
  var $AKF_SHOWRESULT          = 'Mostrar Resultado';
  var $AKF_NUMBEROFFIELDS      = 'Numero de Campos';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Adicionar Formulario';
  var $AKF_EDITFORM            = 'Editar Formulario';
  var $AKF_HEADER              = 'Cabecalho';
  var $AKF_HANDLING            = 'Manipulacao';
  var $AKF_SENDBYEMAIL         = 'Enviar por Email:';
  var $AKF_EMAILS              = 'Emails:';
  var $AKF_SAVETODATABASE      = 'Guardar na BD:';
  var $AKF_ENDPAGETITLE        = 'Titulo Final da Pagina:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Descricao do Final da Pagina:';
  var $AKF_FORMTARGET          = 'Destino do Formulario:';
  var $AKF_TARGETURL           = 'URL de Redirecionamento:';
  var $AKF_SHOWENDPAGE         = 'Mostrar Pagina Final';
  var $AKF_REDIRECTTOURL       = 'Redirecionar para o URL';
  var $AKF_NEWFORMSLAST        = 'Os novos formularios vao para ultimo lugar.';
  var $AKF_SHOWFORMRESULT      = 'Mostrar o resultado do formulario:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Gestor de Campos';
  var $AKF_FIELDTITLE          = 'Titulo do Campo';
  var $AKF_FIELDTYPE           = 'Tipo do Campo';
  var $AKF_FIELDREQUIRED       = 'Campo Obrigatorio?';
  var $AKF_SELECTFORM          = 'Seleccione o Formulario';
  var $AKF_ALLFORMS            = '- Todos os Formularios';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Adicionar Campo';
  var $AKF_EDITFIELD           = 'Editar Campo';
  var $AKF_GENERAL             = 'Geral';
  var $AKF_FORM                = 'Formulario:';
  var $AKF_TYPE                = 'Tipo:';
  var $AKF_VALUE               = 'Valor:';
  var $AKF_STYLE               = 'Estilo:';
  var $AKF_REQUIRED            = 'Obrigatorio:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Editar Definicoes';
  var $AKF_MAILSUBJECT         = 'Assunto do Email:';
  var $AKF_SENDERNAME          = 'Nome do Remetente:';
  var $AKF_SENDEREMAIL         = 'Email do Remetente:';
  var $AKF_SETTINGSSAVED       = 'As definicoes foram guardadas.';
  var $AKF_SETTINGSNOTSAVED    = 'As definicoes nao puderam ser guardadas.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Formularios Guardados';
  var $AKF_NUMBEROFENTRIES     = 'Numero de Entradas';
  var $AKF_STOREDDATA          = 'Entradas Guardadas';
  var $AKF_STOREDIP            = 'IP do remetente';
  var $AKF_STOREDDATE          = 'Date de envio';

  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Por favor preencha todos os campos obrigatorios:';
  var $AKF_REQUIREDFIELD       = 'Campo obrigatorio';
  var $AKF_BUTTONSEND          = 'Enviar';
  var $AKF_BUTTONCLEAR         = 'Limpar';
  var $AKF_FORMEXPIRED         = 'Este formulario expirou!';
  var $AKF_FORMPENDING         = 'Este formulario esta pendente!';
  var $AKF_MAILERHELLO         = 'Ol ';
  var $AKF_MAILERHEADER        = 'Um utilizador do seu site utilizou um formulario para lhe submeter os seguintes dados:';
  var $AKF_MAILERFOOTER        = 'Cumprimentos';
  var $AKF_MAILERERROR         = 'Ocorreu um erro no envio do email quanto foi enviado para:';

  // Help - Forms & Fields
  var $AKF_HELPFORM            = 'Atribua um campo a um formulario utilizando o menu dropdown.';
  var $AKF_HELPTITLE           = 'Introduza um titulo curto para o seu formulario/campo neste campo.';
  var $AKF_HELPDESCRIPTION     = 'Pode utilizar este campo para inserir uma descricao HTML para o seu campo/formulario.';
  var $AKF_HELPTYPE            = 'Escolha de quase todos os campos do formul rio padrÆo e de uma variedade de campos predefinidos. Se deseja dropdowns costumizados, contacte Arthur Konze.';
  var $AKF_HELPVALUE           = 'O campo de valor pode ser usado para atribuir um valor predefinido a um campo. Para criar menus DROPDOWN introduza apenas uma op‡ao por linha para o menu dropdown.  O mesmo aplica-se aos RADIOBUTTON e aos SELECTBOXES.  Na CHECKBOX ser  mostrado como texto descritivo atr s da caixa.';
  var $AKF_HELPSTYLE           = 'Utilize a opcao de estilo para adicionar definicoes CSS ao campo. Por exemplo: para fazer um campo com 200 pixels de largura utilize: width:200px;';
  var $AKF_HELPREQUIRED        = 'Escolha se o campo e de preenchimento obrigatorio pelo utilizador ou nao.';
  var $AKF_HELPORDERING        = 'Utilize a ordenacao para escolher a posicao.';
  var $AKF_HELPSTARTFINISH     = 'Escolha uma data de inicio e de fim para a publicacao utilizando estas duas opcoes.';
  var $AKF_HELPSENDMAIL        = 'Escolha se os resultados do formulario devem-lhe ser enviados por email ou nao.';
  var $AKF_HELPEMAILS          = 'Introduza o endereco de email aqui. Pode colocar varios enderecos separados por virgulas (,).';
  var $AKF_HELPSAVEDB          = 'Escolha se o resultado do formulario deve ser guardado na base de dados.';
  var $AKF_HELPTARGET          = 'Escolha se a pagina acima devera ser mostrada ou se deve ser redirecionado para o endereco(URL) abaixo introduzido.';
  var $AKF_HELPTARGETURL       = 'Introduza o endereco(URL) de redirecionamento. Pode ser qualquer endereco(URL) ou ate outro formulario.';
  var $AKF_HELPSUBJECT         = 'Introduza o assunto para todos os email a enviar neste campo.';
  var $AKF_HELPSENDER          = 'The name entered here will be used as sender for outgoing emails.';
  var $AKF_HELPEMAIL           = 'Um endereco valido de email do remetente para os emails que serao enviados.';
  var $AKF_HELPRESULT          = 'Decida se deseja mostrar os resultados do formulario ao utilizador.';

  // NEW in version 1.01
  var $AKF_MAILCHARSET         = 'Email Charset:';
  var $AKF_HELPCHARSET         = 'Choose a charset for your outgoing emails from the dropdown menu.';
  var $AKF_MAILTABLEFIELD      = 'Field';
  var $AKF_MAILTABLEDATA       = 'Data';
  var $AKF_SELECTFIELD         = 'Select Field';

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