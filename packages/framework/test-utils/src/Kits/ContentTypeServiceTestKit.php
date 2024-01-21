<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\Content\ContentTypeConfiguration;

trait ContentTypeServiceTestKit {
	public function testItProvidesAValidContentTypeConfiguration() {
		$this->assertInstanceOf(ContentTypeConfiguration::class, get_class($this->subject)::getConfiguration());
	}
}
