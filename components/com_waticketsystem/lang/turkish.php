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
DEFINE("_WATS_NAV_NEW","Yeni Ticket");
DEFINE("_WATS_NAV_CATEGORY","Destek Kategorileri");
DEFINE("_WATS_NAV_TICKET","Ticket Numarasi");

// USER
DEFINE("_WATS_USER","Kullanici");
DEFINE("_WATS_USER_SET","Kullanicilar");
DEFINE("_WATS_USER_NAME","Ad");
DEFINE("_WATS_USER_USERNAME","Kullanici Adi");
DEFINE("_WATS_USER_GROUP","Grup");
DEFINE("_WATS_USER_ORG","Organizasyon");
DEFINE("_WATS_USER_ORG_SELECT","Organizasyonu giriniz");
DEFINE("_WATS_USER_EMAIL","E-Posta");
DEFINE("_WATS_USER_NEW","Yeni kullanici yarat");
DEFINE("_WATS_USER_NEW_SELECT","Kullanici seç");
DEFINE("_WATS_USER_NEW_CREATED","Yaratilan kullanici");
DEFINE("_WATS_USER_NEW_FAILED","Bu kullanicinin zaten ticket destek hesabi var");
DEFINE("_WATS_USER_DELETED","Kullanici silindi");
DEFINE("_WATS_USER_EDIT","Kullanici düzenle");
DEFINE("_WATS_USER_DELETE_REC","Kullanici ticketlarini kaldir (tavsiye edilen)");
DEFINE("_WATS_USER_DELETE_NOTREC","Kullanici ticketlarini ve diger ticketlara cevaplarini kaldir (tavsiye edilmeyen)");
DEFINE("_WATS_USER_DELETE","Kullaniciyi sil");
DEFINE("_WATS_USER_ADD","Kullanici ekle");
DEFINE("_WATS_USER_SELECT","Kullanici seç");
DEFINE("_WATS_USER_SET_DESCRIPTION","Kullanicilari düzenle");
DEFINE("_WATS_USER_ADD_LIST","Su kullanicilar eklendi");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Grup Seç");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Kategori");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Açik Ticketlarim");
DEFINE("_WATS_TICKETS_USER_CLOSED","Kapali Ticketlarim");
DEFINE("_WATS_TICKETS_OPEN","Açik Ticketlar");
DEFINE("_WATS_TICKETS_CLOSED","Kapali Tickerlar");
DEFINE("_WATS_TICKETS_DEAD","Bozuk Ticketlar");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Bütün açik ticketlari incele");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Bütün kapali ticketlari incele");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Bütün bozuk ticketlari incele");
DEFINE("_WATS_TICKETS_NAME","Ticket Ismi");
DEFINE("_WATS_TICKETS_POSTS","Gönderiler");
DEFINE("_WATS_TICKETS_DATETIME","Son Gönderi");
DEFINE("_WATS_TICKETS_PAGES","Sayfalar");
DEFINE("_WATS_TICKETS_SUBMIT","Yeni bir ticket aç");
DEFINE("_WATS_TICKETS_SUBMITING","Ticket açiliyor");
DEFINE("_WATS_TICKETS_SUBMITTED","Ticket basariyla açildi");
DEFINE("_WATS_TICKETS_DESC","Açiklama");
DEFINE("_WATS_TICKETS_CLOSE","Ticketi Kapat");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket kapatildi");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket silindi");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket temizlendi");
DEFINE("_WATS_TICKETS_NONE","Hiç ticket bulunamadi");
DEFINE("_WATS_TICKETS_FIRSTPOST","Baslangiç: ");
DEFINE("_WATS_TICKETS_LASTPOST","Gönderen: ");
DEFINE("_WATS_TICKETS_REPLY","cevap Ver");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Cevap Ver ve Kapat");
DEFINE("_WATS_TICKETS_ASSIGN","Ticket Ata");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Atanacak Kisi");
DEFINE("_WATS_TICKETS_ID","Ticket ID");
DEFINE("_WATS_TICKETS_REOPEN","Tekrar Aç");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Lütfen ticketi açmak için bir neden giriniz");
DEFINE("_WATS_TICKETS_STATE_ALL","Hepsi");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Kisisel");
DEFINE("_WATS_TICKETS_STATE_OPEN","Açik");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Kapali");
DEFINE("_WATS_TICKETS_STATE_DEAD","Bozuk");
DEFINE("_WATS_TICKETS_PURGE","Ölü ticketlar içinde ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticketi açan kisi: ");
DEFINE("_WATS_MAIL_REPLY","Cevabi açan kisi: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Sil?");
DEFINE("_WATS_MISC_GO","Git");

//ERRORS
DEFINE("_WATS_ERROR","Bir hata olustu");
DEFINE("_WATS_ERROR_ACCESS","Bu görevi tamamlamak için yeterli hakkiniz yok");
DEFINE("_WATS_ERROR_NOUSER","Bu kaynagi görmek için yetkiniz yok.<br>Giris yapmaniz veya yöneticinizden yetki istemeniz gerekli.");
DEFINE("_WATS_ERROR_NODATA","Formu düzgün doldurmadiniz, lütfen tekrar deneyiniz.");
DEFINE("_WATS_ERROR_NOT_FOUND","Öge bulunamadi");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Yazinizi biçimlendirmek için asagidaki 'tag'leri kullanin:</i></p> 
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
