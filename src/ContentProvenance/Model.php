<?php

namespace Smolblog\ContentProvenance;

use Psr\Log\LoggerInterface;
use Smolblog\Framework\Objects\DomainModel;

/**
 * Domain model for Content Provenance and Authenticity.
 */
class Model extends DomainModel {
	public const SERVICES = [
		ManifestService::class => [
			'env' => ContentProvenanceEnvironment::class,
			'logs' => LoggerInterface::class,
		],
	];
}
