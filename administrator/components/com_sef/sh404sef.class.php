<?php
/**
 * SEF module for Joomla!
 * Originally written for Mambo as 404SEF by W. H. Welch.
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: sh404sef.class.php 365 2008-04-09 20:27:19Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20}
 */
 
// Security check to ensure this file is being included by a parent file.
if (!defined('_VALID_MOS')) die('Direct Access to this location is not allowed.');

DEFINE ('sh404SEF_URLTYPE_404', -2);
DEFINE ('sh404SEF_URLTYPE_NONE', -1);
DEFINE ('sh404SEF_URLTYPE_AUTO', 0);
DEFINE ('sh404SEF_URLTYPE_CUSTOM', 1);
DEFINE ('sh404SEF_MAX_SEF_URL_LENGTH', 255);

DEFINE ('sh404SEF_HOMEPAGE_CODE', 'index.php?'.md5('sh404SEF Homepage url code'));

DEFINE ('SH404SEF_STANDARD_ADMIN', 1);  // define possible levels for adminstration complexity
DEFINE ('SH404SEF_ADVANCED_ADMIN', 2);

if (!defined('sh404SEF_ADMIN_ABS_PATH')) {
  define('sh404SEF_ADMIN_ABS_PATH', str_replace('\\','/',dirname(__FILE__)).'/');
}  
if (!defined('sh404SEF_ABS_PATH')) {
  define('sh404SEF_ABS_PATH', str_replace( '/administrator/components/com_sef', '', sh404SEF_ADMIN_ABS_PATH) );
}
if (!defined('sh404SEF_FRONT_ABS_PATH')) {
  define('sh404SEF_FRONT_ABS_PATH', sh404SEF_ABS_PATH.'components/com_sef/');
}


class shMosSEF extends mosDBTable
{
	/** @var int */
	var $id		= null;
	/** @var int */
	var $cpt	= null;
	/** @var int */
	var $rank	= null;
	/** @var string */
	var $oldurl	= null;
	/** @var string */
	var $newurl	= null;
	/** @var tinyint */
	/** @var date */
	var $dateadd	= null;
	var $can_not_be_deleted	= 0;

	function __construct( &$_db ) {
        parent::__construct( '#__redirection', 'id', $_db );
	}
	
	function check() {
        //initialize
        $this->_error = null;
        $this->oldurl = trim($this->oldurl);
        $this->newurl = trim($this->newurl);
        // check for valid URLs
        if (($this->oldurl == '')||($this->newurl == '')){
            $this->_error .= _COM_SEF_EMPTYURL;
            return false;
        }
        if (preg_match("/^\//i", $this->oldurl)) {
            $this->_error .= _COM_SEF_NOLEADSLASH;
        }
        if ((preg_match("/^index.php/i", $this->newurl)) === false ) {
            $this->_error .= _COM_SEF_BADURL;
        }
        // V 1.2.4.t remove this check. We check for pre-existing non-sef instead of SEF
        if (is_null($this->_error)) {
	        // check for existing URLS
	        $this->_db->setQuery( "SELECT id,oldurl,can_not_be_deleted FROM #__redirection WHERE `newurl` LIKE '".$this->newurl."'");
	        $this->_db->loadObject($xid);
	        // V 1.3.1 don't raise error if both newurl and old url are same. It means we may have changed alias list
	        if ($xid && $xid->id != intval( $this->id )) {
	            $this->_error = _COM_SEF_URLEXIST;
	            return false;
	        }
	        $identical = $xid->id == intval( $this->id ) && $xid->oldurl == $this->oldurl  && $xid->can_not_be_deleted == $this->can_not_be_deleted;
	        return $identical ? 'identical' : true;
	      }else{
	   	    return false;
	      }
	}
}

class sh404SEFMeta extends mosDBTable
{
	/** @var int */
	var $id		= null;
	/** @var string */
	var $newurl	= null;
	/** @var string */
	var $metadesc	= null;
	/** @var string */
	var $metakey	= null;
	/** @var string */
	var $metatitle	= null;
	/** @var string */
	var $metalang	= null;
	/** @var string */
	var $metarobots	= null;
	
	function __construct( &$_db ) {
        parent::__construct( '#__sh404SEF_meta', 'id', $_db );
	}
	
	function check() {
        //initialize
        $this->_error = null;
        $this->newurl = trim($this->newurl);
        $this->metadesc = trim($this->metadesc);
        $this->metakey = trim($this->metakey);
        $this->metatitle = trim($this->metatitle);
        $this->metalang = trim($this->metalang);
        $this->metarobots = trim($this->metarobots);
        // check for valid URLs
        if ($this->newurl == ''){
            $this->_error .= _COM_SEF_EMPTYURL;
            return false;
        }

        if ((preg_match("/^index.php/i", $this->newurl)) === false ) {
            $this->_error .= _COM_SEF_BADURL;
        }
        if (is_null($this->_error)) {
	        // check for existing URLS
	        $this->_db->setQuery( "SELECT id FROM #__sh404SEF_meta WHERE `newurl` LIKE '".$this->newurl."'");
	        $xid = intval( $this->_db->loadResult() );
	        if ($xid && $xid != intval( $this->id )) {
	            $this->_error = _COM_SEF_URLEXIST;
	            return false;
	        }
	        return true;
	   }else{
	   	 return false;
	   }
	}
}

class SEFConfig {

  /* string,  version number */
	var $version = 'Version_1.3.4 - build_288 - Joomla - <a href="http://extensions.siliana.com/">extensions.Siliana.com</a>';
	/* boolean, is 404 SEF enabled  */
	var $Enabled = false;
	/* char,  Character to use for url replacement */
	var $replacement = '-';
	/* char,  Character to use for page spacer */
	var $pagerep = '-';
	/* strip these characters */
	var $stripthese = ',|~|!|@|%|^|(|)|<|>|:|;|{|}|[|]|&|`|�|�|�|�|�|�|�|�|�|�|�|�';  // V 1.2.4.s removed *, breaks Bookmarks
	/* characters replacement table v 1.2.4.f April 4, 2007*/
	var $shReplacements = '�|S, �|O, �|Z, �|s, �|oe, �|z, �|Y, �|Y, �|u, �|A, �|A, �|A, �|A, �|A, �|A, �|A, �|C, �|E, �|E, �|E, �|E, �|I, �|I, �|I, �|I, �|D, �|N, �|O, �|O, �|O, �|O, �|O, �|O, �|U, �|U, �|U, �|U, �|Y, �|s, �|a, �|a, �|a, �|a, �|a, �|a, �|a, �|c, �|e, �|e, �|e, �|e, �|i, �|i, �|i, �|i, �|o, �|n, �|o, �|o, �|o, �|o, �|o, �|o, �|u, �|u, �|u, �|u, �|y, �|y, �|ss';
	/* string,  suffix for "files" */
	var $suffix = '.html';
	/* string,  file to display when there is none */
	var $addFile = '';
	/* trims friendly characters from where they shouldn't be */
	var $friendlytrim = '-|.';
	/* boolean, convert url to lowercase */
	var $LowerCase = false;
	/* boolean, include the section name in url */
	var $ShowSection = false;
	/* boolean, exclude the category name in url */
	var $ShowCat = true;
	/* boolean, use the title_alias instead of the title */
	var $UseAlias = true;
	/* int, id of #__content item to use for static page */
	var $page404 = 0;
	/* Array, contains predefined components. */
	var $predefined = array(
	   	//'contact',
	   	'frontpage',
	   	//'login',
	   	//'newsfeeds',
	   	//'search',
	   	'sef'//,
	   	//'weblinks'
	   	);
	/* Array, contains components 404 SEF will ignore. */
	var $skip = array();
	/* Array, contains components 404 SEF will not add to the DB.
	 * default style URLs will be generated for these components instead
	 */
	var $nocache = array('events');
	// shumisha : additional parameters
	/* Array, contains components 404 SEF will override their own sef_ext file if it has its own plugin. */
	var $shDoNotOverrideOwnSef = array();
	/* boolean,  true (default) to log 404 errors to DB, false otherwise  */
 	var $shLog404Errors = true;
	/* boolean,  true (default) to use in mem cache, false to disable  */
 	var $shUseURLCache = true;
 	/* integer, max number of URL couple (sef + non-sef url) allowed in cache */
	var $shMaxURLInCache = 10000;
	/* boolean,  true (default) to translate texts in URL */
	var $shTranslateURL = true;
	/* boolean,  true (default) will always insert language iso code in URL (for other than default language) */
	var $shInsertLanguageCode = true;
	/* Array, contains components sh404SEF will NOT translate URLs */
	var $notTranslateURLList = array();  // V 1.2.4.m
	/* Array, contains components sh404SEF will NOT insert iso code in URL */
	var $notInsertIsoCodeList = array();
	// cache management
	/* boolean, true if insert Itemid of menu item is none exists */
	var $shInsertGlobalItemidIfNone = true;
	/* boolean, if true insert title of menu item if no Itemid exists for the URL*/
	var $shInsertTitleIfNoItemid = false;
	/* boolean, true if always insert title of menu item. URL Itemid is used, if any, or menu item title*/
	var $shAlwaysInsertMenuTitle= false;
	/* boolean, true if always append Itemid of non-sef URL (or of current menu item if none) to SEF URL */
	var $shAlwaysInsertItemid= false; // v 1.2.4.f
	/* string, default menu name, to be used if $shAlwaysInsertMenuTitle is true, to override menu title */
	var $shDefaultMenuItemName = '';
	/* boolean, if true, Getvars not used in URl will be reappend to it  */
	var $shAppendRemainingGETVars = true;
  
  // virtuemart management
  /* boolean, true if always insert title of shop menu item */
  var $shVmInsertShopName= false;  
  /* boolean, if true, product ID will be prepended to product name */
  var $shInsertProductId = false;  
  /* boolean, if true, product sku will be used instead of name */
  var $shVmUseProductSKU = false; 
  /* boolean, if true, product Manufacturer name will be included in URL */
  var $shVmInsertManufacturerName = false;
  /* boolean, if true, product if will be prepended to manufacturer name */
  var $shInsertManufacturerId = false;
  /* integer, if 0, no categories will be inserted in URL for a product
              if 1, only 'last' category will be inserted in URL
              if 2, all nested categories will be inserted in URL */
  var $shVMInsertCategories = 1;
  
  /* boolean, if true, an additional text will be appended to sef URl when browsing categories
  * ie : .../product_cat/view-all-products.html VS .../product_cat/     */
  var $shVmAdditionalText = true;
  /* boolean, if true, a flypage name will be inserted in URL     */
  var $shVmInsertFlypage = true;
  
  /* boolean, if true, category id will be prepended to category name */
  var $shInsertCategoryId = false;
  /* boolean, if true, numerical id will be prepended to URL, for inclusion in Googlenews  */
  var $shInsertNumericalId = false;
  /* text, list of categories of content to which numerical id should be applied  */
  var $shInsertNumericalIdCatList = '';
  /* boolean, if true, non-sef URL like index.php?option=com_content&task=view&id=12&Itemid=2 will be 301-redirected to their sef equivalent */
  var $shRedirectNonSefToSef = true; 
  /* boolean, if true, Joomla sef URL like /content/view/12/61 will be 301-redirected to their sef equivalent */
  var $shRedirectJoomlaSefToSef = true; 
  /* string, should be set to SSL secure URL of site if any used. No trailing / */
  var $shConfig_live_secure_site = '';
  /* boolean, if true, ed non-sef parameter will be interpreted as a iJoomla param in com_content plugin  */
  var $shActivateIJoomlaMagInContent = true;
  /* boolean, if true, issue id of iJoomla magazine will be prepended to category name */
  var $shInsertIJoomlaMagIssueId = false;
  /* boolean, if true, magazine name will be prepended to all URL */
  var $shInsertIJoomlaMagName = false;
  /* boolean, if true, magazine id will be inserted before magazine title */
  var $shInsertIJoomlaMagMagazineId = false;
  /* boolean, if true, article id will be inserted before article title */
  var $shInsertIJoomlaMagArticleId = false;
  
  
  /* boolean, if true, name of menu item leading to Community builder will be prepended to all URL */
  var $shInsertCBName = false;
  /* boolean, if true, user name will be inserte to all URL wher appropriate. Warning : this will
  *  increase DB space used? Normally user id is still passed as a GET param (ie ...?user=245)   
  *  to save space and increase speed  */
  var $shCBInsertUserName = false;
  /* boolean, if true, id of user will be prepended to its name when previous option is activated
  *  in case two users have the same name */
  var $shCBInsertUserId = true;
  /* boolean, if true user pseudo will be used instead of name */
  var $shCBUseUserPseudo = true;
  
  /* integer, default value for Itemid when using lettermand newsletter component */
  var $shLMDefaultItemid = 0;
  
  /* boolean, if true, default name for board will be prepended to URL */
  var $shInsertFireboardName = false;
  /* boolean, if true name of forum category will be inserted in URL */
  var $shFbInsertCategoryName = true;
  /* boolean, if true, Category id will be prepended to category name, in case 2 categories have same name */
  var $shFbInsertCategoryId = false;
  /* boolean, if true, message subject will be inserted in URL */
  var $shFbInsertMessageSubject = true;
  /* boolean, if true message id will be prepended to subject, in case 2 messages have same subject */
  var $shFbInsertMessageId = true;

  /* MyBlog parameters  V 1.2.4.r*/
  var $shInsertMyBlogName = false;
  var $shMyBlogInsertPostId = true;
  var $shMyBlogInsertTagId = false;
  var $shMyBlogInsertBloggerId = true;

  /* Docman parameters  V 1.2.4.r*/
  var $shInsertDocmanName = false;
  var $shDocmanInsertDocId = true;
  var $shDocmanInsertDocName = true;
  /* integer, if 0, no categories will be inserted in URL for a product
              if 1, only 'last' category will be inserted in URL
              if 2, all nested categories will be inserted in URL */
  var $shDMInsertCategories = 1;
  /* boolean, if true, category id will be prepended to category name */
  var $shDMInsertCategoryId = false;
  
  /* boolean, if true, url will be urlencoded, needed for some non-latin languages */
  var $shEncodeUrl = false;  
  
  /* boolean, if true, Itemid from url on homepage with com_content will be removed, so that com_content plugin
  *  can try guess amore appropriate one  */
  var $guessItemidOnHomepage = true; // V 1.2.4.q
  // V 1.2.4.q : added param to force non-sef if https, as we are not through with some shared ssl servers!
  var $shForceNonSefIfHttps = false;
  
  // V 1.2.4.s try SEF without mod_rewrite
  var $shRewriteMode = 1;  // 0 = mod_rewrite, 1 = AcceptpathInfo index.php 2 = AcceptPathInfo index.php?
  var $shRewriteStrings = array('/','/index.php/','/index.php?/');
  
  // V1.2.4.s  record duplicate URL param 
  var $shRecordDuplicates = true;
  var $shRemoveGeneratorTag = true;
  var $shPutH1Tags = false;
  var $shMetaManagementActivated = true;
  var $shInsertContentTableName = true;
  var $shContentTableName = 'Table';
  
  // V 1.2.4.s auto redirect from www to non-www and vice-versa
  var $shAutoRedirectWww = true;
  var $shVmInsertProductName = true;
  
  // V 1.2.4.t
  /* string, exact URL for homepage, to replace the automatic one. Workaround for splash pagesNo trailing / */
  var $shForcedHomePage = '';
  var $shInsertContentBlogName = false;
  var $shContentBlogName = '';
  
  // Mosets Tree params
  var $shInsertMTreeName = false;
  var $shMTreeInsertListingName = true;
  var $shMTreeInsertListingId = true;
  var $shMTreePrependListingId = true;
  /* integer, if 0, no categories will be inserted in URL for a product
              if 1, only 'last' category will be inserted in URL
              if 2, all nested categories will be inserted in URL */
  var $shMTreeInsertCategories = 1;
  /* boolean, if true, category id will be prepended to category name */
  var $shMTreeInsertCategoryId = false;
  var $shMTreeInsertUserName = true;
  var $shMTreeInsertUserId = true;
   
  // iJoomla NewsPortal params
  var $shInsertNewsPName = false;
  var $shNewsPInsertCatId = false;
  var $shNewsPInsertSecId = false; 
   
  /* Remository parameters  V 1.2.4.t*/
  var $shInsertRemoName = false;
  var $shRemoInsertDocId = true;
  var $shRemoInsertDocName = true;
  /* integer, if 0, no categories will be inserted in URL for a product
              if 1, only 'last' category will be inserted in URL
              if 2, all nested categories will be inserted in URL */
  var $shRemoInsertCategories = 1;
  /* boolean, if true, category id will be prepended to category name */
  var $shRemoInsertCategoryId = false; 
   
  // boolean, if true, task = userProfile is accessed through mysite.com/username in CB
  var $shCBShortUserURL = false; //V 1.2.4.t
  
  // a set of boolean vars, to decide what to do with existing data when upgrading sh404SEF
  var $shKeepStandardURLOnUpgrade = true; //V 1.2.4.t
  var $shKeepCustomURLOnUpgrade = true; //V 1.2.4.t
  var $shKeepMetaDataOnUpgrade = true; //V 1.2.4.t
  var $shKeepModulesSettingsOnUpgrade = true; //V 1.2.4.t
  
  // boolean, to decide whether to replace page numbering by headings in multipage articles
  var $shMultipagesTitle = true; //V 1.2.4.t
   
  // compatiblity variables, for sef_ext files usage from OpenSef/SEf Advance
  var $encode_page_suffix = '';
  var $encode_space_char = '';
  var $encode_lowercase = '';
  var $encode_strip_chars = '';
  var $spec_chars_d;
  var $spec_chars;
  var $content_page_format;  // V 1.2.4.r
  var $content_page_name;  // V 1.2.4.r
  
	// V x 
	var $shKeepConfigOnUpgrade = true;
	
	// security parameters  V x
	var $shSecEnableSecurity = true;
	var $shSecLogAttacks = true;
	var $shSecOnlyNumVars = array('catid','itemid','limit', 'limitstart');
	var $shSecAlphaNumVars = array('id');
	var $shSecNoProtocolVars = array('task','option','no_html','mosmsg', 'lang');
	var $ipWhiteList = '';
	var $ipBlackList = '';
	var $uAgentWhiteList = '';
	var $uAgentBlackList = '';
	var $shSecCheckHoneyPot = false;
	var $shSecHoneyPotKey = '';
	var $shSecEntranceText ="<p>Sorry. You are visiting this site from a suspicious IP address, which triggered our protection system.</p>
    <p>If you <strong>ARE NOT</strong> a malware robot of any kind, please accept our apologies for the unconvenience. You can access the page by clicking here : ";
	var $shSecSmellyPotText = "The following link is here to further trap malicious internet robots, so please don't click on it : ";
	var $monthsToKeepLogs = 1;  // = 1 will keep current months log + the month before
	var $shSecActivateAntiFlood = true;
	var $shSecAntiFloodOnlyOnPOST = false;  // if true, antiflood is activated only if there is some POST data, as in a form
	var $shSecAntiFloodPeriod = 10;		// period over which requests from same IP are counted
	var $shSecAntiFloodCount = 10;		// max number of request from same IP in period above
	
	//var $insertSectionInBlogTableLinks = false; // default should be true, but set to false for compat reason
	
	/* Array, contains whether we should translate URLs per language */
	var $shLangTranslateList = array();  // V 1.2.4.m
	/* Array, contains whether we should insert iso code URLs per language */
	var $shLangInsertCodeList = array();
	/* Array, contains list of default initial URL fragement per component */
	var $defaultComponentStringList = array();  // V 1.2.4.m
	/* Array, contains pagination string, per language */
	var $pageTexts = array();
	
	var $shAdminInterfaceType = SH404SEF_STANDARD_ADMIN;
	
	// V 1.3 RC shCustomTags params
	var $shInsertNoFollowPDFPrint = true;
	var $shInsertReadMorePageTitle = true;
	var $shMultipleH1ToH2 = true;
  
	// V 1.3.1 RC
	var $shVmUsingItemsPerPage = false;  // set to true if using drop-down list to select number of items per page
	var $shSecCheckPOSTData = true;		 // if set to yes, POST data will not be checked for mosconfig, script, base64,
										 // standard vars and cmd file in img names
	var $shSecCurMonth = 0;									 
	var $shSecLastUpdated = 0;                    
	var $shSecTotalAttacks = 0;
	var $shSecTotalConfigVars = 0;
	var $shSecTotalBase64 =0;
	var $shSecTotalScripts = 0;
	var $shSecTotalStandardVars = 0;
	var $shSecTotalImgTxtCmd = 0;
	var $shSecTotalIPDenied = 0;
	var $shSecTotalUserAgentDenied = 0;
	var $shSecTotalFlooding = 0;
	var $shSecTotalPHP = 0;
	var $shSecTotalPHPUserClicked = 0;
	// com_smf params
	var $shInsertSMFName = true;										  
	var $shSMFItemsPerPage = 20;
	var $shInsertSMFBoardId = true;
	var $shInsertSMFTopicId = true;
	var $shinsertSMFUserName = false;
	var $shInsertSMFUserId = true;
	
	// other 
	var $appendToPageTitle = '';
	var $prependToPageTitle = '';
	var $debugToLogFile = false;
	var $debugStartedAt = 0;
	var $debugDuration = 3600;  // time in seconds to log debug data to file. if 0, unlimited, default = 1 hour
	
	// V 1.3.1
	var $shInsertOutboundLinksImage = false;
	var $shImageForOutboundLinks = 'external-black.png';  // default = black image
  
	/* Array, contains list of default itemid per component */
	var $defaultComponentItemidList = array();  // V 1.2.4.m
	var $UseDefaultItemids = true;

	// V 1.3.3
	var $defaultParamList = '';  // holds content of /administrator/components/default.sef.php for editing
	
// End of parameters	
	
	function __construct() {
	
	GLOBAL $sef_config_file;
	$sef_config_file = sh404SEF_ADMIN_ABS_PATH.'config/config.sef.php';
	$sef_default_config_file = sh404SEF_ADMIN_ABS_PATH.'default.sef.php';
	$this->shCheckFilesAccess();
	if (file_exists($sef_config_file)) {
		include($sef_config_file);
	}
	if (file_exists($sef_default_config_file)) {
		include($sef_default_config_file);
	}
    // shumisha : 2007-04-01 version was missing ! 
    //if (isset($version))		$this->version		= $version;  // V 1.2.4.r : removed as would prevent update system to work : version was not updated
    // shumisha : 2007-04-01 new parameters ! 
    if (isset($shUseURLCache))		$this->shUseURLCache		= $shUseURLCache;
    // shumisha : 2007-04-01 new parameters ! 
    if (isset($shMaxURLInCache))		$this->shMaxURLInCache		= $shMaxURLInCache;
    // shumisha : 2007-04-01 new parameters ! 
    if (isset($shTranslateURL))		$this->shTranslateURL		= $shTranslateURL;
    //V 1.2.4.m
    if (isset($shInsertLanguageCode))		$this->shInsertLanguageCode		= $shInsertLanguageCode;
    if (isset($notTranslateURLList))		$this->notTranslateURLList		= $notTranslateURLList;
    if (isset($notInsertIsoCodeList))		$this->notInsertIsoCodeList		= $notInsertIsoCodeList;
    
    // shumisha : 2007-04-03 new parameters ! 
    if (isset($shInsertGlobalItemidIfNone))	$this->shInsertGlobalItemidIfNone	= $shInsertGlobalItemidIfNone;
    if (isset($shInsertTitleIfNoItemid))	$this->shInsertTitleIfNoItemid	= $shInsertTitleIfNoItemid;
    if (isset($shAlwaysInsertMenuTitle))	$this->shAlwaysInsertMenuTitle	= $shAlwaysInsertMenuTitle;
    if (isset($shAlwaysInsertItemid))	$this->shAlwaysInsertItemid	= $shAlwaysInsertItemid;
    if (isset($shDefaultMenuItemName))	$this->shDefaultMenuItemName = $shDefaultMenuItemName;
    if (isset($shAppendRemainingGETVars))	$this->shAppendRemainingGETVars = $shAppendRemainingGETVars;
    if (isset($shVmInsertShopName))	$this->shVmInsertShopName = $shVmInsertShopName;
    
    if (isset($shInsertProductId))	$this->shInsertProductId	= $shInsertProductId;
    if (isset($shVmUseProductSKU))	$this->shVmUseProductSKU	= $shVmUseProductSKU;
    if (isset($shVmInsertManufacturerName))		
      $this->shVmInsertManufacturerName = $shVmInsertManufacturerName;
    if (isset($shInsertManufacturerId))	$this->shInsertManufacturerId = $shInsertManufacturerId;
    if (isset($shVMInsertCategories))	$this->shVMInsertCategories= $shVMInsertCategories;
    if (isset($shVmAdditionalText))	$this->shVmAdditionalText= $shVmAdditionalText;
    if (isset($shVmInsertFlypage))	$this->shVmInsertFlypage= $shVmInsertFlypage;
    
    if (isset($shInsertCategoryId))	$this->shInsertCategoryId= $shInsertCategoryId;
    if (isset($shReplacements))	$this->shReplacements= $shReplacements;
    
    if (isset($shInsertNumericalId))	$this->shInsertNumericalId = $shInsertNumericalId;
    if (isset($shInsertNumericalIdCatList))	$this->shInsertNumericalIdCatList = $shInsertNumericalIdCatList;
    
    if (isset($shRedirectNonSefToSef))	$this->shRedirectNonSefToSef = $shRedirectNonSefToSef;
    if (isset($shRedirectJoomlaSefToSef))	$this->shRedirectJoomlaSefToSef = $shRedirectJoomlaSefToSef;
    if (isset($shConfig_live_secure_site))	
      $this->shConfig_live_secure_site = rtrim( $shConfig_live_secure_site, '/');
      
    if (isset($shActivateIJoomlaMagInContent))
     	$this->shActivateIJoomlaMagInContent = $shActivateIJoomlaMagInContent;
    if (isset($shInsertIJoomlaMagIssueId))
     	$this->shInsertIJoomlaMagIssueId = $shInsertIJoomlaMagIssueId;
    if (isset($shInsertIJoomlaMagName))
     	$this->shInsertIJoomlaMagName = $shInsertIJoomlaMagName;
    if (isset($shInsertIJoomlaMagMagazineId))
     	$this->shInsertIJoomlaMagMagazineId = $shInsertIJoomlaMagMagazineId;
    if (isset($shInsertIJoomlaMagArticleId))
     	$this->shInsertIJoomlaMagArticleId = $shInsertIJoomlaMagArticleId;
     	
    if (isset($shInsertCBName))
     	$this->shInsertCBName = $shInsertCBName;
    if (isset($shCBInsertUserName))
     	$this->shCBInsertUserName = $shCBInsertUserName;
    if (isset($shCBInsertUserId))
     	$this->shCBInsertUserId = $shCBInsertUserId;
    if (isset($shCBUseUserPseudo))
     	$this->shCBUseUserPseudo = $shCBUseUserPseudo; 	
     
    if (isset($shInsertMyBlogName))
     	$this->shInsertMyBlogName = $shInsertMyBlogName;
    if (isset($shMyBlogInsertPostId))
     	$this->shMyBlogInsertPostId = $shMyBlogInsertPostId;
    if (isset($shMyBlogInsertTagId))
     	$this->shMyBlogInsertTagId = $shMyBlogInsertTagId;
    if (isset($shMyBlogInsertBloggerId))
     	$this->shMyBlogInsertBloggerId = $shMyBlogInsertBloggerId;
   	
   	if (isset($shInsertDocmanName))
     	$this->shInsertDocmanName = $shInsertDocmanName;
    if (isset($shDocmanInsertDocId))
     	$this->shDocmanInsertDocId = $shDocmanInsertDocId;
   	if (isset($shDocmanInsertDocName))
     	$this->shDocmanInsertDocName = $shDocmanInsertDocName;
     	
    if (isset($shLog404Errors))
     	$this->shLog404Errors = $shLog404Errors;
     	
    if (isset($shLMDefaultItemid))
     	$this->shLMDefaultItemid = $shLMDefaultItemid;  
     
    if (isset($shInsertFireboardName))
     	$this->shInsertFireboardName = $shInsertFireboardName;   
    if (isset($shFbInsertCategoryName))
     	$this->shFbInsertCategoryName = $shFbInsertCategoryName;
    if (isset($shFbInsertCategoryId))
     	$this->shFbInsertCategoryId = $shFbInsertCategoryId;
    if (isset($shFbInsertMessageSubject))
     	$this->shFbInsertMessageSubject = $shFbInsertMessageSubject;
    if (isset($shFbInsertMessageId))
     	$this->shFbInsertMessageId = $shFbInsertMessageId;  
                  	
    if (isset($shDoNotOverrideOwnSef)) // V 1.2.4.m
     	$this->shDoNotOverrideOwnSef = $shDoNotOverrideOwnSef;   
         
    if (isset($shEncodeUrl)) // V 1.2.4.m
     	$this->shEncodeUrl = $shEncodeUrl;
            
    if (isset($guessItemidOnHomepage)) // V 1.2.4.q
     	$this->guessItemidOnHomepage = $guessItemidOnHomepage;
     	
    if (isset($shForceNonSefIfHttps))	// V 1.2.4.q
      $this->shForceNonSefIfHttps= $shForceNonSefIfHttps;
     
    if (isset($shRewriteMode))	// V 1.2.4.s
      $this->shRewriteMode = $shRewriteMode;
    if (isset($shRewriteStrings))	// V 1.2.4.s
      $this->shRewriteStrings = $shRewriteStrings;
      
    if (isset($shRecordDuplicates))	// V 1.2.4.s
      $this->shRecordDuplicates = $shRecordDuplicates;
    if (isset($shMetaManagementActivated))	// V 1.2.4.s
      $this->shMetaManagementActivated = $shMetaManagementActivated; 
    if (isset($shRemoveGeneratorTag))	// V 1.2.4.s
      $this->shRemoveGeneratorTag = $shRemoveGeneratorTag;                     
    if (isset($shPutH1Tags))	// V 1.2.4.s
      $this->shPutH1Tags = $shPutH1Tags;                     
    if (isset($shInsertContentTableName))	// V 1.2.4.s
      $this->shInsertContentTableName = $shInsertContentTableName;   
    if (isset($shContentTableName))	// V 1.2.4.s
      $this->shContentTableName = $shContentTableName;  
    if (isset($shAutoRedirectWww))	// V 1.2.4.s
      $this->shAutoRedirectWww = $shAutoRedirectWww;
    if (isset($shVmInsertProductName))	// V 1.2.4.s
      $this->shVmInsertProductName = $shVmInsertProductName;  
      
    if (isset($shDMInsertCategories))	// V 1.2.4.t
      $this->shDMInsertCategories = $shDMInsertCategories;   
    if (isset($shDMInsertCategoryId))	// V 1.2.4.t
      $this->shDMInsertCategoryId = $shDMInsertCategoryId;   
      
    if (isset($shForcedHomePage))	// V 1.2.4.t
      $this->shForcedHomePage = $shForcedHomePage;
    if (isset($shInsertContentBlogName))	// V 1.2.4.t
      $this->shInsertContentBlogName = $shInsertContentBlogName;   
    if (isset($shContentBlogName))	// V 1.2.4.t
      $this->shContentBlogName = $shContentBlogName;
      
    if (isset($shInsertMTreeName))	// V 1.2.4.t
      $this->shInsertMTreeName = $shInsertMTreeName; 
    if (isset($shMTreeInsertListingName))	// V 1.2.4.t
      $this->shMTreeInsertListingName = $shMTreeInsertListingName;   
    if (isset($shMTreeInsertListingId))	// V 1.2.4.t
      $this->shMTreeInsertListingId = $shMTreeInsertListingId;   
    if (isset($shMTreePrependListingId))	// V 1.2.4.t
      $this->shMTreePrependListingId = $shMTreePrependListingId;   
    if (isset($shMTreeInsertCategories))	// V 1.2.4.t
      $this->shMTreeInsertCategories = $shMTreeInsertCategories; 
    if (isset($shMTreeInsertCategoryId))	// V 1.2.4.t
      $this->shMTreeInsertCategoryId = $shMTreeInsertCategoryId; 
    if (isset($shMTreeInsertUserName))	// V 1.2.4.t
      $this->shMTreeInsertUserName = $shMTreeInsertUserName; 
    if (isset($shMTreeInsertUserId))	// V 1.2.4.t
      $this->shMTreeInsertUserId = $shMTreeInsertUserId;      
    
    if (isset($shInsertNewsPName))	// V 1.2.4.t
      $this->shInsertNewsPName = $shInsertNewsPName; 
    if (isset($shNewsPInsertCatId))	// V 1.2.4.t
      $this->shNewsPInsertCatId = $shNewsPInsertCatId;  
    if (isset($shNewsPInsertSecId))	// V 1.2.4.t
      $this->shNewsPInsertSecId = $shNewsPInsertSecId;
     
    if (isset($shInsertRemoName))  // V 1.2.4.t
     	$this->shInsertRemoName = $shInsertRemoName;
    if (isset($shRemoInsertDocId))    // V 1.2.4.t
     	$this->shRemoInsertDocId = $shRemoInsertDocId;
   	if (isset($shRemoInsertDocName))    // V 1.2.4.t
     	$this->shRemoInsertDocName = $shRemoInsertDocName; 
    if (isset($shRemoInsertCategories))	// V 1.2.4.t
      $this->shRemoInsertCategories = $shRemoInsertCategories;   
    if (isset($shRemoInsertCategoryId))	// V 1.2.4.t
      $this->shRemoInsertCategoryId = $shRemoInsertCategoryId; 	
     	
    if (isset($shCBShortUserURL))	// V 1.2.4.t
      $this->shCBShortUserURL = $shCBShortUserURL;  
     
    if (isset($shKeepStandardURLOnUpgrade))	// V 1.2.4.t
      $this->shKeepStandardURLOnUpgrade = $shKeepStandardURLOnUpgrade;
    if (isset($shKeepCustomURLOnUpgrade))	// V 1.2.4.t
      $this->shKeepCustomURLOnUpgrade = $shKeepCustomURLOnUpgrade;  
    if (isset($shKeepMetaDataOnUpgrade))	// V 1.2.4.t
      $this->shKeepMetaDataOnUpgrade = $shKeepMetaDataOnUpgrade;  
    if (isset($shKeepModulesSettingsOnUpgrade))	// V 1.2.4.t
      $this->shKeepModulesSettingsOnUpgrade = $shKeepModulesSettingsOnUpgrade;  
      
    if (isset($shMultipagesTitle))	// V 1.2.4.t
      $this->shMultipagesTitle = $shMultipagesTitle;   
      
    // shumisha end of new parameters
		if (isset($Enabled))		$this->Enabled		= $Enabled;
  		if (isset($replacement)) 	$this->replacement	= $replacement;
		if (isset($pagerep)) 		$this->pagerep		= $pagerep;
		if (isset($stripthese)) 	$this->stripthese 	= $stripthese;
		if (isset($friendlytrim)) 	$this->friendlytrim	= $friendlytrim;
		if (isset($suffix))			$this->suffix		= $suffix;
		if (isset($addFile)) 		$this->addFile 		= $addFile;
		if (isset($LowerCase))		$this->LowerCase	= $LowerCase;
		if (isset($ShowSection)) 	$this->ShowSection	= $ShowSection;
		if (isset($HideCat))		$this->HideCat		= $HideCat;
		if (isset($replacement)) 	$this->UseAlias		= $UseAlias;
		if (isset($UseAlias))		$this->page404		= $page404;
		if (isset($predefined))		$this->predefined	= $predefined;
		if (isset($skip))			$this->skip			= $skip;
		if (isset($nocache))		$this->nocache		= $nocache;
		if (isset($ShowCat)) 		$this->ShowCat 		= $ShowCat;
		
    // V x
    if (isset($shKeepConfigOnUpgrade))	// V 1.2.4.x
      $this->shKeepConfigOnUpgrade = $shKeepConfigOnUpgrade; 
    if (isset($shSecEnableSecurity))	// V 1.2.4.x
      $this->shSecEnableSecurity = $shSecEnableSecurity;
    if (isset($shSecLogAttacks))	// V 1.2.4.x
      $this->shSecLogAttacks = $shSecLogAttacks;
    if (isset($shSecOnlyNumVars))	// V 1.2.4.x
      $this->shSecOnlyNumVars = $shSecOnlyNumVars;  
    if (isset($shSecAlphaNumVars))	// V 1.2.4.x
      $this->shSecAlphaNumVars = $shSecAlphaNumVars;
    if (isset($shSecNoProtocolVars))	// V 1.2.4.x
      $this->shSecNoProtocolVars = $shSecNoProtocolVars;    
	$this->ipWhiteList = shReadFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_IP_white_list.txt');
	$this->ipBlackList = shReadFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_IP_black_list.txt');
	$this->uAgentWhiteList = shReadFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_uAgent_white_list.txt');
	$this->uAgentBlackList = shReadFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_uAgent_black_list.txt');
     
      
    if (isset($shSecCheckHoneyPot))	// V 1.2.4.x
      $this->shSecCheckHoneyPot = $shSecCheckHoneyPot;
    if (isset($shSecDebugHoneyPot))	// V 1.2.4.x
      $this->shSecDebugHoneyPot = $shSecDebugHoneyPot;
    if (isset($shSecHoneyPotKey))	// V 1.2.4.x
      $this->shSecHoneyPotKey = $shSecHoneyPotKey;
	if (isset($shSecEntranceText))	// V 1.2.4.x
      $this->shSecEntranceText = $shSecEntranceText;
    if (isset($shSecSmellyPotText))	// V 1.2.4.x
      $this->shSecSmellyPotText = $shSecSmellyPotText;
    if (isset($monthsToKeepLogs))	// V 1.2.4.x
      $this->monthsToKeepLogs = $monthsToKeepLogs;
    if (isset($shSecActivateAntiFlood))	// V 1.2.4.x
      $this->shSecActivateAntiFlood = $shSecActivateAntiFlood;  
    if (isset($shSecAntiFloodOnlyOnPOST))	// V 1.2.4.x
      $this->shSecAntiFloodOnlyOnPOST = $shSecAntiFloodOnlyOnPOST; 
    if (isset($shSecAntiFloodPeriod))	// V 1.2.4.x
      $this->shSecAntiFloodPeriod = $shSecAntiFloodPeriod; 
    if (isset($shSecAntiFloodCount))	// V 1.2.4.x
      $this->shSecAntiFloodCount = $shSecAntiFloodCount;       
  //  if (isset($insertSectionInBlogTableLinks))	// V 1.2.4.x
  //    $this->insertSectionInBlogTableLinks = $insertSectionInBlogTableLinks;
    
    $this->shLangTranslateList = $this->shInitLanguageList( isset($shLangTranslateList)? $shLangTranslateList : null, 0, 0);  
    $this->shLangInsertCodeList = $this->shInitLanguageList( isset($shLangInsertCodeList) ? $shLangInsertCodeList : null, 0, 0);

    if (isset($defaultComponentStringList))	// V 1.2.4.x
      $this->defaultComponentStringList = $defaultComponentStringList;

    $this->pageTexts = $this->shInitLanguageList( isset($pageTexts) ? $pageTexts : null, // V x
    	isset($pagetext) ? $pagetext : 'Page-%s', isset($pagetext) ? $pagetext : 'Page-%s'); // use value from prev versions if any
    	
	if (isset($shAdminInterfaceType))	// V 1.2.4.x
      $this->shAdminInterfaceType = $shAdminInterfaceType;	
	
    // compatibility with version earlier than V x
    if (isset($shShopName))	// V 1.2.4.x
      $this->defaultComponentStringList['virtuemart'] = $shShopName;
    if (isset($shIJoomlaMagName))// V 1.2.4.x
      $this->defaultComponentStringList['magazine'] = $shIJoomlaMagName;
    if (isset($shCBName))// V 1.2.4.x
      $this->defaultComponentStringList['comprofiler'] = $shCBName;
    if (isset($shFireboardName))// V 1.2.4.x
      $this->defaultComponentStringList['fireboard'] = $shFireboardName;
    if (isset($shMyBlogName))// V 1.2.4.x
      $this->defaultComponentStringList['myblog'] = $shMyBlogName;
    if (isset($shDocmanName))// V 1.2.4.x
      $this->defaultComponentStringList['docman'] = $shDocmanName;                    
    if (isset($shMTreeName))// V 1.2.4.x
      $this->defaultComponentStringList['mtree'] = $shMTreeName;
    if (isset($shNewsPName))// V 1.2.4.x
      $this->defaultComponentStringList['news_portal'] = $shNewsPName;
    if (isset($shRemoName))// V 1.2.4.x
      $this->defaultComponentStringList['remository'] = $shRemoName;   
    // end of compatibility code
    
	// V 1.3 RC
	if (isset($shInsertNoFollowPDFPrint))
      $this->shInsertNoFollowPDFPrint = $shInsertNoFollowPDFPrint;
    if (isset($shInsertReadMorePageTitle))
      $this->shInsertReadMorePageTitle = $shInsertReadMorePageTitle;  
    if (isset($shMultipleH1ToH2))
      $this->shMultipleH1ToH2 = $shMultipleH1ToH2;  
	
    // V 1.3.1 RC
    if (isset($shVmUsingItemsPerPage))
      $this->shVmUsingItemsPerPage = $shVmUsingItemsPerPage;
    if (isset($shSecCheckPOSTData))
      $this->shSecCheckPOSTData = $shSecCheckPOSTData;
    if (isset($shSecCurMonth))
      $this->shSecCurMonth = $shSecCurMonth;  
    if (isset($shSecLastUpdated))
      $this->shSecLastUpdated = $shSecLastUpdated;
    if (isset($shSecTotalAttacks))
      $this->shSecTotalAttacks = $shSecTotalAttacks;
    if (isset($shSecTotalConfigVars))
      $this->shSecTotalConfigVars = $shSecTotalConfigVars;
    if (isset($shSecTotalBase64))
      $this->shSecTotalBase64 = $shSecTotalBase64;
    if (isset($shSecTotalScripts))
      $this->shSecTotalScripts = $shSecTotalScripts;
    if (isset($shSecTotalStandardVars))
      $this->shSecTotalStandardVars = $shSecTotalStandardVars;
    if (isset($shSecTotalImgTxtCmd))
      $this->shSecTotalImgTxtCmd = $shSecTotalImgTxtCmd;
    if (isset($shSecTotalIPDenied))
      $this->shSecTotalIPDenied = $shSecTotalIPDenied;
    if (isset($shSecTotalUserAgentDenied))
      $this->shSecTotalUserAgentDenied = $shSecTotalUserAgentDenied;
    if (isset($shSecTotalFlooding))
      $this->shSecTotalFlooding = $shSecTotalFlooding;
    if (isset($shSecTotalPHP))
      $this->shSecTotalPHP = $shSecTotalPHP;
    if (isset($shSecTotalPHPUserClicked))
      $this->shSecTotalPHPUserClicked = $shSecTotalPHPUserClicked;  
      
    if (isset($shInsertSMFName))
      $this->shInsertSMFName = $shInsertSMFName;
    if (isset($shSMFItemsPerPage))
      $this->shSMFItemsPerPage = $shSMFItemsPerPage;
    if (isset($shInsertSMFBoardId))
      $this->shInsertSMFBoardId = $shInsertSMFBoardId;
    if (isset($shInsertSMFTopicId))
      $this->shInsertSMFTopicId = $shInsertSMFTopicId;
    if (isset($shinsertSMFUserName))
      $this->shinsertSMFUserName = $shinsertSMFUserName;
    if (isset($shInsertSMFUserId))
      $this->shInsertSMFUserId = $shInsertSMFUserId;

    if (isset($prependToPageTitle))
      $this->prependToPageTitle = $prependToPageTitle;
    if (isset($appendToPageTitle))
      $this->appendToPageTitle = $appendToPageTitle; 

    if (isset($debugToLogFile))
      $this->debugToLogFile = $debugToLogFile;  
    if (isset($debugStartedAt))
      $this->debugStartedAt = $debugStartedAt;
    if (isset($debugDuration))
      $this->debugDuration = $debugDuration;    
	
    if (isset($defaultComponentItemidList))	// V 1.2.4.x
      $this->defaultComponentItemidList = $defaultComponentItemidList;
    if (isset($UseDefaultItemids))
    $this->UseDefaultItemids = $UseDefaultItemids;  

    // V 1.3.1
    if (isset($shInsertOutboundLinksImage))
      $this->shInsertOutboundLinksImage = $shInsertOutboundLinksImage;
    if (isset($shImageForOutboundLinks))
      $this->shImageForOutboundLinks = $shImageForOutboundLinks;
      
    // V 1.3.3
    $this->defaultParamList = shReadFile(sh404SEF_ADMIN_ABS_PATH . 'default.sef.php', true); // read as a string
      
	// compatiblity variables, for sef_ext files usage from OpenSef/SEf Advance V 1.2.4.p
    $this->encode_page_suffix = '';// if using an opensef sef_ext, we don't let  them manage suffix
    $this->encode_space_char = $this->replacement;
    $this->encode_lowercase = $this->LowerCase;
    $this->encode_strip_chars = $this->stripthese;
    $this->content_page_name = str_replace('%s', '', $this->pageTexts[$GLOBALS['mosConfig_lang']]); // V 1.2.4.r
    $this->content_page_format = '%s'.$this->replacement.'%d'; // V 1.2.4.r
    $shTemp = $this->shGetReplacements();
    foreach ($shTemp as $dest=>$source) {
      $this->spec_chars_d .= $dest.',';
      $this->spec_chars .= $source.',';
    }  
    rtrim($this->spec_chars_d, ',');
    rtrim($this->spec_chars, ',');  
                   
	}  // end of SefConfig
	
	// V x
	function shCheckFileAccess($fileName) {
	
		$ret = is_readable( sh404SEF_ABS_PATH.$fileName) && is_writable( sh404SEF_ABS_PATH.$fileName) ? 
			_COM_SEF_WRITEABLE : _COM_SEF_UNWRITEABLE;
		return $ret;	
	}
	
	function shCheckFilesAccess() {
	
		shIncludeLanguageFile();  // sometimes language file may not be included yet, need it in shCheckFileAccess
		$status = array();
		$status['administrator/components/com_sef/config'] = $this->shCheckFileAccess('administrator/components/com_sef/config');
		$status['administrator/components/com_sef'] = $this->shCheckFileAccess('administrator/components/com_sef');
		$status['administrator/components/com_sef/logs'] = $this->shCheckFileAccess('administrator/components/com_sef/logs');
		$status['administrator/components/com_sef/security'] = $this->shCheckFileAccess('administrator/components/com_sef/security');
		$status['components/com_sef/cache'] = $this->shCheckFileAccess('components/com_sef/cache');
		$this->fileAccessStatus = $status; 
	}
	
	function shInitLanguageList($currentList, $default, $defaultLangDefault) {
		$ret = array();
		if (file_exists( sh404SEF_ABS_PATH . 'administrator/components/com_joomfish/joomfish.class.php' )) { 
			require_once( sh404SEF_ABS_PATH . 'administrator/components/com_joomfish/joomfish.class.php' );
		} else if (file_exists(sh404SEF_ABS_PATH . 'administrator/components/com_nokkaew/nokkaew.class.php')) {
			require_once( sh404SEF_ABS_PATH . 'administrator/components/com_nokkaew/nokkaew.class.php' );
		}
		$shKind = shIsMultilingual();
		if (!$shKind) {
			if (empty($currentList) || !isset($currentList[$GLOBALS['mosConfig_lang']])) {
					$ret[$GLOBALS['mosConfig_lang']] = $defaultLangDefault;
				} else {
					$ret[$GLOBALS['mosConfig_lang']] = $currentList[$GLOBALS['mosConfig_lang']];
	  			}
		} else {
			$activeLanguages = shGetActiveLanguages();
			foreach ($activeLanguages as $language) {
				if (empty($currentList) || !isset($currentList[$language->code])) {
					$ret[$language->code] = $language->code == $GLOBALS['mosConfig_lang'] ? $defaultLangDefault : $default;
				} else {
					$ret[$language->code] = $currentList[$language->code];
	  			}
			}
		}  
		return $ret;  
	}
	
	function saveConfig($return_data=0) {
	
	GLOBAL $database, $sef_config_file, $mosConfig_live_site, $my;
	$quoteGPC = get_magic_quotes_gpc();
	//build the data file
	$config_data = '<?php' . "\n"
		. '// config.sef.php : configuration file for sh404SEF' . "\n"
		. '// ' . $this->version . "\n"
		. '// saved at: ' . date( 'Y-m-d H:i:s') . "\n"
		. '// by: ' . $my->username . ' (id: ' . $my->id . ' )' . "\n"
		. '// domain: ' . $mosConfig_live_site . "\n\n"
		. 'if (!defined(\'_VALID_MOS\')) die(\'Direct Access to this location is not allowed.\');' . "\n\n"
		;
		
		
		foreach ($this as $key=>$value) {
			if ($key != "0" && $key != 'ipWhiteList' && $key != 'ipBlackList'
			                && $key != 'uAgentWhiteList' && $key != 'uAgentBlackList'
			                && $key != 'defaultParamList'
			                ) {
				$config_data .= "\$$key = ";
				if ($key == 'shLangTranslateList' || $key == 'shLangInsertCodeList' || $key == 'defaultComponentStringList'
					|| $key == 'pageTexts' || $key == 'defaultComponentItemidList') {
					$datastring ='';
					foreach($value as $key2=>$data) {
						$datastring .= '"'.$key2.'"=>'.'"'.str_replace('"', '\"', $quoteGPC ? stripslashes($data):$data).'",';
					}
					$datastring = substr($datastring,0,-1);
					$config_data .= "array($datastring)";
				} else 
				switch (gettype($value)) {
					case "boolean":
						$config_data .= ($value ? "true" : "false");
					break;
					case "string":
					    $config_data .= "\"".str_replace('"', '\"', $quoteGPC ? stripslashes($value):$value)."\"";
					break;
					case "integer":
					case "double":
						$config_data .= strval($value);
					break;
					case "array";
						$datastring ='';
						foreach($value as $key2=>$data) {
							$datastring .= '"'.str_replace('"', '\"', $quoteGPC ? stripslashes($data):$data).'",';
						}
						$datastring = substr($datastring,0,-1);
						$config_data .= "array($datastring)";
						break;
					default:
						$config_data .= "null";
					break;
				}
				$config_data .= ";\n";
			}
		}
		$config_data .= '?'.'>';
		if ($return_data == 1) {
			return $config_data;
		}else{
			// write to disk
			//if (is_writable($sef_config_file)) {
				$trans_tbl = get_html_translation_table(HTML_ENTITIES);
				$trans_tbl = array_flip($trans_tbl);
				$config_data =strtr($config_data, $trans_tbl);
				$fd = fopen($sef_config_file, "wb");
				if (fwrite($fd, $config_data, strlen($config_data)) === FALSE) {
       				$ret = 0;
   				}else{
   					$ret = 1;
   				}
				fclose($fd);
				// save lists
				shSaveFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_IP_white_list.txt', $this->ipWhiteList);
				shSaveFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_IP_black_list.txt', $this->ipBlackList);
				shSaveFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_uAgent_white_list.txt', $this->uAgentWhiteList);
				shSaveFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_uAgent_black_list.txt', $this->uAgentBlackList);
				shSaveFile(sh404SEF_ADMIN_ABS_PATH . 'default.sef.php', 
					$quoteGPC ? stripslashes($this->defaultParamList) : $this->defaultParamList);
				
				// V 1.2.4.q : save copy of config file to other location for automatic recovering of config when upgrading
				$fd = fopen(sh404SEF_ABS_PATH.'media/sh404_upgrade_conf_'
                  .str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.php', "w");
				fwrite($fd, $config_data, strlen($config_data));
				fclose($fd);
				// save lists to backup location
				if (!is_writable(sh404SEF_ABS_PATH . 'media/sh404_upgrade_conf_security')) {
				  @mkdir(sh404SEF_ABS_PATH . 'media/sh404_upgrade_conf_security');
				}
				shSaveFile(sh404SEF_ABS_PATH . 'media/sh404_upgrade_conf_security/sh404SEF_IP_white_list.txt', 
							$this->ipWhiteList);
				shSaveFile(sh404SEF_ABS_PATH . 'media/sh404_upgrade_conf_security/sh404SEF_IP_black_list.txt', 
							$this->ipBlackList);
				shSaveFile(sh404SEF_ABS_PATH . 'media/sh404_upgrade_conf_security/sh404SEF_uAgent_white_list.txt', 
							$this->uAgentWhiteList);
				shSaveFile(sh404SEF_ABS_PATH . 'media/sh404_upgrade_conf_security/sh404SEF_uAgent_black_list.txt', 
							$this->uAgentBlackList);
				shSaveFile(sh404SEF_ABS_PATH . 'media/sh404_upgrade_conf_'
                  		.str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.default.php', 
                  		$quoteGPC ? stripslashes($this->defaultParamList) : $this->defaultParamList);
			return $ret;
		}
	}
	
    /**
     * Return array of URL characters to be replaced.
     * Copied from Artio Joomsef V 1.4.0
     *
     * @return array
     */
     
    function shGetReplacements()
    
    {
        // V 1.2.4.q : initialize variable
        static $shReplacements = null;
        if (isset($shReplacements)) return $shReplacements;
        $shReplacements = array();
        $items = explode(',', $this->shReplacements);
        foreach ($items as $item) {
          if (!empty($item)) {  // V 1.2.4.q better protection. Returns null array if empty
            @list($src, $dst) = explode('|', trim($item));
            $shReplacements[trim($src)] = trim($dst);
          }
        }
        return $shReplacements;
    }
    
    /**
     * Return array of URL characters to be replaced.
     * Copied from Artio Joomsef V 1.4.0
     *
     * @return array
     */
     
    function shGetStripCharList()
    
    {
        static $shStripCharList = null;
        if (is_null($shStripCharList)) {
          $shStripCharList = array();
          $shStripCharList = explode('|', $this->stripthese);
        }
        return $shStripCharList;
    }
    
	function set($var, $val) {
	
		if (isset($this->$var)) {
			$this->$var = $val;
			return true;
		}
		return false;
	}
	
	function version() {
	
		return $this->$version;
	}
}

// set of utility functions 

function shSortURL($string) {
// URL must be like : index.php?param2=xxx&option=com_ccccc&param1=zzz
// URL returned will be ! index.php?option=com_ccccc&param1=zzz&param2=xxx
  $ret = '';
  $st = str_replace('&amp;', '&',$string);
  $st = str_replace('index.php', '', $st);
  $st = str_replace('?', '', $st);
  parse_str( $st,$shVars);
  if (count($shVars) > 0) {
    ksort($shVars);  // sort URL array
    $shNewString = '';
    $ret = 'index.php?';
    foreach ($shVars as $key => $value) {
      if (strtolower($key) != 'option') // option is always first parameter
        $shNewString .= '&'.$key.'='.$value;
      else
        $ret .= $key.'='.$value;  
    }     
    $ret .= $ret == 'index.php?' ? ltrim( $shNewString, '&') : $shNewString;
  }
  return $ret;
}

// returns found languages, but will check request language ($_GET or $_POST)
// and use that over user lang if it exists
// returns a lnguage code : en, fr, sp
function shDecideRequestLanguage() {

	$reqLang = mosGetParam( $_REQUEST, 'lang', '' );
	if( $reqLang != '' )
		$finalLang = $reqLang;
	else 
		$finalLang = shDiscoverUserLanguage();
	return $finalLang;
}

/** The function finds the language which is to be used for the user/session
 * 
 * It is possible to choose the language based on the client browsers configuration,
 * the activated language of the configuration and the language a user has choosen in
 * the past. The decision of this order is done in the JoomFish configuration.
 * 
 * This is a modified copy of what's available in Joomfish system bot.  
 * Returns a language code : en, fr, sp
 */
 
function shDiscoverUserLanguage() {

	$shCookieLang = shGetCookieLanguage();
	$userLang = empty( $shCookieLang) ? shGetParamUserLanguage() : $shCookieLang;
	return $userLang;
}

// returns language code (en, fr, sp after lookign up Joomfish params
// probably does not work with NokKaew
function shGetParamUserLanguage() {
	global $mosConfig_lang, $database,$_MAMBOTS;

	if (!shIsMultilingual())
	  return $mosConfig_lang; 
	  
	// check if param query has previously been processed
	if ( !isset($_MAMBOTS->_system_mambot_params['jfSystembot']) ) {
		// load mambot params info
		$query = "SELECT params"
		. "\n FROM #__mambots"
		. "\n WHERE element = 'jfdatabase.systembot'"
		. "\n AND folder = 'system'"
		;
		$database->setQuery( $query );
		$database->loadObject($mambot);	
		
		// save query to class variable
		$_MAMBOTS->_system_mambot_params['jfSystembot'] = $mambot;
	}
	// pull query data from class variable
	$mambot = $_MAMBOTS->_system_mambot_params['jfSystembot'];	
	
	$botParams = new mosParameters( $mambot->params );
	$determitLanguage 		= $botParams->def( 'determitLanguage', 1 );
	$newVisitorAction		= $botParams->def( 'newVisitorAction', "browser" );

	if ($newVisitorAction=="browser" && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
		// no language chooses - assume from browser configuration
		// language negotiation by Kochin Chang, June 16, 2004
		// retrieve active languages from database
		$active_lang = null;
		$activeLanguages = shGetActiveLanguages();
		if( count( $activeLanguages ) == 0 ) {
			return $mosConfig_lang;
		}
		foreach ($activeLanguages as $lang) {
			$active_lang[] = $lang->iso;
		}
		// figure out which language to use
		$browserLang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach( $browserLang as $lang ) {
			$shortLang = substr( $lang, 0, 2 );
			if( in_array($lang, $active_lang) ) {
				$client_lang = $lang;
				break;
			}
			if ( in_array($shortLang, $active_lang) ) {
				$client_lang = $shortLang;
				break;
			}
		}
		// if language is still blank then use first active language!
		if ($client_lang==""){
			$client_lang = $activeLanguages[0]->iso;
		}
	} elseif ($newVisitorAction=="joomfish"){
			// This list is ordered already!
			$activeLanguages = shGetActiveLanguages();
			if( count( $activeLanguages ) == 0 ) {
				return $mosConfig_lang;
			}
			else {
				$client_lang = $activeLanguages[0]->iso;				
			}
			
		} else {// otherwise default use site default language
			$activeLanguages = shGetActiveLanguages();
			if( count( $activeLanguages ) == 0 ) {
				return $mosConfig_lang;
			}
			foreach ($activeLanguages as $lang) {
				if ($lang->code==$mosConfig_lang){
					$client_lang = $lang->iso;
					break;
				}
			}			
			// if language is still blank then use first active language!
			if ($client_lang==""){
				$client_lang = $activeLanguages[0]->iso;
			}
		}
	return $client_lang;
}

function shGetCookieLanguage() {

	$mbfcookie = mosGetParam( $_COOKIE, 'mbfcookie', null );
	if (isset($mbfcookie["lang"]) && $mbfcookie["lang"] != "") {
		$lang = $mbfcookie["lang"];
	} else {
		$lang = '';
	}
	return $lang;
}

/**
* Check if user session exists. Adapted from Joomla original code
*/
function shLookupSession() {

	global $database, $mainframe;
	// initailize session variables
	$session 	= new mosSession( $database );
	$option = strval( strtolower( mosGetParam( $_REQUEST, 'option' ) ) );
	$mainframe = new mosMainFrame( $database, $option, '.' );
	// purge expired sessions
	$session->purge('core');  // can't purge as $mainframe is not initialized yet
	// Session Cookie `name`
	// WARNING : I am using the Hack from 
	$sessionCookieName 	= mosMainFrame::sessionCookieName();
	// Get Session Cookie `value`
	$sessioncookie 		= strval( mosGetParam( $_COOKIE, $sessionCookieName, null ) );
	// Session ID / `value`
	$sessionValueCheck 	= mosMainFrame::sessionCookieValue( $sessioncookie );
	// Check if existing session exists in db corresponding to Session cookie `value` 
	// extra check added in 1.0.8 to test sessioncookie value is of correct length
	$ret = false;
	if ( $sessioncookie && strlen($sessioncookie) == 32 && $sessioncookie != '-' && $session->load($sessionValueCheck) )
		$ret = true;
	unset($mainframe);
	return $ret;
}
	
// redirect user according to its language preference
function shGuessLanguageAndRedirect( $queryString) {

	if (!sh404SEF_DE_ACTIVATE_LANG_AUTO_REDIRECT 
		  && shIsMultilingual() == 'joomfish') { 
		//$referer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
		//$isSelfReferer = rtrim($referer, '/') == rtrim($GLOBALS['mosConfig_live_site'], '/');
		$cookieLang = shGetCookieLanguage();
		$sessionExists = shLookupSession();
		$reqLang = mosGetParam( $_REQUEST, 'lang', '' );
		$targetLang = '';
		if (!$sessionExists /*&& !$isSelfReferer*/)  {  // no session and not coming from self
			if (empty($cookieLang)) {  // this is really first visit (or visitor does not accept cookie)
	   			$discoveredLang = shGetParamUserLanguage();
	   			if ( $discoveredLang != $reqLang)
	   				$targetLang = $discoveredLang;
			} else {  // returning visitor, with only a cookie set
				if ($cookieLang != $reqLang)
					$targetLang = $cookieLang;
			}
		}	
		if (!empty($targetLang)) { // 303 redirect to same URL in preferred language
			$queryString = shSetURLVar( 'index.php?'.$queryString, 'lang', $targetLang);
			_log('Redirecting (303) to user language |cookie = '.$cookieLang. '|session='.$sessionExists.'|req='.$reqLang.'|target='.$targetLang);
	       	shRedirect( $GLOBALS['mosConfig_live_site'].'/'.$queryString, '', 303);
		}   		
	}

}

// 1.2.4.t 10/08/2007 12:17:37 return false if not multilingual
function shIsMultilingual() {

  if (class_exists('JoomFishManager'))
    $shIsMultiLingual = 'joomfish';
  elseif (class_exists('NokKaewManager'))   
    $shIsMultiLingual = 'mambo';
  else  $shIsMultiLingual = false; 
  return $shIsMultiLingual;
  
}

// 1.2.4.t 10/08/2007 12:17:37 return true if param is default language
function shIsDefaultLang( $langName) {

  return $langName == shGetDefaultLang();
}
 
// 1.2.4.t 10/08/2007 12:17:37 return true if param is default language
function shGetDefaultLang() {

  $type = shIsMultilingual();
  switch ($type) {
    case false:
      $shDefaultLang = $GLOBALS['mosConfig_lang'];
    break;
    case 'joomfish':
    	if (!empty($GLOBALS['mosConfig_defaultLang']))
    		$shDefaultLang = $GLOBALS['mosConfig_defaultLang'];
    	else $shDefaultLang = $GLOBALS['mosConfig_lang'];	
    break;
    case 'mambo':
      $shDefaultLang = shGetNameFromIsoCode(empty($GLOBALS['mosConfig_defaultLang']) ? 
      	$GLOBALS['mosConfig_lang']:$GLOBALS['mosConfig_defaultLang']);
    break;
  }
  return $shDefaultLang;
} 


function shAdjustToRewriteMode( $url) {
  global $sefConfig;
   // V 1.2.4.r if no mod_rewrite, need to force trailing /
  /*if (!empty($sefConfig->shRewriteMode)) {  // V 1.2.4.s removed, may not be needed.
    $urlBits = explode('?', $url);
    if (!isset($urlBits[1]))
      $url = rtrim( str_replace( $sefConfig->suffix,'', $url), '/').'/';
    else {
      $url = rtrim( str_replace( $sefConfig->suffix,'', $urlBits[0]), '/').'/?'.$urlBits[1];    
    }  
  }*/
  return $url;
}

function shFinalizeURL( $url) {
  global $sefConfig;
  if (!empty($url) && (strpos($url, '/index.php?/') === false)) {  // V w 27/08/2007 13:38:34 sh_NetURL does not work if 
    $URI = new sh_Net_URL($url);                       // using this rewrite mode as the added ? fools it if there is indeed
    if (!empty($URI->path)) {                          // a query string. Better not do anything
      $url = $URI->protocol.'://'.$URI->host.(!sh404SEF_USE_NON_STANDARD_PORT || empty($URI->port) ? '' : ':'.$URI->port);
      //$shTemp = $sefConfig->LowerCase ? strtolower($URI->path) : $URI->path; // V w 27/08/2007 13:10:00
      //$url .= $sefConfig->shEncodeUrl ? shUrlEncode( $shTemp) :  $shTemp;
      $url .= $sefConfig->shEncodeUrl ? shUrlEncode( $URI->path) :  $URI->path;
      if (count($URI->querystring) > 0) {
        $shTemp = '';
        foreach ($URI->querystring as $key=>$value)
          $shTemp .= '&'.$key.'='.($sefConfig->shEncodeUrl ? shUrlEncode($value) : $value);
        $shTemp = ltrim( $shTemp, '&');  // V x 02/09/2007 21:17:19
        $url .= '?'. $shTemp;  // V x 02/09/2007 21:17:24
      }
      if ($URI->anchor)
        $url .= '#'.($sefConfig->shEncodeUrl ? shUrlEncode($URI->anchor) : $URI->anchor);    
    } 
  }
  // V 1.2.4.s hack to workaround Virtuemart/SearchEngines issue with cookie check
  // V 1.2.4.t fixed bug, was checking for vmcchk instead of vmchk
  if (shIsSearchEngine() && (strpos( $url, 'vmchk') !== false)) {
    $url = str_replace('vmchk/', '', $url);  // remove check, 
                               //cookie will be forced if user agent is searchengine
  }
  $url = shAdjustToRewriteMode ($url);
  return str_replace('&', '&amp;', $url); // V 1.2.4.t XHTML validation
}

// V 1.2.4.p compatibility function with SEFAdvance
function sefencode( $string) {
  return titleToLocation( $string);
}

function titleToLocation(&$title)
{
    global $sefConfig;
    $debug = 0;
    if ($debug) $t[] = $title;
    $shRep = $sefConfig->shGetReplacements();
    if (!empty($shRep)) 
      $title = strtr($title, $shRep);
    if ($debug) $t[] = $title;
    $shStrip = $sefConfig->shGetStripCharList();
    if (!empty($shStrip))
      $title = str_replace( $shStrip, '', $title);
    if ($debug) $t[] = $title;
    // V 1.2.4.t remove spaces
    $title = preg_replace( '/[\s]+/', $sefConfig->replacement, $title);
    if ($debug) $t[] = $title;
    $title = str_replace('\'', $sefConfig->replacement, $title);
    $title = str_replace('"', $sefConfig->replacement, $title);
    // V x strip # as it breaks anchor management
    $title = str_replace('#', $sefConfig->replacement, $title);
    // V u - 26/08/2007 10:26:58 remove question marks
    $title = str_replace('?', $sefConfig->replacement, $title);
    if ($debug) $t[] = $title;
    $title = str_replace('\\', $sefConfig->replacement, $title);
    if ($debug) $t[] = $title;
    // V 1.2.4.t remove duplicate replacement chars
    if (!empty($sefConfig->replacement))  // V x protect/allow empty 
    	$title = preg_replace('/'.preg_quote($sefConfig->replacement).'{2,}/', $sefConfig->replacement, $title);
    if ($debug) $t[] = $title;
    //$title = trim( $title, $sefConfig->friendlytrim);  // V 1.2.4.t add SEF URL trimming of user set characters
                                                         // V u 26/08/2007 10:33:56 removed, need to trim only full url   
    $title = $sefConfig->LowerCase ? strtolower($title) : $title;  // V w 27/08/2007 13:11:48   
    if ($debug) $t[] = $title;
    if ($debug && strpos($t[0], '\'') !== false) {
      var_dump($t);
      die();
    }
    return $title;
}

// V x utility 01/09/2007 22:18:55 function to remove mosmsg var from url
function shCleanUpMosMsg( $string) {
  return preg_replace( '/(&|\?)mosmsg=[^&]*/i', '', $string);
}

// V x utility  function to remove a variable from an URL
function shCleanUpVar( $string, $var) {
  return preg_replace( '/(&|\?)'.preg_quote($var).'=[^&]*/i', '', $string);
}

// V x utility 01/09/2007 22:18:55 function to return mosmsg var from url
function shGetMosMsg( $string) {
  $matches = array();
  $result = preg_match( '/(&|\?)mosmsg=[^&]*/i', $string, $matches);
  if (!empty($matches))
    return trim( $matches[0], '&?');
  else return ''; 
}

// V x utility function to return lang var from url
function shGetURLLang( $string) {
  $matches = array();
  $string = str_replace('&amp;', '&', $string); // normalize
  $result = preg_match( '/(&|\?)lang=[^&]*/i', $string, $matches);
  if (!empty($matches)) {
    $result = trim( $matches[0], '&?');
    $result = str_replace('lang=', '', $result);
    return shGetNameFromIsoCode($result);
  }  
  else return ''; 
}

// V x utility function to return a var from url
function shGetURLVar( $string, $var) {
  $matches = array();
  $string = str_replace('&amp;', '&', $string); // normalize
  $result = preg_match( '/(&|\?)'.preg_quote($var).'=[^&]*/i', $string, $matches);
  if (!empty($matches)) {
    $result = trim( $matches[0], '&?');
    $result = str_replace($var.'=', '', $result);
    return $result;
  }  
  else return ''; 
}

// V x utility function to set  a var in an url
function shSetURLVar( $string, $var, $value) {
  if (empty( $string) || empty($var) || empty($value)) return $string;
  $string = str_replace('&amp;', '&', $string); // normalize
  $exp = '/(&|\?)'.preg_quote($var).'=[^&]*/i';
  $result = preg_match( $exp, $string);
  if ($result)  // var already in URL
    $result = preg_replace( $exp, '$1'.$var.'='.$value, $string);
  else {  // var does not exist in URL
  	$result = $string.(strpos( $string, '?') !== false ? '&':'?').$var.'='.$value;
  	$result = shSortURL($result);
  }
  return $result; 
}

// V 1.2.4.q utility function to clean language and pagination info from url
function shCleanUpPag( $string) {
  $shTempString = preg_replace( '/(&|\?)limit=[^&]*/i', '', $string);
  $shTempString = preg_replace( '/(&|\?)limitstart=[^&]*/i', '', $shTempString);
  return $shTempString;
}

// V 1.2.4.t utility function to clean language from url
function shCleanUpLang( $string) {
  return preg_replace( '/(&|\?)lang=[a-zA-Z]{2,3}/iU', '', $string);
}

// V 1.2.4.q utility function to clean language and pagination info from url
function shCleanUpLangAndPag( $string) {
  $shTempString = shCleanUpLang( $string);
  $shTempString = shCleanUpPag($shTempString);
  return $shTempString;
}

// V 1.2.4.t utility function to clean anchor from url
function shCleanUpAnchor( $string) {
  $bits = explode('#', $string);
  return $bits[0];
}


// V 1.2.4.t
function shIncludeLanguageFile() {
  if (defined( '_COM_SEF_SH_REDIR_404')) return;
  if (file_exists(sh404SEF_ADMIN_ABS_PATH.'language/'.$GLOBALS['mosConfig_lang'].'.php')) {
    include_once(sh404SEF_ADMIN_ABS_PATH.'language/'.$GLOBALS['mosConfig_lang'].'.php');
  }
  else {
    include_once(sh404SEF_ADMIN_ABS_PATH.'language/english.php');
  }
} 


 function shGETGarbageCollect() {  // V 1.2.4.m moved to main component from plugins
    // builds up a string using all remaining GET parameters, to be appended to the URL without any sef transformation
    // those variables passed litterally must be removed from $string as well, so that they are not stored in DB
    global $sefConfig, $shGETVars;
    if (!$sefConfig->shAppendRemainingGETVars || empty($shGETVars)) return '';
    $ret = '';
    ksort($shGETVars);
    foreach ($shGETVars as $param => $value) {
      $ret .= '&'.$param.'='.(empty($value) ? '0' : $value);  // V 1.2.4.s allow value = 0
    }
    return $ret;
  }

  function shRebuildNonSefString( $string) { // V 1.2.4.m moved to main component from plugins
    // rebuild a non-sef string, removing all GET vars that were not turned into SEF
    // as we do not want to store them in DB
    
    global $sefConfig, $shRebuildNonSef;
    if (!$sefConfig->shAppendRemainingGETVars || empty($shRebuildNonSef)) return $string;
    $shNewString = '';
    if (!empty($shRebuildNonSef)) {
      foreach ($shRebuildNonSef as $param) {  // need to sort, and still place option in first pos.
      	if (strpos($param, 'sh404SEF_title=') !== false)
      		$param = str_replace('sh404SEF_title=', 'title=', $param);
      	$shNewString .= $param;  
      }
      $ret = shSortUrl('index.php?'.ltrim( $shNewString, '&'));  
    }
    return $ret;
  }

  function shRemoveFromGETVarsList( $paramName) {
    global $shGETVars, $sefConfig, $shRebuildNonSef;
    
    if (!$sefConfig->shAppendRemainingGETVars) return null;
    if (!empty($paramName)) {
      if (isset($shGETVars[$paramName])) {
        if (!empty($shGETVars[$paramName]))
          $shValue = $shGETVars[$paramName];
        else $shValue = '0'; // V 1.2.4.s 
        $shRebuildNonSef[] = '&'.$paramName.'='.$shValue;  // build up a non-sef string with the GET vars used to 
                                             // build the SEF string. This string will be the one stored in db instead of
                                             // the full, original one
        unset( $shGETVars[@$paramName]);
      }
    }  
  }

  function shAddToGETVarsList( $paramName, $paramValue) {  // V 1.2.4.m
    global $shGETVars, $shRebuildNonSef;
    if (empty( $paramName)) return;
    $shGETVars[$paramName] = $paramValue;
  }

  function shFinalizePlugin( $string, $title, &$shAppendString, $shItemidString, 
                             $limit, $limitstart, $shLangName) { // V 1.2.4.s
  if (!empty($shItemidString))
    $title[] = $shItemidString; // V 1.2.4.m
  // stitch back additional parameters, not sef-ified
	$shAppendString .= shGETGarbageCollect();  // add automatically all GET variables that had not been used already
  if (!empty($shAppendString)) 
    $shAppendString = '?'.ltrim( $shAppendString, '&'); // don't add to $string, otherwise it will be stored in the DB 
	return sef_404::sefGetLocation( shRebuildNonSefString( $string), $title, null, (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), (isset($shLangName) ? @$shLangName : null));
  }

  function shInitializePlugin($lang, &$shLangName, &$shLangIso, $option) {
  global $mosConfig_lang;  

    $shLangName = empty($lang) ? $mosConfig_lang : shGetNameFromIsoCode( $lang);
    $shLangIso = (shTranslateUrl($option, $shLangName)) ? 
      (isset($lang) ? $lang : shGetIsoCodeFromName( $mosConfig_lang))
                                       : (isset($GLOBALS['mosConfig_defaultLang']) ? 
                                             shGetIsoCodeFromName($GLOBALS['mosConfig_defaultLang'])
                                           : shGetIsoCodeFromName( $mosConfig_lang));
     if (strpos($shLangIso, '_') !== false) {   //11/08/2007 14:30:16 mambo compat
      $shTemp = explode( '_', $shLangIso);
      $shLangIso = $shTemp[0];  
     }    
    // added protection : do not SEF if component is not installed. Do not attempt to build SEF URL
    // if component is not installed, or else plugin may try to read from comp DB tables. This will cause DB table names
    // to be displayed
    return !sh404SEF_CHECK_COMP_IS_INSTALLED 
    		|| ( sh404SEF_CHECK_COMP_IS_INSTALLED &&
    			 file_exists(sh404SEF_ABS_PATH.'components/'.$option.'/'.str_replace('com_', '',$option).'.php'));                                    
  }
 
  function shLoadPluginLanguage ( $pluginName, $language, $defaultString) {  // V 1.2.4.m
  global $sh_LANG;
  // load the Language File
  if (file_exists( sh404SEF_ADMIN_ABS_PATH.'language/plugins/'.$pluginName.'.php' )) {
  	include_once( sh404SEF_ADMIN_ABS_PATH.'language/plugins/'.$pluginName.'.php' );
  }	
  else die('sh404SEF - missing language file for plugin '.$pluginName.'. Cannot continue.');	

  if (!isset($sh_LANG[$language][$defaultString]))
    return 'en';
  else return $language;  
  } 

  function shInsertIsoCodeInUrl($compName, $shLang = null) {  // V 1.2.4.m
  global $sefConfig, $mosConfig_lang;
  $shLang = empty($shLang) ? $mosConfig_lang : $shLang;  // V 1.2.4.q
  if (empty($compName) || !$sefConfig->shInsertLanguageCode  // if no compname or global param is off
  		|| $sefConfig->shLangInsertCodeList[$shLang] == 2  // set to not insertcode
      	|| ( $sefConfig->shLangInsertCodeList[$shLang] == 0 && shGetDefaultlang() == $shLang) // or set to default 
       )  // but this is default language
      return false;
    $compName = str_replace('com_', '', $compName);
    return !in_array($compName, $sefConfig->notInsertIsoCodeList);
  }
  
  function shTranslateUrl ($compName, $shLang = null) {  // V 1.2.4.m  // V 1.2.4.q added $shLang param
  global $sefConfig, $mosConfig_lang;
    
    $shLang = empty($shLang) ? $mosConfig_lang : $shLang;
    if (empty($compName) || !$sefConfig->shTranslateURL
      || $sefConfig->shLangTranslateList[$shLang] == 2 ) // set to not translate 
      return false;
    $compName = str_replace('com_', '', $compName);
    $result = !in_array($compName, $sefConfig->notTranslateURLList);
    return $result;
  }
 
// V 1.2.4.q returns true if current page is home page.
function shIsCurrentPageHome() {
  global $option, $shHomeLink;
  
  $currentPage = shSortUrl( preg_replace( '/(&|\?)lang=[a-zA-Z]{2,3}/iU', '', $_SERVER['QUERY_STRING'])); // V 1.2.4.t
  $currentPage = ltrim( str_replace('index.php', '', $currentPage), '/');
  $currentPage = ltrim( $currentPage, '?');
  $shHomePage = preg_replace( '/(&|\?)lang=[a-zA-Z]{2,3}/iU', '', $shHomeLink);
  $shHomePage = ltrim( str_replace('index.php', '', $shHomePage), '/');
  $shHomePage = ltrim( $shHomePage, '?');
  return  $currentPage == $shHomePage;
}  

function shUrlEncode( $path) {
  $ret = $path;
  if (!empty($path)) {
    $bits = explode('/', $path);
    $enc = array();
    if (count($bits)) {
      foreach ($bits as $key=>$value) {
        $enc[$key] = rawurlencode($value); 
      }
      $ret = implode($enc,'/');
    }  
  }
  return $ret;
}
function shUrlDecode( $path) {
  $ret = $path;
  if (!empty($path)) {
    $bits = explode('/', $path);
    $dec = array();
    if (count($bits)) {
      foreach ($bits as $key=>$value) {
        $dec[$key] = rawurldecode($value);  
      }
      $ret = implode($dec,'/');
    }  
  }
  return $ret;
}

// returns default items per page from menu items params. menu item selected by its id taken from a URL
function shGetDefaultDisplayNumFromURL($url) {
	  
  	$menuItemid = shGetURLVar($url, 'Itemid');
  	return shGetDefaultDisplayNum($menuItemid);
}

// returns default items per page from menu items params. menu item selected by its id taken from a URL
function shGetDefaultDisplayNum($menuItemid) {
	global $mainframe, $database, $mosConfig_list_limit;
	  
  	$ret = $mosConfig_list_limit; // defaults to site default items per page value
  	if (empty($menuItemid)) return $ret;  // no itemid 
	$menu 	= new mosMenu( $database );
	$menuItem = $menu->load($menuItemid);  // load menu item from DB
	if (empty($menuItem)) return $ret;  // if none, default
	$params = new mosParameters( $menu->params );  // get params from menu item
	$ret = $params->get('display_num', $mosConfig_list_limit);  // get number of items per page
	return $ret; 
}

function getSefUrlFromDatabase($url, &$sefString)  // V 1.2.4.t
{
    global $database;
    $query = "SELECT oldurl, dateadd FROM #__redirection WHERE newurl = '".$database->getEscaped($url)."'";
    $database->setQuery($query); // 10/08/2007 22:10:05 mambo compat 
    if ($database->loadObject($result)) {
      $sefString = $result->oldurl;
      if (empty($result->oldurl))
        return sh404SEF_URLTYPE_404;
      return $result->dateadd == '0000-00-00' ? sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM; 
    } else 
      return sh404SEF_URLTYPE_NONE;  
}

// V 1.2.4.t check both cache and DB
function shGetSefURLFromCacheOrDB($string, &$sefString) {
	global $sefConfig;
	if (empty($string)) return sh404SEF_URLTYPE_NONE;
	$sefString = '';
	$urlType = sh404SEF_URLTYPE_NONE;
	if ($sefConfig->shUseURLCache)
	$urlType = shGetSefURLFromCache($string, $sefString);
	// Check if the url is already saved in the database.
	if ($urlType == sh404SEF_URLTYPE_NONE) {
		$urlType = getSefUrlFromDatabase($string, $sefString);
		if ($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404)
		return $urlType;
		else {
			if ($sefConfig->shUseURLCache) {
				shAddSefURLToCache( $string, $sefString, $urlType);
			}
		}
	}
	return $urlType;
}

// add URL to DB and cache. URL must no exists, this is insert, not update
function shAddSefUrlToDBAndCache( $nonSefUrl, $sefString, $rank, $urlType) {
  global $database;
  $sefString = ltrim( $sefString, '/'); // V 1.2.4.t just in case you forgot to remove leading slash 
  switch ($urlType) {
  	case sh404SEF_URLTYPE_AUTO :
  		$dateAdd = '0000-00-00';
    break;
  	case sh404SEF_URLTYPE_CUSTOM :
  		$dateAdd = date("Y-m-d");
    break;
  	case sh404SEF_URLTYPE_NONE :
  		return null;
    break;
  }
  $query = '';
  if ($urlType == sh404SEF_URLTYPE_AUTO) {  // before adding a full sef, we must check it does not already exists as a 404
  	$query = 'SELECT id FROM #__redirection where oldurl=\''.$sefString.'\' AND newurl = \'\';';
  	_log('Querying for 404 : '.$query);
  	$database->setQuery($query);
  	$database->loadObject($result); // instead of inserting, we must update this 404 record
  	if (!empty($result))
  	  	$query = 'UPDATE #__redirection SET '.  // V 1.2.4.q
    		"newurl='".addslashes(urldecode($nonSefUrl))."', rank='".$rank."', dateadd='".$dateAdd.'\' '
  	  		."WHERE oldurl = '".$sefString."';";
  	else $query = '';  		
  }
  if (empty($query)) { 
  	$query = "INSERT INTO #__redirection (oldurl, newurl, rank, dateadd) ".  // V 1.2.4.q
    	"VALUES ('".$sefString."', '".addslashes(urldecode($nonSefUrl))."', '".$rank."', '".$dateAdd."')";  // V 1.2.4.q
  }	
  _log('Querying to insert/update sef record : '.$query);
  $database->setQuery($query);
  if (!$database->query()) {
  	_log('Bad query '. $query);
  }
  // shumisha 2007-03-13 added URL caching, need to store this new URL
  shAddSefURLToCache( $nonSefUrl, $sefString, $urlType);
}
  
// V 1.2.4.t build up a string with a page number
function shBuildPageNumberString( $pagenum) {
global $sefConfig;

  if ($sefConfig->pagetext && (false !== strpos($sefConfig->pagetext, '%s'))){
  	return str_replace('%s', $pagenum, $sefConfig->pagetext);
  } else {
  	return $pagenum;
  } 
}  

function shReadFile($shFileName, $asString = false){
	$ret = array();
	if (is_readable($shFileName)) {
		$shFile = fOpen($shFileName, 'r');
		do {
			$shRead = fgets($shFile,1024);
			if (!empty($shRead) && substr($shRead, 0, 1) != '#') $ret[] = trim(stripslashes($shRead));
		}
		while (!feof($shFile));
   		fclose($shFile);
	}
	if ($asString)
		$ret = implode("\n", $ret);
	return $ret;
}

function shSaveFile($shFileName, $fileData){
	if (empty($shFileName)) return;
	$fileIsThere = file_exists($shFileName); 
  	if (!$fileIsThere || ($fileIsThere && is_writable($shFileName))) {
  		$dataFile=fopen( $shFileName,'wb');
   		if ($dataFile) {
   			if (is_array($fileData)) {
   				$fileData = implode("\n",$fileData); //make sure we write a string
   			}
	   		fWrite( $dataFile, empty($fileData) ? '':$fileData);
       		fClose( $dataFile);
    	}
	}
}
	
// shumisha utility function to obtain iso code from language name
function shGetIsoCodeFromName($langName) {
  global $database, $shIsoCodeCache, $mosConfig_locale, $mosConfig_lang;
  if (!isset( $shIsoCodeCache[$langName])) {
    $type = shIsMultilingual();
    if ($type != false) {
      if ($type == 'joomfish') {
        $isFish17 = strpos(JoomFishManager::getVersion(), 'V1.7') !== false;
      	$select = $isFish17 ? 'iso, code' : 'iso, shortcode, code';
      }
      $query = 'SELECT '.($type == 'joomfish' ? $select :'mambo,name')
               .' FROM '.($type == 'joomfish' ? '#__languages':'#__nok_language').' WHERE 1';
      $database->setQuery($query);
      $rows = $database->loadObjectList();
      foreach ($rows as $row) {
      	if ($type == 'joomfish')
      		$jfIsoCode = $isFish17 ? $row->iso : (empty($row->shortcode) ? $row->iso:$row->shortcode);
      	$shIsoCodeCache[($type == 'joomfish' ? $row->code:$row->name)] = ($type == 'joomfish' ? $jfIsoCode:$row->mambo);
      }
    } else { // no joomfish, so it has to be default language
      $shTemp = explode( '_', $mosConfig_locale);
      $langName = $mosConfig_lang;
      $shIsoCodeCache[$mosConfig_lang] = $shTemp[0] ? $shTemp[0] : 'en';
    }
  }
  return empty($shIsoCodeCache[$langName]) ? 'en' : $shIsoCodeCache[$langName];
}

// shumisha utility function to obtain language name from iso code
function shGetNameFromIsoCode($langCode) {
  global $database, $shLangNameCache, $mosConfig_lang, $shLangNameCache;
  if (empty( $shLangNameCache)) {
    $type = shIsMultilingual();
    if ($type !== false) {
      if ($type == 'joomfish') {
        $isFish17 = strpos(JoomFishManager::getVersion(), 'V1.7') !== false;
      	$select = $isFish17 ? 'iso, code' : 'iso, shortcode, code';
      }
      $query = 'SELECT '.($type == 'joomfish' ? $select:'mambo, name')
               .' FROM '.($type == 'joomfish' ? '#__languages':'#__nok_language').' WHERE 1';
      $database->setQuery($query);
      $rows = $database->loadObjectList();
      foreach ($rows as $row) {
      	if ($type == 'joomfish')
      		$jfIsoCode = $isFish17 ? $row->iso : (empty($row->shortcode) ? $row->iso:$row->shortcode);
        $shLangNameCache[($type == 'joomfish' ? $jfIsoCode:$row->mambo)] = ($type == 'joomfish' ? $row->code:$row->name);
      }
      return empty($shLangNameCache[$langCode]) ? $mosConfig_lang : $shLangNameCache[$langCode];
    } else { // no joomfish, so it has to be default language
      return $mosConfig_lang;  
    }
  } else return empty($shLangNameCache[$langCode]) ? $mosConfig_lang : $shLangNameCache[$langCode];
}

// utility function to return list of available languages / isolate from JFish/Nokkaew compat issues
function shGetActiveLanguages() {
    
    static $shActiveLanguages = null;  // cache this, to reduce DB queries
    if (!is_null($shActiveLanguages))
	  return $shActiveLanguages;
	  
	$shKind = shIsMultilingual();
	if (empty($shKind)) {  // not multilingual
		$shLang->code = $GLOBALS['mosConfig_lang'];
		$shTemp = explode( '_', $GLOBALS['mosConfig_locale']);
		$shLang->iso = $shTemp[0] ? $shTemp[0] : 'en';
		$shActiveLanguages[] = $shLang;
		return $shActiveLanguages;
	}
	if ($shKind == 'joomfish')
		$tempList = JoomFishManager::getActiveLanguages();
	else {
        if (empty($database) && class_exists('mamboDatabase')) {  // sometimes with Mambo, $databse is not set
            $database =& mamboDatabase::getInstance();
            $flag = true;
        } 	
		$tempList = array();
		
		$database->setQuery( 'SELECT * FROM #__nok_language WHERE active=1 order by ordering' );
		$rows = $database->loadObjectList('id');
		if( $rows ) {
			foreach ($rows as $row) {
			    $shTemp = null;
				$shTemp->code = $row->name;
				$shTemp->iso = $row->mambo;
				$tempList[] = $shTemp;
			}
		}
	}		
	foreach ($tempList as $language) {
	    $shLang = null;
		$shLang->code = $language->code;
		$shLang->iso = $language->iso;
		$shActiveLanguages[] = $shLang;
	}
	return $shActiveLanguages;	
}

// returns prefix for $option component, as per user settings
function shGetComponentPrefix( $option) {
	
	if (empty($option)) return '';
	global $sefConfig;
	$option = str_replace('com_', '', $option);
	$prefix = '';
	$prefix = empty($sefConfig->defaultComponentStringList[@$option]) ? 
		'':$sefConfig->defaultComponentStringList[@$option];
	return $prefix;
}

// returns default Itemid for $option component, as per user settings
function shGetDefaultItemid( $option) {
	
	if (empty($option)) return '';
	global $sefConfig;
	$option = str_replace('com_', '', $option);
	$itemid = '';
	$itemid = empty($sefConfig->defaultComponentItemidList[@$option]) ? 
		'':intval($sefConfig->defaultComponentItemidList[@$option]);
	return $itemid;
}

function shRedirect( $url, $msg='', $redirKind = '301' ) {

   global $mainframe, $sefConfig;

    // specific filters
    if (class_exists('InputFilter')) { // mambo does not have Input filters
		$iFilter = new InputFilter();
		$url = $iFilter->process( $url );
		if (!empty($msg)) {
			$msg = $iFilter->process( $msg );
		}

		if ($iFilter->badAttributeValue( array( 'href', $url ))) {
			$url = $GLOBALS['mosConfig_live_site'];
		}
    }
	if (trim( $msg )) {
	 	if (strpos( $url, '?' ) !== false && $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode] != '/index.php?/') {
			$url .= '&mosmsg=' . rawurlencode( $msg );
		} else {
			$url .= '?mosmsg=' . rawurlencode( $msg );
		}
	}

	if (headers_sent()) {
		echo "<script>document.location.href='$url';</script>\n";
	} else {
		@ob_end_clean(); // clear output buffer
		switch ($redirKind) {
      case '302':
        $redirHeader ='HTTP/1.1 302 Moved Temporarily';
      break;
      case '303':
        $redirHeader ='HTTP/1.1 303 See Other';
      break;
      default:
        $redirHeader = 'HTTP/1.1 301 Moved Permanently'; 
      break;
    }
		header( $redirHeader );
		header( "Location: ". $url );
	}
	exit();
}

// Net/Url.php From the PEAR Library (http://pear.php.net/package/sh_Net_URL)
// +-----------------------------------------------------------------------+
// | Copyright (c) 2002-2004, Richard Heyes                                |
// | All rights reserved.                                                  |
// |                                                                       |
// | Redistribution and use in source and binary forms, with or without    |
// | modification, are permitted provided that the following conditions    |
// | are met:                                                              |
// |                                                                       |
// | o Redistributions of source code must retain the above copyright      |
// |   notice, this list of conditions and the following disclaimer.       |
// | o Redistributions in binary form must reproduce the above copyright   |
// |   notice, this list of conditions and the following disclaimer in the |
// |   documentation and/or other materials provided with the distribution.|
// | o The names of the authors may not be used to endorse or promote      |
// |   products derived from this software without specific prior written  |
// |   permission.                                                         |
// |                                                                       |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
// |                                                                       |
// +-----------------------------------------------------------------------+
// | Author: Richard Heyes <richard at php net>                            |
// +-----------------------------------------------------------------------+
//
// $Id: sh404sef.class.php 365 2008-04-09 20:27:19Z silianacom-svn $
//
// sh_Net_URL Class
class sh_Net_URL
{
    /**
    * Full url
    * @var string
    */
    var $url;
    /**
    * Protocol
    * @var string
    */
    var $protocol;
    /**
    * Username
    * @var string
    */
    var $username;
    /**
    * Password
    * @var string
    */
    var $password;
    /**
    * Host
    * @var string
    */
    var $host;
    /**
    * Port
    * @var integer
    */
    var $port;
    /**
    * Path
    * @var string
    */
    var $path;
    /**
    * Query string
    * @var array
    */
    var $querystring;
    /**
    * Anchor
    * @var string
    */
    var $anchor;
    /**
    * Whether to use []
    * @var bool
    */
    var $useBrackets;
    /**
    * PHP4 Constructor
    *
    * @see __construct()
    */
    function sh_Net_URL($url = null, $useBrackets = true)
    {
        $this->__construct($url, $useBrackets);
    }
    /**
    * PHP5 Constructor
    *
    * Parses the given url and stores the various parts
    * Defaults are used in certain cases
    *
    * @param string $url         Optional URL
    * @param bool   $useBrackets Whether to use square brackets when
    *                            multiple querystrings with the same name
    *                            exist
    */
    function __construct($url = null, $useBrackets = true)
    {
        $HTTP_SERVER_VARS  = !empty($_SERVER) ? $_SERVER : $GLOBALS['HTTP_SERVER_VARS'];
        $this->useBrackets = $useBrackets;
        $this->url         = $url;
        $this->user        = '';
        $this->pass        = '';
        $this->host        = '';
        $this->port        = 80;
        $this->path        = '';
        $this->querystring = array();
        $this->anchor      = '';
        // Only use defaults if not an absolute URL given
        if (!preg_match('/^[a-z0-9]+:\/\//i', $url)) {
            $this->protocol    = (isset ($HTTP_SERVER_VARS['HTTPS']) ?
            						 (@$HTTP_SERVER_VARS['HTTPS'] == 'on' ? 'https' : 'http') : 'http');
            /**
            * Figure out host/port
            */
            if (!empty($HTTP_SERVER_VARS['HTTP_HOST']) AND preg_match('/^(.*)(:([0-9]+))?$/U', $HTTP_SERVER_VARS['HTTP_HOST'], $matches)) {
                $host = $matches[1];
                if (!empty($matches[3])) {
                    $port = $matches[3];
                } else {
                    $port = $this->getStandardPort($this->protocol);
                }
            }
            $this->user        = '';
            $this->pass        = '';
            $this->host        = !empty($host) ? $host : (isset($HTTP_SERVER_VARS['SERVER_NAME']) ? $HTTP_SERVER_VARS['SERVER_NAME'] : 'localhost');
            $this->port        = !empty($port) ? $port : (isset($HTTP_SERVER_VARS['SERVER_PORT']) ? $HTTP_SERVER_VARS['SERVER_PORT'] : $this->getStandardPort($this->protocol));
            $this->path        = !empty($HTTP_SERVER_VARS['PHP_SELF']) ? $HTTP_SERVER_VARS['PHP_SELF'] : '/';
            $this->querystring = isset($HTTP_SERVER_VARS['QUERY_STRING']) ? $this->_parseRawQuerystring($HTTP_SERVER_VARS['QUERY_STRING']) : null;
            $this->anchor      = '';
        }
        // Parse the url and store the various parts
        if (!empty($url)) {
            $urlinfo = parse_url($url);
            // Default querystring
            $this->querystring = array();
            foreach ($urlinfo as $key => $value) {
                switch ($key) {
                    case 'scheme':
                        $this->protocol = $value;
                        $this->port     = $this->getStandardPort($value);
                        break;
                    case 'user':
                    case 'pass':
                    case 'host':
                    case 'port':
                        $this->$key = $value;
                        break;
                    case 'path':
                        if ($value{0} == '/') {
                            $this->path = $value;
                        } else {
                            $path = dirname($this->path) == DIRECTORY_SEPARATOR ? '' : dirname($this->path);
                            $this->path = sprintf('%s/%s', $path, $value);
                        }
                        break;
                    case 'query':
                        $this->querystring = $this->_parseRawQueryString($value);
                        break;
                    case 'fragment':
                        $this->anchor = $value;
                        break;
                }
            }
        }
    }
    /**
    * Returns full url
    *
    * @return string Full url
    * @access public
    */
    function getURL()
    {
        $querystring = $this->getQueryString();
        $this->url = $this->protocol . '://'
                   . $this->user . (!empty($this->pass) ? ':' : '')
                   . $this->pass . (!empty($this->user) ? '@' : '')
                   . $this->host . ($this->port == $this->getStandardPort($this->protocol) ? '' : ':' . $this->port)
                   . $this->path
                   . (!empty($querystring) ? '?' . $querystring : '')
                   . (!empty($this->anchor) ? '#' . $this->anchor : '');
        return $this->url;
    }
    /**
    * Adds a querystring item
    *
    * @param  string $name       Name of item
    * @param  string $value      Value of item
    * @param  bool   $preencoded Whether value is urlencoded or not, default = not
    * @access public
    */
    function addQueryString($name, $value, $preencoded = false)
    {
        if ($preencoded) {
            $this->querystring[$name] = $value;
        } else {
            $this->querystring[$name] = is_array($value) ? array_map('rawurlencode', $value): rawurlencode($value);
        }
    }
    /**
    * Removes a querystring item
    *
    * @param  string $name Name of item
    * @access public
    */
    function removeQueryString($name)
    {
        if (isset($this->querystring[$name])) {
            unset($this->querystring[$name]);
        }
    }
    /**
    * Sets the querystring to literally what you supply
    *
    * @param  string $querystring The querystring data. Should be of the format foo=bar&x=y etc
    * @access public
    */
    function addRawQueryString($querystring)
    {
        $this->querystring = $this->_parseRawQueryString($querystring);
    }
    /**
    * Returns flat querystring
    *
    * @return string Querystring
    * @access public
    */
    function getQueryString()
    {
        if (!empty($this->querystring)) {
            foreach ($this->querystring as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $querystring[] = $this->useBrackets ? sprintf('%s[%s]=%s', $name, $k, $v) : ($name . '=' . $v);
                    }
                } elseif (!is_null($value)) {
                    $querystring[] = $name . '=' . $value;
                } else {
                    $querystring[] = $name;
                }
            }
            $querystring = implode(ini_get('arg_separator.output'), $querystring);
        } else {
            $querystring = '';
        }
        return $querystring;
    }
    /**
    * Parses raw querystring and returns an array of it
    *
    * @param  string  $querystring The querystring to parse
    * @return array                An array of the querystring data
    * @access private
    */
    function _parseRawQuerystring($querystring)
    {
        $parts  = preg_split('/[' . preg_quote(ini_get('arg_separator.input'), '/') . ']/', $querystring, -1, PREG_SPLIT_NO_EMPTY);
        $return = array();
        foreach ($parts as $part) {
            if (strpos($part, '=') !== false) {
                $value = substr($part, strpos($part, '=') + 1);
                $key   = substr($part, 0, strpos($part, '='));
            } else {
                $value = null;
                $key   = $part;
            }
            if (substr($key, -2) == '[]') {
                $key = substr($key, 0, -2);
                if (@!is_array($return[$key])) {
                    $return[$key]   = array();
                    $return[$key][] = $value;
                } else {
                    $return[$key][] = $value;
                }
            } elseif (!$this->useBrackets AND !empty($return[$key])) {
                $return[$key]   = (array)$return[$key];
                $return[$key][] = $value;
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }
    /**
    * Resolves //, ../ and ./ from a path and returns
    * the result. Eg:
    *
    * /foo/bar/../boo.php    => /foo/boo.php
    * /foo/bar/../../boo.php => /boo.php
    * /foo/bar/.././/boo.php => /foo/boo.php
    *
    * This method can also be called statically.
    *
    * @param  string $url URL path to resolve
    * @return string      The result
    */
    function resolvePath($path)
    {
        $path = explode('/', str_replace('//', '/', $path));
        for ($i=0; $i<count($path); $i++) {
            if ($path[$i] == '.') {
                unset($path[$i]);
                $path = array_values($path);
                $i--;
            } elseif ($path[$i] == '..' AND ($i > 1 OR ($i == 1 AND $path[0] != '') ) ) {
                unset($path[$i]);
                unset($path[$i-1]);
                $path = array_values($path);
                $i -= 2;
            } elseif ($path[$i] == '..' AND $i == 1 AND $path[0] == '') {
                unset($path[$i]);
                $path = array_values($path);
                $i--;
            } else {
                continue;
            }
        }
        return implode('/', $path);
    }
    /**
    * Returns the standard port number for a protocol
    *
    * @param  string  $scheme The protocol to lookup
    * @return integer         Port number or NULL if no scheme matches
    *
    * @author Philippe Jausions <Philippe.Jausions@11abacus.com>
    */
    function getStandardPort($scheme)
    {
        switch (strtolower($scheme)) {
            case 'http':    return 80;
            case 'https':   return 443;
            case 'ftp':     return 21;
            case 'imap':    return 143;
            case 'imaps':   return 993;
            case 'pop3':    return 110;
            case 'pop3s':   return 995;
            default:        return null;
       }
    }
    /**
    * Forces the URL to a particular protocol
    *
    * @param string  $protocol Protocol to force the URL to
    * @param integer $port     Optional port (standard port is used by default)
    */
    function setProtocol($protocol, $port = null)
    {
        $this->protocol = $protocol;
        $this->port = is_null($port) ? $this->getStandardPort() : $port;
    }
}

function shCloseLogFile() {

	global $shLogger;
	if (!empty($shLogger)) {
		$shLogger->log('Closing log file at shutdown'."\n\n");
		if (!empty($shLogger->logFile))
			fClose( $shLogger->logFile);
  }  
}

function _log($text, $data = '') {

	global $sefConfig, $shLogger;
	static $shutdownRegistered = false;
	
	if (empty($sefConfig) || empty($sefConfig->debugToLogFile)) return;
	if (!empty($sefConfig->debugDuration) && (time()-$sefConfig->debugStartedAt) > $sefConfig->debugDuration)
		return;
	if (empty($shLogger)) {
		$shLogger = new shSimpleLogger( $GLOBALS['mosConfig_live_site'], 
										sh404SEF_ADMIN_ABS_PATH.'logs/',
										'sh404SEF_debug_log',
										$sefConfig->debugToLogFile);
	}
	if (!$shutdownRegistered) {
		register_shutdown_function('shCloseLogFile');
		$shutdownRegistered = true;
	}
	$shLogger->log($text, $data);
}

class shSimpleLogger {

  var $traceFileName = '';
  var $isActive = 0;
  var $logFile = null;
  
  function shSimpleLogger( $siteName, $basePath, $fileName, $isActive) {
  global $sefConfig;
    if (empty($isActive)) {
      $this->isActive = 0;
      return;
    } else $this->isActive = 1; 
    $traceFileName = $basePath.$sefConfig->debugStartedAt.'.'.$fileName.'_'
              .str_replace('/','_',str_replace('http://', '', $siteName))
              .'.log';
    // Create file
    $fileIsThere = file_exists($traceFileName);
    $sep = "\t"; 
  	if (!$fileIsThere) { // create file
  	  $shortVersion = explode(' - ', $GLOBALS['sefConfig']->version);
  	  $fileHeader = 'sh404SEF trace file - created : '.$this->logTime(). ' by sh404SEF '.$shortVersion[0]
          .' for '.$siteName."\n\n".str_repeat('-',25).' PHP Configuration '.str_repeat('-',25)."\n\n";
      $config = $this->parsePHPConfig();
      $line = str_repeat('-',69)."\n\n"; 
  	} else $fileHeader = '';
    $file = fopen($traceFileName, 'ab');
    if ($file) {
      if (!empty($fileHeader)) {
        fWrite( $file, $fileHeader);
        fWrite( $file, print_r($config, true));
        fwrite( $file, $line);
      } 	  
      $this->logFile = $file;
    } else {
      $this->isActive = 0;
      return;
    }
  }
  
  function logTime() {
    return date('Y-m-d')."\t".date('H:i:s');
  }
  
  function log($text, $data='') {
    if (empty($this->isActive) || empty($text)) return;
    $logData = empty($data) ? '' : ":\t".print_r($data, true);
    fWrite($this->logFile, $this->logTime()."\t".$text.$logData."\n");
  }
  
  function parsePHPConfig() {
  // by Andrew dot Boag at catalyst dot net dot nz
  // found on php.net doc
  
    ob_start();
    phpinfo(-1);
    $s = ob_get_contents();
    ob_end_clean();
    $a = $mtc = array();
    if (preg_match_all('/<tr><td class="e">(.*?)<\/td><td class="v">(.*?)<\/td>(:?<td class="v">(.*?)<\/td>)?<\/tr>/',$s,$mtc,PREG_SET_ORDER)){
        foreach($mtc as $v){
            if($v[2] == '<i>no value</i>') continue;
            $a[$v[1]] = $v[2];
        }
    }
    return $a;
  }
}

?>
