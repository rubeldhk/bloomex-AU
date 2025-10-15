<?php 
/* @package Xmap
 * @author Guillermo Vargas, http://joomla.vargas.co.cr
 * Turkish translation by http://www.turkiye-destani.com  
 */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

if( !defined( 'JOOMAP_LANG' )) {
    define('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define('_XMAP_CFG_COM_TITLE',			'Xmap Ayarlar');
    define('_XMAP_CFG_OPTIONS',			'G�r�nt�leme Ayarlar�');
    define('_XMAP_CFG_CSS_CLASSNAME',		'CSS Class Ad�');
    define('_XMAP_CFG_EXPAND_CATEGORIES',	'��erik Kategorilerini Geni�let');
    define('_XMAP_CFG_EXPAND_SECTIONS',	'��erik B�l�mlerini Geni�let');
    define('_XMAP_CFG_SHOW_MENU_TITLES',	'Men� Ba�l�klar�n� G�ster');
    define('_XMAP_CFG_NUMBER_COLUMNS',	'Kolon Say�s�');
    define('_XMAP_EX_LINK',				'D�� Ba�lant�y� ��aretle');
    define('_XMAP_CFG_CLICK_HERE', 		'Buraya T�klay�m');
    define('_XMAP_CFG_GOOGLE_MAP',		'Google Site Haritasi');
    define('_XMAP_EXCLUDE_MENU',			'D��lanacak Men� ID leri');
    define('_XMAP_TAB_DISPLAY',			'G�r�nt�leme');
    define('_XMAP_TAB_MENUS',				'Men�ler');
    define('_XMAP_CFG_WRITEABLE',			'Yaz�labilir');
    define('_XMAP_CFG_UNWRITEABLE',		'Yaz�lamaz');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Kaydettikten sonra yaz�lamaz yap');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Kay�t ederken yaz�labilme iznini de�i�tir');
    define('_XMAP_GOOGLE_LINK',			'Google Ba�lant�s�');
    define('_XMAP_CFG_INCLUDE_LINK',		'Yazara g�r�nmez ba�lant�');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'Eklemek istemedi�iniz men� ID lerini belirtiniz.<br /><strong>NOT</strong><br />ID leri virgul ile ay�r�n�z!');

    // -- Menus --------------------------------------------------------------------
    define('_XMAP_CFG_SET_ORDER',			'Men� G�r�nt�leme S�ras�n� Ayarla');
    define('_XMAP_CFG_MENU_SHOW',			'G�ster');
    define('_XMAP_CFG_MENU_REORDER',		'Yeniden S�rala');
    define('_XMAP_CFG_MENU_ORDER',		'S�rala');
    define('_XMAP_CFG_MENU_NAME',			'Men� �smi');
    define('_XMAP_CFG_DISABLE',			'Kapatmamak i�in t�klay�n�z.');
    define('_XMAP_CFG_ENABLE',			'A�mak i�in t�klay�n�z');
    define('_XMAP_SHOW',					'G�ster');
    define('_XMAP_NO_SHOW',				'G�sterme');

    // -- Toolbar ------------------------------------------------------------------
    define('_XMAP_TOOLBAR_SAVE', 			'Kaydet');
    define('_XMAP_TOOLBAR_CANCEL', 		'�ptal');

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG',			'[ %s ] dil dosyas� bulunamad�, varsay�lan dil: �ngilizce<br />');
    define('_XMAP_ERR_CONF_SAVE',         'HATA: Ayarlar kay�t edilemedi.');
    define('_XMAP_ERR_NO_CREATE',         'HATA: Ayarlar tablosu yarat�lamad�');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'HATA: Varsay�lan ayarlar y�klenemedi');
    define('_XMAP_ERR_NO_PREV_BU',        'UYARI: �nceki yedekleme silinemedi');
    define('_XMAP_ERR_NO_BACKUP',         'HATA: Yedekleme olu�turulamad�');
    define('_XMAP_ERR_NO_DROP_DB',        'HATA: Ayarlar tablosu bo�alt�lamad�');
    define('_XMAP_ERR_NO_SETTINGS',		'HATA: Veritaban�ndak� ayarlar y�klenemedi: <a href="%s">Ayarlar tablosu yarat</a>');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      'Settings restored');
    define('_XMAP_MSG_SET_BACKEDUP',      'Ayarlar kaydedildi');
    define('_XMAP_MSG_SET_DB_CREATED',    'Ayarlar tablosu yarat�ld�');
    define('_XMAP_MSG_SET_DEF_INSERT',    'Varsay�lan ayarlar y�klendi');
    define('_XMAP_MSG_SET_DB_DROPPED','Xmap\'s tables have been saved!');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'Tema d�zenle'); // Edit template
	
    // -- Sitemap (Frontend) -------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT',	'Ba�lant�y� yeni pencerede a�');
	
    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'Sitede goster');
    define('_XMAP_CFG_MENU_SHOW_XML',		'XML Site Haritasinda g�ster');
    define('_XMAP_CFG_MENU_PRIORITY',		'Onem');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'De�i�me S�kl���');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Herzaman');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Saatlik');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'G�nl�k');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Haftal�k');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Ayl�k');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Y�ll�k');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Hi�bir Zaman');

    define('_XMAP_TIT_SETTINGS_OF',			'%s i�in se�imler');
    define('_XMAP_TAB_SITEMAPS',			'Site Haritalar�');
    define('_XMAP_MSG_NO_SITEMAPS',			'Yarat�lm�� Site Haritas� Yok');
    define('_XMAP_MSG_NO_SITEMAP',			'Bu Site Haritas� haz�r de�il');
    define('_XMAP_MSG_LOADING_SETTINGS',		'Y�kleme Se�imleri...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Hata. Site Haritas�n� y�kleyemiyor.');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Hata. Site Haritasi kay�t edilemiyor.');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Hata. Site Haritas� cache silinemiyor');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'Varsay�lan Site Haritas� silinemiyor!');
    define('_XMAP_MSG_CACHE_CLEANED',			'Cache temizlendi!');
    define('_XMAP_CHARSET',				'ISO-8859-1');
    define('_XMAP_SITEMAP_ID',				'Site Haritas� ID');
    define('_XMAP_ADD_SITEMAP',				'Site Haritas� Ekle');
    define('_XMAP_NAME_NEW_SITEMAP',			'Yeni Site Haritas�');
    define('_XMAP_DELETE_SITEMAP',			'Sil');
    define('_XMAP_SETTINGS_SITEMAP',			'Ayarlar');
    define('_XMAP_COPY_SITEMAP',			'Kopyala');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'Varsay�lan Olarak Ata');
    define('_XMAP_EDIT_MENU',				'Se�enekler');
    define('_XMAP_DELETE_MENU',				'Sil');
    define('_XMAP_CLEAR_CACHE',				'Cache temizle');
    define('_XMAP_MOVEUP_MENU',		'Yukar�');
    define('_XMAP_MOVEDOWN_MENU',	'A�a��');
    define('_XMAP_ADD_MENU',		'Men� ekle');
    define('_XMAP_COPY_OF',		'%s\nin kopyas�');
    define('_XMAP_INFO_LAST_VISIT',	'En son ziyaret');
    define('_XMAP_INFO_COUNT_VIEWS',	'Ziyaret say�s�');
    define('_XMAP_INFO_TOTAL_LINKS',	'Ba�lant� say�s�');
    define('_XMAP_CFG_URLS',		'Site Haritas�n�n URL\'si');
    define('_XMAP_XML_LINK_TIP',	'Ba�lant�y� kopyala ve Google ve Yahoo\'ya g�nder');
    define('_XMAP_HTML_LINK_TIP',	'Bu Site Haritas�n�n URL\'si. Men� eklemek i�in kullanabilirsiniz.');
    define('_XMAP_CFG_XML_MAP',		'XML Site Haritas�');
    define('_XMAP_CFG_HTML_MAP',	'HTML Site Haritas�');
    define('_XMAP_XML_LINK',		'Google ba�lant�s�');
    define('_XMAP_CFG_XML_MAP_TIP',	'Arama motorlar� i�in yarat�lan XML dosyas�');
    define('_XMAP_ADD', 'Kaydet');
    define('_XMAP_CANCEL', '�ptal');
    define('_XMAP_LOADING', 'Y�kleniyor...');
    define('_XMAP_CACHE', 'Cache');
    define('_XMAP_USE_CACHE', 'Cache Kullan');
    define('_XMAP_CACHE_LIFE_TIME', 'Cache �mr�');
    define('_XMAP_NEVER_VISITED', 'Hi�bir Zaman');


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
