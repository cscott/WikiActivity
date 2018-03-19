<?php

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'WikiActivity',
	'descriptionmsg' => 'myhome-desc',
	'author' => array('Inez Korczyński', 'Maciej Brencz', '[http://www.wikia.com/wiki/User:Marooned Maciej Błaszkowski (Marooned)]'),
	'url' => 'https://github.com/Wikia/app/tree/dev/extensions/wikia/MyHome'
);

$dir = dirname(__FILE__) . '/';

// haleyjd: skinnable appearance
$wgSpecialWikiActivitySkin = 'default';

// haleyjd: use ResourceLoader to load JavaScript
$wgResourceModules['ext.SpecialWikiActivity'] = array(
	'position'      => 'bottom',
	'scripts'       => 'WikiActivity.js',
	'styles'        => array(
		"skins/shared/sprite.css",
		"skins/{$wgSpecialWikiActivitySkin}/ActivityFeed.css",
	),
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'WikiActivity',
);

// Special:WikiActivity
$wgAutoloadClasses['SpecialWikiActivity'] = $dir.'SpecialWikiActivity.class.php';
$wgSpecialPages['WikiActivity'] = 'SpecialWikiActivity';
$wgSpecialPageGroups['WikiActivity'] = 'changes';
$wgExtensionMessagesFiles['WikiActivityAliases'] = "$dir/SpecialWikiActivity.alias.php";

// hooks

// NB: see notes in MyHome.class.php on why this is disabled.
//$wgHooks['InitialQueriesMainPage'][] = 'MyHome::getInitialMainPage';
//$wgHooks['GetPreferences'][] = 'MyHome::onGetPreferences';
$wgHooks['RevisionInsertComplete'][] = 'MyHome::onRevisionInsertComplete';
