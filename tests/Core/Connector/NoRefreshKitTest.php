<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Entities\Connection;

final class TestUnrefreshableConnector {
	use NoRefreshKit;
}

final class NoRefreshKitTest extends TestCase {
	public function testItsConnectionsNeverNeedRefreshing() {
		$connector = new TestUnrefreshableConnector();

		$this->assertFalse($connector->connectionNeedsRefresh($this->createStub(Connection::class)));
	}

	public function testItAlwaysReturnsTheSameConnectionWhenRefreshing() {
		$connection = $this->createStub(Connection::class);
		$connector = new TestUnrefreshableConnector();

		$this->assertEquals($connection, $connector->refreshConnection($connection));
	}
}
