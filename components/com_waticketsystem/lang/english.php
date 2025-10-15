<?php
/**
 * FileName: english.php
 * Date: 06/06/2006
 * License: GNU General Public License
 * File Version #: 5
 * WATS Version #: 2.0.0.1
 * Author: James Kennard james@webamoeba.com (www.webamoeba.co.uk)
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","New Ticket");
DEFINE("_WATS_NAV_CATEGORY","Support Categories");
DEFINE("_WATS_NAV_TICKET","Ticket Number");

// USER
DEFINE("_WATS_USER","User");
DEFINE("_WATS_USER_SET","Users");
DEFINE("_WATS_USER_NAME","Name");
DEFINE("_WATS_USER_USERNAME","Username");
DEFINE("_WATS_USER_GROUP","Group");
DEFINE("_WATS_USER_ORG","Organisation");
DEFINE("_WATS_USER_ORG_SELECT","Enter organisation");
DEFINE("_WATS_USER_EMAIL","Email");
DEFINE("_WATS_USER_NEW","Create new user");
DEFINE("_WATS_USER_NEW_SELECT","Select a user");
DEFINE("_WATS_USER_NEW_CREATED","Created user");
DEFINE("_WATS_USER_NEW_FAILED","This user already has a ticket support account");
DEFINE("_WATS_USER_DELETED","User deleted");
DEFINE("_WATS_USER_EDIT","Edit User");
DEFINE("_WATS_USER_DELETE_REC","Remove users tickets (recommended)");
DEFINE("_WATS_USER_DELETE_NOTREC","Remove users tickets and replies to other tickets (not recommended)");
DEFINE("_WATS_USER_DELETE","Delete User");
DEFINE("_WATS_USER_ADD","Add User");
DEFINE("_WATS_USER_SELECT","Select User");
DEFINE("_WATS_USER_SET_DESCRIPTION","Manage Users");
DEFINE("_WATS_USER_ADD_LIST","The following users were added");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Select Group");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Category");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","My Open Tickets");
DEFINE("_WATS_TICKETS_USER_CLOSED","My Closed Tickets");
DEFINE("_WATS_TICKETS_OPEN","Open Tickets");
DEFINE("_WATS_TICKETS_CLOSED","Closed Tickets");
DEFINE("_WATS_TICKETS_DEAD","Dead Tickets");
DEFINE("_WATS_TICKETS_OPEN_VIEW","View all open tickets");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","View all closed tickets");
DEFINE("_WATS_TICKETS_DEAD_VIEW","View all dead tickets");
DEFINE("_WATS_TICKETS_NAME","Ticket Name");
DEFINE("_WATS_TICKETS_POSTS","Posts");
DEFINE("_WATS_TICKETS_DATETIME","Last Post");
DEFINE("_WATS_TICKETS_PAGES","Pages");
DEFINE("_WATS_TICKETS_SUBMIT","Submit a new ticket");
DEFINE("_WATS_TICKETS_SUBMITING","Submitting ticket");
DEFINE("_WATS_TICKETS_SUBMITTED","Ticket submitted successfully");
DEFINE("_WATS_TICKETS_DESC","Description");
DEFINE("_WATS_TICKETS_CLOSE","Close Ticket");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket closed");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket deleted");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket purged");
DEFINE("_WATS_TICKETS_NONE","no tickets found");
DEFINE("_WATS_TICKETS_FIRSTPOST","started: ");
DEFINE("_WATS_TICKETS_LASTPOST","posted by: ");
DEFINE("_WATS_TICKETS_REPLY","Reply");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Reply and Close");
DEFINE("_WATS_TICKETS_ASSIGN","Assign ticket");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Assigned to");
DEFINE("_WATS_TICKETS_ID","Ticket ID");
DEFINE("_WATS_TICKETS_REOPEN","Reopen");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Please give a reason why you want to reopen this ticket");
DEFINE("_WATS_TICKETS_STATE_ALL","All");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Personal");
DEFINE("_WATS_TICKETS_STATE_OPEN","Open");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Closed");
DEFINE("_WATS_TICKETS_STATE_DEAD","Dead");
DEFINE("_WATS_TICKETS_PURGE","Purge dead tickets in ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticket submitted by: ");
DEFINE("_WATS_MAIL_REPLY","Reply submitted by: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Delete me?");
DEFINE("_WATS_MISC_GO","Go");

//ERRORS
DEFINE("_WATS_ERROR","An error has occurred");
DEFINE("_WATS_ERROR_ACCESS","You do NOT have sufficient access rights to complete this task");
DEFINE("_WATS_ERROR_NOUSER","You are not authorized to view this resource.<br>You need to login or request access from an administrator.");
DEFINE("_WATS_ERROR_NODATA","You have not correctly filled in the form, please try again.");
DEFINE("_WATS_ERROR_NOT_FOUND","Item not found");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Use the 'tags' shown below to format your text:</i></p> 
<table width='100%'border='0'cellspacing='5'cellpadding='0'> 
  <tr valign='top'> 
    <td><b>bold</b></td> 
    <td><b>[b]</b>bold<b>[/b]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td><i>italic</i> </td> 
    <td><b>[i]</b>italic<b>[/i]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td> <u>underline</u></td> 
    <td><b>[u]</b>underline<b>[/u]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td>code</td> 
    <td><b>[code]</b>value='123';<b>[/code] </b></td> 
  </tr> 
  <tr valign='top'> 
    <td><font size='+2'>SIZE</font></td> 
    <td><b>[size=25]</b>HUGE<b>[/size]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td><font color='#FF0000'>RED</font></td> 
    <td><b>[color=red]</b>RED<b> [/color]</b></td> 
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
