<?php

namespace Smolblog\Test;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;

class TestCase extends PHPUnitTestCase {
	protected mixed $subject;

	protected function randomId(bool $scrub = false): Identifier {
		return $this->scrubId(new RandomIdentifier());
	}

	protected function scrubId(Identifier $id): Identifier {
		return Identifier::fromByteString($id->toByteString());
	}
}
