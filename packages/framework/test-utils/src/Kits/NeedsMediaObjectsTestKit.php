<?php

namespace Smolblog\Test\Kits;

use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Core\ContentV1\Media\Media;
use Smolblog\Foundation\Value\Fields\Identifier;

trait NeedsMediaObjectsTestKit {
	#[TestDox('It implements the NeedsMediaObjects interface.')]
	public function testNeedsMediaObjects() {
		$result = [];
		$actual = $this->subject->getMediaIds();
		$this->assertIsArray($actual);
		foreach ($actual as $md) {
			$this->assertInstanceOf(Identifier::class, $md);
			$result[] = $this->createStub(Media::class);
		}

		// Test this does not throw an error.
		$this->subject->setMediaObjects($result);
	}
}
