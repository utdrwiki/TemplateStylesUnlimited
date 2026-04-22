<?php
/**
 * @license GPL-2.0-or-later
 *
 * @file
 */

namespace MediaWiki\Extension\TemplateStylesUnlimited;

use MediaWiki\Content\Hook\ContentModelCanBeUsedOnHook;
use MediaWiki\Extension\TemplateStyles\Hooks\TemplateStylesStylesheetSanitizerHook;
use MediaWiki\Title\Title;
use Wikimedia\CSS\Grammar\MatcherFactory;
use Wikimedia\CSS\Sanitizer\StylePropertySanitizer;
use Wikimedia\CSS\Sanitizer\StylesheetSanitizer;

class Hooks implements
	ContentModelCanBeUsedOnHook,
	TemplateStylesStylesheetSanitizerHook
{
	/**
	 * Prevents the TemplateStyles content model from being used outside of
	 * the MediaWiki namespace, because it is completely unrestricted in our
	 * implementation.
	 * @param mixed $modelId Content mode ID
	 * @param Title $title Title object of the page being edited
	 * @param mixed &$ok Whether the content model can be used
	 * @return void|bool False to abort further processing
	 */
	public function onContentModelCanBeUsedOn( $modelId, $title, &$ok ) {
		if ( $modelId === 'sanitized-css' && !$title->isSiteCssConfigPage() ) {
			$ok = false;
			return false;
		}
	}

	/**
	 * Replace TemplateStyles's sanitizer with a no-op sanitizer.
	 * @param StylesheetSanitizer &$sanitizer
	 * @param StylePropertySanitizer $propertySanitizer
	 * @param MatcherFactory $matcherFactory
	 * @return false To abort further processing
	 */
	public function onTemplateStylesStylesheetSanitizer(
		StylesheetSanitizer &$sanitizer,
		StylePropertySanitizer $propertySanitizer,
		MatcherFactory $matcherFactory
	): bool {
		$sanitizer = new NoopStylesheetSanitizer();
		return false;
	}
}
