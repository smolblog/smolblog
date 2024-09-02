<?php

namespace Smolblog\ContentProvenance;

use Elephox\Mimey\MimeTypesInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Foundation\DomainModel;

/**
 * Domain model for Content Provenance and Authenticity.
 */
class Model extends DomainModel {
	public const SERVICES = [
		ManifestService::class => [
			'env' => ContentProvenanceEnvironment::class,
			'mimes' => MimeTypesInterface::class,
			'logs' => LoggerInterface::class,
		],
	];
}
