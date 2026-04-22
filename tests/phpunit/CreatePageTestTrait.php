<?php

namespace MediaWiki\Extension\TemplateStylesUnlimited\Tests;

use MediaWiki\Content\ContentHandler;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Status\Status;
use MediaWiki\Title\Title;

trait CreatePageTestTrait {
	protected function createPage(
		string $page,
		string $text,
		string $contentModel = 'sanitized-css',
	): Status {
		$title = Title::newFromText( $page );
		$content = ContentHandler::makeContent( $text, $title, $contentModel );
		$services = $this->getServiceContainer();
		$page = $services->getWikiPageFactory()->newFromTitle( $title );
		$user = static::getTestSysop()->getUser();
		$updater = $services->getPageUpdaterFactory()
			->newPageUpdater( $page, $user )
			->setContent( SlotRecord::MAIN, $content );
		$updater->saveRevision( 'Test for TemplateStylesUnlimited' );
		return $updater->getStatus();
	}
}
