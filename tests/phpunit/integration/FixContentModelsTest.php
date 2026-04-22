<?php

namespace MediaWiki\Extension\TemplateStylesUnlimited\Tests\Integration;

use MediaWiki\Extension\TemplateStylesUnlimited\Maintenance\FixContentModels;
use MediaWiki\Extension\TemplateStylesUnlimited\Tests\CreatePageTestTrait;
use MediaWiki\Tests\Maintenance\MaintenanceBaseTestCase;
use MediaWiki\Title\Title;

/**
 * @group TemplateStylesUnlimited
 * @group Database
 * @covers \MediaWiki\Extension\TemplateStylesUnlimited\Maintenance\FixContentModels
 */
class FixContentModelsTest extends MaintenanceBaseTestCase {
	use CreatePageTestTrait;

	private const TEMPLATE_PAGE_NAME = 'Template:Test/style.css';
	private const MW_CSS_PAGE_NAME = 'MediaWiki:Should remain.css';
	private const WIKITEXT_PAGE_NAME = 'Test';
	private const JSON_PAGE_NAME = 'Category:Test.json';

	protected function getMaintenanceClass() {
		return FixContentModels::class;
	}

	public function addDBDataOnce() {
		$this->clearHook( 'ContentModelCanBeUsedOn' );
		$this->assertStatusGood( $this->createPage( self::TEMPLATE_PAGE_NAME, '' ) );
		$this->assertStatusGood( $this->createPage( self::MW_CSS_PAGE_NAME, '' ) );
		$this->assertStatusGood( $this->createPage( self::WIKITEXT_PAGE_NAME, '', CONTENT_MODEL_WIKITEXT ) );
		$this->assertStatusGood( $this->createPage( self::JSON_PAGE_NAME, '{}', CONTENT_MODEL_JSON ) );
	}

	public function testFixesContentModels() {
		$this->maintenance->execute();
		$this->expectOutputRegex( '/Done updating/' );
		$this->expectOutputRegex( '|Updating content model for page ' . self::TEMPLATE_PAGE_NAME . '|' );
		$templatePage = Title::newFromText( self::TEMPLATE_PAGE_NAME );
		$mwCssPage = Title::newFromText( self::MW_CSS_PAGE_NAME );
		$wikitextPage = Title::newFromText( self::WIKITEXT_PAGE_NAME );
		$jsonPage = Title::newFromText( self::JSON_PAGE_NAME );
		$this->assertEquals( CONTENT_MODEL_CSS, $templatePage->getContentModel() );
		$this->assertEquals( 'sanitized-css', $mwCssPage->getContentModel() );
		$this->assertEquals( CONTENT_MODEL_WIKITEXT, $wikitextPage->getContentModel() );
		$this->assertEquals( CONTENT_MODEL_JSON, $jsonPage->getContentModel() );
	}
}
