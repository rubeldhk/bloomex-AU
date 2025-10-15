<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php
/* @package joomap
 * @author: Jozsef Tamas Herczeg, http://www.soft-trans.hu/
 */

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define("_JOOMAP_CFG_COM_TITLE", "Joomap beállításai");
    define("_JOOMAP_CFG_OPTIONS", "Megjelenítés beállításai");
    define("_JOOMAP_CFG_TITLE", "Név");
    define("_JOOMAP_CFG_CSS_CLASSNAME", "CSS osztálynév");
    define("_JOOMAP_CFG_EXPAND_CATEGORIES","A tartalomkategóriák kibontása");
    define("_JOOMAP_CFG_EXPAND_SECTIONS","A tartalomszekciók kibontása");
    define("_JOOMAP_CFG_SHOW_MENU_TITLES", "A menüpontok megjelenítése");
    define("_JOOMAP_CFG_NUMBER_COLUMNS", "Az oszlopok száma");
    define('_JOOMAP_EX_LINK', 'A külsõ hivatkozások megjelölése');
    define('_JOOMAP_CFG_CLICK_HERE', 'Kattintson ide');
    define('_JOOMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_JOOMAP_EXCLUDE_MENU',			'Menüazonosítók kizárása');
    define('_JOOMAP_TAB_DISPLAY',			'Megjelenítés');
    define('_JOOMAP_TAB_MENUS',				'Menük');
    define('_JOOMAP_CFG_WRITEABLE',			'Írható');
    define('_JOOMAP_CFG_UNWRITEABLE',		'Írásvédett');
    define('_JOOMAP_MSG_MAKE_UNWRITEABLE',	'Mentés után megjelölés [ <span style="color: red;">írásvédettként</span> ]');
    define('_JOOMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Az írásvédettség hatálytalanítása mentéskor');
    define('_JOOMAP_GOOGLE_LINK',			'Google hivatkozás');

    // -- Tips ---------------------------------------------------------------------
    define('_JOOMAP_EXCLUDE_MENU_TIP',		'Adja meg a helytérképbõl kihagyandó menüazonosítókat.<br /><strong>MEGJEGYZÉS</strong><br />Válassza el vesszõvel az azoosítókat!');
    define('_JOOMAP_CFG_GOOGLE_MAP_TIP',	'A GoogleSiteMap számára generált XML fájl');
    define('_JOOMAP_GOOGLE_LINK_TIP',		'Másolja ki a hivatkozást és küldje be a GoogleSiteMap-nek');

    // -- Menus --------------------------------------------------------------------
    define("_JOOMAP_CFG_SET_ORDER", "Határozza meg a menük megjelenítésének sorrendjét");
    define("_JOOMAP_CFG_MENU_SHOW", "Látszik");
    define("_JOOMAP_CFG_MENU_REORDER", "Átrendezés");
    define("_JOOMAP_CFG_MENU_ORDER", "Sorrend");
    define("_JOOMAP_CFG_MENU_NAME", "Menünév");
    define("_JOOMAP_CFG_DISABLE", "Kattintson rá a letiltáshoz");
    define("_JOOMAP_CFG_ENABLE", "Kattintson rá az engedélyezéshez");
    define('_JOOMAP_SHOW','Látszik');
    define('_JOOMAP_NO_SHOW','Nem látszik');

    // -- Toolbar ------------------------------------------------------------------
    define("_JOOMAP_TOOLBAR_SAVE", "Mentés");
    define("_JOOMAP_TOOLBAR_CANCEL", "Mégse");

    // -- Errors -------------------------------------------------------------------
    define('_JOOMAP_ERR_NO_LANG','Nem található ilyen nyelv [ %s ], betöltésre került az alapértelmezett nyelv: angol<br />'); // %s = $GLOBALS['mosConfig_lang']
    define('_JOOMAP_ERR_CONF_SAVE',         '<h2>Nem sikerült a beállítások mentése.</h2>');
    define('_JOOMAP_ERR_NO_CREATE',         'HIBA: Nem hozható létre a Settings tábla');
    define('_JOOMAP_ERR_NO_DEFAULT_SET',    'HIBA: Nem szúrhatók be az alapértelmezett beállítások');
    define('_JOOMAP_ERR_NO_PREV_BU',        'FIGYELEM! Nem dobható el az elõzõ biztonsági mentés');
    define('_JOOMAP_ERR_NO_BACKUP',         'HIBA: Nem hozható létre a biztonsági mentés');
    define('_JOOMAP_ERR_NO_DROP_DB',        'HIBA: Nem dobható el a Settings tábla');

    // -- Config -------------------------------------------------------------------
    define('_JOOMAP_MSG_SET_RESTORED',      'A beállítások visszaállítása kész<br />');
    define('_JOOMAP_MSG_SET_BACKEDUP',      'A beállítások mentése kész<br />');
    define('_JOOMAP_MSG_SET_DB_CREATED',    'A Settings tábla létrehozása kész<br />');
    define('_JOOMAP_MSG_SET_DEF_INSERT',    'Az alapértelmezett beállítások beszúrása kész');
    define('_JOOMAP_MSG_SET_DB_DROPPED',    'A Settings tábla eldobása megtörtént');
	
    // -- CSS ----------------------------------------------------------------------
    define('_JOOMAP_CSS',					'JooMap CSS');
    define('_JOOMAP_CSS_EDIT',				'Sablon szerkesztése'); // Edit template
	
    // -- Sitemap ------------------------------------------------------------------
    define('_JOOMAP_SHOW_AS_EXTERN_ALT','Új ablakban nyílik meg a hivatkozás');
    define('_JOOMAP_PREVIEW','Elõnézet');
    define('_JOOMAP_SITEMAP_NAME','Helytérkép');
}
?>
