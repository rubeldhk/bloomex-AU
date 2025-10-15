<?php

/**
 * FileName: brazilian.php
 * Date: 09/10/2006
 * License: GNU General Public License
 * File Version #: 1
 * WATS Version #: 2.0.0.5
 * Author: Mauro Machado mauro@machado.eti.br (www.machado.eti.br)
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","Novo Pedido");
DEFINE("_WATS_NAV_CATEGORY","Ir para a Categoria");
DEFINE("_WATS_NAV_TICKET","Ir para o Pedido");

// USER
DEFINE("_WATS_USER","Usuário");
DEFINE("_WATS_USER_SET","Usuários");
DEFINE("_WATS_USER_NAME","Nome");
DEFINE("_WATS_USER_USERNAME","Nome do Usuário");
DEFINE("_WATS_USER_GROUP","Grupo");
DEFINE("_WATS_USER_ORG","Organização");
DEFINE("_WATS_USER_ORG_SELECT","Dê entrada da organização");
DEFINE("_WATS_USER_EMAIL","E-mail");
DEFINE("_WATS_USER_NEW","Criar novo usuário");
DEFINE("_WATS_USER_NEW_SELECT","Selecione um usuário");
DEFINE("_WATS_USER_NEW_CREATED","Usuário Criado");
DEFINE("_WATS_USER_NEW_FAILED","Este usuário já tem uma conta de suporte por pedido criada");
DEFINE("_WATS_USER_DELETED","Usuário Apagado");
DEFINE("_WATS_USER_EDIT","Editar Usuário");
DEFINE("_WATS_USER_DELETE_REC","Remover pedidos do usuário (recomendado)");
DEFINE("_WATS_USER_DELETE_NOTREC","Remove os pedidos do usuário e responde aos outros pedidos (não recomendado)");
DEFINE("_WATS_USER_DELETE","Apagar Usuário");
DEFINE("_WATS_USER_ADD","Adicionar Usuário");
DEFINE("_WATS_USER_SELECT","Seleccionar Usuário");
DEFINE("_WATS_USER_SET_DESCRIPTION","Gerenciar Usuários");
DEFINE("_WATS_USER_ADD_LIST","Os seguintes usuários foram adicionados");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Selecionar Grupo");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Categoria");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Os Meus Pedidos Abertos");
DEFINE("_WATS_TICKETS_USER_CLOSED","Os Meus Pedidos Fechados");
DEFINE("_WATS_TICKETS_OPEN","Pedidos Abertos");
DEFINE("_WATS_TICKETS_CLOSED","Pedidos Fechados");
DEFINE("_WATS_TICKETS_DEAD","Pedidos Arquivados");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Ver todos os pedidos abertos");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Ver todos os pedidos fechados");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Ver todos os pedidos arquivados");
DEFINE("_WATS_TICKETS_NAME","Nome do Pedido ");
DEFINE("_WATS_TICKETS_POSTS","Entradas ");
DEFINE("_WATS_TICKETS_DATETIME","Última Entrada ");
DEFINE("_WATS_TICKETS_PAGES","Páginas");
DEFINE("_WATS_TICKETS_SUBMIT","Submeter um novo pedido");
DEFINE("_WATS_TICKETS_SUBMITING","A submeter o pedido");
DEFINE("_WATS_TICKETS_SUBMITTED","O pedido foi submetido com sucesso");
DEFINE("_WATS_TICKETS_DESC","Descrição");
DEFINE("_WATS_TICKETS_CLOSE","Fechar o Pedido");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Pedido Fechado");
DEFINE("_WATS_TICKETS_DELETED_COMP","Pedido apagado");
DEFINE("_WATS_TICKETS_PURGED_COMP","Pedido removido");
DEFINE("_WATS_TICKETS_NONE","não foram encontrados pedidos");
DEFINE("_WATS_TICKETS_FIRSTPOST","iniciado: ");
DEFINE("_WATS_TICKETS_LASTPOST","submetido por: ");
DEFINE("_WATS_TICKETS_REPLY","Responder");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Responder e Fechar");
DEFINE("_WATS_TICKETS_ASSIGN","Atribuir pedido");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Atribuido a");
DEFINE("_WATS_TICKETS_ID","ID do Pedido");
DEFINE("_WATS_TICKETS_REOPEN","Reabrir");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Por favor indique o motivo pelo qual quer reabrir este pedido");
DEFINE("_WATS_TICKETS_STATE_ALL","Todos");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Pessoal");
DEFINE("_WATS_TICKETS_STATE_OPEN","Abertos");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Fechados");
DEFINE("_WATS_TICKETS_STATE_DEAD","Arquivados");
DEFINE("_WATS_TICKETS_PURGE","Apagar pedidos arquivados em ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Pedido submetido por: ");
DEFINE("_WATS_MAIL_REPLY","Resposta submetida por: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Apagar?");
DEFINE("_WATS_MISC_GO","Ir");

//ERRORS
DEFINE("_WATS_ERROR","Ocorreu um erro");
DEFINE("_WATS_ERROR_ACCESS","NÃO tem direitos suficientes para completar esta tarefa");
DEFINE("_WATS_ERROR_NOUSER","Não está autorizado a visualizar este recurso.<br>É necessário autenticar na Intranet com nome de usuário e senha da rede.");
DEFINE("_WATS_ERROR_NODATA","O formulário não foi preenchido corretamente, por favor tente novamente.");
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