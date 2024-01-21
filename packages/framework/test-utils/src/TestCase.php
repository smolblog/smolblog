<?php

namespace Smolblog\Test;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\RandomIdentifier;

class TestCase extends PHPUnitTestCase {
	protected mixed $subject;

	protected function randomId(bool $scrub = false): Identifier {
		$id = new RandomIdentifier();

		return $scrub ? $this->scrubId($id) : $id;
	}

	protected function scrubId(Identifier $id): Identifier {
		return Identifier::fromByteString($id->toByteString());
	}
}
