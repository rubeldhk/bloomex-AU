<?php
/* @package Xmap
 * @author Guillermo Vargas, http://joomla.vargas.co.cr/
 * @translator Jozsef Tamas Herczeg, http://www.joomlandia.eu/
*/

defined( '_VALID_MOS' ) or die( 'A k�zvetlen hozz�f�r�s ehhez a helyhez nem enged�lyezett.' );

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define('_XMAP_CFG_COM_TITLE', 'Xmap be�ll�t�sai');
    define('_XMAP_CFG_OPTIONS', 'Megjelen�t�s be�ll�t�sai');
    define('_XMAP_CFG_CSS_CLASSNAME', 'CSS oszt�lyn�v');
    define('_XMAP_CFG_EXPAND_CATEGORIES','A tartalomkateg�ri�k kibont�sa');
    define('_XMAP_CFG_EXPAND_SECTIONS','A tartalomszekci�k kibont�sa');
    define('_XMAP_CFG_SHOW_MENU_TITLES', 'A men�pontok megjelen�t�se');
    define('_XMAP_CFG_NUMBER_COLUMNS', 'Az oszlopok sz�ma');
    define('_XMAP_EX_LINK', 'A k�ls� hivatkoz�sok megjel�l�se');
    define('_XMAP_CFG_CLICK_HERE', 'Kattints ide');
    define('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_XMAP_EXCLUDE_MENU',			'Kiz�rand� men�azonos�t�k');
    define('_XMAP_TAB_DISPLAY',			'Megjelen�t�s');
    define('_XMAP_TAB_MENUS',				'Men�k');
    define('_XMAP_CFG_WRITEABLE',			'�rhat�');
    define('_XMAP_CFG_UNWRITEABLE',		'�r�sv�dett');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'�r�sv�dett� t�tel ment�s ut�n');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Az �r�sv�detts�g hat�lytalan�t�sa ment�skor');
    define('_XMAP_GOOGLE_LINK',			'Google hivatkoz�s');
    define('_XMAP_CFG_INCLUDE_LINK',		'A szerz�re mutat� l�thatatlan hivatkoz�s');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'Add meg a helyt�rk�pb�l kihagyand� men�azonos�t�kat.<br /><strong>MEGJEGYZ�S</strong><br />V�laszd el vessz�vel az azoos�t�kat!');

    // -- Menus --------------------------------------------------------------------
    define('_XMAP_CFG_SET_ORDER', '�ll�tsd be a men�k megjelen�t�s�nek sorrendj�t');
    define('_XMAP_CFG_MENU_SHOW', 'L�tszik');
    define('_XMAP_CFG_MENU_REORDER', '�trendez�s');
    define('_XMAP_CFG_MENU_ORDER', 'Sorrend');
    define('_XMAP_CFG_MENU_NAME', 'Men�n�v');
    define('_XMAP_CFG_DISABLE', 'Kattints r� a letilt�shoz');
    define('_XMAP_CFG_ENABLE', 'Kattints r� az enged�lyez�shez');
    define('_XMAP_SHOW','L�tszik');
    define('_XMAP_NO_SHOW','Nem l�tszik');

    // -- Toolbar ------------------------------------------------------------------
    define('_XMAP_TOOLBAR_SAVE', 'Ment�s');
    define('_XMAP_TOOLBAR_CANCEL', 'M�gse');

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG','[ %s ] nyelvi f�jl nem tal�lhat�, bet�lt�sre ker�lt az alap�rtelmezett nyelv: angol<br />'); // %s = $GLOBALS['mosConfig_lang']
    define('_XMAP_ERR_CONF_SAVE',         'HIBA: A be�ll�t�sok ment�se nem siker�lt.');
    define('_XMAP_ERR_NO_CREATE',         'HIBA: Nem hozhat� l�tre a Settings t�bla');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'HIBA: Nem sz�rhat�k be az alap�rtelmezett be�ll�t�sok');
    define('_XMAP_ERR_NO_PREV_BU',        'FIGYELEM! Nem dobhat� el az el�z� biztons�gi ment�s');
    define('_XMAP_ERR_NO_BACKUP',         'HIBA: Nem hozhat� l�tre a biztons�gi ment�s');
    define('_XMAP_ERR_NO_DROP_DB',        'HIBA: Nem dobhat� el a Settings t�bla');
    define('_XMAP_ERR_NO_SETTINGS',		'HIBA: Nem t�lthet�k be az adatb�zisb�l a be�ll�t�sok: <a href="%s">A Settings t�bla l�trehoz�sa</a>');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      'A be�ll�t�sok vissza�ll�t�sa k�sz');
    define('_XMAP_MSG_SET_BACKEDUP',      'A be�ll�t�sok ment�se k�sz');
    define('_XMAP_MSG_SET_DB_CREATED',    'A Settings t�bla l�trehoz�sa k�sz');
    define('_XMAP_MSG_SET_DEF_INSERT',    'Az alap�rtelmezett be�ll�t�sok besz�r�sa k�sz');
    define('_XMAP_MSG_SET_DB_DROPPED',    'A Settings t�bla eldob�sa megt�rt�nt');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'Sablon szerkeszt�se'); // Edit template
	
    // -- Sitemap (Frontend) -------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT','�j ablakban ny�lik meg a hivatkoz�s');
	
    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'L�that� a webhelyen');
    define('_XMAP_CFG_MENU_SHOW_XML',		'L�that� az XML oldalt�rk�pben');
    define('_XMAP_CFG_MENU_PRIORITY',		'Priorit�s');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'Gyakoris�g m�dos�t�sa');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Mindig');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'�r�nk�nt');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Naponta');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Hetente');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Havonta');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'�vente');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Soha');

    define('_XMAP_TIT_SETTINGS_OF',			'%s be�ll�t�sai');
    define('_XMAP_TAB_SITEMAPS',			'Oldalt�rk�pek');
    define('_XMAP_MSG_NO_SITEMAPS',			'M�g nem t�rt�nt meg az oldalt�rk�p l�trehoz�sa');
    define('_XMAP_MSG_NO_SITEMAP',			'Ez az oldalt�rk�p nem el�rhet�');
    define('_XMAP_MSG_LOADING_SETTINGS',		'Be�ll�t�sok bet�lt�se...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Hiba. Nem t�lthet� be az oldalt�rk�p');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Hiba. Nem menthet� az oldalt�rk�p tulajdons�ga.');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Hiba. Nem t�r�lhet� az oldalt�rk�p gyors�t�t�ra');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'Az alap�rtelmezett oldalt�rk�p nem t�r�lhet�!');
    define('_XMAP_MSG_CACHE_CLEANED',			'A gyors�t�t�r t�rl�se k�sz!');
    define('_XMAP_CHARSET',				'ISO-8859-2');
    define('_XMAP_SITEMAP_ID',				'Az oldalt�rk�p azonos�t�ja');
    define('_XMAP_ADD_SITEMAP',				'Oldalt�rk�p hozz�ad�sa');
    define('_XMAP_NAME_NEW_SITEMAP',			'�j oldalt�rk�p');
    define('_XMAP_DELETE_SITEMAP',			'T�rl�s');
    define('_XMAP_SETTINGS_SITEMAP',			'Be�ll�t�sok');
    define('_XMAP_COPY_SITEMAP',			'M�sol�s');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'Alap�rtelmez�sk�nt');
    define('_XMAP_EDIT_MENU',				'Be�ll�t�sok');
    define('_XMAP_DELETE_MENU',				'T�rl�s');
    define('_XMAP_CLEAR_CACHE',				'Gyors�t�t�r t�rl�se');
    define('_XMAP_MOVEUP_MENU',		'Fel');
    define('_XMAP_MOVEDOWN_MENU',	'Le');
    define('_XMAP_ADD_MENU',		'Men�k hozz�ad�sa');
    define('_XMAP_COPY_OF',		'%s m�solata');
    define('_XMAP_INFO_LAST_VISIT',	'Utols� l�togat�s');
    define('_XMAP_INFO_COUNT_VIEWS',	'L�togat�sok sz�ma');
    define('_XMAP_INFO_TOTAL_LINKS',	'Hivatkoz�sok sz�ma');
    define('_XMAP_CFG_URLS',		'Az oldalt�rk�p URL-je');
    define('_XMAP_XML_LINK_TIP',	'A hivatkoz�s m�sol�sa �s bek�ld�se a Google-nek �s a Yahoonak');
    define('_XMAP_HTML_LINK_TIP',	'Ez az oldalt�rk�p URL-je. Men�pontok l�trehoz�s�hoz is felhaszn�lhatod.');
    define('_XMAP_CFG_XML_MAP',		'XML oldalt�rk�p');
    define('_XMAP_CFG_HTML_MAP',	'HTML oldalt�rk�p');
    define('_XMAP_XML_LINK',		'Google hivatkoz�s');
    define('_XMAP_CFG_XML_MAP_TIP',	'A keres�motorok sz�m�ra gener�lt XML f�jl');
    define('_XMAP_ADD', 'Ment�s');
    define('_XMAP_CANCEL', 'M�gse');
    define('_XMAP_LOADING', 'Bet�lt�s...');
    define('_XMAP_CACHE', 'Gyors�t�t�r');
    define('_XMAP_USE_CACHE', 'A gyors�t�t�r haszn�lata');
    define('_XMAP_CACHE_LIFE_TIME', 'A gyors�t�t�r �lettartama');
    define('_XMAP_NEVER_VISITED', 'Soha');

	// New on Xmap 1.1
	define('_XMAP_PLUGINS','Plugins');	
	define( '_XMAP_INSTALL_3PD_WARN', 'Warning: Installing 3rd party extensions may compromise your server\'s security.' );
	define('_XMAP_INSTALL_NEW_PLUGIN', 'Install new Plugins');
	define('_XMAP_UNKNOWN_AUTHOR','Unknown author');
	define('_XMAP_PLUGIN_VERSION','Version %s');
	define('_XMAP_TAB_INSTALL_PLUGIN','Install');
	define('_XMAP_TAB_EXTENSIONS','Extensions');
	define('_XMAP_TAB_INSTALLED_EXTENSIONS','Installed Extensions');
	define('_XMAP_NO_PLUGINS_INSTALLED','No custom plugins installed');
	define('_XMAP_AUTHOR','Author');
	define('_XMAP_CONFIRM_DELETE_SITEMAP','Are you sure you want to delete this sitemap?');
	define('_XMAP_CONFIRM_UNINSTALL_PLUGIN','Are you sure you want to uninstall this plugin?');
	define('_XMAP_UNINSTALL','Uninstall');
	define('_XMAP_EXT_PUBLISHED','Published');
	define('_XMAP_EXT_UNPUBLISHED','Unpublished');
	define('_XMAP_PLUGIN_OPTIONS','Options');
	define('_XMAP_EXT_INSTALLED_MSG','The extension was installed successfully, please review their options and then publish the extension.');
	define('_XMAP_CONTINUE','Continue...');
	define('_XMAP_MSG_EXCLUDE_CSS_SITEMAP','Do not include the CSS within the Sitemap');
	define('_XMAP_MSG_EXCLUDE_XSL_SITEMAP','Use classic XML Sitemap display');

	// New on Xmap 1.1
	define('_XMAP_MSG_SELECT_FOLDER','Please select a directory');
	define('_XMAP_UPLOAD_PKG_FILE','Upload Package File');
	define('_XMAP_UPLOAD_AND_INSTALL','Upload File &amp; Install');
	define('_XMAP_INSTALL_F_DIRECTORY','Install from directory');
	define('_XMAP_INSTALL_DIRECTORY','Install directory');
	define('_XMAP_INSTALL','Install');
	define('_XMAP_WRITEABLE','Writeable');
	define('_XMAP_UNWRITEABLE','Unwriteable');

}
