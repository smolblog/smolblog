<?php

namespace Smolblog\IntegrationTest\ReferenceApp;

use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\KeypairGenerator;

class Model extends DomainModel {
	public const AUTO_SERVICES = [
		KeypairGenerator::class,
	];
}
