<?php defined( '_VALID_MOS' ) or die( 'Прямой доступ по этому адресу запрещён.' ); ?>
<?php
/* @package joomap
 */

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define("_JOOMAP_CFG_COM_TITLE", "Конфигурация Joomap");
    define("_JOOMAP_CFG_OPTIONS", "Настройки отображения");
    define("_JOOMAP_CFG_TITLE", "Заголовок");
    define("_JOOMAP_CFG_CSS_CLASSNAME", "Имя класса CSS");
    define("_JOOMAP_CFG_EXPAND_CATEGORIES","Раскрыть категории");
    define("_JOOMAP_CFG_EXPAND_SECTIONS","Раскрыть секции");
    define("_JOOMAP_CFG_SHOW_MENU_TITLES", "Показать заголовки меню");
    define("_JOOMAP_CFG_NUMBER_COLUMNS", "Количество столбцов");
    define('_JOOMAP_EX_LINK', 'Помечать внешние ссылки');
    define('_JOOMAP_CFG_CLICK_HERE', 'Нажмите сюда');
    define('_JOOMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_JOOMAP_EXCLUDE_MENU',			'Исключить идентификаторы меню');
    define('_JOOMAP_TAB_DISPLAY',			'Вид');
    define('_JOOMAP_TAB_MENUS',				'Меню');
    define('_JOOMAP_CFG_WRITEABLE',			'Запись разрешена');
    define('_JOOMAP_CFG_UNWRITEABLE',		'Только для чтения');
    define('_JOOMAP_MSG_MAKE_UNWRITEABLE',	'После сохранения пометить <span style="color: red;">"только для чтения"</span>');
    define('_JOOMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Снять параметр <span style="color: red;">"только для чтения"</span> при сохранении');
    define('_JOOMAP_GOOGLE_LINK',			'Googlelink');

    // -- Tips ---------------------------------------------------------------------
    define('_JOOMAP_EXCLUDE_MENU_TIP',		'Укажите идентификаторы меню, которые вы не хотите включать в карту сайта.<br /><strong>ВАЖНО</strong><br />Разделяйте идентификаторы через запятую!');
    define('_JOOMAP_CFG_GOOGLE_MAP_TIP',	'Создать XML-файл для GoogleSiteMap');
    define('_JOOMAP_GOOGLE_LINK_TIP',		'Скопируйте ссылку и зарегистрируйте на GoogleSiteMap');

    // -- Menus --------------------------------------------------------------------
    define("_JOOMAP_CFG_SET_ORDER", "Установить порядок отображения меню");
    define("_JOOMAP_CFG_MENU_SHOW", "Показать");
    define("_JOOMAP_CFG_MENU_REORDER", "Двигать");
    define("_JOOMAP_CFG_MENU_ORDER", "Порядок");
    define("_JOOMAP_CFG_MENU_NAME", "Имя меню");
    define("_JOOMAP_CFG_DISABLE", "Запретить");
    define("_JOOMAP_CFG_ENABLE", "Разрешить");
    define('_JOOMAP_SHOW','Показывать');
    define('_JOOMAP_NO_SHOW','Не показывать');

    // -- Toolbar ------------------------------------------------------------------
    define("_JOOMAP_TOOLBAR_SAVE", "Сохранить");
    define("_JOOMAP_TOOLBAR_CANCEL", "Отменить");

    // -- Errors -------------------------------------------------------------------
    define('_JOOMAP_ERR_NO_LANG','Отсутствует [ %s ] язык, загружен язык по умолчанию: английский<br />'); // %s = $GLOBALS['mosConfig_lang']
    define('_JOOMAP_ERR_CONF_SAVE',         '<h2>Не удалось сохранить конфигурацию.</h2>');
    define('_JOOMAP_ERR_NO_CREATE',         'ОШИБКА: Невозможно создать таблицу настроек');
    define('_JOOMAP_ERR_NO_DEFAULT_SET',    'ОШИБКА: Невозможно установить настройки по умолчанию');
    define('_JOOMAP_ERR_NO_PREV_BU',        'ВНИМАНИЕ: Невозможно удалить предыдущую резервную копию');
    define('_JOOMAP_ERR_NO_BACKUP',         'ОШИБКА: Невозможно создать резервную копию');
    define('_JOOMAP_ERR_NO_DROP_DB',        'ОШИБКА: Невозможно удалить таблицу настроек');

    // -- Config -------------------------------------------------------------------
    define('_JOOMAP_MSG_SET_RESTORED',      'Настройки восстановлены<br />');
    define('_JOOMAP_MSG_SET_BACKEDUP',      'Настройки сохранены<br />');
    define('_JOOMAP_MSG_SET_DB_CREATED',    'Создана таблица настроек<br />');
    define('_JOOMAP_MSG_SET_DEF_INSERT',    'Установлены настроки по умолчанию');
    define('_JOOMAP_MSG_SET_DB_DROPPED',    'Таблица настроек удалена');
	
    // -- CSS ----------------------------------------------------------------------
    define('_JOOMAP_CSS',					'JooMap CSS');
    define('_JOOMAP_CSS_EDIT',				'Редактировать шаблон'); // Edit template
	
    // -- Sitemap ------------------------------------------------------------------
    define('_JOOMAP_SHOW_AS_EXTERN_ALT','Ссылки открывать в новом окне');
    define('_JOOMAP_PREVIEW','Предварительный просмотр');
    define('_JOOMAP_SITEMAP_NAME','Sitemap');
}
?>
