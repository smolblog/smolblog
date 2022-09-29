<?php

namespace Smolblog\Core\Plugin;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\App;
use Smoltest\PluginStub\Plugin as TestPlugin;

final class PluginPackageTest extends TestCase {
	public function testItCanFindComposerConfigAndCreateFromIt() {
		$package = PluginPackage::createFromComposer('smoltest/plugin-stub');

		$this->assertEquals('smoltest/plugin-stub', $package->package);
		$this->assertEquals('1.2.3', $package->version);
		$this->assertEquals('Test Plugin', $package->description);
		$this->assertEquals(json_decode('[{"name":"Smolblog Tester","email":"dev@smolblog.org"}]'), $package->authors);
		$this->assertEquals('Plugin Stub', $package->title);
		$this->assertEquals([], $package->errors);

		$app = $this->createStub(App::class);
		$this->assertInstanceOf(TestPlugin::class, $package->createPlugin(app: $app));
	}

	public function testItDoesNotFindComposerConfigAndGivesEmptyPackage() {
		$package = PluginPackage::createFromComposer('notfound');

		$this->assertEquals('notfound', $package->package);
		$this->assertEquals('0', $package->version);
		$this->assertEquals('', $package->description);
		$this->assertEquals([], $package->authors);
		$this->assertEquals('notfound', $package->title);
		$this->assertEquals(['Package `notfound` could not be processed.'], $package->errors);

		$app = $this->createStub(App::class);
		$this->assertNull($package->createPlugin(app: $app));
	}
}
