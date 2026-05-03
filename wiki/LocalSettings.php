<?php
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# https://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

$wgReadOnly = getenv('MW_WG_READONLY') ? true : false;
ini_set('display_errors', false);

if (getenv('MW_DEBUG')) {    
    $wgShowExceptionDetails = true;
    $wgShowDBErrorBacktrace = true;
    $wgDebugToolbar = true;
}

## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename = getenv('MW_WG_SITENAME');
$wgMetaNamespace = getenv('MW_WG_METANAMESPACE');

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "";
$wgScriptExtension = ".php";

## Path to articles is set up so that pages are reachable on /Page_Name
$wgArticlePath = "/$1";

## The protocol and server name to use in fully-qualified URLs
$wgServer = getenv('MW_WG_SERVER');

## The relative URL path to the skins directory
$wgStylePath = "$wgScriptPath/skins";

## The relative URL path to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
$wgLogo = getenv("MW_WG_LOGO") ?: "$wgScriptPath/resources/assets/wiki.png";

## The relative URL path to the favicon.  Should be a path to a Favicon.ico file.
if (getenv("MW_FAVICON")) {
    $wgFavicon = getenv("MW_FAVICON");
}

## UPO means: this is also a user preference option

$wgEnableEmail = getenv("MW_WG_ENABLE_EMAIL") ? true : false;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = getenv("MW_WG_EMERGENCY_CONTACT") ?: "apache@localhost";
$wgPasswordSender = getenv("MW_WG_PASSWORD_SENDER") ?: "apache@localhost";

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;

## Database settings
$wgDBtype = getenv("MW_WG_DBTYPE") ?: "mysql";
$wgDBserver = getenv("MW_WG_DBSERVER") ?: "mysql";
$wgDBname = getenv("MW_WG_DBNAME") ?: "mediawiki";
$wgDBuser = getenv('MW_WG_DBUSER');
$wgDBpassword = getenv('MW_WG_DBPASS');

# MySQL specific settings
$wgDBprefix = getenv("MW_WG_DBPREFIX") ?: "";

# MySQL table options to use during installation or update
$wgDBTableOptions = getenv("MW_WG_DBTABLEOPTIONS") ?: "ENGINE=InnoDB, DEFAULT CHARSET=utf8";

# Experimental charset support for MySQL 5.0.
$wgDBmysql5 = getenv("MW_WG_DBMYSQL5") ? (bool) getenv("MW_WG_DBMYSQL5") : true;

## Shared memory settings
$wgMainCacheType = CACHE_NONE;
$wgMemCachedServers = array();

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";

# InstantCommons allows wiki to use images from http://commons.wikimedia.org
$wgUseInstantCommons = false;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = getenv("MW_WG_SHELL_LOCALE") ?: "C.UTF-8";

## If you want to use image uploads under safe mode,
## create the directories images/archive, images/thumb and
## images/temp, and make them all writable. Then uncomment
## this, if it's not already uncommented:
#$wgHashedUploadDirectory = false;

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
#$wgCacheDirectory = "$IP/cache";

# Site language code, should be one of the list in ./languages/Names.php
$wgLanguageCode = getenv("MW_WG_LANGUAGECODE");

$wgSecretKey = getenv("MW_WG_SECRET");

# Site upgrade key. Must be set to a string (default provided) to turn on the
# web installer while LocalSettings.php is in place
$wgUpgradeKey = getenv("MW_WG_UPGRADE_KEY") ?: "b31022590a7b3b8f";

if (getenv('MW_DISABLE_API')) {
    $wgEnableAPI = false;
}
if (getenv('MW_DISABLE_FEED')) {
    $wgFeed = false;
}

if (getenv('MW_REFERRER_POLICY')) {
    $wgReferrerPolicy = getenv('MW_REFERRER_POLICY');
}

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "";
$wgRightsText = "";
$wgRightsIcon = "";

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'vector', 'monobook':
$wgDefaultSkin = "vector";

# Enabled skins.
# The following skins were automatically enabled:
wfLoadSkin( 'MonoBook' );
wfLoadSkin( 'Vector' );
# This one is available on demand, move along, there is nothing to see here
getenv('MW_SKIN_MATERIAL_ENABLE') !== false && wfLoadSkin( 'Material' );

# Config for Vector Skin
if (getenv('MW_SKIN_VECTOR_DEFAULT_SKIN_VERSION')) {
    $wgVectorDefaultSkinVersion = getenv('MW_SKIN_VECTOR_DEFAULT_SKIN_VERSION');
}
if (getenv('MW_SKIN_VECTOR_RESPONSIVE_ENABLE')) {
    $wgVectorResponsive = true;
}

# Subpages
if (getenv('MW_NS_WITH_SUBPAGES_MAIN')) {
    $wgNamespacesWithSubpages[NS_MAIN] = true;
}
if (getenv('MW_NS_WITH_SUBPAGES_TEMPLATE')) {
    $wgNamespacesWithSubpages[NS_TEMPLATE] = true;
}

# Enabled Extensions. Most extensions are enabled by including the base extension file here
# but check specific extension documentation for more details
# The following extensions were automatically enabled:
wfLoadExtension( 'Interwiki' );
wfLoadExtension( 'Renameuser' );
wfLoadExtension( 'SyntaxHighlight_GeSHi' );
wfLoadExtension( 'ParserFunctions' );
if (getenv('MW_PARSERFUNCTIONS_ENABLE_STRING_FUNCTIONS')) {
    $wgPFEnableStringFunctions = true;
}
wfLoadExtension( 'ImageMap' );

# VisualEditor Extension
wfLoadExtension( 'VisualEditor' );

# Enable by default for everybody
$wgDefaultUserOptions['visualeditor-enable'] = 1;

# Don't allow users to disable it
# $wgHiddenPrefs[] = 'visualeditor-enable';

# OPTIONAL: Enable VisualEditor's experimental code features
# #$wgDefaultUserOptions['visualeditor-enable-experimental'] = 1;

wfLoadExtension( 'Parsoid', 'vendor/wikimedia/parsoid/extension.json' );
# with MV 1.39 parsoid autoconfig works and there is no need for
# customising $wgVirtualRestConfig.
$wgVisualEditorParsoidAutoConfig = true;

# TemplateData Extension
wfLoadExtension( 'TemplateData' );

# CategoryTree Extension
wfLoadExtension( 'CategoryTree' );
$wgUseAjax = true;
if (getenv('MW_CATEGORYTREE_SIDEBAR_ROOT')) {
    $wgCategoryTreeSidebarRoot = getenv('MW_CATEGORYTREE_SIDEBAR_ROOT');
}

# ExternalData
wfLoadExtension( 'ExternalData' );

if (getenv('MW_EXTERNALDATA_DIRECTORY_PATH')) {
    $edgDirectoryPath = json_decode(getenv('MW_EXTERNALDATA_DIRECTORY_PATH'), true);
}
	
# Semantic Stuff
wfLoadExtension( 'SemanticMediaWiki' );
enableSemantics(getenv('MW_SMW_ENABLE_SEMANTICS_DOMAIN'));
wfLoadExtension( 'SemanticCompoundQueries' );
wfLoadExtension( 'PageForms' );

# HierarchyBuilder Extension
wfLoadExtension( 'HierarchyBuilder' );

# more exts
wfLoadExtension( 'Arrays' );
wfLoadExtension( 'HeaderTabs' );

if (getenv('MW_APPROVED_REVS')) {
    wfLoadExtension( 'ApprovedRevs' );
}

# NativeSvgHandler needs opt-in due to xss concerns
if (getenv('MW_NATIVESVGHANDLER')) {
    wfLoadExtension( 'NativeSvgHandler' );
}

# draw.io, needs opt-in because you need to trust diagrams.net
if (getenv('MW_DRAWIOEDITOR')) {
    wfLoadExtension( 'DrawioEditor' );
}
if (getenv('MW_DRAWIOEDITOR_IMAGE_TYPE')) {
    $wgDrawioEditorImageType = getenv('MW_DRAWIOEDITOR_IMAGE_TYPE');
}
if (getenv('MW_DRAWIOEDITOR_IMAGE_INTERACTIVE')) {
    $wgDrawioEditorImageInteractive = true;
}
if (getenv('MW_DRAWIOEDITOR_BACKEND_URL')) {
    $wgDrawioEditorBackendUrl = getenv('MW_DRAWIOEDITOR_BACKEND_URL');
}

# for easing migrations
wfLoadExtension( 'ReplaceText' );
$wgGroupPermissions['bureaucrat']['replacetext'] = true;

if (getenv("MW_USERMERGE")) {
    wfLoadExtension( 'UserMerge' );
    $wgGroupPermissions['bureaucrat']['usermerge'] = getenv('MW_PERMISSION_BUREAUCRAT_USERMERGE') ? true : false;
}

if (getenv('MW_WG_RAWHTML') === 'true') {
    $wgRawHtml = true;
}

# Rights config
$wgGroupPermissions['*']['read'] = getenv("MW_PERMISSIONS_READ") ? false : true;
$wgGroupPermissions['user']['read'] = getenv("MW_PERMISSIONS_USER_READ") ? false : true;

$wgGroupPermissions['*']['edit'] = getenv("MW_PERMISSIONS_EDIT") ? false : true;
$wgGroupPermissions['user']['edit'] = getenv("MW_PERMISSIONS_USER_EDIT") ? false : true;

$wgGroupPermissions['*']['createpage'] = getenv("MW_PERMISSIONS_CREATEPAGE") ? false : true;
$wgGroupPermissions['user']['createpage'] = getenv("MW_PERMISSIONS_USER_CREATEPAGE") ? false : true;

$wgGroupPermissions['*']['editmyprivateinfo'] = getenv("MW_PERMISSIONS_EDITMYPRIVATEINFO") ? false : true;

# Rights for auth config
$wgGroupPermissions['*']['createaccount'] = getenv('MW_AUTH_CREATEACCOUNT') ? false : true;
$wgGroupPermissions['*']['autocreateaccount'] = getenv('MW_AUTH_AUTOCREATEUSER') ? true : false;

# Auth_remoteuser Extension
if (getenv('MW_AUTH_REMOTEUSER')) {
    $wgAuthRemoteuserUserName = getenv('MW_AUTH_REMOTEUSER_USER_NAME') ? (bool) getenv('MW_AUTH_REMOTEUSER_USER_NAME') : null;
}

# Pluggable Auth
if (getenv('MW_AUTH_PLUGGABLE')) {
    wfLoadExtension( 'PluggableAuth' );
    $wgPluggableAuth_Config = [];
    $wgPluggableAuth_EnableAutoLogin = getenv('MW_AUTH_PLUGGABLE_ENABLE_AUTO_LOGIN') ? (bool) getenv('MW_AUTH_PLUGGABLE_ENABLE_AUTO_LOGIN') : false;
    $wgPluggableAuth_EnableLocalLogin = getenv('MW_AUTH_PLUGGABLE_ENABLE_LOCAL_LOGIN') ? (bool) getenv('MW_AUTH_PLUGGABLE_ENABLE_LOCAL_LOGIN') : false;
    $wgPluggableAuth_EnableLocalProperties = getenv('MW_AUTH_PLUGGABLE_ENABLE_LOCAL_PROPERTIES') ? (bool) getenv('MW_AUTH_PLUGGABLE_ENABLE_LOCAL_PROPERTIES') : false;
}

# OpenID Connect
if (getenv('MW_AUTH_OIDC')) {
    wfLoadExtension( 'OpenIDConnect' );
    $wgPluggableAuth_Config['OIDC'] = [
        'plugin' => 'OpenIDConnect',
        'data' => [
            'providerURL' => getenv('MW_AUTH_OIDC_IDP_URL'),
            'clientID' => getenv('MW_AUTH_OIDC_CLIENT_ID'),
            'clientsecret' => getenv('MW_AUTH_OIDC_CLIENT_SECRET'),
        ]
    ];
    if (getenv('MW_AUTH_OIDC_SCOPE')) {
        $wgPluggableAuth_Config['OIDC']['data']['scope'] = explode(" ", getenv('MW_AUTH_OIDC_SCOPE'));
    }
    $wgOpenIDConnect_UseRealNameAsUserName = getenv('MW_AUTH_OIDC_USE_REAL_NAME_AS_USERNAME') ? (bool) getenv('MW_AUTH_OIDC_USE_REAL_NAME_AS_USERNAME') : false;
    $wgOpenIDConnect_UseEmailNameAsUserName = getenv('MW_AUTH_OIDC_USER_EMAIL_NAME_AS_USERNAME') ? (bool) getenv('MW_AUTH_OIDC_USER_EMAIL_NAME_AS_USERNAME') : false;
    $wgOpenIDConnect_MigrateUsersByUserName = getenv('MW_AUTH_OIDC_MIGRATE_USERS_BY_USERNAME') ? (bool) getenv('MW_AUTH_OIDC_MIGRATE_USERS_BY_USERNAME') : false;
    $wgOpenIDConnect_MigrateUsersByEmail = getenv('MW_AUTH_OIDC_MIGRATE_USERS_BY_EMAIL') ? (bool) getenv('MW_AUTH_OIDC_MIGRATE_USERS_BY_EMAIL') : false;
    $wgOpenIDConnect_ForceLogout = getenv('MW_AUTH_OIDC_FORCE_LOGOUT') ? (bool) getenv('MW_AUTH_OIDC_FORCE_LOGOUT') : false;
    // override this when you can't simply change the 'sub' claim (eg. because you are using keycloak and don't want to deploy a script to override the sub claim)
    $wgOpenIDConnect_SubjectUserInfoClaim = getenv('MW_AUTH_OIDC_SUBJECT_USERINFO_CLAIM') ? getenv('MW_AUTH_OIDC_SUBJECT_USERINFO_CLAIM') : 'sub';
}

if (getenv('MW_FILE_EXTENSION_ALLOW_SVG')) {
    $wgFileExtensions[] = 'svg';
}

# Scribunto
if (getenv('MW_SCRIBUNTO_ENABLE')) {
    wfLoadExtension('Scribunto');
    $wgScribuntoDefaultEngine = getenv('MW_SCRIBUNTO_DEFAULT_ENGINE') ?: 'luastandalone';
    $wgScribuntoUseGeSHi = getenv('MW_SCRIBUNTO_USE_GESHI') ? (bool) getenv('MW_SCRIBUNTO_USE_GESHI') : false;
}

if (getenv('MW_KROKI_ENABLE')) {
    wfLoadExtension( 'Kroki' );
    $wgKrokiServerEndpoint = getenv('MW_KROKI_SERVER_ENDPOINT') ?: "https://kroki.io";
}
