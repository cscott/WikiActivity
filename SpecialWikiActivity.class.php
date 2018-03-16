<?php

class SpecialWikiActivity extends UnlistedSpecialPage {
	var $activeTab;
	var $classWatchlist;
	var $loggedIn;

	private $defaultView;
	private $feedSelected;

	function __construct() {
		parent::__construct('WikiActivity', '' /* no restriction */, true /* listed */);
	}

	function execute($par) {
		global $wgOut, $wgUser, $wgBlankImgUrl, $wgEditPageFrameOptions;

		$wgEditPageFrameOptions = "SAMEORIGIN";
		$this->setHeaders();

		// choose default view (RT #68074)
		if ($wgUser->isLoggedIn()) {
			$this->defaultView = MyHome::getDefaultView();
			if ($par == '') {
				$par = $this->defaultView;
			}
		}
		else {
			$this->defaultView = false;
		}

		// watchlist feed
		if($par == 'watchlist') {
			$this->classWatchlist = "selected";

			// not available for anons
			if($wgUser->isAnon()) {
					$wgOut->wrapWikiMsg( '<div id="myhome-log-in">$1</div>', array('myhome-log-in', wfGetReturntoParam()) );

				// TODO: probably remove this entirely; wikia-specific for AJAX login
				//oasis-activity-watchlist-login
				// RT #23970
/*
				$wgOut->addInlineScript(<<<JS
$(function() {
	$('#myhome-log-in').find('a').click(function(ev) {
		openLogin(ev);
	});
});
JS
				);
*/
				return;
			}
			else {
				$this->feedSelected = 'watchlist';
				$feedProxy = new WatchlistFeedAPIProxy();
				$feedRenderer = new WatchlistFeedRenderer();
			}
		} else {
		//for example: wiki-domain.com/wiki/Special:WikiActivity
			$this->feedSelected = 'activity';
			$feedProxy = new ActivityFeedAPIProxy();
			$feedRenderer = new ActivityFeedRenderer();
		}

		$feedProvider = new DataFeedProvider($feedProxy);

		global $wgJsMimeType, $wgExtensionsPath;
		$wgOut->addScript("<script type=\"{$wgJsMimeType}\" src=\"{$wgExtensionsPath}/wikia/MyHome/WikiActivity.js\"></script>\n");
		// TODO / FIXME: SASS-based style junk
		//$wgOut->addExtensionStyle(AssetsManager::getInstance()->getSassCommonURL('extensions/wikia/MyHome/oasis.scss'));

		wfRunHooks( 'SpecialWikiActivityExecute', array( $wgOut, $wgUser ));

		$data = $feedProvider->get(50);  // this breaks when set to 60...

		// use message from MyHome as special page title
		$wgOut->setPageTitle(wfMessage('wikiactivity')->text());

		$template = new EasyTemplate(dirname(__FILE__).'/templates');
		$template->set('data', $data['results']);

		$showMore = isset($data['query-continue']);
		if ($showMore) {
			$template->set('query_continue', $data['query-continue']);
		}
		if (empty($data['results'])) {
			$template->set('emptyMessage', wfMessage("myhome-activity-feed-empty")->parseAsBlock());
		}

		$template->set_vars(array(
			'showMore' => $showMore,
			'type' => $this->feedSelected,
			'wgBlankImgUrl' => $wgBlankImgUrl,
		));

		$wgOut->addHTML($template->render('activityfeed.oasis'));

		// page header: replace subtitle with navigation
		global $wgHooks;
		$wgHooks['PageHeaderIndexAfterExecute'][] = array($this, 'addNavigation');

		if ($wgUser->isAnon()) {
			// FIXME / TODO: Method is deprecated in 1.27; removed in 1.30
			//$this->getOutput()->setSquidMaxage( 3600 ); // 1 hour
			// FIXME/TODO: no such stuff
			#$this->getOutput()->tagWithSurrogateKeys(
			#	MyHome::getWikiActivitySurrogateKey()
			#);
		}
	}

	/**
	 * Replaces page header's subtitle with navigation for WikiActivity
	 *
	 * @author macbre
	 */
	function addNavigation(&$moduleObject, &$params) {
		global $wgUser;

		$template = new EasyTemplate(dirname(__FILE__).'/templates');

		// RT #68074: show default view checkbox for logged-in users only
		$showDefaultViewSwitch = $wgUser->isLoggedIn() && ($this->defaultView != $this->feedSelected);

		$template->set_vars(array(
			'classWatchlist' => $this->classWatchlist,
			'defaultView' => $this->defaultView,
			'loggedIn' => $wgUser->isLoggedIn(),
			'showDefaultViewSwitch' => $showDefaultViewSwitch,
			'type' => $this->feedSelected,
		));

		// replace subtitle with navigation for WikiActivity
		$moduleObject->pageSubtitle = $template->render('navigation.oasis');

		return true;
	}
}
