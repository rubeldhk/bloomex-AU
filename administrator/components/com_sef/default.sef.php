<?php
// config.custom.sef.php : custom.configuration file for sh404SEF
// Version_1.3.4 - build_288 - Joomla - <a href="http://extensions.siliana.com/">extensions.Siliana.com/</a>

// DO NOT REMOVE THIS LINE :
if (!defined('_VALID_MOS')) die('Direct Access to this location is not allowed.');
// DO NOT REMOVE THIS LINE


if (!defined('SH404SEF_COMPAT_SHOW_SECTION_IN_CAT_LINKS')) {

// SECTION : GLOBAL PARAMETERS for sh404sef ---------------------------------------------------------------------

// compatibility with past version. Set to 0 so that
// section is not added in (table) category links. This was a bug in past versions
// as sh404SEF would not insert section, even if ShowSection param was set to Yes
DEFINE ('SH404SEF_COMPAT_SHOW_SECTION_IN_CAT_LINKS', 1);

// set to 1 if using other than port 80 for http
DEFINE ('sh404SEF_USE_NON_STANDARD_PORT', 0);

// if not 0, will be used instead of Homepage itemid to display 404 error page
DEFINE ('sh404SEF_PAGE_NOT_FOUND_FORCED_ITEMID', 0);

// if not 0, sh404SEF will do a 301 redirect from http://yoursite.com/index.php
// or http://yoursite.com/index.php?lang=xx to http://yoursite.com/
// this may not work on some web servers, which transform yoursite.com into
// yoursite.com/index.php, thus creating and endless loop. If your server does
// that, set this param to 0
DEFINE ('sh404SEF_REDIRECT_IF_INDEX_PHP', 0);

// if superadmin logged in, force non-sef, for testing and setting up purpose
DEFINE ('sh404SEF_NON_SEF_IF_SUPERADMIN', 0);

// set to 1 to prevent 303 auto redirect based on user language
// use with care, will prevent language switch to work for users without javascript
DEFINE ('sh404SEF_DE_ACTIVATE_LANG_AUTO_REDIRECT', 1);

// if 1, SEF URLs will only be built for installed components.
DEFINE ('sh404SEF_CHECK_COMP_IS_INSTALLED', 1);

// if 1, all outbound links on page will be reached through a redirect
// to avoid page rank leakage
DEFINE ('sh404SEF_REDIRECT_OUTBOUND_LINKS', 0);

// time to live for url cache in hours : default = 168h = 1 week
// Set to 0 to keep cache forever
DEFINE ('SH404SEF_URL_CACHE_TTL', 168);

// number of cache write before checking cache TTL.
DEFINE ('SH404SEF_URL_CACHE_WRITES_TO_CHECK_TTL', 1000);

// if set to 1, an email will be send to site admin when an attack is logged
// if the site is live, you could be drowning in email rapidly !!!
DEFINE ('sh404SEF_SEC_MAIL_ATTACKS_TO_ADMIN', 0);

DEFINE ('sh404SEF_SEC_EMAIL_TO_ADMIN_SUBJECT', 'Your site %sh404SEF_404_SITE_NAME% was subject to an attack');
DEFINE ('sh404SEF_SEC_EMAIL_TO_ADMIN_BODY',
'Hello !'."nn".'This is sh404SEF security component, running at your site (%sh404SEF_404_SITE_URL%).'
."nn".'I have just blocked an attack on your site. Please check details below : '
."n".  '------------------------------------------------------------------------'
."n".  '%sh404SEF_404_ATTACK_DETAILS%'
."n".  '------------------------------------------------------------------------'
."nn".'Thanks for using sh404SEF!'
."nn"
);

// number of pages between checks to remove old log files
// if 1, we check at every page request
DEFINE ('SH404SEF_PAGES_TO_CLEAN_LOGS', 10000);

// SECTION : Deeppockets plugin parameters -----------------------------------------------------------------

// set to 0 to have no cat inserted  /234-ContentTitle/
// set to 1 to have only last cat added /123-CatTitle/234-ContentTitle/
// set to 2 to have all nested cats inserted /456-Cat1Title/123-Cat2Title/234-ContentTitle/
define ('SH404SEF_DP_INSERT_ALL_CATEGORIES', 2);

// if non-zero, id of each cat will be inserted in the url /123-CatTitle/
define ('SH404SEF_DP_INSERT_CAT_ID', 0);

// if non-zero, id of each content element will be inserted in url /234-ContentTitle/
define ('SH404SEF_DP_INSERT_CONTENT_ID', 0);

// if non-zero, DP links to content element will be identical to those
// generated for Joomla regular content - usefull if this content can also be
// accessed outside of DP, to avoid duplicate content penalties
define ('SH404SEF_DP_USE_JOOMLA_URL', 0);

// SECTION : com_smf plugin parameters --------------------------------------------------------------------------
// set to 1 use simple URLs, without all details
define ('sh404SEF_SMF_PARAMS_SIMPLE_URLS', 0);

// prefix used in the DB for the SMF tables
define ('sh404SEF_SMF_PARAMS_TABLE_PREFIX', 'smf_');

// not used
define ('sh404SEF_SMF_PARAMS_ENABLE_STICKY', 0);

// SECTION : SOBI2 plugin parameters ----------------------------------------------------------------------------

// set to 1 to always include categories in SOBI2 entries
// details pages url
define ('sh404SEF_SOBI2_PARAMS_ALWAYS_INCLUDE_CATS', 0);

// set to 1 so that entry id is prepended to url
define ('sh404SEF_SOBI2_PARAMS_INCLUDE_ENTRY_ID', 0);

// set to 1 so that category id is prepended to category name
define ('sh404SEF_SOBI2_PARAMS_INCLUDE_CAT_ID', 0);

// end of parameters
}  // end of initial test