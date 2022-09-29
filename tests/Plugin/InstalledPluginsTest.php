<?php

namespace Smolblog\Core\Plugin;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Endpoint\{EndpointRequest, EndpointResponse};
use Smolblog\Test\EndpointTestToolkit;

final class InstalledPluginsTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$this->endpoint = new InstalledPlugins(
			installedPackages: [
				PluginPackage::createFromComposer('smoltest/plugin-stub'),
				PluginPackage::createFromComposer('notfound'),
			],
			activePlugins: ['smoltest/plugin-stub' => $this->createStub(\Smoltest\PluginStub\Plugin::class)],
		);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest();
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}
}
