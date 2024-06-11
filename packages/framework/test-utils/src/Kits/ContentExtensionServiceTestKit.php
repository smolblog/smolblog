<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\Content\ContentExtensionConfiguration;

trait ContentExtensionServiceTestKit {
	public function testItProvidesAValidContentExtensionConfiguration() {
		$this->assertInstanceOf(ContentExtensionConfiguration::class, get_class($this->subject)::getConfiguration());
	}
}
