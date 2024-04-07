<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\ContentV1\ContentExtensionConfiguration;

trait ContentExtensionServiceTestKit {
	public function testItProvidesAValidContentExtensionConfiguration() {
		$this->assertInstanceOf(ContentExtensionConfiguration::class, get_class($this->subject)::getConfiguration());
	}
}
