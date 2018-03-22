<?php

class HotSpotsRenderer extends FeedRenderer {

	public function __construct() {
		parent::__construct('hot-spots');
	}

	public function render($data, $wrap = true, $parameters = array()) {
		$this->template->set('data', $data);
		$content = $this->template->render('hot.spots');
		if ( $wrap ) {
			$content = $this->wrap($content, false);
		}

		return $content;
	}
}
