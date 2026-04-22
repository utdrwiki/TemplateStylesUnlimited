<?php

namespace MediaWiki\Extension\TemplateStylesUnlimited\Maintenance;

use MediaWiki\Context\RequestContext;
use MediaWiki\Maintenance\Maintenance;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

// @codeCoverageIgnoreStart
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";
// @codeCoverageIgnoreEnd

class FixContentModels extends Maintenance {
	private RequestContext $context;

	public function __construct() {
		parent::__construct();
		$this->context = RequestContext::getMain();
		$this->requireExtension( 'TemplateStylesUnlimited' );
		$this->addDescription( $this->msg( 'templatestylesunlimited-fix-content-models-desc' ) );
	}

	/**
	 * Shorthand method to grab a plaintext localized message.
	 * @param string $key Message key
	 * @param string ...$args Message parameters
	 * @return string Plaintext message
	 */
	private function msg( string $key, string ...$args ) {
		return $this->context->msg( $key, ...$args )->plain();
	}

	public function execute() {
		$user = User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] );
		$services = $this->getServiceContainer();

		// Fetch pages to update
		$dbr = $this->getReplicaDB();
		$pages = $services->getPageStore()
			->newSelectQueryBuilder()
			->where( [
				$dbr->expr( 'page_namespace', '!=', NS_MEDIAWIKI ),
				'page_content_model' => 'sanitized-css',
			] )
			->fetchPageRecords();

		// Change content models
		$contentModelChangeFactory = $services->getContentModelChangeFactory();
		$statusFormatter = $services->getFormatterFactory()->getStatusFormatter( $this->context );
		foreach ( $pages as $page ) {
			$this->output( $this->msg(
				'templatestylesunlimited-fix-content-models-updating-page',
				Title::newFromPageIdentity( $page )->getPrefixedDBkey()
			) );
			$status = $contentModelChangeFactory
				->newContentModelChange( $user, $page, CONTENT_MODEL_CSS )
				->doContentModelChange(
					$this->context,
					$this->msg( 'templatestylesunlimited-fix-content-models-summary' ),
					true
				);
			if ( !$status->isOK() ) {
				$this->fatalError( $statusFormatter->getMessage( $status )->plain() );
			}
		}

		// Report success
		$this->output( $this->msg( 'templatestylesunlimited-fix-content-models-done' ) );
	}
}

// @codeCoverageIgnoreStart
$maintClass = FixContentModels::class;
require_once RUN_MAINTENANCE_IF_MAIN;
// @codeCoverageIgnoreEnd
