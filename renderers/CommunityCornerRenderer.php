<?php

class CommunityCornerRenderer extends FeedRenderer {
	public function __construct() {
		parent::__construct('community-corner');
	}

	public function render($data, $wrap = true, $parameters = array()) {
		global $wgUser;
		$isAdmin = $wgUser->isAllowed('editinterface');
		$this->template->set('data', $data);
		$this->template->set('isAdmin', $isAdmin);
		$content = $this->template->render('community.corner');
		if ( $wrap ) {
			$content = $this->wrap($content, false);
		}
		return $content;
	}
}
