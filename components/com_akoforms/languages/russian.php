<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : ...
* Homepage   : ...
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Да';
  var $AKF_NO                  = 'Нет';
  var $AKF_PUBLISHED           = 'Опубликовано';
  var $AKF_PUBLISHING          = 'Публикация';
  var $AKF_STARTPUBLISHING     = 'Начало публикации:';
  var $AKF_FINISHPUBLISHING    = 'Конец публикации:';
  var $AKF_PUBPENDING          = 'Опубликовано, но Не закончено';
  var $AKF_PUBCURRENT          = 'Опубликовано и Действует';
  var $AKF_PUBEXPIRED          = 'Опубликовано, но Просрочено';
  var $AKF_UNPUBLISHED         = 'Не опубликовано';
  var $AKF_REORDER             = 'Изменить';
  var $AKF_ORDERING            = 'Очерёдность:';
  var $AKF_TITLE               = 'Заголовок:';
  var $AKF_DESCRIPTION         = 'Описание:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Редактировать язык';
  var $AKF_PATH                = 'Расположение:';
  var $AKF_FILEWRITEABLE       = 'Пожалуйста заметьте: Файл должен иметь разрешение на запись чтобы сохранить ваши изменения.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Редактор форм';
  var $AKF_FORMTITLE           = 'Имя формы';
  var $AKF_SENDMAIL            = 'Послать Email';
  var $AKF_STOREDB             = 'Записать в БД';
  var $AKF_FINISHING           = 'Страница после заполнения формы';
  var $AKF_FORMPAGE            = 'Страница с формами';
  var $AKF_REDIRECTION         = 'Перенаправление';
  var $AKF_SHOWRESULT          = 'Показать результат';
  var $AKF_NUMBEROFFIELDS      = 'Количество полей';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Добавить форму';
  var $AKF_EDITFORM            = 'Редактировать форму';
  var $AKF_HEADER              = 'Заголовок';
  var $AKF_HANDLING            = 'Обработка';
  var $AKF_SENDBYEMAIL         = 'Послать по Email:';
  var $AKF_EMAILS              = 'Email:';
  var $AKF_SAVETODATABASE      = 'Сохранить в базу данных:';
  var $AKF_ENDPAGETITLE        = 'Заголовок:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Описание:';
  var $AKF_FORMTARGET          = 'После заполнения:';
  var $AKF_TARGETURL           = 'Перенапрваить на URL:';
  var $AKF_SHOWENDPAGE         = 'Показывать эту страницу';
  var $AKF_REDIRECTTOURL       = 'Перенаправить на URL';
  var $AKF_NEWFORMSLAST        = 'Новые формы идут на последнее место.';
  var $AKF_SHOWFORMRESULT      = 'Показать результат формы:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Редактор полей';
  var $AKF_FIELDTITLE          = 'Заголовок поля';
  var $AKF_FIELDTYPE           = 'Тип поля';
  var $AKF_FIELDREQUIRED       = 'Необходимо заполнить';
  var $AKF_SELECTFORM          = 'Выбрать из';
  var $AKF_ALLFORMS            = '- Все формы';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Добавить поле';
  var $AKF_EDITFIELD           = 'Редактировать поле';
  var $AKF_GENERAL             = 'Общий';
  var $AKF_FORM                = 'Форма:';
  var $AKF_TYPE                = 'Тип:';
  var $AKF_VALUE               = 'Значение:';
  var $AKF_STYLE               = 'Стиль:';
  var $AKF_REQUIRED            = 'Обязательно:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Редактировать настройки';
  var $AKF_MAILSUBJECT         = 'Тема емайла:';
  var $AKF_SENDERNAME          = 'Имя посылающего:';
  var $AKF_SENDEREMAIL         = 'Email посылающего:';
  var $AKF_SETTINGSSAVED       = 'Настройки сохранены.';
  var $AKF_SETTINGSNOTSAVED    = 'Настройки не могут быть сохранены.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Сохранённые формы';
  var $AKF_NUMBEROFENTRIES     = 'Количество записей';
  var $AKF_STOREDDATA          = 'Сохранённая информация';
  var $AKF_STOREDIP            = 'IP пославшего';
  var $AKF_STOREDDATE          = 'Дата отправки';


  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Пожалуйста заполните все необходимые поля:';
  var $AKF_REQUIREDFIELD       = 'Обязательные поля';
  var $AKF_BUTTONSEND          = 'Послать';
  var $AKF_BUTTONCLEAR         = 'Очистить';
  var $AKF_FORMEXPIRED         = 'Эта форма просрочена!';
  var $AKF_FORMPENDING         = 'Форма в данным момент не подтверждена!';
  var $AKF_MAILERHELLO         = 'Привет';
  var $AKF_MAILERHEADER        = 'Пользователь вашего вебсайта использовал форму чтобы послать вам следующую информацию:';
  var $AKF_MAILERFOOTER        = 'Искренне';
  var $AKF_MAILERERROR         = 'Произошла почтовая ошибка когда посылалось на:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = 'Из выпадающего меню выберите форму к которой нужно присоеденить это поле.';
  var $AKF_HELPTITLE           = 'Введите короткое описание вашего поля\формы в этом поле.';
  var $AKF_HELPDESCRIPTION     = 'Здесь вы можете ввести совместимое с хтмл описание для вашего поля или формы.';
  var $AKF_HELPTYPE            = 'Вы можете выбрать почти любую стандартную форму из уже настроенных полей. Если вам нужно особенное, не представленное здесь выпадающее меню, свяжитесь с разработчиком Arthur Konze.';
  var $AKF_HELPVALUE           = 'Это поле предназначено для создания выпадающего меню. Для этого просто введите варианты ответов так, чтобы они не были на одной линии. Тоже самое можете сделать с RADIOBUTTON и SELECTBOXES. Для CHECKBOX введёный текст будет использован рядом как описание.';
  var $AKF_HELPSTYLE           = 'Используйте опции стилей CSS для применения на поле. Например чтобы сделать поле в 200 pixels шириной, введите: width:200px;';
  var $AKF_HELPREQUIRED        = 'Выберите обязан ли посетитель заполнять поле или нет.';
  var $AKF_HELPORDERING        = 'Используйте очерёдность чтобы выбрать позицию.';
  var $AKF_HELPSTARTFINISH     = 'Выберите начало и конец публикации используя эти две опции.';
  var $AKF_HELPSENDMAIL        = 'Выберите посылать результаты на Email или нет.';
  var $AKF_HELPEMAILS          = 'Введите здесь адрес email. Вы можете использовать несколько почтовых адресов разделяя их запятыми (,).';
  var $AKF_HELPSAVEDB          = 'Выберите должен ли сохранятся результат в базу данных.';
  var $AKF_HELPTARGET          = 'Выберите после заполнения и отправки данных посетитель останется на этой же странице или будет перенаправлен на другой URL.';
  var $AKF_HELPTARGETURL       = 'Введите URL на который хотите перенаправлять посетителей после заполнения формы. Это может быть URL на другую форму.';
  var $AKF_HELPSUBJECT         = 'Тема введёная здесь будет использована для всех исходящих email\'ов.';
  var $AKF_HELPSENDER          = 'Имя введёное здесь будет использовано как имя посылающего в исходящих письмах.';
  var $AKF_HELPEMAIL           = 'Ваш действующий email введёный здесь будет вставлен в исходящих письмах.';
  var $AKF_HELPRESULT          = 'Выберите показывать или нет пользователю результат заполнения перед отправкой.';

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