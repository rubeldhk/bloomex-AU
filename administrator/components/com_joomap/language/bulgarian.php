<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php
/* @package joomap
 * @author: Daniel Grothe, http://www.ko-ca.com/
 */

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define("_JOOMAP_CFG_COM_TITLE", "Joomap Конфигуриране");
    define("_JOOMAP_CFG_OPTIONS", "Външен вид");
    define("_JOOMAP_CFG_TITLE", "Заглавие");
    define("_JOOMAP_CFG_CSS_CLASSNAME", "Име на CSS-клас");
    define("_JOOMAP_CFG_EXPAND_CATEGORIES","Разшири категориите на съдържанието");
    define("_JOOMAP_CFG_EXPAND_SECTIONS","Разшири секциите на съдържанието");
    define("_JOOMAP_CFG_SHOW_MENU_TITLES", "Покажи заглавията в менюто");
    define("_JOOMAP_CFG_NUMBER_COLUMNS", "Брой колони");
    define('_JOOMAP_EX_LINK', 'Маркирай вътрешните връзки');
    define('_JOOMAP_CFG_CLICK_HERE', 'Кликни тук');
    define('_JOOMAP_CFG_GOOGLE_MAP',		'Google карта на сайта');
    define('_JOOMAP_EXCLUDE_MENU',			'Не включвай ID на менюто');
    define('_JOOMAP_TAB_DISPLAY',			'Покажи');
    define('_JOOMAP_TAB_MENUS',				'Менюта');
    define('_JOOMAP_CFG_WRITEABLE',			'Запис разрешен');
    define('_JOOMAP_CFG_UNWRITEABLE',		'Запис забранен');
    define('_JOOMAP_MSG_MAKE_UNWRITEABLE',	'След запис маркирай като [ <span style="color: red;">заключен</span> ]');
    define('_JOOMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'При запис пренебрегва забраната за записване');
    define('_JOOMAP_GOOGLE_LINK',			'Google-връзка');

    // -- Tips ---------------------------------------------------------------------
    define('_JOOMAP_EXCLUDE_MENU_TIP',		'Напишете ID на менютата, които да не бъдат включени в картата на сайта.<br /><strong>NOTE</strong><br />Отелете различните ID със запетая!');
    define('_JOOMAP_CFG_GOOGLE_MAP_TIP',	'XML-файл генериран за GoogleSiteMap');
    define('_JOOMAP_GOOGLE_LINK_TIP',		'Копирай връзката и я изпрати на GoogleSiteMap');

    // -- Menus --------------------------------------------------------------------
    define("_JOOMAP_CFG_SET_ORDER", "Настройка за реда на показване на менютата");
    define("_JOOMAP_CFG_MENU_SHOW", "Покажи");
    define("_JOOMAP_CFG_MENU_REORDER", "Пренареждане");
    define("_JOOMAP_CFG_MENU_ORDER", "Подредба");
    define("_JOOMAP_CFG_MENU_NAME", "Име на менюто");
    define("_JOOMAP_CFG_DISABLE", "Кликни за да го изключиш");
    define("_JOOMAP_CFG_ENABLE", "Кликни за да го включиш");
    define('_JOOMAP_SHOW','Покажи');
    define('_JOOMAP_NO_SHOW','Не показвай');

    // -- Toolbar ------------------------------------------------------------------
    define("_JOOMAP_TOOLBAR_SAVE", "Запази");
    define("_JOOMAP_TOOLBAR_CANCEL", "Отказ");

    // -- Errors -------------------------------------------------------------------
    define('_JOOMAP_ERR_NO_LANG','No such language [ %s ] found, loaded default language: english<br />'); // %s = $GLOBALS['mosConfig_lang']
    define('_JOOMAP_ERR_CONF_SAVE',         '<h2>Failed to save the configuration.</h2>');
    define('_JOOMAP_ERR_NO_CREATE',         'ERROR: Not able to create Settings table');
    define('_JOOMAP_ERR_NO_DEFAULT_SET',    'ERROR: Not able to insert default Settings');
    define('_JOOMAP_ERR_NO_PREV_BU',        'WARNING: Not able to drop previous backup');
    define('_JOOMAP_ERR_NO_BACKUP',         'ERROR: Not able to create backup');
    define('_JOOMAP_ERR_NO_DROP_DB',        'ERROR: Not able to drop Settings table');

    // -- Config -------------------------------------------------------------------
    define('_JOOMAP_MSG_SET_RESTORED',      'Settings restored<br />');
    define('_JOOMAP_MSG_SET_BACKEDUP',      'Settings saved<br />');
    define('_JOOMAP_MSG_SET_DB_CREATED',    'Settings table created<br />');
    define('_JOOMAP_MSG_SET_DEF_INSERT',    'Default Settings inserted');
    define('_JOOMAP_MSG_SET_DB_DROPPED',    'Settings table dropped');
	
    // -- CSS ----------------------------------------------------------------------
    define('_JOOMAP_CSS',					'JooMap CSS');
    define('_JOOMAP_CSS_EDIT',				'Редакция шаблон'); // Edit template
	
    // -- Sitemap ------------------------------------------------------------------
    define('_JOOMAP_SHOW_AS_EXTERN_ALT','Отваря се в нов прозорец');
    define('_JOOMAP_PREVIEW','Преглед');
    define('_JOOMAP_SITEMAP_NAME','Карта на сайта');
}
?>
