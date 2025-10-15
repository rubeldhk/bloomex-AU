<?php
/* @package xmap
 * @author: Guillermo Vargas http://joomla.vargas.co.cr
 * @translation: Sven Luthman, http://www.genealogi.nu/ http://www.glassrecept.se/
 */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

if( !defined( 'JOOMAP_LANG' )) {
    define('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define('_XMAP_CFG_COM_TITLE',			'Xmap konfiguration');
    define('_XMAP_CFG_OPTIONS',				'Visa alternativ');
    define('_XMAP_CFG_CSS_CLASSNAME',		'CSS Klassnamn');
    define('_XMAP_CFG_EXPAND_CATEGORIES',	'Expandera inneh�llskategorier');
    define('_XMAP_CFG_EXPAND_SECTIONS',		'Expandera inneh�llssektioner');
    define('_XMAP_CFG_SHOW_MENU_TITLES',	'Visa menytitlar');
    define('_XMAP_CFG_NUMBER_COLUMNS',		'Antalet kolumner');
    define('_XMAP_EX_LINK',					'Markera externa l�nkar');
    define('_XMAP_CFG_CLICK_HERE', 			'Klicka h�r');
    define('_XMAP_CFG_GOOGLE_MAP',			'Google platskarta');
    define('_XMAP_EXCLUDE_MENU',			'Uteslut f�ljade meny ID�s');
    define('_XMAP_TAB_DISPLAY',				'Visa');
    define('_XMAP_TAB_MENUS',				'Menyer');
    define('_XMAP_CFG_WRITEABLE',			'Skrivbar');
    define('_XMAP_CFG_UNWRITEABLE',			'Ej skrivbar');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'g�r EJ SKRIVBAR efter sparande');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Forcera skrivskyddet vid sparandet');
    define('_XMAP_GOOGLE_LINK',				'Googlel�nk');
    define('_XMAP_CFG_INCLUDE_LINK',		'Osynlig l�nk till f�rfattaren');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'Specificera meny ID�s du inte vill ha med i platskartan.<br /><strong>OBS</strong><br />separera ID�n med kommatecken!');

    // -- Menus --------------------------------------------------------------------
    define('_XMAP_CFG_SET_ORDER',			'Ange menyns sorteringsordning');
    define('_XMAP_CFG_MENU_SHOW',			'Visa');
    define('_XMAP_CFG_MENU_REORDER',		'Sortera om');
    define('_XMAP_CFG_MENU_ORDER',			'Ordning');
    define('_XMAP_CFG_MENU_NAME',			'Menynamn');
    define('_XMAP_CFG_DISABLE',				'Klicka f�r att avaktivera');
    define('_XMAP_CFG_ENABLE',				'Klicka f�r aktivering');
    define('_XMAP_SHOW',					'Visa');
    define('_XMAP_NO_SHOW',					'Visa\'s inte');

    // -- Toolbar ------------------------------------------------------------------
    define('_XMAP_TOOLBAR_SAVE', 			'Spara');
    define('_XMAP_TOOLBAR_CANCEL', 			'Avbryt');

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG',				'Spr�kfil [ %s ] inte funnen, laddade standardspr�ket: english<br />');
    define('_XMAP_ERR_CONF_SAVE',			'FEL: Misslyckades att spara konfigurationen.');
    define('_XMAP_ERR_NO_CREATE',			'FEL: G�r inte att skapa inst�llningstabell');
    define('_XMAP_ERR_NO_DEFAULT_SET',		'FEL: G�r inte att �terst�lla standardinst�llningar');
    define('_XMAP_ERR_NO_PREV_BU',			'VARNING: Gick inte att radera f�rg�ende backup');
    define('_XMAP_ERR_NO_BACKUP',			'FEL: Klarar inte att skapa backup');
    define('_XMAP_ERR_NO_DROP_DB',			'FEL: Klarar inte att sl�ppa inst�llningstabell');
    define('_XMAP_ERR_NO_SETTINGS',			'FEL: G�r inte att ladda inst�llningar fr�n databas: <a href="%s">Skapa inst�llningstabell</a>');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',		'Inst�llningar �terst�llda');
    define('_XMAP_MSG_SET_BACKEDUP',		'Inst�llningar sparade');
    define('_XMAP_MSG_SET_DB_CREATED',		'Inst�llningstabell skapad');
    define('_XMAP_MSG_SET_DEF_INSERT',		'Grundinst�llningar infogade');
    define('_XMAP_MSG_SET_DB_DROPPED',		'Xmap\'s tabeller har blivit sparade!');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',						'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'�ndra mall'); // �ndra template
	
    // -- Sitemap (Frontend) -------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT',		'L�nk �ppnar nytt f�nster');
	
    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'Visas p� webbplatsen');
    define('_XMAP_CFG_MENU_SHOW_XML',		'Visa i XML webbplatskarta');
    define('_XMAP_CFG_MENU_PRIORITY',		'Prioritet');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'�ndra periodicitet');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',	'Alltid');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',	'Timvis');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',	'Dagligen');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',	'Veckovis');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',	'M�natligen');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',	'�rligen');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',	'Aldrig');

    define('_XMAP_TIT_SETTINGS_OF',			'Inst�llningar f�r %s');
    define('_XMAP_TAB_SITEMAPS',			'Platskartor');
    define('_XMAP_MSG_NO_SITEMAPS',			'Det �r ingen platskarta skapad �nnu');
    define('_XMAP_MSG_NO_SITEMAP',			'Denna platskarta �r ej tillg�nglig');
    define('_XMAP_MSG_LOADING_SETTINGS',		'Laddar inst�llningar...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',	'Fel, kan inte �ppna platskartan');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Fel, kan inte spara property f�r platskartan.');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Fel, kan inte rensa platskartans cache');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'Kan inte radera default platskarta!');
    define('_XMAP_MSG_CACHE_CLEANED',			'Cacheminnet �r rensat!');
    define('_XMAP_CHARSET',					'UTF-8');
    define('_XMAP_SITEMAP_ID',				'Sitemap\'s ID');
    define('_XMAP_ADD_SITEMAP',				'L�gg till platskarta');
    define('_XMAP_NAME_NEW_SITEMAP',		'Ny platskarta');
    define('_XMAP_DELETE_SITEMAP',			'Radera');
    define('_XMAP_SETTINGS_SITEMAP',		'Inst�llningar');
    define('_XMAP_COPY_SITEMAP',			'Kopiera');
    define('_XMAP_SITEMAP_SET_DEFAULT',		'�terst�ll grundinst�llning');
    define('_XMAP_EDIT_MENU',				'Alternativ');
    define('_XMAP_DELETE_MENU',				'Radera');
    define('_XMAP_CLEAR_CACHE',				'Rensa cacheminnet');
    define('_XMAP_MOVEUP_MENU',				'Upp');
    define('_XMAP_MOVEDOWN_MENU',			'Ner');
    define('_XMAP_ADD_MENU',				'L�gg till menyer');
    define('_XMAP_COPY_OF',					'Kopia av %s');
    define('_XMAP_INFO_LAST_VISIT',			'Sista bes�k');
    define('_XMAP_INFO_COUNT_VIEWS',		'Antalet bes�k');
    define('_XMAP_INFO_TOTAL_LINKS',		'Antalet l�nkar');
    define('_XMAP_CFG_URLS',				'Platskartans\'ornas URL');
    define('_XMAP_XML_LINK_TIP',			'Kopiera l�nk och �verl�mna till Google och Yahoo');
    define('_XMAP_HTML_LINK_TIP',			'Detta �r URL f�r platskartan\'orna. Du kan anv�nda den f�r att skapa poster till dina menyer.');
    define('_XMAP_CFG_XML_MAP',				'XML platskarta');
    define('_XMAP_CFG_HTML_MAP',			'HTML platskarta');
    define('_XMAP_XML_LINK',				'Googlel�nk');
    define('_XMAP_CFG_XML_MAP_TIP',			'XML-filen skapad f�r s�kmotorerna');
    define('_XMAP_ADD',						'Spara');
    define('_XMAP_CANCEL',					'Avbryt');
    define('_XMAP_LOADING',					'Laddar...');
    define('_XMAP_CACHE',					'Cache');
    define('_XMAP_USE_CACHE',				'Anv�nd Cache');
    define('_XMAP_CACHE_LIFE_TIME',			'Cache livsl�ngd');
    define('_XMAP_NEVER_VISITED',			'Aldrig');


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
