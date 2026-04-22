<?php

namespace MediaWiki\Extension\TemplateStylesUnlimited\Tests\Integration;

use MediaWiki\Extension\TemplateStylesUnlimited\Tests\CreatePageTestTrait;
use MediaWikiLangTestCase;

/**
 * @group TemplateStylesUnlimited
 * @group Database
 * @covers \MediaWiki\Extension\TemplateStylesUnlimited\Hooks
 */
class HooksTest extends MediaWikiLangTestCase {
	use CreatePageTestTrait;

	public function provideAllowedContentModel(): array {
		return [
			'Allowed to create template styles in MediaWiki namespace' => [
				'MediaWiki:Template style.css',
				null,
			],
			'Not allowed to create template styles outside of MediaWiki namespace' => [
				'Template:Template/style.css',
				'content-not-allowed-here',
			],
		];
	}

	/**
	 * @dataProvider provideAllowedContentModel
	 */
	public function testAllowedContentModel(
		string $page,
		?string $expectedErrorMessage,
	) {
		$status = $this->createPage( $page, '' );
		if ( $expectedErrorMessage === null ) {
			$this->assertStatusGood( $status );
		} else {
			$this->assertStatusMessage( $expectedErrorMessage, $status );
		}
	}
}
