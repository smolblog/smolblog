<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\Content\Media\Media;
use Smolblog\Core\Content\Media\NeedsMediaObjects;

trait NeedsMediaRenderedTestKit {
	/** @testdox It implements the NeedsMediaRendered interface. */
	public function testNeedsMediaRendered() {
		if (in_array(NeedsMediaObjects::class, class_implements($this->subject))) {
			$this->subject->setMediaObjects([
				$this->createStub(Media::class),
				$this->createStub(Media::class),
				$this->createStub(Media::class),
			]);
		}

		$result = [];
		$actual = $this->subject->getMediaObjects();
		$this->assertIsArray($actual);
		foreach ($actual as $md) {
			$this->assertInstanceOf(Media::class, $md);
			$result[] = '<img src="sonk.jpg">';
		}

		// Test this does not throw an error.
		$this->subject->setMediaHtml($result);
	}
}
