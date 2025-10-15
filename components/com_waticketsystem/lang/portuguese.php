<?php

/**
 * FileName: portuguese.php
 * Date: 29/05/2006
 * License: GNU General Public License
 * File Version #: 1
 * WATS Version #: 2.0.0.1
 * Author: Jorge Rosado info@jrpi.pt (www.jrpi.pt)
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","Novo Ticket");
DEFINE("_WATS_NAV_CATEGORY","Ir para a Categoria");
DEFINE("_WATS_NAV_TICKET","Ir para o Ticket");

// USER
DEFINE("_WATS_USER","Utilizador");
DEFINE("_WATS_USER_SET","Utilizadores");
DEFINE("_WATS_USER_NAME","Nome");
DEFINE("_WATS_USER_USERNAME","Nome do Utilizador");
DEFINE("_WATS_USER_GROUP","Grupo");
DEFINE("_WATS_USER_ORG","Organização");
DEFINE("_WATS_USER_ORG_SELECT","Dê entrada da organização");
DEFINE("_WATS_USER_EMAIL","E-mail");
DEFINE("_WATS_USER_NEW","Criar novo utilizador");
DEFINE("_WATS_USER_NEW_SELECT","Selecione um utilizador");
DEFINE("_WATS_USER_NEW_CREATED","Utilizador Criado");
DEFINE("_WATS_USER_NEW_FAILED","Este utilizador já tem uma conta de suporte por ticket criada");
DEFINE("_WATS_USER_DELETED","Utilizador Apagado");
DEFINE("_WATS_USER_EDIT","Editar Utilizador");
DEFINE("_WATS_USER_DELETE_REC","Remover tickets do utilizador (recomendado)");
DEFINE("_WATS_USER_DELETE_NOTREC","Remove os tickets do utilizador e responde aos outros tickets (não recomendado)");
DEFINE("_WATS_USER_DELETE","Apagar Utilizador");
DEFINE("_WATS_USER_ADD","Adicionar Utilizador");
DEFINE("_WATS_USER_SELECT","Seleccionar Utilizador");
DEFINE("_WATS_USER_SET_DESCRIPTION","Gerir Utilizadores");
DEFINE("_WATS_USER_ADD_LIST","Os seguintes utilizadores foram adicionados");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Seleccionar Grupo");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Categoria");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Os Meus Tickets Abertos");
DEFINE("_WATS_TICKETS_USER_CLOSED","Os Meus Tickets Fechados");
DEFINE("_WATS_TICKETS_OPEN","Tickets Abertos");
DEFINE("_WATS_TICKETS_CLOSED","Tickets Fechados");
DEFINE("_WATS_TICKETS_DEAD","Tickets Arquivados");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Ver todos os tickets abertos");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Ver todos os tickets fechados");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Ver todos os tickets arquivados");
DEFINE("_WATS_TICKETS_NAME","Nome do Ticket ");
DEFINE("_WATS_TICKETS_POSTS","Entradas ");
DEFINE("_WATS_TICKETS_DATETIME","Última Entrada ");
DEFINE("_WATS_TICKETS_PAGES","Páginas");
DEFINE("_WATS_TICKETS_SUBMIT","Submeter um novo ticket");
DEFINE("_WATS_TICKETS_SUBMITING","A submeter o ticket");
DEFINE("_WATS_TICKETS_SUBMITTED","O ticket foi submetido com sucesso");
DEFINE("_WATS_TICKETS_DESC","Descrição");
DEFINE("_WATS_TICKETS_CLOSE","Fechar o Ticket");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket Fechado");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket apagado");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket removido");
DEFINE("_WATS_TICKETS_NONE","não foram encontrados tickets");
DEFINE("_WATS_TICKETS_FIRSTPOST","iniciado: ");
DEFINE("_WATS_TICKETS_LASTPOST","submetido por: ");
DEFINE("_WATS_TICKETS_REPLY","Responder");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Responder e Fechar");
DEFINE("_WATS_TICKETS_ASSIGN","Atribuir ticket");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Atribuido a");
DEFINE("_WATS_TICKETS_ID","ID do Ticket");
DEFINE("_WATS_TICKETS_REOPEN","Reabrir");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Por favor indeique o motivo pelo qual quer reabrir este ticket");
DEFINE("_WATS_TICKETS_STATE_ALL","Todos");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Pessoal");
DEFINE("_WATS_TICKETS_STATE_OPEN","Abertos");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Fechados");
DEFINE("_WATS_TICKETS_STATE_DEAD","Arquivados");
DEFINE("_WATS_TICKETS_PURGE","Apagar tickets arquivados em ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticket submetido por: ");
DEFINE("_WATS_MAIL_REPLY","Resposta submetida por: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Apagar?");
DEFINE("_WATS_MISC_GO","Ir");

//ERRORS
DEFINE("_WATS_ERROR","Ocorreu um erro");
DEFINE("_WATS_ERROR_ACCESS","NÃO tem direitos suficientes para completar esta tarefa");
DEFINE("_WATS_ERROR_NOUSER","Não está autorizado a visualizar este recurso.<br>Tem de iniciar sessão or requerer acesso a um administrador.");
DEFINE("_WATS_ERROR_NODATA","O formulário não foi preenchido correctamente, por favor tente novamente.");
DEFINE("_WATS_ERROR_NOT_FOUND","Item não encontrado");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Utilize as 'tags' abaixo indicadas para formatar o seu texto:</i></p> 
<table width='100%'border='0'cellspacing='5'cellpadding='0'> 
  <tr valign='top'> 
    <td><b>negrito</b></td> 
    <td><b>[b]</b>negrito<b>[/b]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td><i>itálico</i> </td> 
    <td><b>[i]</b>itálico<b>[/i]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td> <u>sublinhado</u></td> 
    <td><b>[u]</b>sublinhado<b>[/u]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td>code</td> 
    <td><b>[code]</b>value='123';<b>[/code] </b></td> 
  </tr> 
  <tr valign='top'> 
    <td><font size='+2'>TAMANHO</font></td> 
    <td><b>[size=25]</b>GRANDE<b>[/size]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td><font color='#FF0000'>Vermelho</font></td> 
    <td><b>[color=red]</b>Vermelho<b> [/color]</b></td> 
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