<?php

namespace Smolblog\Core\Connection;

use Smolblog\Core\Connection\Entities\AuthRequestState;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Entities\ConnectionInitData;
use Smolblog\Core\Connection\Services\ConnectionHandler;
use Smolblog\Core\Connection\Services\NoRefreshKit;
use Smolblog\Test\TestCase;

require_once __DIR__ . '/_base.php';

final class NoRefreshKitTest extends TestCase {
	private ConnectionHandler $handler;

	protected function setUp(): void {
		// Using NoRefreshKit should remove the need to implement any refresh-related methods.
		// A failure here should be considered a test failure!
		$this->handler = new class($this->makeConnection()) implements ConnectionHandler {
			use NoRefreshKit;
			public static function getKey(): string { return 'test'; }
			public function __construct(private Connection $conn) {}
			public function getInitializationData(string $callbackUrl): ConnectionInitData {
				return new ConnectionInitData(url: '//smol.blog/', state: 'abc123', info: []);
			}
			public function createConnection(string $code, AuthRequestState $info): Connection { return $this->conn; }
			public function getChannels(Connection $connection): array { return []; }
		};
	}

	public function testNeedsRefresh() {
		$this->assertFalse($this->handler->connectionNeedsRefresh($this->makeConnection()));
	}

	public function testRefreshConnection() {
		$conn = $this->makeConnection();
		$this->assertEquals($conn, $this->handler->refreshConnection($conn));
	}

	private function makeConnection(): Connection {
		return new Connection(
			userId: $this->randomId(),
			handler: 'test',
			handlerKey: $this->randomId()->toString(),
			displayName: 'Test',
			details: [],
		);
	}
}
