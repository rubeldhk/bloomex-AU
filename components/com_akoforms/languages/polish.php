<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Tomasz 'Bolo' Fabiszewski
* Homepage   : www.mambopl.com
**/

defined( '_VALID_MOS' ) or die( 'Bezpo�redni dost�p do tej lokalizacji jest zabroniony.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Tak';
  var $AKF_NO                  = 'Nie';
  var $AKF_PUBLISHED           = 'Opublikowany';
  var $AKF_PUBLISHING          = 'Publikowanie';
  var $AKF_STARTPUBLISHING     = 'Pocz�tek publikacji:';
  var $AKF_FINISHPUBLISHING    = 'Zako�czenie publikacji:';
  var $AKF_PUBPENDING          = 'Opublikowany lecz oczekuje';
  var $AKF_PUBCURRENT          = 'Opublikowany i aktualny';
  var $AKF_PUBEXPIRED          = 'Opublikowany lecz nieaktualny';
  var $AKF_UNPUBLISHED         = 'Nieopublikowany';
  var $AKF_REORDER             = 'Zmie� kolejno��';
  var $AKF_ORDERING            = 'Kolejno��:';
  var $AKF_TITLE               = 'Nazwa:';
  var $AKF_DESCRIPTION         = 'Opis:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Edytuj plik j�zykowy';
  var $AKF_PATH                = '�cie�ka:';
  var $AKF_FILEWRITEABLE       = 'Uwaga: plik musi posiada� prawo zapisu.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Manager formularzy';
  var $AKF_FORMTITLE           = 'Nazwa formularza';
  var $AKF_SENDMAIL            = 'E-mail';
  var $AKF_STOREDB             = 'Baza danych';
  var $AKF_FINISHING           = 'Podsumowanie';
  var $AKF_FORMPAGE            = 'Strona formularza';
  var $AKF_REDIRECTION         = 'Przekierowanie';
  var $AKF_SHOWRESULT          = 'Poka� wynik';
  var $AKF_NUMBEROFFIELDS      = 'Ilo�� p�l';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Dodaj formularz';
  var $AKF_EDITFORM            = 'Edytuj formularz';
  var $AKF_HEADER              = 'Nag��wek';
  var $AKF_HANDLING            = 'Zbi�r danych';
  var $AKF_SENDBYEMAIL         = 'Wy�lij przez e-mail:';
  var $AKF_EMAILS              = 'Adresy:';
  var $AKF_SAVETODATABASE      = 'Zapisz w bazie:';
  var $AKF_ENDPAGETITLE        = 'Tytu� strony podsumowania:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Opis strony podsumowania:';
  var $AKF_FORMTARGET          = 'Cel formularza:';
  var $AKF_TARGETURL           = 'URL przekierowania:';
  var $AKF_SHOWENDPAGE         = 'Poka� podsumowanie';
  var $AKF_REDIRECTTOURL       = 'Przekieruj na adres';
  var $AKF_NEWFORMSLAST        = 'Nowe formularze s� na ko�cu listy.';
  var $AKF_SHOWFORMRESULT      = 'Poka� wyniki formularza:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Zarz�dzanie polami';
  var $AKF_FIELDTITLE          = 'Nazwa pola';
  var $AKF_FIELDTYPE           = 'Rodzaj pola';
  var $AKF_FIELDREQUIRED       = 'Pole wymagane';
  var $AKF_SELECTFORM          = 'Wybierz formularz';
  var $AKF_ALLFORMS            = '- Wszystkie';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Dodaj pole';
  var $AKF_EDITFIELD           = 'Edytuj pole';
  var $AKF_GENERAL             = 'G��wne';
  var $AKF_FORM                = 'Formularz:';
  var $AKF_TYPE                = 'Rodzaj:';
  var $AKF_VALUE               = 'Warto��:';
  var $AKF_STYLE               = 'Styl:';
  var $AKF_REQUIRED            = 'Wymagane:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Edytuj ustawienia';
  var $AKF_MAILSUBJECT         = 'Temat e-maila:';
  var $AKF_SENDERNAME          = 'Nazwa nadawcy:';
  var $AKF_SENDEREMAIL         = 'E-mail nadawcy:';
  var $AKF_SETTINGSSAVED       = 'Ustawienia zosta�y zapisane.';
  var $AKF_SETTINGSNOTSAVED    = 'Ustawienia nie mog�y by� zapisane.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Zapisane formularze';
  var $AKF_NUMBEROFENTRIES     = 'Ilo�� wpis�w';
  var $AKF_STOREDDATA          = 'Zapisane dane';
  var $AKF_STOREDIP            = 'IP wype�niaj�cego';
  var $AKF_STOREDDATE          = 'Data wys�ania';


  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Prosz� wype�ni� wszystkie wymagane pola:';
  var $AKF_REQUIREDFIELD       = 'Pola wymagane';
  var $AKF_BUTTONSEND          = 'Wy�lij';
  var $AKF_BUTTONCLEAR         = 'Wyczy��';
  var $AKF_FORMEXPIRED         = 'Formularz jest ju� nieaktualny!';
  var $AKF_FORMPENDING         = 'Formularz oczekuje na opublikowanie!';
  var $AKF_MAILERHELLO         = 'Witaj';
  var $AKF_MAILERHEADER        = 'U�ytkownik Twojego serwisu u�y� formularza aby przys�a� Ci nast�puj�ce dane:';
  var $AKF_MAILERFOOTER        = 'Z powa�aniem';
  var $AKF_MAILERERROR         = 'Wyst�pi� b��d wysy�ania e-mail do:';

  // Help - Forms & Fields
  var $AKF_HELPFORM            = 'Przypisz pole do formularza u�ywaj�c listy wyboru.';
  var $AKF_HELPTITLE           = 'Podaj kr�tk� nazw� dla tego pola.';
  var $AKF_HELPDESCRIPTION     = 'W tym polu mo�esz r�wnie� u�y� HTML aby je opisa�.';
  var $AKF_HELPTYPE            = 'Wybierz z niemal wszystkich standard�w oraz kilku predefiniowanych p�l. Je�li potrzebujesz w�asnego, skontaktuj si� z autorem komponentu.';
  var $AKF_HELPVALUE           = 'Pole warto�ci mo�e by� u�yte do przypisania zak�adanej warto�ci w formularzu. By utworzy� menu listy wyboru po prostu wprowad� warto�ci w osobnych liniach. To samo dotyczy p�l wyboru.';
  var $AKF_HELPSTYLE           = 'U�yj opcji styli aby ustawi� definicje CSS dla pola. Na przyk�ad aby pole mia�o 200 pixeli szeroko�ci wpisz: width:200px;';
  var $AKF_HELPREQUIRED        = 'Wybierz czy pole musi by� wybrane/wype�nione czy te� nie.';
  var $AKF_HELPORDERING        = 'U�yj zmiany kolejno�ci aby ustawi� pozycj�.';
  var $AKF_HELPSTARTFINISH     = 'Wybierz daty rozpocz�cia oraz zako�czenia publikowania.';
  var $AKF_HELPSENDMAIL        = 'Wybierz czy wype�nione dane maj� by� wysy�ane poprzez e-mail.';
  var $AKF_HELPEMAILS          = 'Tutaj podaj adres e-mail. Mo�esz poda� kilka adres�w oddzielaj�c je przecinkami (,).';
  var $AKF_HELPSAVEDB          = 'Wybierz czy dane z wype�nionego formularza maj� by� zapisywane w bazie danych.';
  var $AKF_HELPTARGET          = 'Wybierz czy powinna by� pokazywana strona podsumowuj�ca czy te� wype�niaj�cy b�dzie przekierowany do podanego URL.';
  var $AKF_HELPTARGETURL       = 'Podaj tutaj URL do przekierowania. To mo�e by� dowolny URL lub nast�pny formularz.';
  var $AKF_HELPSUBJECT         = 'Wpisz temat dla wszystkich wychodz�cych list�w e-mail dot. tego formularza.';
  var $AKF_HELPSENDER          = 'Podana nazwa zostanie u�yta jako nazwa nadawcy e-mail.';
  var $AKF_HELPEMAIL           = 'Poprawny adres e-mail nadawcy wychodz�cych e-maili.';
  var $AKF_HELPRESULT          = 'Zadecyduj czy chcesz pokaza� u�ytkownikowi podsumowanie formularza.';

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