<?php

namespace Smolblog\Core\Plugin;

use PHPUnit\Framework\TestCase;
use Smolblog\App\Smolblog;
use Smolblog\Core\Endpoint\{EndpointRequest, EndpointResponse};
use Smolblog\Test\EndpointTestToolkit;

class TestPlugin implements Plugin {
	public static function config(): PluginPackage {
		return new PluginPackage(
			package: 'smolblog/test',
			version: '1.0.0',
			title: 'Smolblog Test Plugin',
			description: 'A test plugin for a test system.',
		);
	}

	public static function setup(Smolblog $app) {}
}

final class InstalledPluginsTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$this->endpoint = new InstalledPlugins(installedPlugins: [TestPlugin::class]);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest();
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}
}
