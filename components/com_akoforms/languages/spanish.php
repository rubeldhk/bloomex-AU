<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 fibal
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Javier Galeote aka funmaking
* Homepage   : www.mambohispano.org
**/

defined( '_VALID_MOS' ) or die( 'El acceso directo a esta direccion no esta permitida.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'S&iacute;';
  var $AKF_NO                  = 'No';
  var $AKF_PUBLISHED           = 'Publicado';
  var $AKF_PUBLISHING          = 'Publicaci&oacute;n';
  var $AKF_STARTPUBLISHING     = 'Comienzo de la publicaci&oacute;n:';
  var $AKF_FINISHPUBLISHING    = 'Finalizaci&oacute;n de la publicaci&oacute;n:';
  var $AKF_PUBPENDING          = 'Publicado, pero est&aacute; Pendiente';
  var $AKF_PUBCURRENT          = 'Publicado y en Funcionamiento';
  var $AKF_PUBEXPIRED          = 'Publicado, pero ha Expirado';
  var $AKF_UNPUBLISHED         = 'No Publicado';
  var $AKF_REORDER             = 'Reordenar';
  var $AKF_ORDERING            = 'Orden:';
  var $AKF_TITLE               = 'T&iacute;tulo:';
  var $AKF_DESCRIPTION         = 'Descripci&oacute;n:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Editar Idioma';
  var $AKF_PATH                = 'Directorio:';
  var $AKF_FILEWRITEABLE       = 'Por favor, atenci&oacute;n: El archivo debe ser escribible para guardar los cambios.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Gesti&oacute;n de Formulario';
  var $AKF_FORMTITLE           = 'T&iacute;tulo del Formulario';
  var $AKF_SENDMAIL            = 'Enviar Email';
  var $AKF_STOREDB             = 'Base de datos';
  var $AKF_FINISHING           = 'Finalizaci&oacute;n';
  var $AKF_FORMPAGE            = 'P&aacute;gina de Formulario';
  var $AKF_REDIRECTION         = 'Redirecci&oacute;n';
  var $AKF_SHOWRESULT          = 'Mostrar Resultado';
  var $AKF_NUMBEROFFIELDS      = 'No. de Campos';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'A&ntilde;adir Formulario';
  var $AKF_EDITFORM            = 'Editar Formulario';
  var $AKF_HEADER              = 'Cabecera';
  var $AKF_HANDLING            = 'Direcci&oacute;n';
  var $AKF_SENDBYEMAIL         = 'Enviar por Email:';
  var $AKF_EMAILS              = 'Emails:';
  var $AKF_SAVETODATABASE      = 'Salvar a la Base de Datos:';
  var $AKF_ENDPAGETITLE        = 'T&iacute;tulo de Final de P&aacute;gina:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Descripci&oacute;n del Final de la P&aacute;gina:';
  var $AKF_FORMTARGET          = 'Destino del Formulario:';
  var $AKF_TARGETURL           = 'URL redireccionada:';
  var $AKF_SHOWENDPAGE         = 'Mostrar el Final de la P&aacute;gina';
  var $AKF_REDIRECTTOURL       = 'Redireccionar a una URL';
  var $AKF_NEWFORMSLAST        = 'Los nuevos Formularios van al &uacute;ltimo lugar.';
  var $AKF_SHOWFORMRESULT      = 'Mostrar Resultado del Formulario:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Gestor de Campos';
  var $AKF_FIELDTITLE          = 'T&iacute;tulo del Campo';
  var $AKF_FIELDTYPE           = 'Tipo de Campo';
  var $AKF_FIELDREQUIRED       = 'Campo requerido';
  var $AKF_SELECTFORM          = 'Seleccionar Formulario';
  var $AKF_ALLFORMS            = '- Todos los Formularios';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'A&ntilde;adir Campo';
  var $AKF_EDITFIELD           = 'Editar Campo';
  var $AKF_GENERAL             = 'General';
  var $AKF_FORM                = 'Formulario:';
  var $AKF_TYPE                = 'Tipo:';
  var $AKF_VALUE               = 'Valor:';
  var $AKF_STYLE               = 'Estilo:';
  var $AKF_REQUIRED            = 'Requerido:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Editar Preferencias';
  var $AKF_MAILSUBJECT         = 'T&iacute;tulo del email:';
  var $AKF_SENDERNAME          = 'Nombre del remitente:';
  var $AKF_SENDEREMAIL         = 'Email del remitente:';
  var $AKF_SETTINGSSAVED       = 'Las preferencias han sido guardadas.';
  var $AKF_SETTINGSNOTSAVED    = 'Las preferencias no han podido ser guardadas.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Formularios Almacenados';
  var $AKF_NUMBEROFENTRIES     = 'No. de Entradas';
  var $AKF_STOREDDATA          = 'Datos Almacenados';
  var $AKF_STOREDIP            = 'IP del remitente';
  var $AKF_STOREDDATE          = 'Fecha de envio';


  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Por favor rellene todos los Campos requeridos:';
  var $AKF_REQUIREDFIELD       = 'Campo requerido';
  var $AKF_BUTTONSEND          = 'Enviar';
  var $AKF_BUTTONCLEAR         = 'Limpiar';
  var $AKF_FORMEXPIRED         = '&iexcl;Este Formulario ha expirado!';
  var $AKF_FORMPENDING         = '&iexcl;Este Formulario est&aacute; actualmente pendiente!';
  var $AKF_MAILERHELLO         = 'Hola';
  var $AKF_MAILERHEADER        = 'Un usuario de su sitio ha usado un formulario para enviarle a usted lo siguiente:';
  var $AKF_MAILERFOOTER        = 'Sinceremente';
  var $AKF_MAILERERROR         = 'Ha habido un error de correo, mientras se enviaba a:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = 'Asigne este campo a alg&uacute;n formulario usando el men&uacute; desplegable.';
  var $AKF_HELPTITLE           = 'Introduzca un peque&ntilde;o t&iacute;tulo para su formulario/campo en este campo.';
  var $AKF_HELPDESCRIPTION     = 'Puede usar este campo para insertar una descripci&oacute;n en html para su formulario/campo.';
  var $AKF_HELPTYPE            = 'Escoja entre todos los formularios estandar y la variedad de campos predefinidos. Si necesita personalizar el men&uacute; desplegable, contacte con Arthur Konze.';
  var $AKF_HELPVALUE           = 'El campo valor puede ser usado para asignar un valor predefinido a un campo. Para crear men&uacute;s desplegables (DROPDOWN) solo introduzca cuaquier opci&oacute;n del men&uacute; desplegable en l&iacute;neas separadas. Lo mismo para aplicar a un Bot&oacute;n de Opci&oacute;n (RADIOBUTTON) y a las cajas de selecci&oacute;n (SELECTBOXES). En la Casilla de Verificaci&oacute;n (CHECKBOX) se mostrar&aacute; un texto descriptivo debajo de la caja de texto.';
  var $AKF_HELPSTYLE           = 'Use la opci&oacute;n de estilo para a&ntilde;adir definiciones y CSS al campo. Por ejemplo, para hacer un campo de 200 pixels de ancho, introduzca: width:200px;';
  var $AKF_HELPREQUIRED        = 'Escoja cuando el campo debe ser rellenado por el usuario o no.';
  var $AKF_HELPORDERING        = 'Use el orden para escoger la posici&oacute;n.';
  var $AKF_HELPSTARTFINISH     = 'Escoja un inicio y un final de la fecha de publicaci&oacute;n usando estas dos opciones.';
  var $AKF_HELPSENDMAIL        = 'Escoja si el resultado del formulario ser&aacute; enviado por email o no.';
  var $AKF_HELPEMAILS          = 'Introduzca su direcci&oacute;n de email aqu&iacute;. Puede introducir varios separ&aacute;ndolos con una coma (,).';
  var $AKF_HELPSAVEDB          = 'Escoja si el resultado del formulario ser&aacute; guardado en la base datos.';
  var $AKF_HELPTARGET          = 'Escoja si la p&aacute;gina resultante ser&aacute; mostrada o si ser&aacute; redireccionada bajo una URL.';
  var $AKF_HELPTARGETURL       = 'Introduzca una redirecci&oacute;n URL aqu&iacute;. Puede ser cualquier URL, hasta otro formulario.';
  var $AKF_HELPSUBJECT         = 'Introduzca un t&iacute;tulo para todos los emails salientes dentro de este campo.';
  var $AKF_HELPSENDER          = 'El nombre introducido aqu&iacute; ser&aacute; usado como remitente de los emails salientes.';
  var $AKF_HELPEMAIL           = 'Una direcci&oacute;n de email v&aacute;lida para el remitente de los emails';
  var $AKF_HELPRESULT          = 'Decida si quiere mostrar el resultado del formulario al usuario.';

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

$AKFLANG =new  akfLanguage();

?>