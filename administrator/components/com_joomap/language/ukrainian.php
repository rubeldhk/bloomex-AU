<?php defined( '_VALID_MOS' ) or die( 'Прямий доступ за цією адресою заборонений.' ); ?>
<?php
/* @package joomap
 */

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define("_JOOMAP_CFG_COM_TITLE", "Конфігурація Joomap");
    define("_JOOMAP_CFG_OPTIONS", "Налаштування відображення");
    define("_JOOMAP_CFG_TITLE", "Заголовок");
    define("_JOOMAP_CFG_CSS_CLASSNAME", "Ім'я класу CSS");
    define("_JOOMAP_CFG_EXPAND_CATEGORIES","Розкрити категорії");
    define("_JOOMAP_CFG_EXPAND_SECTIONS","Розкрити секції");
    define("_JOOMAP_CFG_SHOW_MENU_TITLES", "Показати заголовки меню");
    define("_JOOMAP_CFG_NUMBER_COLUMNS", "Кількість стовпців");
    define('_JOOMAP_EX_LINK', 'Позначати зовнішні гиперлінки');
    define('_JOOMAP_CFG_CLICK_HERE', 'Натисніть сюди');
    define('_JOOMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_JOOMAP_EXCLUDE_MENU',			'Виключити ідентифікатори меню');
    define('_JOOMAP_TAB_DISPLAY',			'Вигляд');
    define('_JOOMAP_TAB_MENUS',				'Меню');
    define('_JOOMAP_CFG_WRITEABLE',			'Запис дозволений');
    define('_JOOMAP_CFG_UNWRITEABLE',		'Тільки для читання');
    define('_JOOMAP_MSG_MAKE_UNWRITEABLE',	'Після збереження помітити <span style="color: red;">"тільки для читання"</span>');
    define('_JOOMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Зняти параметр <span style="color: red;">"тільки для читання"</span> при збереженні');
    define('_JOOMAP_GOOGLE_LINK',			'Googlelink');

    // -- Tips ---------------------------------------------------------------------
    define('_JOOMAP_EXCLUDE_MENU_TIP',		'Вкажіть ідентифікатори меню, які ви не хочете включати в карту сайту.<br /><strong>ВАЖЛИВО</strong><br />Розділяйте ідентифікатори через кому!');
    define('_JOOMAP_CFG_GOOGLE_MAP_TIP',	'Створити XML-файл для GoogleSiteMap');
    define('_JOOMAP_GOOGLE_LINK_TIP',		'Скопіюйте гіперлінку та зареєструйте на GoogleSiteMap');

    // -- Menus --------------------------------------------------------------------
    define("_JOOMAP_CFG_SET_ORDER", "Встановити порядок відображення меню");
    define("_JOOMAP_CFG_MENU_SHOW", "Показати");
    define("_JOOMAP_CFG_MENU_REORDER", "Рухати");
    define("_JOOMAP_CFG_MENU_ORDER", "Порядок");
    define("_JOOMAP_CFG_MENU_NAME", "Ім'я меню");
    define("_JOOMAP_CFG_DISABLE", "Заборонити");
    define("_JOOMAP_CFG_ENABLE", "Дозволити");
    define('_JOOMAP_SHOW','Показати');
    define('_JOOMAP_NO_SHOW','Не показувати');

    // -- Toolbar ------------------------------------------------------------------
    define("_JOOMAP_TOOLBAR_SAVE", "Зберегти");
    define("_JOOMAP_TOOLBAR_CANCEL", "Відмінити");

    // -- Errors -------------------------------------------------------------------
    define('_JOOMAP_ERR_NO_LANG','Відсутня [ %s ] мова, завантажена мова за умовчанням: англійська<br />'); // %s = $GLOBALS['mosConfig_lang']
    define('_JOOMAP_ERR_CONF_SAVE',         '<h2>Не вдалося зберегти конфігурацію.</h2>');
    define('_JOOMAP_ERR_NO_CREATE',         'ПОМИЛКА: Неможливо створити таблицю налаштувань');
    define('_JOOMAP_ERR_NO_DEFAULT_SET',    'ПОМИЛКА: Неможливо встановити налаштування за умовчанням');
    define('_JOOMAP_ERR_NO_PREV_BU',        'УВАГА: Неможливо знищити попередню резервну копію');
    define('_JOOMAP_ERR_NO_BACKUP',         'ПОМИЛКА: Неможливо створити резервну копію');
    define('_JOOMAP_ERR_NO_DROP_DB',        'ПОМИЛКА: Неможливо знищити таблицю налаштувань');

    // -- Config -------------------------------------------------------------------
    define('_JOOMAP_MSG_SET_RESTORED',      'Налаштування відновленні<br />');
    define('_JOOMAP_MSG_SET_BACKEDUP',      'Налаштування збережені<br />');
    define('_JOOMAP_MSG_SET_DB_CREATED',    'Створена таблиця налаштувань<br />');
    define('_JOOMAP_MSG_SET_DEF_INSERT',    'Встановлені налаштування за умовчанням');
    define('_JOOMAP_MSG_SET_DB_DROPPED',    'Таблиця налаштувань строек удалена');
	
    // -- CSS ----------------------------------------------------------------------
    define('_JOOMAP_CSS',					'JooMap CSS');
    define('_JOOMAP_CSS_EDIT',				'Редагувати шаблон'); // Edit template
	
    // -- Sitemap ------------------------------------------------------------------
    define('_JOOMAP_SHOW_AS_EXTERN_ALT','Гіперлінки відкривати в новому вікні');
    define('_JOOMAP_PREVIEW','Попередній перегляд');
    define('_JOOMAP_SITEMAP_NAME','Sitemap');
}
?>
