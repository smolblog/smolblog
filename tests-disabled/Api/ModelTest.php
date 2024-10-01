<?php

namespace Smolblog\Api;

use Smolblog\Test\TestCase;

class ModelTest extends TestCase {
	public function testAllEndpointsCanBeRegistered() {
		foreach (Model::getDependencyMap() as $class => $params) {
			if (!in_array(Endpoint::class, class_implements($class))) { continue; }

			$this->assertInstanceOf(EndpointConfig::class, $class::getConfiguration());
		}
	}
}
