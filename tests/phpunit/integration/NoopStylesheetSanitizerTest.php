<?php

namespace MediaWiki\Extension\TemplateStylesUnlimited\Tests\Integration;

use MediaWiki\Context\RequestContext;
use MediaWiki\Extension\TemplateStylesUnlimited\Tests\CreatePageTestTrait;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Title\Title;
use MediaWikiLangTestCase;

/**
 * @group TemplateStylesUnlimited
 * @group Database
 * @covers \MediaWiki\Extension\TemplateStylesUnlimited\NoopStylesheetSanitizer
 * @covers \MediaWiki\Extension\TemplateStylesUnlimited\Hooks::onTemplateStylesStylesheetSanitizer
 */
class NoopStylesheetSanitizerTest extends MediaWikiLangTestCase {
	use CreatePageTestTrait;

	public function testCSSIsUnsanitized() {
		$this->assertStatusGood( $this->createPage(
			'MediaWiki:My template style.css',
			<<<EOF
			.test:has(.test) {
				background-image: url("https://example.com/image.png");
				color: var(--test-color);
			}
			EOF,
		) );
		$revisionId = Title::newFromText( 'MediaWiki:My template style.css' )->getLatestRevID();
		$popt = ParserOptions::newFromContext( RequestContext::getMain() );
		$popt->setWrapOutputClass( 'templatestylesunlimited-test' );
		$services = $this->getServiceContainer();
		$parser = $services->getParserFactory()->create();
		$out = $parser->parse(
			'<templatestyles src="MediaWiki:My template style.css" />',
			Title::newFromText( 'Test' ),
			$popt
		);
		$parsed = $out->runOutputPipeline( $popt )->getContentHolderText();
		$this->assertEquals(
			$parsed,
			// phpcs:disable Generic.Files.LineLength
			"<div class=\"mw-content-ltr templatestylesunlimited-test\" lang=\"en\" dir=\"ltr\"><style data-mw-deduplicate=\"TemplateStyles:r$revisionId/templatestylesunlimited-test\">.test:has(.test) { background-image: url(\"https://example.com/image.png\"); color: var(--test-color); }</style></div>",
			// phpcs:enable
		);
	}
}
