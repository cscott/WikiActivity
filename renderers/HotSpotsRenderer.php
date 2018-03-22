<?php

class HotSpotsRenderer extends FeedRenderer {

	public function __construct() {
		parent::__construct('hot-spots');
	}

	public function render($data) {
		$this->template->set('data', $data);
		$content = $this->template->render('hot.spots');
		//$content = $this->wrap($content, false);

		return $content;
	}
}
