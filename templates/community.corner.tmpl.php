<section class="CommunityCornerModule module">
	<h2><?=wfMessage('myhome-community-corner-header')->text()?></h2>
	<div id="myhome-community-corner-content"><?=wfMessage('community-corner')->inContentLanguage()->parse()?></div>

<?php if ( $isAdmin ) { ?>
	<div id="myhome-community-corner-edit"><a class="more" href="<?=Title::newFromText('community-corner', NS_MEDIAWIKI)->getLocalURL('action=edit')?> rel="nofollow"><?=wfMessage('oasis-myhome-community-corner-edit')->text()?></a></div>
<?php } ?>
</section>
