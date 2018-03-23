<?php

class WatchlistFeedAPIProxy implements iAPIProxy {

	var $APIparams;

	public function __construct() {
		$this->APIparams = array();
		$this->APIparams['action'] = 'query';
		$this->APIparams['list'] = 'watchlist';
		$this->APIparams['wlprop'] = 'ids|title|flags|user|comment|timestamp|sizes|loginfo';
	}

	public function get($limit, $start = null) {
		if(!empty($start)) {
			$this->APIparams['wlstart'] = $start;
		} else {
			unset($this->APIparams['wlstart']);
		}

		$this->APIparams['wllimit'] = $limit;

		$api = new ApiMain(new FauxRequest($this->APIparams));
		$api->execute();
		$res = $api->getResult()->getResultData();

		// haleyjd: strip metadata from query results
		$res = ApiResult::stripMetadata($res);

		$out = array();

		if(isset($res['query']) && isset($res['query']['watchlist'])) {
			$out['results'] = $res['query']['watchlist'];
		}

		if(isset($res['query-continue'])) {
			$out['query-continue'] = $res['query-continue']['watchlist']['wlstart'];
		}

		return $out;
	}

}
