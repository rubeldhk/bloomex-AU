<?php defined( '_VALID_MOS' ) or die( 'No se permite el acceso directo a esta posición.' ); ?>
<?php
/* @package joomap
 * @author: Daniel Grothe, http://www.ko-ca.com/
 * translated by: Andrés Victoria Ortega
 */

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define("_JOOMAP_CFG_COM_TITLE", "Configuración de Joomap");
    define("_JOOMAP_CFG_OPTIONS", "Opciones de configuración");
    define("_JOOMAP_CFG_TITLE", "Título");
    define("_JOOMAP_CFG_CSS_CLASSNAME", "Nombre de la clase CSS");
    define("_JOOMAP_CFG_EXPAND_CATEGORIES","Expandir el contenido de las categorías");
    define("_JOOMAP_CFG_EXPAND_SECTIONS","Expandir el contenido de las secciones");
    define("_JOOMAP_CFG_SHOW_MENU_TITLES", "Mostrar los títulos de los menús");
    define("_JOOMAP_CFG_NUMBER_COLUMNS", "Número de columnas");
    define('_JOOMAP_EX_LINK', 'Marcar enlaces externos');
    define('_JOOMAP_CFG_CLICK_HERE', 'Pulse aquí');
    define('_JOOMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_JOOMAP_EXCLUDE_MENU',			'Excluir IDs del menú');
    define('_JOOMAP_TAB_DISPLAY',			'Mostrar');
    define('_JOOMAP_TAB_MENUS',				'Menús');
    define('_JOOMAP_CFG_WRITEABLE',			'No protegido contra escritura');
    define('_JOOMAP_CFG_UNWRITEABLE',		'Protegido contra escritura');
    define('_JOOMAP_MSG_MAKE_UNWRITEABLE',	'Tras grabarlo marcarlo como [ <span style="color: red;">protegido contra escritura</span> ]');
    define('_JOOMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Anular la protección contra escritura al grabar');
    define('_JOOMAP_GOOGLE_LINK',			'Googlelink');

    // -- Tips ---------------------------------------------------------------------
    define('_JOOMAP_EXCLUDE_MENU_TIP',		'Especifica los IDs del menú que no quiere incluir en el mapa del sitio.<br /><strong>NOTA</strong><br />¡Separe los IDs con comas!');
    define('_JOOMAP_CFG_GOOGLE_MAP_TIP',	'Fichero XML generado para el mapa del sitio de Google');
    define('_JOOMAP_GOOGLE_LINK_TIP',		'Copie el enlace y envíelo a Google');

    // -- Menus --------------------------------------------------------------------
    define("_JOOMAP_CFG_SET_ORDER", "Seleccionar el orden en el que se muestran los menús");
    define("_JOOMAP_CFG_MENU_SHOW", "Mostrar");
    define("_JOOMAP_CFG_MENU_REORDER", "Reordenar");
    define("_JOOMAP_CFG_MENU_ORDER", "Ordenar");
    define("_JOOMAP_CFG_MENU_NAME", "Nombre del Menú");
    define("_JOOMAP_CFG_DISABLE", "Pulse para desactivar");
    define("_JOOMAP_CFG_ENABLE", "Pulse para activar");
    define('_JOOMAP_SHOW','Mostrar');
    define('_JOOMAP_NO_SHOW','No mostrar');

    // -- Toolbar ------------------------------------------------------------------
    define("_JOOMAP_TOOLBAR_SAVE", "Guardar");
    define("_JOOMAP_TOOLBAR_CANCEL", "Cancelar");

    // -- Errors -------------------------------------------------------------------
    define('_JOOMAP_ERR_NO_LANG','No se ha encontrado el lenguaje [ %s ], se carga el lenguaje por defecto: inglés<br />'); // %s = $GLOBALS['mosConfig_lang']
    define('_JOOMAP_ERR_CONF_SAVE',         '<h2>Fallo al guardar la configuración.</h2>');
    define('_JOOMAP_ERR_NO_CREATE',         'ERROR: No se ha podido crear la tabla de opciones');
    define('_JOOMAP_ERR_NO_DEFAULT_SET',    'ERROR: No se han podido insertar las opciones por defecto');
    define('_JOOMAP_ERR_NO_PREV_BU',        'ATENCIÓN: No se ha podido borrar la copia de seguridad anterior');
    define('_JOOMAP_ERR_NO_BACKUP',         'ERROR: No se ha podido crear la copia de seguridad');
    define('_JOOMAP_ERR_NO_DROP_DB',        'ERROR: No se ha podido borrar la tabla de opciones');

    // -- Config -------------------------------------------------------------------
    define('_JOOMAP_MSG_SET_RESTORED',      'Opciones restauradas<br />');
    define('_JOOMAP_MSG_SET_BACKEDUP',      'Opciones guardadas<br />');
    define('_JOOMAP_MSG_SET_DB_CREATED',    'Tabla de opciones creada<br />');
    define('_JOOMAP_MSG_SET_DEF_INSERT',    'Opciones por defecto insertadas');
    define('_JOOMAP_MSG_SET_DB_DROPPED',    'Tabla de opciones borrada');
	
    // -- CSS ----------------------------------------------------------------------
    define('_JOOMAP_CSS',					'JooMap CSS');
    define('_JOOMAP_CSS_EDIT',				'Editar plantilla'); // Edit template
	
    // -- Sitemap ------------------------------------------------------------------
    define('_JOOMAP_SHOW_AS_EXTERN_ALT','El enlace se abre en una nueva ventana');
    define('_JOOMAP_PREVIEW','Previsualización');
    define('_JOOMAP_SITEMAP_NAME','Mapa del sitio');
}
?>
