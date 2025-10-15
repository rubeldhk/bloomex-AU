<?php
/**
 * FileName: spanish.php
 * Date: 29/05/2006
 * License: GNU General Public License
 * File Version #: 1
 * WATS Version #: 2.0.0.1
 * Author: Urano Gonzalez urano@uranogonzalez.com (www.uranogonzalez.com)
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","Ticket Nuevo");
DEFINE("_WATS_NAV_CATEGORY","Ir a Categoria");
DEFINE("_WATS_NAV_TICKET","Ir a Ticket");

// USER
DEFINE("_WATS_USER","Usuario");
DEFINE("_WATS_USER_SET","Usuarios");
DEFINE("_WATS_USER_NAME","Nombre");
DEFINE("_WATS_USER_USERNAME","Usuario");
DEFINE("_WATS_USER_GROUP","Grupo");
DEFINE("_WATS_USER_ORG","Organización");
DEFINE("_WATS_USER_ORG_SELECT","Teclea la organización");
DEFINE("_WATS_USER_EMAIL","Email");
DEFINE("_WATS_USER_NEW","Crear un nuevo usuario");
DEFINE("_WATS_USER_NEW_SELECT","Seleccionar un usuario");
DEFINE("_WATS_USER_NEW_CREATED","Crear usuario");
DEFINE("_WATS_USER_NEW_FAILED","Este usuario ya tiene una cuenta en el sistema de tickets");
DEFINE("_WATS_USER_DELETED","Usuario eliminado");
DEFINE("_WATS_USER_EDIT","Editar Usuario");
DEFINE("_WATS_USER_DELETE_REC","Eliminar los tickets del usuario (recomendado)");
DEFINE("_WATS_USER_DELETE_NOTREC","Eliminar los tickets del usuario y las respuestas a otros tockets (no recomendado)");
DEFINE("_WATS_USER_DELETE","Eliminar Usuario");
DEFINE("_WATS_USER_ADD","Agregar Usuario");
DEFINE("_WATS_USER_SELECT","Seleccionar Usuario");
DEFINE("_WATS_USER_SET_DESCRIPTION","Administrar Usuarios");
DEFINE("_WATS_USER_ADD_LIST","Los siguientes usuarios fueron agregados");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Seleccionar Grupo");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Categoría");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Mis Tickets Abiertos");
DEFINE("_WATS_TICKETS_USER_CLOSED","Mis Tickets Cerrados");
DEFINE("_WATS_TICKETS_OPEN","Tickets Abiertos");
DEFINE("_WATS_TICKETS_CLOSED","Tickets Cerrados");
DEFINE("_WATS_TICKETS_DEAD","Tickets finalizados");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Ver todos los tickets abiertos");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Ver todos los tickets cerrados");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Ver todos los tickets finalizados");
DEFINE("_WATS_TICKETS_NAME","Nombre del Ticket");
DEFINE("_WATS_TICKETS_POSTS","Respuestas");
DEFINE("_WATS_TICKETS_DATETIME","Ultima respuesta");
DEFINE("_WATS_TICKETS_PAGES","Páginas");
DEFINE("_WATS_TICKETS_SUBMIT","Enviar un nuevo ticket");
DEFINE("_WATS_TICKETS_SUBMITING","Enviando ticket");
DEFINE("_WATS_TICKETS_SUBMITTED","Ticket enviado exitosamente");
DEFINE("_WATS_TICKETS_DESC","Descripción");
DEFINE("_WATS_TICKETS_CLOSE","Cerrar Ticket");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket cerrado");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket eliminado");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket purgado");
DEFINE("_WATS_TICKETS_NONE","no se encontraron tickets");
DEFINE("_WATS_TICKETS_FIRSTPOST","Iniciado: ");
DEFINE("_WATS_TICKETS_LASTPOST","enviado por: ");
DEFINE("_WATS_TICKETS_REPLY","Responder");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Responder y Cerrar");
DEFINE("_WATS_TICKETS_ASSIGN","Asignar ticket");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Asignado a");
DEFINE("_WATS_TICKETS_ID","Ticket ID");
DEFINE("_WATS_TICKETS_REOPEN","Rebrir");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Por favor da el motivo por el que quieres reabrir este ticket");
DEFINE("_WATS_TICKETS_STATE_ALL","Todos");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Personales");
DEFINE("_WATS_TICKETS_STATE_OPEN","Abiertos");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Cerrados");
DEFINE("_WATS_TICKETS_STATE_DEAD","Finalizados");
DEFINE("_WATS_TICKETS_PURGE","Purgar tickets finalizados en ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticket enviado por: ");
DEFINE("_WATS_MAIL_REPLY","Respuesta enviada por: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","¿Eliminarme?");
DEFINE("_WATS_MISC_GO","Ir");

//ERRORS
DEFINE("_WATS_ERROR","Ocurrió un error");
DEFINE("_WATS_ERROR_ACCESS","No tienes suficientes privilegios para completar esta tarea");
DEFINE("_WATS_ERROR_NOUSER","No estás autorizado para ver este recurso.<br>Necesitas registrarte o pedir acceso a un administrador.");
DEFINE("_WATS_ERROR_NODATA","No has completado los datos correctamente, por favor intentalo nuevamente.");
DEFINE("_WATS_ERROR_NOT_FOUND","Item no encontrado");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Usa las 'tags' mostradas abajo para formatear tu texto:</i></p>
<table width='100%'border='0'cellspacing='5'cellpadding='0'>
  <tr valign='top'>
    <td><b>negrita</b></td>
    <td><b>[b]</b>negrita<b>[/b]</b></td>
  </tr>
  <tr valign='top'>
    <td><i>itálica</i> </td>
    <td><b>[i]</b>itálica<b>[/i]</b></td>
  </tr>
  <tr valign='top'>
    <td> <u>subrayado</u></td>
    <td><b>[u]</b>subrayado<b>[/u]</b></td>
  </tr>
  <tr valign='top'>
    <td>Código</td>
    <td><b>[code]</b>value='123';<b>[/code] </b></td>
  </tr>
  <tr valign='top'>
    <td><font size='+2'>Tamaño</font></td>
    <td><b>[size=25]</b>GRANDE<b>[/size]</b></td>
  </tr>
  <tr valign='top'>
    <td><font color='#FF0000'>ROJO</font></td>
    <td><b>[color=red]</b>ROJO<b> [/color]</b></td>
  </tr>
  <tr valign='top'>
    <td style='cursor: pointer; color: #0000FF;'><u>weblink </u></td>
    <td><b>[url=http://webamoeba.co.uk]webamoeba[/url]</b></td>
  </tr>
  <tr valign='top'>
    <td style='cursor: pointer; color: #0000FF;'><u>fred@bloggs.com</u></td>
    <td><b>[email=bbcode@webamoeba.co.uk]mail[/email]</b></td>
  </tr>
</table> ");
?>
