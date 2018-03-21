<nav class="activity-nav">
	<ul>
		<?php if ( $loggedIn ) {
			if ( $type == 'watchlist' ){
				?><li class="watchlist"><?= FeedRenderer::getSpecialPageLink('WikiActivity/activity', true, 'myhome-activity-feed') ?></li><?
			} else {
				?><li class="watchlist"><?= FeedRenderer::getSpecialPageLink('WikiActivity/watchlist', true, 'oasis-button-wiki-activity-watchlist') ?></li><?
			}
		} ?>
		<li><?= FeedRenderer::getSpecialPageLink('RecentChanges', false, 'oasis-button-wiki-activity-feed') ?></li>
	</ul>
<?php
	// render checkbox select default view
	if ($showDefaultViewSwitch) {
?>
	<p>
		<input type="checkbox" id="wikiactivity-default-view-switch" data-type="<?= $type ?>" disabled="disabled">
		<label for="wikiactivity-default-view-switch"><?= wfMessage('myhome-default-view-checkbox', wfMessage("myhome-{$type}-feed")->text())->text() ?></label>
	</p>
<?php
	}
?>
</nav>