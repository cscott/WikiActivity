<?/*<div class="activityfeed reset clearfix">*/?>

<h2 class="dark_text_2"><?= wfMessage("myhome-{$type}-feed")->text() ?></h2>

<?php
	echo $defaultSwitch;
?>
<div id="myhome-<?= $type ?>-feed-content">
<?php
	echo $content;
?>
</div>
<?php
	if (!empty($showMore)) {
?>
	<div class="myhome-feed-more"><a id="myhome-<?= $type ?>-feed-more" onclick="MyHome.fetchMore(this)" rel="nofollow"><?= wfMessage('myhome-activity-more')->text() ?></a></div>
<?php
	}
?>
<?/*</div>*/?>
