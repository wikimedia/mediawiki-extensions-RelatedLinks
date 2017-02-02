<?php

/***********************************************************
 * Name:     RelatedLinks
 * Desc:     Custom Related Link SideBar
 *
 * Version:  1.0.0
 *
 * Author:   sleepinglion
 * Homepage: https://www.mediawiki.org/wiki/Extension:RelatedLinks
 * 			 https://github.com/sleepinglion
 *           http://www.sleepinglion.pe.kr
 * 			 
 *
 * License:  MIT
 *
 ***********************************************************
 */

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'RelatedLinks' );
	
	/*
	# Tell MediaWiki about the new special page and its class name
	/* wfWarn(
	 'Deprecated PHP entry point used for WikiEditor extension. Please use wfLoadExtension instead, ' .
	 'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	 );*/
	
	return true;
} else {
	//die( 'This version of the Nuke extension requires MediaWiki 1.25+' );
	
	$wgExtensionCredits['specialpage'][] = array(
		'path' => __FILE__,
		'name' => 'RelatedLinks',
		'author' => 'sleepinglion',
		'url' => 'https://www.mediawiki.org/wiki/Extension:RelatedLinks',
		'descriptionmsg' => 'Custom Related Link SideBar',
		'version' => '1.0'
	);

	$wgAutoloadClasses['SpecialRelatedLinks'] = __DIR__ . '/SpecialRelatedLinks.php'; # Location of the SpecialMyExtension class (Tell MediaWiki to load this file)
	$wgMessagesDirs['RelatedLinks'] = __DIR__ . "/i18n"; # Location of localisation files (Tell MediaWiki to load them)
	$wgExtensionMessagesFiles['RelatedLinksAlias'] = __DIR__ . '/RelatedLinks.alias.php'; # Location of an aliases file (Tell MediaWiki to load it)
	$wgSpecialPages['RelatedLinks'] = 'SpecialRelatedLinks'; # Tell MediaWiki about the new special page and its class name
}
