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
		global $wgHooks;

		$out  = $this->getOutput();
		$user = $this->getUser();

		$this->setHeaders();

		// choose default view (RT #68074)
		if ($user->isLoggedIn()) {
			$this->defaultView = MyHome::getDefaultView();
			if ($par == '') {
				$par = $this->defaultView;
			}
		} else {
			$this->defaultView = false;
		}

		// watchlist feed
		if($par == 'watchlist') {
			$this->classWatchlist = "selected";

			// not available for anons
			if($user->isAnon()) {
				$out->wrapWikiMsg( '<div id="myhome-log-in">$1</div>', array('myhome-log-in', $this->getReturntoParam()) );
				return;
			} else {
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

		// haleyjd: use ResourceLoader for scripts and styles
		$out->addModules('ext.SpecialWikiActivity.ajax');
		$wgHooks['BeforePageDisplay'][] = 'SpecialWikiActivity::onBeforePageDisplay';

		wfRunHooks( 'SpecialWikiActivityExecute', array( $out, $user ));

		$data = $feedProvider->get(50);  // this breaks when set to 60...

		// use message from MyHome as special page title
		$out->setPageTitle(wfMessage('wikiactivity')->text());

		$template = new EasyTemplate(dirname(__FILE__).'/templates');
		$template->set('data', $data['results']);

		// FIXME: old-style query continuation
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
		));

		// haleyjd: add navigation header here, now
		$this->addNavigation();
		$out->addHTML($template->render('activityfeed.oasis'));

		if ($user->isAnon()) {
			// FIXME / TODO: Method is deprecated in 1.27; removed in 1.30
			//$this->getOutput()->setSquidMaxage( 3600 ); // 1 hour
			// FIXME/TODO: no such stuff
			#$this->getOutput()->tagWithSurrogateKeys(
			#	MyHome::getWikiActivitySurrogateKey()
			#);
		}
	}
	
	/**
	 * Add module style sheets at the end of processing
	 *
	 * @author haleyjd
	 */
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin) {
		$out->addModuleStyles('ext.SpecialWikiActivity.styles');
		return true;
	}
	
	/**
	 *
	 */
	function getReturnToParam() {
		$request  = $this->getRequest();
		$page = Title::newFromURL( $request->getVal( 'title', '' ) );
		$page = $request->getVal( 'returnto', $page );
		if ( strval( $page ) !== '' ) {
			$a['returnto'] = $page;
			$query = $request->getVal( 'returntoquery', '' );
			if( $query != '' ) {
				$a['returntoquery'] = $query;
			}
		}
		$returnto = wfArrayToCGI( $a );

		return $returnto;
	}

	/**
	 * Replaces page header's subtitle with navigation for WikiActivity
	 *
	 * @author macbre
	 */
	function addNavigation() {
		$out  = $this->getOutput();
		$user = $this->getUser();

		$template = new EasyTemplate(dirname(__FILE__).'/templates');

		// RT #68074: show default view checkbox for logged-in users only
		$showDefaultViewSwitch = $user->isLoggedIn() && ($this->defaultView != $this->feedSelected);

		$template->set_vars(array(
			'classWatchlist' => $this->classWatchlist,
			'defaultView' => $this->defaultView,
			'loggedIn' => $user->isLoggedIn(),
			'showDefaultViewSwitch' => $showDefaultViewSwitch,
			'type' => $this->feedSelected,
		));

		// replace subtitle with navigation for WikiActivity
		//$moduleObject->pageSubtitle = $template->render('navigation.oasis');
		$out->addHTML($template->render('navigation.oasis'));

		return true;
	}
}
