<?php

class ActivityFeedAPIProxy implements iAPIProxy {

	var $APIparams;

	public function __construct($includeNS = null, $userName = null) {
		$this->APIparams = array();
		$this->APIparams['action'] = 'query';
		$this->APIparams['list'] = 'recentchanges';
		$this->APIparams['rcprop'] = 'comment|timestamp|ids|title|loginfo|user';
		$this->APIparams['rcshow'] = '!bot';
		if (!is_null($includeNS)) $this->APIparams['rcnamespace'] = $includeNS;
		if (!empty($userName)) $this->APIparams['rcuser'] = $userName;
	}

	public function get($limit, $start = null) {
		if(!empty($start)) {
			$this->APIparams['rcstart'] = $start;
		} else {
			unset($this->APIparams['rcstart']);
		}

		$this->APIparams['rclimit'] = $limit;
		$api = new ApiMain(new FauxRequest($this->APIparams));
		$api->execute();
		$res = $api->getResult()->getResultData();
		$out = array();

		if(isset($res['query']) && isset($res['query']['recentchanges'])) {
			$out['results'] = $res['query']['recentchanges'];
		}

		// FIXME: this is not how query continuation works any longer AFAIK
		if(isset($res['query-continue'])) {
			$out['query-continue'] = $res['query-continue']['recentchanges']['rcstart'];
		}

		// haleyjd: remove metadata keys from query results
		$out = ApiResult::stripMetadata($out);
		
		return $out;
	}

}
