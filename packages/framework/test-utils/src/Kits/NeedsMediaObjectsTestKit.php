<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\Content\Media\Media;
use Smolblog\Framework\Objects\Identifier;

trait NeedsMediaObjectsTestKit {
	/** @testdox It implements the NeedsMediaObjects interface. */
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
