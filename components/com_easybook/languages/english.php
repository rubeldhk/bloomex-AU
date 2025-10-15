<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
**/

// Install language definitions
DEFINE("_GUESTBOOK_INSTALL_NAME","EasyBook - A Guestbook Component for Joomla!");
DEFINE("_GUESTBOOK_INSTALL_PROCESS","Installation process:");
DEFINE("_GUESTBOOK_INSTALL_FINISHED","Installation finished.");
DEFINE("_GUESTBOOK_INSTALL_IMAGEOKAY","<font color='green'>FINISHED:</font> Image for the menu entry has been created.");
DEFINE("_GUESTBOOK_INSTALL_IMAGEFAILED","<font color='red'>ERROR:</font> Image for the menu entry could not be created.");
DEFINE("_GUESTBOOK_INSTALL_LICENSE"," Released under the terms and conditions of the <a target='_blank' href='http://www.gnu.org/copyleft/gpl.html'>GNU General Public License</a>.");

// Header language definitions
DEFINE("_GUESTBOOK_SIGN","Sign guestbook");
DEFINE("_GUESTBOOK_VIEW","View guestbook");
DEFINE("_GUESTBOOK_AFTERENTRIE","guestbook entry");
DEFINE("_GUESTBOOK_AFTERENTRIES","guestbook entries");
DEFINE("_GUESTBOOK_PAGES","Pages:");
DEFINE("_GUESTBOOK_ONLYREGISTERED","Only registered users are allowed to sign the guestbook.<br />Please login or register.");

// Guestbook language definitions
DEFINE("_GUESTBOOK_NAME","Name");
DEFINE("_GUESTBOOK_ENTRY","Entry");
DEFINE("_GUESTBOOK_FROM","from");
DEFINE("_GUESTBOOK_SIGNEDON","Signed on:");
DEFINE("_GUESTBOOK_NOLOCATION","");
DEFINE("_GUESTBOOK_ENTRYOFFLINE","Entry is offline");

// Form language definitions
DEFINE("_GUESTBOOK_VALIDATE","Name is missing, please enter a name.");
DEFINE("_GUESTBOOK_VALIDATE2","Email is either wrong or missing, please verify.");
DEFINE("_GUESTBOOK_VALIDATE3","Please enter a message.");
DEFINE("_GUESTBOOK_VALIDATE4","SPAM PROTECTION: Your message contains one or more words from the bad words list. Please correct your entry!");
DEFINE("_GUESTBOOK_IPADRESS","IP Adress:");
DEFINE("_GUESTBOOK_ENTERNAME","Your Name:");
DEFINE("_GUESTBOOK_ENTERMAIL","Your E-Mail:");
DEFINE("_GUESTBOOK_SHOWMAIL","Show E-Mail:");
DEFINE("_GUESTBOOK_ENTERPAGE","Your Homepage:");
DEFINE("_GUESTBOOK_ENTERLOCA","Your Location:");
DEFINE("_GUESTBOOK_ENTERVOTE","Your Vote:");
DEFINE("_GUESTBOOK_ENTERICQ","Your ICQ:");
DEFINE("_GUESTBOOK_ENTERAIM","Your AIM:");
DEFINE("_GUESTBOOK_ENTERMSN","Your MSN:");
DEFINE("_GUESTBOOK_ENTERYAH","Your YAH:");
DEFINE("_GUESTBOOK_ENTERSKYPE","Your Skype:");
DEFINE("_GUESTBOOK_PLEASEVOTE","Please vote");
DEFINE("_GUESTBOOK_VOTEGOOD","Very Good");
DEFINE("_GUESTBOOK_VOTEBAD","Bad");
DEFINE("_GUESTBOOK_ENTERTEXT","Type your message:");
DEFINE("_GUESTBOOK_SENDFORM","Send");
DEFINE("_GUESTBOOK_CLEARFORM","Clear");

// Save language definitions
DEFINE("_GUESTBOOK_SAVED","Entry saved to guestbook.");
DEFINE("_GUESTBOOK_REFUSED","Entry was NOT saved to guestbook -&gt; the antispamming system has refused your entry");

// Admin language definitions
DEFINE("_GUESTBOOK_DELENTRY","Delete entry");
DEFINE("_GUESTBOOK_DELMESSAGE","The entry has been canceled.");
DEFINE("_GUESTBOOK_COMMENTVALIDATE","Please enter the comment.");
DEFINE("_GUESTBOOK_ADMINSCOMMENT","Admins Comment");
DEFINE("_GUESTBOOK_COMMENTSAVED","Your comment has been saved.");
DEFINE("_GUESTBOOK_ADMIN","Admin");
DEFINE("_GUESTBOOK_AEDIT","Edit");
DEFINE("_GUESTBOOK_ACOMMENT","Comment");
DEFINE("_GUESTBOOK_ADELETE","Delete");
DEFINE("_GUESTBOOK_ASAVE","Save");
DEFINE("_GUESTBOOK_ABACK","Back");

// BBCode definitions
DEFINE("_GUESTBOOK_BBCODEBUTTONURL","web address");
DEFINE("_GUESTBOOK_BBCODEBUTTONMAIL","email adress");
DEFINE("_GUESTBOOK_BBCODEBUTTONIMAGE","load image for web");
DEFINE("_GUESTBOOK_BBCODEBUTTONBOLD","bold text");
DEFINE("_GUESTBOOK_BBCODEBUTTONITALIC","italic text");
DEFINE("_GUESTBOOK_BBCODEBUTTONUNDERLINE","underline text");
DEFINE("_GUESTBOOK_BBCODEBUTTONQUOTE","Quote");
DEFINE("_GUESTBOOK_BBCODEBUTTONCODE","Code");
DEFINE("_GUESTBOOK_BBCODEBUTTONLISTOPEN","open list");
DEFINE("_GUESTBOOK_BBCODEBUTTONLISTCLOSE","close list");
DEFINE("_GUESTBOOK_BBCODEBUTTONLISTITEM","list entry");
DEFINE("_GUESTBOOK_BBCODEURL1","Enter the URL here.");
DEFINE("_GUESTBOOK_BBCODEURL2","Enter the web page title.");
DEFINE("_GUESTBOOK_BBCODEURL3","web page title ");
DEFINE("_GUESTBOOK_BBCODEMAIL","Enter the e-mail address.");
DEFINE("_GUESTBOOK_BBCODEBOLD","Enter the text which should appear bold.");
DEFINE("_GUESTBOOK_BBCODEITALIC","Enter the text which should appear italic.");
DEFINE("_GUESTBOOK_BBCODEUNDERLINE","Enter the text that should be underlined.");
DEFINE("_GUESTBOOK_BBCODEIMAGE","Enter the URL of the picture you want to show.");
DEFINE("_GUESTBOOK_BBCODELIST","Enter the new list element. A group of list items must always be surrounded by an open-list and close-list element,");

// Email language definitions
DEFINE("_GUESTBOOK_ADMINMAILHEADER","New guestbook entry");
DEFINE("_GUESTBOOK_ADMINMAIL","Hello Admin,\n\nA user has posted a new message to your guestbook at $mosConfig_live_site:\n");
DEFINE("_GUESTBOOK_USERMAILHEADER","Thanks for signing the guestbook! ");
DEFINE("_GUESTBOOK_USERMAIL","Hello User,\n\nMany thanks for your comment in the guestbook at $mosConfig_live_site:\n");
DEFINE("_GUESTBOOK_MAILFOOTER","Please do not respond to this message as it is automatically generated, and for information purpose only.\n");

// New in version 3.1
DEFINE("_GUESTBOOK_REQUIREDFIELD","Required field");

// Mailnotification definitions
DEFINE("_GUESTBOOK_MAILNOTIFICATIONPUBLISH","Publish or unpublish entry");

// Mailservercheck definitions
DEFINE("_GUESTBOOK_BADMAILSERVER","Problem with email address, please enter a valid email address!");

// Wordfilter definitions
DEFINE("_GUESTBOOK_BADWORDFOUND","Banned word detected thus no entry");
DEFINE("_GUESTBOOK_BADWORDFRONTENDFOUND","A banned word was found, the following entry was deleted: ID ");
DEFINE("_GUESTBOOK_BADWORDFRONTENDFOUNDSUBJECT","Banned word in the guest book");
DEFINE("_GUESTBOOK_BADWORDFRONTENDFOUNDWORD","Banned word");

// Spamfix definitions
DEFINE("_GUESTBOOK_RELOADCODE","Reload Code:");
DEFINE("_GUESTBOOK_RELOADDESC","Reloads the code if it's unreadable.");
DEFINE("_GUESTBOOK_ENTERCODE","Code:");
DEFINE("_GUESTBOOK_CODEDESCRIPTION","Enter the code shown");
DEFINE("_GUESTBOOK_CODEIMAGE","Enter this code in the left field");
DEFINE("_GUESTBOOK_CODEWRONG","Wrong security code!");
DEFINE("_GUESTBOOK_SIDERROR","Session error, please enable cookies");

// Modified by Thomas Mader
DEFINE("_GUESTBOOK_PUBLISH","Publish");
DEFINE("_GUESTBOOK_UNPUBLISH","Unpublish");
DEFINE("_GUESTBOOK_PUBLISHTEXT","Saved changes");
DEFINE("_GUESTBOOK_PUBLISHBACK","Back");
DEFINE("_GUESTBOOK_ACCESSDENIED","No authorizations!");

// Backendlanguage definitions - View entries
DEFINE("_GUESTBOOK_ADMIN_SEARCH","Search:");
DEFINE("_GUESTBOOK_ADMIN_AUTHOR","Author");
DEFINE("_GUESTBOOK_ADMIN_MESSAGE","Message");
DEFINE("_GUESTBOOK_ADMIN_DATE","Date");
DEFINE("_GUESTBOOK_ADMIN_RATE","Rating");
DEFINE("_GUESTBOOK_ADMIN_COMMENT","Comment");
DEFINE("_GUESTBOOK_ADMIN_PUBLISHED","Published");
DEFINE("_GUESTBOOK_ADMIN_DISPLAY","View");

// Backendlanguage definitions - Credits
DEFINE("_GUESTBOOK_ADMIN_CREDITS","<p><b>Programm</b><br>
If you have any wishes or want to report a bug please contact us at easybook@easy-joomla.org or report a bug in the bug tracker at http://forge.joomla.org/sf/projects/easybook
		</p>
        <p>
        <b>Warranties</b><br />
        We do not guarantee functionality or fitness for any purpose.<br /><br />
        <b>Modifications:</b><br />
        This guestbook was modified and elaborated by A.Raji <a target='_blank' href='http://www.filmanleitungen.de'>Filmanleitungen</a>, D.Jardin <a target='_blank' href='http://www.snipersister.de'>SniperSister</a>, S.Langsch <a target='_blank' href='http://www.langsch-edv.de'>Langsch EDV</a> and <a target='_blank' href='http://www.nonstop-design.de'>Cedric May</a>!<br /><br />
        <b>Involved persons:</b><br />
        <li>Achim Raji (aka cybergurk - jug-berlin) <a target='_blank' href='http://www.filmanleitungen.de'>Filmanleitungen</a> Idea, realisation and programming </li>
        <li>David Jardin (aka SniperSister - JUG-Cologne) <a target='_blank' href='http://www.snipersister.de'>SniperSister</a> Idea, realisation and programming</li>
        <li>Siegmund Langsch <a target='_blank' href='http://www.langsch-edv.de'>Langsch EDV</a> Programming</li>
        <li><a target='_blank' href='http://www.nonstop-design.de/'>Cedric May</a> (JUG-Cologne) Proof reading, Betatester, Design</li>
        </p>
        <p></p><table class='adminform' width='100%' style='margin:0px;'>
		             <tr><td style='vertical-align:top !important; padding:5px; padding:10px; width:30%;' rowspan='1'>
		             <div style='border-style:solid; border-color:#CCCCCC; padding:5px;'>
		             <h2>Donate Project EasyBook</h2>
		             <ul><li>Support the advancement and free availability of EasyBook please with a small donation - thank you!</ul></li>

							<div style='text-align:center;'>
								<a href='https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=easybook%40easy-joomla%2eorg&item_name=Donate%20to%20Project%20EasyBook&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8' title='Donate Projekt EasyBook' target='_blank'>
									<img src='components/com_easybook/images/donate-button.png' alt='Donate Project EasyBook' title='Donate  Projekt EasyBook' border='0'/>
								</a>
							</div></div></td></tr>
				</table>");

// Backendlanguage definitions - Edit language
DEFINE("_GUESTBOOK_ADMIN_PATH","Path");
DEFINE("_GUESTBOOK_ADMIN_WRITEABLE_NOTICE","Note: The file must be writable to save your changes.");

// Backendlanguage definitions - Overview
DEFINE("_GUESTBOOK_ADMIN_MIGRATORTOOLLINK","Migration Tool");
DEFINE("_GUESTBOOK_ADMIN_VIEWENTRYSLINK","View Entries");
DEFINE("_GUESTBOOK_ADMIN_EDITCONFIGLINK","Edit Configuration");
DEFINE("_GUESTBOOK_ADMIN_EDITLANGUAGELINK","Edit Language");
DEFINE("_GUESTBOOK_ADMIN_WORDSLINK","Edit Wordlist");
DEFINE("_GUESTBOOK_ADMIN_ABOUTLINK","About EasyBook");

// Backendlanguage definitions - Migratortool
DEFINE("_GUESTBOOK_ADMIN_MIGRATION_OK","Entry with the following No has been migrated: ");
DEFINE("_GUESTBOOK_ADMIN_MIGRATION_ERROR","Entry with the following No could not be migrated: ");
DEFINE("_GUESTBOOK_ADMIN_MIGRATION_YAH","Import entries from Akobook BWP with field for Yahoo messenger");
DEFINE("_GUESTBOOK_ADMIN_MIGRATION","Import entries from Akobook BWP RC1 or Akobook plus or Akobook 3.42 without field for Yahoo messenger ");

// Backendlanguage definitions - SaveFiles
DEFINE("_GUESTBOOK_ADMIN_NOTWRITABLE","File is not writable!");
DEFINE("_GUESTBOOK_ADMIN_CONFIGNOTWRITABLE","Configuration file is not writable!");
DEFINE("_GUESTBOOK_ADMIN_LANGSAVED","Language saved");
DEFINE("_GUESTBOOK_ADMIN_WORDSAVED","Words saved");
DEFINE("_GUESTBOOK_ADMIN_WARNING","Warning...");
DEFINE("_GUESTBOOK_ADMIN_LANGNOTWRITABLE","The script requires write access (CHMOD 766) to update the language file");
DEFINE("_GUESTBOOK_ADMIN_WORDFILTERNOTWRITABLE","The script requires write access (CHMOD 766) to update the word filters");

// Backendlanguage definitions - Configuration
DEFINE("_GUESTBOOK_ADMIN_CONFIGSAVED","Configuration saved");
DEFINE("_GUESTBOOK_ADMIN_BACKENDPAGE","Backend");
DEFINE("_GUESTBOOK_ADMIN_FRONTENDPAGE","Frontend");
DEFINE("_GUESTBOOK_ADMIN_SECURITYPAGE","Security");
DEFINE("_GUESTBOOK_ADMIN_FIELDSPAGE","Fields");
DEFINE("_GUESTBOOK_ADMIN_TOOLSPAGE","Tools");
DEFINE("_GUESTBOOK_ADMIN_YES","Yes");
DEFINE("_GUESTBOOK_ADMIN_NO","No");
DEFINE("_GUESTBOOK_ADMIN_OFFLINE","Guestbook offline:");
DEFINE("_GUESTBOOK_ADMIN_OFFLINEDESC","Switch the guestbook frontend offline.");
DEFINE("_GUESTBOOK_ADMIN_OFFLINEMSG","Offline message:");
DEFINE("_GUESTBOOK_ADMIN_OFFLINEMSGDESC","Message presented to the frontend users if guestbook is offline.");
DEFINE("_GUESTBOOK_ADMIN_AUTOPUBLISH","Auto-publish entries");
DEFINE("_GUESTBOOK_ADMIN_AUTOPUBLISHDESC","Auto-publish new guestbook entries.");
DEFINE("_GUESTBOOK_ADMIN_MAILADMIN","Notify webmaster");
DEFINE("_GUESTBOOK_ADMIN_MAILADMINDESC","Notify webmaster when new entries are posted.");
DEFINE("_GUESTBOOK_ADMIN_MAILADMINTOOLTIP","This enables you to modify entries directly by email, to either comment or publish them, or even delete or withhold them if you prefer!");
DEFINE("_GUESTBOOK_ADMIN_ADMINMAIL","Webmasters email");
DEFINE("_GUESTBOOK_ADMIN_ADMINMAILDESC","Email address to send notifications to.");
DEFINE("_GUESTBOOK_ADMIN_THANKUSER","Thank you, user ");
DEFINE("_GUESTBOOK_ADMIN_THANKUSERDESC","Send -Thank You- mail to the user.");
DEFINE("_GUESTBOOK_ADMIN_WORDWRAP","Automatic word wrap");
DEFINE("_GUESTBOOK_ADMIN_WORDWRAPDESC","Enforced word wrap to prevent excessive length of words.");
DEFINE("_GUESTBOOK_ADMIN_WORDWRAPCOUNT","Automatic word wrap after");
DEFINE("_GUESTBOOK_ADMIN_WORDWRAPCOUNTDESC","Number of characters before a word wrap is added.");
DEFINE("_GUESTBOOK_ADMIN_ENTRIESPERPAGE","Entries per Page");
DEFINE("_GUESTBOOK_ADMIN_ENTRIESPERPAGEDESC","Number of entries shown per page.");
DEFINE("_GUESTBOOK_ADMIN_SORTING","Guestbook sorting");
DEFINE("_GUESTBOOK_ADMIN_SORTINGDESCRIPTION","Sorting of new guestbook entries.");
DEFINE("_GUESTBOOK_ADMIN_SORTINGDESC","New entries first");
DEFINE("_GUESTBOOK_ADMIN_SORTINGASC","New entries last");
DEFINE("_GUESTBOOK_ADMIN_MAXRATE","Highest rating");
DEFINE("_GUESTBOOK_ADMIN_MAXRATEDESC","Highest possible website rating.");
DEFINE("_GUESTBOOK_ADMIN_ALLOWENTRY","Allow Entries");
DEFINE("_GUESTBOOK_ADMIN_ALLOWENTRYDESC","Allow users to write new entries.");
DEFINE("_GUESTBOOK_ADMIN_ANONENTRY","Anonymous Entries");
DEFINE("_GUESTBOOK_ADMIN_ANONENTRYDESC","Allow unregistered users to write entries.");
DEFINE("_GUESTBOOK_ADMIN_BBCODE","Allow BBCode");
DEFINE("_GUESTBOOK_ADMIN_BBCODEDESC","Allow the use of simple BBCode in entries.");
DEFINE("_GUESTBOOK_ADMIN_LINKSUPPORT","Allow links to other internet pages");
DEFINE("_GUESTBOOK_ADMIN_LINKSUPPORTDESC","Allow the use of links to other internet pages in the entries.");
DEFINE("_GUESTBOOK_ADMIN_MAILSUPPORT","Allow email addresses");
DEFINE("_GUESTBOOK_ADMIN_MAILSUPPORTDESC","Allow mailto links in entries.");
DEFINE("_GUESTBOOK_ADMIN_PICSUPPORT","Allow Pictures");
DEFINE("_GUESTBOOK_ADMIN_PICSUPPORTDESC","Allow the use of pictures in entries.");
DEFINE("_GUESTBOOK_ADMIN_SMILIESUPPORT","Allow Smilies");
DEFINE("_GUESTBOOK_ADMIN_SMILIESUPPORTDESC","Allow the use of smilies in entries.");
DEFINE("_GUESTBOOK_ADMIN_SHOWFOOTER","Show Copyright");
DEFINE("_GUESTBOOK_ADMIN_TIP","Tip");
DEFINE("_GUESTBOOK_ADMIN_EXAMPLE","Example");
DEFINE("_GUESTBOOK_ADMIN_USEABILITY","Useability");
DEFINE("_GUESTBOOK_ADMIN_SHOWFOOTERDESC","Display the copyright notice in Easybooks footer. <b>Please pay attention to the tooltip</b>");
DEFINE("_GUESTBOOK_ADMIN_SHOWFOOTERTOOLTIP","If you do not want to show the &quot;powered by ...&quot; link, please sponsor the <b>further development</b> and <b>free avalaibility</b> by making a small donation to the copyright hoders - or choose another place for the link to the credits, for example on a separate page or by using a tool like <b>mosCredits</b> - thank you!");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXTYPE","Spamfix picture type");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXTYPEDESC","Picture type when showing the Spamfix picture.");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIX","Show Spamfix");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXDESC","Shows a captcha image which protects against spambots");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXBGCOLOUR","Spamfix background colour");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXBGCOLOURDESC","Background colour of spamfix picture. Please enter a hex value with six digits without leading #.");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXCODECOLOUR","Spamfix Code color");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXCODECOLOURDESC","Code color of spamfix picture. Please enter a hex value with six digits without leading #.");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXLINECOLOUR","Spamfix line color");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXLINECOLOURDESC","Line color of spamfix picture. Please enter a hex value with six digits without leading #.");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXBORDERCOLOUR","Spamfix border color");
DEFINE("_GUESTBOOK_ADMIN_SPAMFIXBORDERCOLOURDESC","Spamfix picture border colour. Please enter a hex value with six digits without leading #.");
DEFINE("_GUESTBOOK_ADMIN_WORDFILTER","Use the word filter");
DEFINE("_GUESTBOOK_ADMIN_WORDFILTEREDIT","*Edit the word filter*");
DEFINE("_GUESTBOOK_ADMIN_WORDFILTERDESC","Use the word filter for words to be excluded in new entries!");
DEFINE("_GUESTBOOK_ADMIN_WORDFILTERFRONT","Use the word filter in the frontend ");
DEFINE("_GUESTBOOK_ADMIN_WORDFILTERFRONTDESC","Use the word filter for words to be excluded while accessing the entries");
DEFINE("_GUESTBOOK_ADMIN_WORDFILTERMAIL","Send email on word filter violation;");
DEFINE("_GUESTBOOK_ADMIN_WORDFILTERMAILDESC","Notify you by mail if a banned word is found in an entry.");
DEFINE("_GUESTBOOK_ADMIN_WORDFILTERTOOLTIP","Note: You become only receive this mail if you activated the option <b>"._GUESTBOOK_ADMIN_WORDFILTERFRONT."</b>!");
DEFINE("_GUESTBOOK_ADMIN_MAILCHECK","Mail server check");
DEFINE("_GUESTBOOK_ADMIN_MAILCHECKDESC","Check authenticity of mail server. <b>NOTE:</b> This option does not function with Freehoster!");
DEFINE("_GUESTBOOK_ADMIN_MAILCHECKTOOLTIP","Example: if a user enters <b>name@example.com</b> a ping is sent to <b>example.com</b>. Only if the server replies, the entry is considered to be valid! <b>NOTE:</b> This option does not function with Freehoster!");
DEFINE("_GUESTBOOK_ADMIN_MIGRATORTOOL","Import entries from former");
DEFINE("_GUESTBOOK_ADMIN_MIGRATORTOOLDESC","Tool to import the entries of your old Akobook.");
DEFINE("_GUESTBOOK_ADMIN_SHOWMAIL","Show e-mail field");
DEFINE("_GUESTBOOK_ADMIN_MAILMANDATORY","E-Mail-Field mandatory?");
DEFINE("_GUESTBOOK_ADMIN_SHOWHOMEPAGE","Show homepage field");
DEFINE("_GUESTBOOK_ADMIN_SHOWLOCATION","Show location field");
DEFINE("_GUESTBOOK_ADMIN_SHOWICQ","Show ICQ field");
DEFINE("_GUESTBOOK_ADMIN_SHOWAIM","Show AIM field");
DEFINE("_GUESTBOOK_ADMIN_SHOWMSN","Show MSN field");
DEFINE("_GUESTBOOK_ADMIN_SHOWYAH","Show Yahoo field");
DEFINE("_GUESTBOOK_ADMIN_SHOWSKYPE","Show Skype field");
DEFINE("_GUESTBOOK_ADMIN_SHOWRATE","Show rating field");

// Backendlanguage definitions - Edit entry
DEFINE("_GUESTBOOK_ADMIN_MARKENTRYFORACTION","Please mark an entry for ");
DEFINE("_GUESTBOOK_ADMIN_EDITENTRY","Edit of");
DEFINE("_GUESTBOOK_ADMIN_CREATEENTRY","Create of");
DEFINE("_GUESTBOOK_ADMIN_GUESTBOOKENTRY","Guestbook Entry");
DEFINE("_GUESTBOOK_ADMIN_NAME","Name");
DEFINE("_GUESTBOOK_ADMIN_EMAIL","E-Mail");
DEFINE("_GUESTBOOK_ADMIN_HOMEPAGE","Homepage");
DEFINE("_GUESTBOOK_ADMIN_LOCATION","Location");
DEFINE("_GUESTBOOK_ADMIN_ICQ","ICQ");
DEFINE("_GUESTBOOK_ADMIN_AIM","AIM");
DEFINE("_GUESTBOOK_ADMIN_MSN","MSN");
DEFINE("_GUESTBOOK_ADMIN_YAH","YAH");
DEFINE("_GUESTBOOK_ADMIN_SKYPE","Skype");
DEFINE("_GUESTBOOK_ADMIN_IP","User IP");
?>